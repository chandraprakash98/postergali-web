<?php

namespace App\Services\Batches\Actions;

use App\Models\Job;
use App\Models\Offer;
use App\Services\FcmTokenResolver;
use App\Services\FirebaseNotificationService;
use Illuminate\Support\Facades\Log;

class CheckExpiringSoonPostersAction
{
    public function __construct(
        protected FirebaseNotificationService $firebaseService = new FirebaseNotificationService(),
        protected FcmTokenResolver $tokenResolver = new FcmTokenResolver()
    ) {}

    /**
     * Finds posters expiring within the configured window (default 24 hours) and dispatches FCM reminder.
     *
     * @param bool $dryRun
     * @return array ['found' => int, 'sent' => int, 'skipped' => int]
     */
    public function execute(bool $dryRun = false): array
    {
        $cutoff = now()->addHours(
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

        $sent = 0;
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

        $found = $expiringJobs->count() + $expiringOffers->count();
        return compact('found', 'sent', 'skipped');
    }

    protected function sendExpiringFcm(Job|Offer $poster, string $type): bool
    {
        $phone = $type === 'job' ? $poster->phone_number : $poster->mobile_number;
        $token = $this->tokenResolver->resolve($phone, $poster->device_id);

        if (!$token) {
            Log::info("No FCM token for expiring {$type} #{$poster->id}");
            return false;
        }

        $title = config('posters.day_before_expiry_notification.title', '⏰ Poster Expires Tomorrow | पोस्टर कल समाप्त हो रहा है');
        $body = "⏰ Your poster \"{$poster->business_name}\" expires tomorrow! Create a new poster on PosterGali to keep your business live.\n"
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
}
