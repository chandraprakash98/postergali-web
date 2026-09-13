<?php

namespace App\Services\Batches\Actions;

use App\Models\Job;
use App\Models\Offer;
use App\Services\FcmTokenResolver;
use App\Services\FirebaseNotificationService;
use Illuminate\Support\Facades\Log;

class CheckExpiredPostersAction
{
    public function __construct(
        protected FirebaseNotificationService $firebaseService = new FirebaseNotificationService(),
        protected FcmTokenResolver $tokenResolver = new FcmTokenResolver()
    ) {}

    /**
     * Finds posters whose expiry date has passed and dispatches FCM notification.
     *
     * @param bool $dryRun
     * @return array ['found' => int, 'sent' => int, 'skipped' => int]
     */
    public function execute(bool $dryRun = false): array
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

        $sent = 0;
        $skipped = 0;

        foreach ($expiredJobs as $job) {
            if (!$dryRun) {
                if ($this->sendExpiredFcm($job, 'job')) {
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
                if ($this->sendExpiredFcm($offer, 'offer')) {
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

        $found = $expiredJobs->count() + $expiredOffers->count();
        return compact('found', 'sent', 'skipped');
    }

    protected function sendExpiredFcm(Job|Offer $poster, string $type): bool
    {
        $phone = $type === 'job' ? $poster->phone_number : $poster->mobile_number;
        $token = $this->tokenResolver->resolve($phone, $poster->device_id);

        if (!$token) {
            Log::info("No FCM token for expired {$type} #{$poster->id}");
            return false;
        }

        $title = config('posters.on_expiry_notification.title', '⚠️ Poster Expired | पोस्टर समाप्त हो गया');
        $body = "⚠️ Your poster \"{$poster->business_name}\" has expired. Create a new poster on PosterGali and keep reaching more people.\n"
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
}
