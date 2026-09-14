<?php

namespace App\Console\Commands;

use App\Services\PostergaliAlphaBatchService;
use Illuminate\Console\Command;

class PostergaliAlphaBatchCommand extends Command
{
    protected $signature = 'postergali-alpha {--dry-run : Run without sending notifications or updating database}';
    protected $description = 'Execute PosterGali Alpha batch: f1 (day 1 before expiry) and f2 (expiring today).';

    public function handle(PostergaliAlphaBatchService $service): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->warn('Running postergali-alpha in DRY RUN mode. No notifications will be dispatched.');
        }

        $this->info('Starting postergali-alpha batch...');
        $result = $service->execute($dryRun);

        $this->table(
            ['Task', 'Posters Found', 'Sent', 'Skipped'],
            [
                [
                    'Function 1: Day 1 Before Expiry',
                    $result['day_before']['found'],
                    $result['day_before']['sent'],
                    $result['day_before']['skipped'],
                ],
                [
                    'Function 2: Expiring Today / Expired',
                    $result['expiring_today']['found'],
                    $result['expiring_today']['sent'],
                    $result['expiring_today']['skipped'],
                ],
                [
                    'TOTAL',
                    $result['day_before']['found'] + $result['expiring_today']['found'],
                    $result['total_sent'],
                    $result['total_skipped'],
                ],
            ]
        );

        $this->info("postergali-alpha completed in {$result['duration_ms']}ms.");
        return Command::SUCCESS;
    }
}
