<?php

namespace App\Console\Commands;

use App\Services\PosterExpiryNotificationService;
use Illuminate\Console\Command;

class CheckPosterExpiryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posters:check-expiry {--dry-run : Check without sending notifications or mutating the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired and expiring Job and Offer posters and send Firebase FCM notifications.';

    /**
     * Execute the console command.
     */
    public function handle(PosterExpiryNotificationService $service): int
    {
        $dryRun = (bool) $this->option('dry-run');

        \Illuminate\Support\Facades\Log::info("🚀 [Batch:posters:check-expiry] Started (dryRun=" . ($dryRun ? 'true' : 'false') . ")");

        $this->info('Starting poster expiry check batch...');
        if ($dryRun) {
            $this->warn('Running in DRY-RUN mode. No notifications will be sent and no database records modified.');
        }

        $result = $service->processExpiringPosters($dryRun);

        if (($result['status'] ?? '') === 'disabled') {
            $this->warn('Poster expiry notification batch is currently DISABLED in config (POSTER_EXPIRY_NOTIFICATION_ENABLED=false).');
            \Illuminate\Support\Facades\Log::warning("⚠️ [Batch:posters:check-expiry] Skipped - batch is DISABLED in config.");
            return Command::SUCCESS;
        }

        \Illuminate\Support\Facades\Log::info("✅ [Batch:posters:check-expiry] Finished. Sent={$result['notifications_sent']}, Skipped={$result['skipped_no_token']}, MilestonesSent={$result['milestones_sent']}");

        $this->table(
            ['Metric', 'Value'],
            [
                ['Day-Before-Expiry Jobs Found', $result['day_before_jobs'] ?? 0],
                ['Day-Before-Expiry Offers Found', $result['day_before_offers'] ?? 0],
                ['On-Expiry (Expired) Jobs Found', $result['on_expiry_jobs'] ?? 0],
                ['On-Expiry (Expired) Offers Found', $result['on_expiry_offers'] ?? 0],
                ['View Milestone Notifications Sent', $result['milestones_sent'] ?? 0],
                ['View Milestones Skipped (No Token)', $result['milestones_skipped'] ?? 0],
                ['Total FCM Notifications Dispatched', $result['notifications_sent']],
                ['Posters Skipped (No FCM Token)', $result['skipped_no_token']],
                ['Dry Run Mode', $result['dry_run'] ? 'Yes' : 'No'],
            ]
        );

        $this->info('Poster expiry check batch completed successfully.');

        return Command::SUCCESS;
    }
}
