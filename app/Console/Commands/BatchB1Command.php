<?php

namespace App\Console\Commands;

use App\Services\Batches\BatchB1Service;
use Illuminate\Console\Command;

class BatchB1Command extends Command
{
    protected $signature = 'batch:b1 {--dry-run : Run without sending notifications or updating database}';
    protected $description = 'Execute Batch B1 (every 2 mins): check expired, expiring in 1 day, and view milestones.';

    public function handle(BatchB1Service $service): int
    {
        $dryRun = (bool) $this->option('dry-run');
        if ($dryRun) {
            $this->warn('Running Batch B1 in DRY RUN mode. No notifications will be dispatched.');
        }

        $this->info('Starting Batch B1...');
        $result = $service->execute($dryRun);

        $this->table(
            ['Task', 'Found', 'Sent', 'Skipped'],
            [
                ['Expired Posters', $result['expired']['found'], $result['expired']['sent'], $result['expired']['skipped']],
                ['Expiring Soon (1 day)', $result['expiring']['found'], $result['expiring']['sent'], $result['expiring']['skipped']],
                ['View Milestones', $result['milestones']['found'], $result['milestones']['sent'], $result['milestones']['skipped']],
                ['TOTAL', '-', $result['total_sent'], $result['total_skipped']],
            ]
        );

        $this->info("Batch B1 completed in {$result['duration_ms']}ms.");
        return Command::SUCCESS;
    }
}
