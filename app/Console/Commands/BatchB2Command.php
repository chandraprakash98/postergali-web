<?php

namespace App\Console\Commands;

use App\Services\Batches\BatchB2Service;
use Illuminate\Console\Command;

class BatchB2Command extends Command
{
    protected $signature = 'batch:b2 {--dry-run : Run without sending notifications or updating database}';
    protected $description = 'Execute Batch B2 (evening 7 PM): check view milestones and evening alerts.';

    public function handle(BatchB2Service $service): int
    {
        $dryRun = (bool) $this->option('dry-run');
        if ($dryRun) {
            $this->warn('Running Batch B2 in DRY RUN mode. No notifications will be dispatched.');
        }

        $this->info('Starting Batch B2...');
        $result = $service->execute($dryRun);

        $this->table(
            ['Task', 'Found', 'Sent', 'Skipped'],
            [
                ['View Milestones', $result['milestones']['found'], $result['milestones']['sent'], $result['milestones']['skipped']],
                ['TOTAL', '-', $result['total_sent'], $result['total_skipped']],
            ]
        );

        $this->info("Batch B2 completed in {$result['duration_ms']}ms.");
        return Command::SUCCESS;
    }
}
