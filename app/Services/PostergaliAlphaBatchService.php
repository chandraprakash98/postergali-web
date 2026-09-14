<?php

namespace App\Services;

use App\Models\BatchRunLog;
use App\Models\Job;
use App\Models\Offer;
use Carbon\Carbon;
use Cron\CronExpression;
use Illuminate\Support\Facades\Log;

class PostergaliAlphaBatchService
{
    public const BATCH_NAME = 'postergali-alpha';
    public const DEFAULT_SCHEDULE = '0 6 * * *'; // Everyday at 6:00 AM IST
    public const DEFAULT_TIMEZONE = 'Asia/Kolkata';

    public function __construct(
        protected FirebaseNotificationService $firebaseService = new FirebaseNotificationService(),
        protected FcmTokenResolver $tokenResolver = new FcmTokenResolver()
    ) {}

    public static function getSchedule(): string
    {
        return (string) config('posters.batch.alpha.schedule', self::DEFAULT_SCHEDULE);
    }

    public static function getTimezone(): string
    {
        return (string) config('posters.batch.alpha.timezone', self::DEFAULT_TIMEZONE);
    }

    public static function isEnabled(): bool
    {
        return (bool) config('posters.batch.alpha.enabled', true);
    }

    /**
     * Compute next upcoming run in Carbon instance based on schedule and timezone.
     */
    public static function getNextRun(?string $schedule = null, ?string $timezone = null): Carbon
    {
        $cronStr = $schedule ?? self::getSchedule();
        $tz = $timezone ?? self::getTimezone();

        try {
            $cron = new CronExpression($cronStr);
            $nextDateTime = $cron->getNextRunDate('now', 0, false, $tz);
            return Carbon::instance($nextDateTime)->timezone($tz);
        } catch (\Throwable) {
            return Carbon::now($tz)->addMinutes(10);
        }
    }

    /**
     * Human-readable description of the cron schedule.
     */
    public static function getScheduleHuman(?string $schedule = null): string
    {
        $cron = $schedule ?? self::getSchedule();

        $map = [
            '0 6 * * *'   => 'Everyday at 6:00 AM IST',
            '0 7 * * *'   => 'Everyday at 7:00 AM IST',
            '0 8 * * *'   => 'Everyday at 8:00 AM IST',
            '0 9 * * *'   => 'Everyday at 9:00 AM IST',
            '0 10 * * *'  => 'Everyday at 10:00 AM IST',
            '0 18 * * *'  => 'Everyday at 6:00 PM IST',
            '0 19 * * *'  => 'Everyday at 7:00 PM IST',
            '0 20 * * *'  => 'Everyday at 8:00 PM IST',
            '*/1 * * * *' => 'Every minute',
            '* * * * *'   => 'Every minute',
            '*/2 * * * *' => 'Every 2 minutes',
            '*/5 * * * *' => 'Every 5 minutes',
            '*/10 * * * *'=> 'Every 10 minutes',
            '*/15 * * * *'=> 'Every 15 minutes',
            '*/30 * * * *'=> 'Every 30 minutes',
            '0 * * * *'   => 'Every hour',
        ];

        if (isset($map[$cron])) {
            return $map[$cron];
        }

        // Parse standard "m h * * *"
        if (preg_match('/^(\d+)\s+(\d+)\s+\*\s+\*\s+\*$/', $cron, $matches)) {
            $minute = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $hour   = (int) $matches[2];
            $amPm   = $hour >= 12 ? 'PM' : 'AM';
            $hour12 = $hour % 12;
            $hour12 = $hour12 === 0 ? 12 : $hour12;
            return sprintf('Everyday at %02d:%s %s', $hour12, $minute, $amPm);
        }

        return $cron;
    }

    /**
     * Function 1 (f1):
     * Responsible for sending notification 1 day before the poster expires (within 24h window).
     *
     * @param bool $dryRun
     * @return array ['jobs_found' => int, 'offers_found' => int, 'found' => int, 'sent' => int, 'skipped' => int]
     */
    public function sendDayBeforeExpiryNotifications(bool $dryRun = false): array
    {
        $windowHours = (int) config('posters.expiry_notification.window_hours', 24);
        $cutoff = now()->addHours($windowHours);

        $expiringJobs = Job::whereNotNull('expires_at')
            ->whereNull('day_before_expiry_notified_at')
            ->where('status', 'approved')
            ->where('expires_at', '>', now())
            ->where('expires_at', '<=', $cutoff)
            ->get();

        $expiringOffers = Offer::whereNotNull('expires_at')
            ->whereNull('day_before_expiry_notified_at')
            ->where('status', 'approved')
            ->where('expires_at', '>', now())
            ->where('expires_at', '<=', $cutoff)
            ->get();

        $jobsFound   = $expiringJobs->count();
        $offersFound = $expiringOffers->count();
        $sent        = 0;
        $skipped     = 0;

        foreach ($expiringJobs as $job) {
            if (!$dryRun) {
                if ($this->dispatchPreExpiryFcm($job, 'job')) {
                    $sent++;
                } else {
                    $skipped++;
                }
                $job->day_before_expiry_notified_at = now();
                $job->save();
            } else {
                $skipped++;
            }
        }

        foreach ($expiringOffers as $offer) {
            if (!$dryRun) {
                if ($this->dispatchPreExpiryFcm($offer, 'offer')) {
                    $sent++;
                } else {
                    $skipped++;
                }
                $offer->day_before_expiry_notified_at = now();
                $offer->save();
            } else {
                $skipped++;
            }
        }

        return [
            'jobs_found'   => $jobsFound,
            'offers_found' => $offersFound,
            'found'        => $jobsFound + $offersFound,
            'sent'         => $sent,
            'skipped'      => $skipped,
        ];
    }

    /**
     * Function 2 (f2):
     * Responsible for sending notification to posters whose expiry has arrived/passed (expiring today / expired).
     *
     * @param bool $dryRun
     * @return array ['jobs_found' => int, 'offers_found' => int, 'found' => int, 'sent' => int, 'skipped' => int]
     */
    public function sendExpiringTodayNotifications(bool $dryRun = false): array
    {
        $expiredJobs = Job::whereNotNull('expires_at')
            ->whereNull('expired_notified_at')
            ->where('status', 'approved')
            ->where('expires_at', '<=', now())
            ->get();

        $expiredOffers = Offer::whereNotNull('expires_at')
            ->whereNull('expired_notified_at')
            ->where('status', 'approved')
            ->where('expires_at', '<=', now())
            ->get();

        $jobsFound   = $expiredJobs->count();
        $offersFound = $expiredOffers->count();
        $sent        = 0;
        $skipped     = 0;

        foreach ($expiredJobs as $job) {
            if (!$dryRun) {
                if ($this->dispatchExpiredFcm($job, 'job')) {
                    $sent++;
                } else {
                    $skipped++;
                }
                $job->expired_notified_at = now();
                $job->status = 'expired';
                $job->save();
            } else {
                $skipped++;
            }
        }

        foreach ($expiredOffers as $offer) {
            if (!$dryRun) {
                if ($this->dispatchExpiredFcm($offer, 'offer')) {
                    $sent++;
                } else {
                    $skipped++;
                }
                $offer->expired_notified_at = now();
                $offer->status = 'expired';
                $offer->save();
            } else {
                $skipped++;
            }
        }

        return [
            'jobs_found'   => $jobsFound,
            'offers_found' => $offersFound,
            'found'        => $jobsFound + $offersFound,
            'sent'         => $sent,
            'skipped'      => $skipped,
        ];
    }

    /**
     * Master batch executor for postergali-alpha.
     * Executes f1 and f2, records execution log to batch_run_logs.
     */
    public function execute(bool $dryRun = false): array
    {
        $startTime = microtime(true);
        Log::info('[Batch:postergali-alpha] Started' . ($dryRun ? ' (dry-run)' : ''));

        // Function 1: Day 1 before expiry
        $dayBeforeResult = $this->sendDayBeforeExpiryNotifications($dryRun);

        // Function 2: Expiring today / expired
        $expiringTodayResult = $this->sendExpiringTodayNotifications($dryRun);

        $totalSent    = ($dayBeforeResult['sent'] ?? 0) + ($expiringTodayResult['sent'] ?? 0);
        $totalSkipped = ($dayBeforeResult['skipped'] ?? 0) + ($expiringTodayResult['skipped'] ?? 0);
        $durationMs   = (int) round((microtime(true) - $startTime) * 1000);

        if (!$dryRun) {
            BatchRunLog::create([
                'batch_name'         => self::BATCH_NAME,
                'ran_at'             => now(),
                'status'             => 'success',
                'dry_run'            => false,
                'day_before_jobs'    => $dayBeforeResult['jobs_found'] ?? 0,
                'day_before_offers'  => $dayBeforeResult['offers_found'] ?? 0,
                'on_expiry_jobs'     => $expiringTodayResult['jobs_found'] ?? 0,
                'on_expiry_offers'   => $expiringTodayResult['offers_found'] ?? 0,
                'notifications_sent' => $totalSent,
                'skipped_no_token'   => $totalSkipped,
                'duration_ms'        => $durationMs,
            ]);
        }

        Log::info(sprintf(
            '[Batch:postergali-alpha] Completed in %dms. DayBefore(F1)=%d sent, ExpiringToday(F2)=%d sent, Skipped=%d',
            $durationMs,
            $dayBeforeResult['sent'],
            $expiringTodayResult['sent'],
            $totalSkipped
        ));

        return [
            'batch'          => self::BATCH_NAME,
            'day_before'     => $dayBeforeResult,
            'expiring_today' => $expiringTodayResult,
            'total_sent'     => $totalSent,
            'total_skipped'  => $totalSkipped,
            'duration_ms'    => $durationMs,
            'dry_run'        => $dryRun,
        ];
    }

    /**
     * Dispatch FCM pre-expiry (1 day before) notification.
     */
    protected function dispatchPreExpiryFcm(Job|Offer $poster, string $type): bool
    {
        $phone = $type === 'job' ? $poster->phone_number : $poster->mobile_number;
        $token = $this->tokenResolver->resolve($phone, $poster->device_id);

        if (!$token) {
            Log::info("No FCM token found for 1-day-before expiring {$type} #{$poster->id}");
            return false;
        }

        $title = config('posters.day_before_expiry_notification.title', '⏰ Poster Expires Tomorrow | पोस्टर कल समाप्त हो रहा है');
        $body  = "⏰ Your poster \"{$poster->business_name}\" expires tomorrow! Create a new poster on PosterGali to keep your business live.\n"
               . "⏰ आपका पोस्टर \"{$poster->business_name}\" कल समाप्त हो रहा है! PosterGali पर नया पोस्टर बनाएं।";

        $result = $this->firebaseService->sendToToken($token, $title, $body, [
            'type'          => 'day_before_expiry',
            'item_type'     => $type,
            'item_id'       => (string) $poster->id,
            'business_name' => (string) $poster->business_name,
            'expires_at'    => $poster->expires_at?->toIso8601String() ?? '',
        ]);

        return !empty($result['success']);
    }

    /**
     * Dispatch FCM expired / expiring today notification.
     */
    protected function dispatchExpiredFcm(Job|Offer $poster, string $type): bool
    {
        $phone = $type === 'job' ? $poster->phone_number : $poster->mobile_number;
        $token = $this->tokenResolver->resolve($phone, $poster->device_id);

        if (!$token) {
            Log::info("No FCM token found for expired {$type} #{$poster->id}");
            return false;
        }

        $title = config('posters.on_expiry_notification.title', '⚠️ Poster Expired | पोस्टर समाप्त हो गया');
        $body  = "⚠️ Your poster \"{$poster->business_name}\" has expired. Create a new poster on PosterGali and keep reaching more people.\n"
               . "⚠️ आपके पोस्टर \"{$poster->business_name}\" की अवधि समाप्त हो गई। PosterGali पर नया पोस्टर बनाएं।";

        $result = $this->firebaseService->sendToToken($token, $title, $body, [
            'type'          => 'on_expiry',
            'item_type'     => $type,
            'item_id'       => (string) $poster->id,
            'business_name' => (string) $poster->business_name,
            'expires_at'    => $poster->expires_at?->toIso8601String() ?? '',
        ]);

        return !empty($result['success']);
    }

    /**
     * Get clean, simple status report for the admin panel.
     */
    public function getStatus(): array
    {
        $schedule = self::getSchedule();
        $timezone = self::getTimezone();
        $enabled  = self::isEnabled();

        $lastRunLog = BatchRunLog::where('batch_name', self::BATCH_NAME)
            ->latest('ran_at')
            ->first();

        $lastRunIst = $lastRunLog && $lastRunLog->ran_at
            ? $lastRunLog->ran_at->copy()->timezone($timezone)
            : null;

        $upcomingIst = $enabled ? self::getNextRun($schedule, $timezone) : null;

        $recentRuns = BatchRunLog::where('batch_name', self::BATCH_NAME)
            ->latest('ran_at')
            ->limit(15)
            ->get()
            ->map(function ($log) use ($timezone) {
                return [
                    'id'                 => $log->id,
                    'ran_at_formatted'   => $log->ran_at ? $log->ran_at->timezone($timezone)->format('d M Y, h:i:s A') : 'N/A',
                    'ran_at_human'       => $log->ran_at ? $log->ran_at->diffForHumans() : 'N/A',
                    'status'             => ucfirst($log->status ?? 'success'),
                    'day_before_jobs'    => (int) ($log->day_before_jobs ?? 0),
                    'day_before_offers'  => (int) ($log->day_before_offers ?? 0),
                    'on_expiry_jobs'     => (int) ($log->on_expiry_jobs ?? 0),
                    'on_expiry_offers'   => (int) ($log->on_expiry_offers ?? 0),
                    'notifications_sent' => (int) ($log->notifications_sent ?? 0),
                    'skipped_no_token'   => (int) ($log->skipped_no_token ?? 0),
                    'duration_ms'        => (int) ($log->duration_ms ?? 0),
                ];
            });

        return [
            'batch_name'         => self::BATCH_NAME,
            'enabled'            => $enabled,
            'status'             => $enabled ? 'Active' : 'Disabled',
            'schedule'           => $schedule,
            'schedule_human'     => self::getScheduleHuman($schedule),
            'timezone'           => $timezone,
            'total_runs'         => BatchRunLog::where('batch_name', self::BATCH_NAME)->count(),
            'total_sent'         => (int) BatchRunLog::where('batch_name', self::BATCH_NAME)->sum('notifications_sent'),

            // Last Run
            'last_run_formatted' => $lastRunIst ? $lastRunIst->format('d M Y, h:i A') . ' IST' : 'Never ran',
            'last_run_human'     => $lastRunLog && $lastRunLog->ran_at ? $lastRunLog->ran_at->diffForHumans() : null,
            'last_run_status'    => $lastRunLog ? ucfirst($lastRunLog->status) : null,
            'last_run_duration'  => $lastRunLog ? (int) $lastRunLog->duration_ms : 0,
            'last_run_sent'      => $lastRunLog ? (int) $lastRunLog->notifications_sent : 0,
            'last_run_skipped'   => $lastRunLog ? (int) $lastRunLog->skipped_no_token : 0,
            'last_run_day_before'=> $lastRunLog ? (int) ($lastRunLog->day_before_jobs + $lastRunLog->day_before_offers) : 0,
            'last_run_on_expiry' => $lastRunLog ? (int) ($lastRunLog->on_expiry_jobs + $lastRunLog->on_expiry_offers) : 0,

            // Upcoming Run
            'upcoming_formatted' => $upcomingIst ? $upcomingIst->format('d M Y, h:i A') . ' IST' : 'N/A',
            'upcoming_time_only' => $upcomingIst ? $upcomingIst->format('h:i A') . ' IST' : 'N/A',
            'upcoming_human'     => $upcomingIst ? $upcomingIst->diffForHumans() : 'Disabled',
            'upcoming_iso'       => $upcomingIst ? $upcomingIst->toIso8601String() : null,

            // Recent history
            'recent_runs'        => $recentRuns,
        ];
    }
}
