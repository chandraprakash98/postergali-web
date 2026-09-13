<?php

namespace App\Console\Commands;

use App\Services\PosterExpiryNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class NotifyExpiringPostersCommand extends Command
{
    protected $signature = 'posters:notify-expiring {--dry-run : Run without sending notifications or updating database}';
    protected $description = 'Send FCM notification to customers whose poster expires within 1 day.';

    public function handle(PosterExpiryNotificationService $service): int
    {
        $dryRun = (bool) $this->option('dry-run');
        if ($dryRun) {
            $this->warn('Running in DRY RUN mode. No notifications will be dispatched.');
        }

        Log::info('[Batch:posters:notify-expiring] Started' . ($dryRun ? ' (dry-run)' : ''));
        $this->info('Running: notify expiring posters (expires within 1 day)...');

        $result = $service->sendExpiringNotifications($dryRun);

        $this->table(
            ['Metric', 'Value'],
            [
                ['Posters Found (expiring in 1 day)', $result['found']],
                ['Notifications Sent',               $result['sent']],
                ['Skipped (no FCM token)',            $result['skipped']],
            ]
        );

        Log::info("[Batch:posters:notify-expiring] Done. Found={$result['found']}, Sent={$result['sent']}, Skipped={$result['skipped']}");
        $this->info('Batch completed.');
        return Command::SUCCESS;
    }
}
