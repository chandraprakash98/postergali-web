<?php

namespace App\Console\Commands;

use App\Services\PosterExpiryNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class NotifyExpiredPostersCommand extends Command
{
    protected $signature = 'posters:notify-expired {--dry-run : Run without sending notifications or updating database}';
    protected $description = 'Send FCM notification to customers whose poster has expired.';

    public function handle(PosterExpiryNotificationService $service): int
    {
        $dryRun = (bool) $this->option('dry-run');
        if ($dryRun) {
            $this->warn('Running in DRY RUN mode. No notifications will be dispatched.');
        }

        Log::info('[Batch:posters:notify-expired] Started' . ($dryRun ? ' (dry-run)' : ''));
        $this->info('Running: notify expired posters...');

        $result = $service->sendExpiredNotifications($dryRun);

        $this->table(
            ['Metric', 'Value'],
            [
                ['Posters Found (expired)', $result['found']],
                ['Notifications Sent',      $result['sent']],
                ['Skipped (no FCM token)',  $result['skipped']],
            ]
        );

        Log::info("[Batch:posters:notify-expired] Done. Found={$result['found']}, Sent={$result['sent']}, Skipped={$result['skipped']}");
        $this->info('Batch completed.');
        return Command::SUCCESS;
    }
}
