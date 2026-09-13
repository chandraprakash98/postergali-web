<?php

namespace App\Services\Batches;

use App\Models\BatchRunLog;
use App\Services\Batches\Actions\CheckViewMilestonesAction;
use Illuminate\Support\Facades\Log;

class BatchB2Service
{
    public const BATCH_NAME = 'B2';
    public const DEFAULT_SCHEDULE = '0 19 * * *'; // Everyday at 7:00 PM IST
    public const DEFAULT_TIMEZONE = 'Asia/Kolkata';

    public function __construct(
        protected CheckViewMilestonesAction $milestonesAction = new CheckViewMilestonesAction()
        // Future evening actions can be injected here easily
    ) {}

    /**
     * Run Batch B2 (evening 7:00 PM IST):
     * 1. Checks views milestones
     * (Future evening tasks can be added here)
     *
     * @param bool $dryRun
     * @return array
     */
    public function execute(bool $dryRun = false): array
    {
        $startTime = microtime(true);
        Log::info('[Batch:B2] Started' . ($dryRun ? ' (dry-run)' : ''));

        // 1. View milestones check
        $milestonesResult = $this->milestonesAction->execute($dryRun);

        // Future evening tasks: add below cleanly

        $totalSent = (int) ($milestonesResult['sent'] ?? 0);
        $totalSkipped = (int) ($milestonesResult['skipped'] ?? 0);
        $durationMs = (int) round((microtime(true) - $startTime) * 1000);

        if (!$dryRun) {
            BatchRunLog::create([
                'batch_name'         => self::BATCH_NAME,
                'ran_at'             => now(),
                'status'             => 'success',
                'dry_run'            => false,
                'day_before_jobs'    => 0,
                'day_before_offers'  => 0,
                'on_expiry_jobs'     => 0,
                'on_expiry_offers'   => 0,
                'notifications_sent' => $totalSent,
                'skipped_no_token'   => $totalSkipped,
                'duration_ms'        => $durationMs,
            ]);
        }

        Log::info("[Batch:B2] Completed in {$durationMs}ms. Sent={$totalSent}, Skipped={$totalSkipped}");

        return [
            'batch'         => self::BATCH_NAME,
            'milestones'    => $milestonesResult,
            'total_sent'    => $totalSent,
            'total_skipped' => $totalSkipped,
            'duration_ms'   => $durationMs,
        ];
    }
}
