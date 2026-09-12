<?php

namespace App\Services;

use App\Models\BatchRunLog;
use App\Models\Customer;
use App\Models\Job;
use App\Models\Notification;
use App\Models\Offer;
use Illuminate\Support\Facades\Log;

class PosterExpiryNotificationService
{
    public function __construct(
        protected FirebaseNotificationService $firebaseService = new FirebaseNotificationService(),
        protected PosterPostingLimitService $limitService = new PosterPostingLimitService(),
    ) {}

    /**
     * Process both "Day Before Expiry" and "On Poster Expiry" notifications.
     *
     * @param bool $dryRun If true, does not mutate the database or send real network requests
     * @return array Summary statistics of the batch run
     */
    public function processExpiringPosters(bool $dryRun = false): array
    {
        $startTime = microtime(true);
        $enabled = (bool) config('posters.expiry_notification.enabled', true);
        if (!$enabled) {
            $durationMs = (int) round((microtime(true) - $startTime) * 1000);
            BatchRunLog::create([
                'ran_at'            => now(),
                'status'            => 'disabled',
                'dry_run'           => $dryRun,
                'day_before_jobs'   => 0,
                'day_before_offers' => 0,
                'on_expiry_jobs'    => 0,
                'on_expiry_offers'  => 0,
                'notifications_sent'=> 0,
                'skipped_no_token'  => 0,
                'duration_ms'       => $durationMs,
            ]);
            return [
                'status'            => 'disabled',
                'day_before_jobs'   => 0,
                'day_before_offers' => 0,
                'on_expiry_jobs'    => 0,
                'on_expiry_offers'  => 0,
                'notifications_sent'=> 0,
                'skipped_no_token'  => 0,
            ];
        }

        $windowHours = (int) config('posters.expiry_notification.window_hours', 24);
        $dayBeforeCutoff = now()->addHours($windowHours);

        $notificationsSent = 0;
        $skippedNoToken = 0;

        // =========================================================================
        // 1. DAY BEFORE EXPIRY (expires_at > now() && expires_at <= cutoff)
        // =========================================================================
        $dayBeforeJobs = Job::whereNotNull('expires_at')
            ->whereNull('day_before_expiry_notified_at')
            ->where('status', '!=', 'rejected')
            ->where('expires_at', '>', now())
            ->where('expires_at', '<=', $dayBeforeCutoff)
            ->get();

        $dayBeforeOffers = Offer::whereNotNull('expires_at')
            ->whereNull('day_before_expiry_notified_at')
            ->where('status', '!=', 'rejected')
            ->where('expires_at', '>', now())
            ->where('expires_at', '<=', $dayBeforeCutoff)
            ->get();

        foreach ($dayBeforeJobs as $job) {
            $sent = $this->notifyDayBefore($job, 'job', $dryRun);
            if ($sent) {
                $notificationsSent++;
            } else {
                $skippedNoToken++;
            }

            if (!$dryRun) {
                $job->day_before_expiry_notified_at = now();
                $job->expiry_notified_at = now();
                $job->save();
            }
        }

        foreach ($dayBeforeOffers as $offer) {
            $sent = $this->notifyDayBefore($offer, 'offer', $dryRun);
            if ($sent) {
                $notificationsSent++;
            } else {
                $skippedNoToken++;
            }

            if (!$dryRun) {
                $offer->day_before_expiry_notified_at = now();
                $offer->expiry_notified_at = now();
                $offer->save();
            }
        }

        // =========================================================================
        // 2. ON POSTER EXPIRY (expires_at <= now())
        // =========================================================================
        $onExpiryJobs = Job::whereNotNull('expires_at')
            ->whereNull('expired_notified_at')
            ->where('status', '!=', 'rejected')
            ->where('expires_at', '<=', now())
            ->get();

        $onExpiryOffers = Offer::whereNotNull('expires_at')
            ->whereNull('expired_notified_at')
            ->where('status', '!=', 'rejected')
            ->where('expires_at', '<=', now())
            ->get();

        foreach ($onExpiryJobs as $job) {
            $sent = $this->notifyOnExpiry($job, 'job', $dryRun);
            if ($sent) {
                $notificationsSent++;
            } else {
                $skippedNoToken++;
            }

            if (!$dryRun) {
                $job->expired_notified_at = now();
                $job->expiry_notified_at = now();
                if ($job->status === 'approved') {
                    $job->status = 'expired';
                }
                $job->save();
            }
        }

        foreach ($onExpiryOffers as $offer) {
            $sent = $this->notifyOnExpiry($offer, 'offer', $dryRun);
            if ($sent) {
                $notificationsSent++;
            } else {
                $skippedNoToken++;
            }

            if (!$dryRun) {
                $offer->expired_notified_at = now();
                $offer->expiry_notified_at = now();
                if ($offer->status === 'approved') {
                    $offer->status = 'expired';
                }
                $offer->save();
            }
        }

        // 3. Process any pending view milestones (e.g. view_count increased directly in DB)
        $milestoneService = new PosterMilestoneNotificationService($this->firebaseService, $this);
        $milestoneResults = $milestoneService->checkAllPendingMilestones($dryRun);
        $notificationsSent += $milestoneResults['milestones_sent'];
        $skippedNoToken    += $milestoneResults['milestones_skipped'];

        $durationMs = (int) round((microtime(true) - $startTime) * 1000);

        $result = [
            'status'           => 'success',
            'day_before_jobs'  => $dayBeforeJobs->count(),
            'day_before_offers'=> $dayBeforeOffers->count(),
            'on_expiry_jobs'   => $onExpiryJobs->count(),
            'on_expiry_offers' => $onExpiryOffers->count(),
            'jobs_processed'   => $dayBeforeJobs->count() + $onExpiryJobs->count(),
            'offers_processed' => $dayBeforeOffers->count() + $onExpiryOffers->count(),
            'milestones_sent'  => $milestoneResults['milestones_sent'],
            'milestones_skipped'=> $milestoneResults['milestones_skipped'],
            'notifications_sent'=> $notificationsSent,
            'skipped_no_token' => $skippedNoToken,
            'dry_run'          => $dryRun,
        ];

        BatchRunLog::create([
            'ran_at'            => now(),
            'status'            => 'success',
            'dry_run'           => $dryRun,
            'day_before_jobs'   => $result['day_before_jobs'],
            'day_before_offers' => $result['day_before_offers'],
            'on_expiry_jobs'    => $result['on_expiry_jobs'],
            'on_expiry_offers'  => $result['on_expiry_offers'],
            'notifications_sent'=> $result['notifications_sent'],
            'skipped_no_token'  => $result['skipped_no_token'],
            'duration_ms'       => $durationMs,
        ]);

        return $result;
    }

    /**
     * Dispatch Day Before Expiry Notification.
     */
    protected function notifyDayBefore(Job|Offer $poster, string $type, bool $dryRun): bool
    {
        $phone = $type === 'job' ? $poster->phone_number : $poster->mobile_number;
        $token = $this->resolveFcmToken($phone, $poster->device_id);

        if (!$token) {
            Log::info("No FCM token found for Day Before Expiry on {$type} #{$poster->id}");
            return false;
        }

        $title = config(
            'posters.day_before_expiry_notification.title',
            '⏰ Poster Expires Tomorrow | पोस्टर कल समाप्त हो रहा है'
        );

        $bodyEn = config(
            'posters.day_before_expiry_notification.body_en',
            '⏰ Reminder: Your poster ":business_name" expires tomorrow! Create a new poster on PosterGali to keep your business live without interruption.'
        );

        $bodyHi = config(
            'posters.day_before_expiry_notification.body_hi',
            '⏰ सूचना: आपका पोस्टर ":business_name" कल समाप्त हो रहा है! अपना प्रचार बिना रुके जारी रखने के लिए PosterGali पर नया पोस्टर बनाएं।'
        );

        $replacements = [':business_name' => (string) $poster->business_name];
        $combinedBody = strtr($bodyEn, $replacements) . "\n" . strtr($bodyHi, $replacements);

        if (!$dryRun) {
            $this->firebaseService->sendToToken($token, $title, $combinedBody, [
                'type' => 'day_before_expiry',
                'item_type' => $type,
                'item_id' => $poster->id,
                'business_name' => $poster->business_name,
                'expires_at' => $poster->expires_at?->toIso8601String() ?? '',
            ]);
        }

        return true;
    }

    /**
     * Dispatch On Poster Expiry Notification.
     */
    protected function notifyOnExpiry(Job|Offer $poster, string $type, bool $dryRun): bool
    {
        $phone = $type === 'job' ? $poster->phone_number : $poster->mobile_number;
        $token = $this->resolveFcmToken($phone, $poster->device_id);

        if (!$token) {
            Log::info("No FCM token found for On Expiry on {$type} #{$poster->id}");
            return false;
        }

        $title = config(
            'posters.on_expiry_notification.title',
            '⚠️ Poster Expired | पोस्टर समाप्त हो गया'
        );

        $bodyEn = config(
            'posters.on_expiry_notification.body_en',
            '⚠️ Your poster ":business_name" has expired today. Create a new poster on PosterGali and keep reaching more people online.'
        );

        $bodyHi = config(
            'posters.on_expiry_notification.body_hi',
            '⚠️ आपके पोस्टर ":business_name" की अवधि आज समाप्त हो गई है। नया पोस्टर बनाएं और PosterGali पर अधिक लोगों तक पहुंचना जारी रखें।'
        );

        $replacements = [':business_name' => (string) $poster->business_name];
        $combinedBody = strtr($bodyEn, $replacements) . "\n" . strtr($bodyHi, $replacements);

        if (!$dryRun) {
            $this->firebaseService->sendToToken($token, $title, $combinedBody, [
                'type' => 'on_expiry',
                'item_type' => $type,
                'item_id' => $poster->id,
                'business_name' => $poster->business_name,
                'expires_at' => $poster->expires_at?->toIso8601String() ?? '',
            ]);
        }

        return true;
    }

    /**
     * Resolve the FCM device token using the poster phone number or device ID.
     */
    public function resolveFcmToken(?string $phoneNumber, ?string $deviceId): ?string
    {
        $phoneNumber = trim((string) $phoneNumber);
        $deviceId = trim((string) $deviceId);

        // 1. Search in Customer model by phone variants
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

        // 2. Search in Notification table by phone or device_id
        if (!empty($phoneNumber) || !empty($deviceId)) {
            $query = Notification::whereNotNull('fcm_tocken')->where('fcm_tocken', '!=', '');

            if (!empty($phoneNumber) && !empty($deviceId)) {
                $variants = $this->limitService->getPhoneVariants($phoneNumber);
                $query->where(function ($q) use ($variants, $deviceId) {
                    $q->whereIn('mobile', $variants)
                      ->orWhere('device_id', $deviceId);
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

        // 3. Fallback: Check if device_id itself is an FCM token (FCM tokens typically 80+ chars)
        if (strlen($deviceId) >= 80 && !str_contains($deviceId, ' ')) {
            return $deviceId;
        }

        return null;
    }
}
