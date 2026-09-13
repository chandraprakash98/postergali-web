<?php

namespace App\Services;

use App\Models\Job;
use App\Models\Offer;
use Illuminate\Support\Facades\Log;

class PosterMilestoneNotificationService
{
    public function __construct(
        protected FirebaseNotificationService $firebaseService = new FirebaseNotificationService(),
        protected ?FcmTokenResolver $tokenResolver = null,
        protected ?PosterExpiryNotificationService $expiryService = null,
    ) {
        $this->tokenResolver = $tokenResolver ?? new FcmTokenResolver();
    }

    /**
     * Check if a poster has crossed any view milestones (e.g. 100, 200, 300)
     * and send a celebration notification to the owner.
     *
     * @param Job|Offer $poster
     * @param string $type 'job' or 'offer'
     * @param bool $dryRun
     * @return int|null The milestone achieved (e.g. 100, 200, 300) or null if none
     */
    public function checkAndNotifyViewMilestone(Job|Offer $poster, string $type = 'job', bool $dryRun = false): ?int
    {
        $thresholds = (array) config('posters.view_milestones.thresholds', [100, 200, 300, 400, 500, 1000]);
        sort($thresholds, SORT_NUMERIC);

        $currentViews = (int) $poster->view_count;
        $lastNotified = (int) ($poster->last_view_milestone_notified ?? 0);

        // Find the highest milestone that currentViews has reached/crossed, which is > lastNotified
        $achievedMilestone = null;
        foreach ($thresholds as $threshold) {
            if ($currentViews >= $threshold && $threshold > $lastNotified) {
                $achievedMilestone = $threshold;
            }
        }

        if (!$achievedMilestone) {
            return null;
        }

        // Resolve FCM token
        $phone = $type === 'job' ? $poster->phone_number : $poster->mobile_number;
        $token = $this->tokenResolver->resolve($phone, $poster->device_id);

        $title = config(
            'posters.view_milestones.title',
            '🎉 Views Milestone Crossed! | व्यूज माइलस्टोन सूचना'
        );

        $bodyEn = config(
            'posters.view_milestones.body_en',
            '🚀 Great news! Your poster ":business_name" has crossed :views views on PosterGali! Keep tracking your responses.'
        );

        $bodyHi = config(
            'posters.view_milestones.body_hi',
            '🚀 खुशखबरी! PosterGali पर आपके पोस्टर ":business_name" ने :views व्यूज पूरे कर लिए हैं! नए ग्राहकों से जुड़े रहें।'
        );

        $replacements = [
            ':business_name' => (string) $poster->business_name,
            ':views' => (string) $achievedMilestone,
        ];

        $resolvedBodyEn = strtr($bodyEn, $replacements);
        $resolvedBodyHi = strtr($bodyHi, $replacements);
        $combinedBody = $resolvedBodyEn . "\n" . $resolvedBodyHi;

        if ($token) {
            if (!$dryRun) {
                $sendResult = $this->firebaseService->sendToToken($token, $title, $combinedBody, [
                    'type' => 'view_milestone',
                    'item_type' => $type,
                    'item_id' => $poster->id,
                    'business_name' => $poster->business_name,
                    'milestone_views' => (string) $achievedMilestone,
                    'current_views' => (string) $currentViews,
                ]);

                if (!($sendResult['success'] ?? false)) {
                    Log::error("Milestone {$achievedMilestone} FCM send failed for {$type} #{$poster->id}: " . ($sendResult['error'] ?? $sendResult['reason'] ?? 'unknown'));
                    return null;
                }

                // Mark milestone as notified only when successfully dispatched
                $poster->last_view_milestone_notified = $achievedMilestone;
                $poster->save();
            }

            return $achievedMilestone;
        }

        Log::warning("Milestone {$achievedMilestone} views reached for {$type} #{$poster->id} (phone: {$phone}, device_id: {$poster->device_id}), but NO matching FCM token could be found.");
        return null;
    }

    /**
     * Check all posters in database that have pending view milestones
     * (e.g. view_count >= milestone and view_count > last_view_milestone_notified).
     *
     * @param bool $dryRun
     * @return array
     */
    public function checkAllPendingMilestones(bool $dryRun = false): array
    {
        $thresholds = (array) config('posters.view_milestones.thresholds', [100, 200, 300, 400, 500, 1000]);
        sort($thresholds, SORT_NUMERIC);
        $minThreshold = !empty($thresholds) ? (int) min($thresholds) : 100;

        $jobs = Job::where('view_count', '>=', $minThreshold)
            ->where(function ($q) use ($minThreshold) {
                $q->whereNull('last_view_milestone_notified')
                  ->orWhere('last_view_milestone_notified', '<', $minThreshold)
                  ->orWhereColumn('view_count', '>', 'last_view_milestone_notified');
            })
            ->get();

        $offers = Offer::where('view_count', '>=', $minThreshold)
            ->where(function ($q) use ($minThreshold) {
                $q->whereNull('last_view_milestone_notified')
                  ->orWhere('last_view_milestone_notified', '<', $minThreshold)
                  ->orWhereColumn('view_count', '>', 'last_view_milestone_notified');
            })
            ->get();

        $sentCount = 0;
        $skippedCount = 0;

        foreach ($jobs as $job) {
            $milestone = $this->checkAndNotifyViewMilestone($job, 'job', $dryRun);
            if ($milestone) {
                $sentCount++;
            } else {
                $skippedCount++;
            }
        }

        foreach ($offers as $offer) {
            $milestone = $this->checkAndNotifyViewMilestone($offer, 'offer', $dryRun);
            if ($milestone) {
                $sentCount++;
            } else {
                $skippedCount++;
            }
        }

        return [
            'milestones_sent'    => $sentCount,
            'milestones_skipped' => $skippedCount,
            'jobs_checked'       => $jobs->count(),
            'offers_checked'     => $offers->count(),
        ];
    }
}
