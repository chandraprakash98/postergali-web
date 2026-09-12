<?php

namespace App\Services;

use App\Models\Job;
use App\Models\Offer;
use Illuminate\Support\Facades\Log;

class PosterMilestoneNotificationService
{
    public function __construct(
        protected FirebaseNotificationService $firebaseService = new FirebaseNotificationService(),
        protected PosterExpiryNotificationService $expiryService = new PosterExpiryNotificationService(),
    ) {}

    /**
     * Check if a poster has crossed any view milestones (e.g. 100, 200, 300)
     * and send a celebration notification to the owner.
     *
     * @param Job|Offer $poster
     * @param string $type 'job' or 'offer'
     * @return int|null The milestone achieved (e.g. 100, 200, 300) or null if none
     */
    public function checkAndNotifyViewMilestone(Job|Offer $poster, string $type = 'job'): ?int
    {
        $thresholds = (array) config('posters.view_milestones.thresholds', [100, 200, 300, 400, 500]);
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
        $token = $this->expiryService->resolveFcmToken($phone, $poster->device_id);

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
            $this->firebaseService->sendToToken($token, $title, $combinedBody, [
                'type' => 'view_milestone',
                'item_type' => $type,
                'item_id' => $poster->id,
                'business_name' => $poster->business_name,
                'milestone_views' => (string) $achievedMilestone,
                'current_views' => (string) $currentViews,
            ]);
        } else {
            Log::info("Milestone {$achievedMilestone} views reached for {$type} #{$poster->id}, but no FCM token found.");
        }

        // Mark milestone as notified
        $poster->last_view_milestone_notified = $achievedMilestone;
        $poster->save();

        return $achievedMilestone;
    }
}
