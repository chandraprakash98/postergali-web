<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Job;
use App\Models\Notification;
use App\Models\Offer;
use App\Services\FirebaseNotificationService;
use App\Services\PosterExpiryNotificationService;
use App\Services\PosterMilestoneNotificationService;
use Illuminate\Console\Command;

class TestFcmCommand extends Command
{
    protected $signature = 'posters:test-fcm
                            {token? : Device FCM token to test}
                            {--link-job= : Job ID to link this token to}
                            {--link-offer= : Offer ID to link this token to}
                            {--link-phone= : Phone number to associate with this token in DB}
                            {--views= : Set views on the linked poster and trigger view milestone}
                            {--check-pending : Check all posters in DB for pending view milestones}';

    protected $description = 'Test FCM notifications, verify Firebase credentials, and trigger view milestones';

    public function handle(): int
    {
        $firebaseService  = new FirebaseNotificationService();
        $milestoneService = new PosterMilestoneNotificationService();
        $expiryService    = new PosterExpiryNotificationService();

        $token = $this->argument('token');
        if (empty($token)) {
            // Default to user's provided token
            $token = 'f-4H5YTTTPGRHq14MFPpgX:APA91bGn1TnFXYESvmFKLZ3YDIdlI2i89Mi199nP6CeEfEs05QIHhT02-vwOAEoJDr3ElOMvHDWgnLguCauQSOMhIiFqqhavGAGLJ2Tgds2LkCNP7nL_4zE';
        }

        $this->info("==================================================");
        $this->info("   PosterGali FCM & Milestone Diagnostics Tool    ");
        $this->info("==================================================");

        // 1. Check Firebase credentials file
        $credentialsPath = $firebaseService->getCredentialsPath();
        $this->line("Checking Firebase Service Account Credentials:");
        $this->line("Path: <comment>{$credentialsPath}</comment>");

        if (!file_exists($credentialsPath)) {
            $this->error("❌ Credentials file NOT found at: {$credentialsPath}");
            $this->warn("To fix this:");
            $this->warn("1. Upload your 'firebase-service-account.json' to the VPS at:");
            $this->warn("   /var/www/postergali-web/storage/app/firebase/firebase-service-account.json");
            $this->warn("2. Run: chmod 644 storage/app/firebase/firebase-service-account.json");
            $this->warn("3. Run: chown www-data:www-data storage/app/firebase/firebase-service-account.json");
            return 1;
        }

        if (!is_readable($credentialsPath)) {
            $this->error("❌ Credentials file exists but is NOT readable by PHP!");
            $this->warn("Run: chmod 644 {$credentialsPath}");
            return 1;
        }

        $this->info("✅ Firebase credentials file is valid and readable!");

        // 2. Link token to Job or Offer or Phone if requested
        $phone = $this->option('link-phone');
        $jobId = $this->option('link-job');
        $offerId = $this->option('link-offer');

        if ($jobId) {
            $job = Job::find($jobId);
            if ($job) {
                $job->device_id = $token;
                $job->save();
                $phone = $job->phone_number;
                $this->info("✅ Linked token directly to Job #{$job->id} (device_id updated).");
            } else {
                $this->warn("⚠️ Job #{$jobId} not found in database.");
            }
        }

        if ($offerId) {
            $offer = Offer::find($offerId);
            if ($offer) {
                $offer->device_id = $token;
                $offer->save();
                $phone = $offer->mobile_number;
                $this->info("✅ Linked token directly to Offer #{$offer->id} (device_id updated).");
            } else {
                $this->warn("⚠️ Offer #{$offerId} not found in database.");
            }
        }

        if ($phone) {
            // Save in customers table
            Customer::updateOrCreate(
                ['mobile' => $phone],
                ['fcm' => $token]
            );

            // Save in notifications table
            Notification::create([
                'mobile' => $phone,
                'fcm_tocken' => $token,
                'device_id' => substr($token, 0, 100),
            ]);

            $this->info("✅ Linked token to phone '{$phone}' in both 'customers' and 'notifications' tables!");
        }

        // 3. Set views and test milestone if requested
        $views = $this->option('views');
        if ($views !== null && ($jobId || $offerId)) {
            $viewsCount = (int) $views;
            if ($jobId && isset($job)) {
                $job->view_count = $viewsCount;
                $job->last_view_milestone_notified = 0; // reset for test
                $job->save();
                $this->info("Updated Job #{$job->id} view_count = {$viewsCount} (reset last milestone to 0).");

                $this->line("Triggering view milestone notification for Job #{$job->id}...");
                $achieved = $milestoneService->checkAndNotifyViewMilestone($job, 'job');
                if ($achieved) {
                    $this->info("🎉 View milestone {$achieved} notification dispatched successfully for Job #{$job->id}!");
                } else {
                    $this->warn("⚠️ Milestone not dispatched. Views ({$viewsCount}) may not meet threshold (100) or token resolution failed.");
                }
                return 0;
            } elseif ($offerId && isset($offer)) {
                $offer->view_count = $viewsCount;
                $offer->last_view_milestone_notified = 0;
                $offer->save();
                $this->info("Updated Offer #{$offer->id} view_count = {$viewsCount} (reset last milestone to 0).");

                $this->line("Triggering view milestone notification for Offer #{$offer->id}...");
                $achieved = $milestoneService->checkAndNotifyViewMilestone($offer, 'offer');
                if ($achieved) {
                    $this->info("🎉 View milestone {$achieved} notification dispatched successfully for Offer #{$offer->id}!");
                } else {
                    $this->warn("⚠️ Milestone not dispatched. Views ({$viewsCount}) may not meet threshold (100) or token resolution failed.");
                }
                return 0;
            }
        }

        // 4. Check all pending milestones if requested
        if ($this->option('check-pending')) {
            $this->line("Checking all posters in DB for pending view milestones...");
            $res = $milestoneService->checkAllPendingMilestones(false);
            $this->info("Checked: {$res['jobs_checked']} jobs, {$res['offers_checked']} offers.");
            $this->info("Dispatched: {$res['milestones_sent']} milestone notifications ({$res['milestones_skipped']} skipped due to missing token).");
            return 0;
        }

        // 5. Send Direct Test Push Notification
        $this->line("");
        $this->line("Sending Test Push Notification to token:");
        $this->line("<comment>" . substr($token, 0, 30) . "..." . substr($token, -15) . "</comment>");

        $result = $firebaseService->sendToToken(
            $token,
            "🎉 PosterGali FCM Test | टेस्ट सूचना",
            "🚀 Congratulations! Your FCM push notifications are working properly on PosterGali! | आपकी सूचनाएं सही तरीके से काम कर रही हैं।",
            [
                'type' => 'test_fcm',
                'sent_at' => now()->toIso8601String(),
            ]
        );

        if ($result['success']) {
            $this->info("==================================================");
            $this->info("✅ SUCCESS! Notification sent successfully via Firebase!");
            $this->info("Message ID: " . ($result['message_id'] ?? 'N/A'));
            $this->info("==================================================");
            $this->line("Please check your mobile device now!");
            return 0;
        }

        $this->error("==================================================");
        $this->error("❌ FAILED to send FCM notification!");
        $this->error("Reason: " . ($result['reason'] ?? $result['error'] ?? 'Unknown error'));
        $this->error("==================================================");
        return 1;
    }
}
