<?php

namespace App\Services;

use App\Models\BatchRunLog;
use App\Models\Customer;
use App\Models\Job;
use App\Models\Notification;
use App\Models\Offer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PosterExpiryNotificationService
{
    // ── Schedule constants (used by console.php & admin UI) ──────────────────
    public const DEFAULT_SCHEDULE = '0 6 * * *'; // Every day at 6:00 AM IST
    public const DEFAULT_TIMEZONE = 'Asia/Kolkata';

    public static function getSchedule(): string
    {
        return (string) config('posters.expiry_notification.schedule', self::DEFAULT_SCHEDULE);
    }

    public static function getTimezone(): string
    {
        return (string) config('posters.expiry_notification.timezone', self::DEFAULT_TIMEZONE);
    }

    /**
     * Return a human-readable description of the configured cron schedule.
     * Handles the two common cases used in this project; falls back to the
     * raw cron expression for anything else.
     */
    public static function getScheduleHuman(): string
    {
        $cron = self::getSchedule();

        $knownLabels = [
            '0 6 * * *'   => 'Everyday at 6:00 AM IST',
            '*/15 * * * *' => 'Every 15 mins',
            '* * * * *'   => 'Every minute',
        ];

        return $knownLabels[$cron] ?? $cron;
    }

    /**
     * Calculate the next scheduled run time in the configured IST timezone.
     * Always returns a future Carbon instance.
     */
    public static function getNextRunIst(): Carbon
    {
        $timezone = self::getTimezone();
        $cron     = self::getSchedule();
        $now      = Carbon::now($timezone);

        // For the default daily-at-6am schedule, compute precisely
        if ($cron === self::DEFAULT_SCHEDULE) {
            $next = $now->copy()->setTime(6, 0, 0);
            if (!$next->isFuture()) {
                $next->addDay();
            }
            return $next;
        }

        // For */15 every-15-min schedule
        if ($cron === '*/15 * * * *') {
            $next = $now->copy()->addMinutes(15 - ($now->minute % 15))->setSecond(0);
            if (!$next->isFuture()) {
                $next->addMinutes(15);
            }
            return $next;
        }

        // Generic fallback: just add 1 minute so it is always in the future
        return $now->copy()->addMinute();
    }

    // ─────────────────────────────────────────────────────────────────────────

    public function __construct(
        protected FirebaseNotificationService $firebaseService = new FirebaseNotificationService(),
        protected PosterPostingLimitService $limitService = new PosterPostingLimitService(),
    ) {}

    // =========================================================================
    // BATCH 1 — Posters expiring within 1 day
    // =========================================================================

    /**
     * Find approved posters expiring within 24 hours and send a reminder FCM notification.
     * Marks each poster with `day_before_expiry_notified_at` so it is never double-notified.
     */
    public function sendExpiringNotifications(bool $dryRun = false): array
    {
        if (!config('posters.expiry_notification.enabled', true)) {
            Log::info('Expiring notifications disabled in config.');
            return ['found' => 0, 'sent' => 0, 'skipped' => 0];
        }

        $startTime = microtime(true);
        $cutoff    = now()->addHours(
            (int) config('posters.expiry_notification.window_hours', 24)
        );

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

        $sent    = 0;
        $skipped = 0;

        foreach ($expiringJobs as $job) {
            if (!$dryRun) {
                if ($this->sendExpiringFcm($job, 'job')) {
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
                if ($this->sendExpiringFcm($offer, 'offer')) {
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

        $found      = $expiringJobs->count() + $expiringOffers->count();
        $durationMs = (int) round((microtime(true) - $startTime) * 1000);

        if (!$dryRun) {
            BatchRunLog::create([
                'batch_name'         => 'notify-expiring',
                'ran_at'             => now(),
                'status'             => 'success',
                'dry_run'            => false,
                'day_before_jobs'    => $expiringJobs->count(),
                'day_before_offers'  => $expiringOffers->count(),
                'on_expiry_jobs'     => 0,
                'on_expiry_offers'   => 0,
                'notifications_sent' => $sent,
                'skipped_no_token'   => $skipped,
                'duration_ms'        => $durationMs,
            ]);
        }

        return compact('found', 'sent', 'skipped');
    }

    // =========================================================================
    // BATCH 2 — Posters that have already expired
    // =========================================================================

    /**
     * Find approved posters whose expiry date has passed and send an "expired" FCM notification.
     * Marks each poster with `expired_notified_at` and updates status to 'expired'.
     */
    public function sendExpiredNotifications(bool $dryRun = false): array
    {
        if (!config('posters.expiry_notification.enabled', true)) {
            Log::info('Expired notifications disabled in config.');
            return ['found' => 0, 'sent' => 0, 'skipped' => 0];
        }

        $startTime = microtime(true);

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

        $sent    = 0;
        $skipped = 0;

        foreach ($expiredJobs as $job) {
            if (!$dryRun) {
                if ($this->sendExpiredFcm($job, 'job')) {
                    $sent++;
                } else {
                    $skipped++;
                }
                $job->expired_notified_at = now();
                $job->status              = 'expired';
                $job->save();
            } else {
                $skipped++;
            }
        }

        foreach ($expiredOffers as $offer) {
            if (!$dryRun) {
                if ($this->sendExpiredFcm($offer, 'offer')) {
                    $sent++;
                } else {
                    $skipped++;
                }
                $offer->expired_notified_at = now();
                $offer->status              = 'expired';
                $offer->save();
            } else {
                $skipped++;
            }
        }

        $found      = $expiredJobs->count() + $expiredOffers->count();
        $durationMs = (int) round((microtime(true) - $startTime) * 1000);

        if (!$dryRun) {
            BatchRunLog::create([
                'batch_name'         => 'notify-expired',
                'ran_at'             => now(),
                'status'             => 'success',
                'dry_run'            => false,
                'day_before_jobs'    => 0,
                'day_before_offers'  => 0,
                'on_expiry_jobs'     => $expiredJobs->count(),
                'on_expiry_offers'   => $expiredOffers->count(),
                'notifications_sent' => $sent,
                'skipped_no_token'   => $skipped,
                'duration_ms'        => $durationMs,
            ]);
        }

        return compact('found', 'sent', 'skipped');
    }

    // =========================================================================
    // Private FCM Helpers
    // =========================================================================

    private function sendExpiringFcm(Job|Offer $poster, string $type): bool
    {
        $phone = $type === 'job' ? $poster->phone_number : $poster->mobile_number;
        $token = $this->resolveFcmToken($phone, $poster->device_id);

        if (!$token) {
            Log::info("No FCM token for expiring {$type} #{$poster->id}");
            return false;
        }

        $title = '⏰ Poster Expires Tomorrow | पोस्टर कल समाप्त हो रहा है';
        $body  = "⏰ Your poster \"{$poster->business_name}\" expires tomorrow! Create a new poster on PosterGali to keep your business live.\n"
               . "⏰ आपका पोस्टर \"{$poster->business_name}\" कल समाप्त हो रहा है! PosterGali पर नया पोस्टर बनाएं।";

        $this->firebaseService->sendToToken($token, $title, $body, [
            'type'          => 'day_before_expiry',
            'item_type'     => $type,
            'item_id'       => (string) $poster->id,
            'business_name' => (string) $poster->business_name,
            'expires_at'    => $poster->expires_at?->toIso8601String() ?? '',
        ]);

        return true;
    }

    private function sendExpiredFcm(Job|Offer $poster, string $type): bool
    {
        $phone = $type === 'job' ? $poster->phone_number : $poster->mobile_number;
        $token = $this->resolveFcmToken($phone, $poster->device_id);

        if (!$token) {
            Log::info("No FCM token for expired {$type} #{$poster->id}");
            return false;
        }

        $title = '⚠️ Poster Expired | पोस्टर समाप्त हो गया';
        $body  = "⚠️ Your poster \"{$poster->business_name}\" has expired. Create a new poster on PosterGali and keep reaching more people.\n"
               . "⚠️ आपके पोस्टर \"{$poster->business_name}\" की अवधि समाप्त हो गई। PosterGali पर नया पोस्टर बनाएं।";

        $this->firebaseService->sendToToken($token, $title, $body, [
            'type'          => 'on_expiry',
            'item_type'     => $type,
            'item_id'       => (string) $poster->id,
            'business_name' => (string) $poster->business_name,
            'expires_at'    => $poster->expires_at?->toIso8601String() ?? '',
        ]);

        return true;
    }

    // =========================================================================
    // FCM Token Resolution (unchanged logic)
    // =========================================================================

    public function resolveFcmToken(?string $phoneNumber, ?string $deviceId): ?string
    {
        $phoneNumber = trim((string) $phoneNumber);
        $deviceId    = trim((string) $deviceId);

        // 1. Customer table by phone
        if (!empty($phoneNumber)) {
            $variants = $this->limitService->getPhoneVariants($phoneNumber);
            if (!empty($variants)) {
                $customerToken = Customer::whereIn('mobile', $variants)
                    ->whereNotNull('fcm')
                    ->where('fcm', '!=', '')
                    ->value('fcm');
                if ($customerToken) {
                    return $customerToken;
                }
            }
        }

        // 2. Notification table by phone or device_id
        if (!empty($phoneNumber) || !empty($deviceId)) {
            $query = Notification::whereNotNull('fcm_tocken')->where('fcm_tocken', '!=', '');

            if (!empty($phoneNumber) && !empty($deviceId)) {
                $variants = $this->limitService->getPhoneVariants($phoneNumber);
                $query->where(function ($q) use ($variants, $deviceId) {
                    $q->whereIn('mobile', $variants)->orWhere('device_id', $deviceId);
                });
            } elseif (!empty($phoneNumber)) {
                $variants = $this->limitService->getPhoneVariants($phoneNumber);
                $query->whereIn('mobile', $variants);
            } else {
                $query->where('device_id', $deviceId);
            }

            $notificationToken = $query->latest('id')->value('fcm_tocken');
            if ($notificationToken) {
                return $notificationToken;
            }
        }

        // 3. device_id itself may be an FCM token (80+ chars, no spaces)
        if (strlen($deviceId) >= 80 && !str_contains($deviceId, ' ')) {
            return $deviceId;
        }

        return null;
    }
}
