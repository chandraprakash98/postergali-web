<?php

namespace App\Services\Batches;

use App\Models\BatchRunLog;
use App\Services\Batches\Actions\CheckExpiredPostersAction;
use App\Services\Batches\Actions\CheckExpiringSoonPostersAction;
use App\Services\Batches\Actions\CheckViewMilestonesAction;
use Illuminate\Support\Facades\Log;

class BatchB1Service
{
    public const BATCH_NAME = 'B1';
    public const DEFAULT_SCHEDULE = '*/2 * * * *';
    public const DEFAULT_TIMEZONE = 'Asia/Kolkata';

    public function __construct(
        protected CheckExpiredPostersAction $expiredAction = new CheckExpiredPostersAction(),
        protected CheckExpiringSoonPostersAction $expiringAction = new CheckExpiringSoonPostersAction(),
        protected CheckViewMilestonesAction $milestonesAction = new CheckViewMilestonesAction()
    ) {}

    /**
     * Run Batch B1 (high frequency, every 2 minutes):
     * 1. Checks expired posters
     * 2. Checks posters about to expire in 1 day
     * 3. Checks views milestones
     *
     * @param bool $dryRun
     * @return array
     */
    public function execute(bool $dryRun = false): array
    {
        $startTime = microtime(true);
        Log::info('[Batch:B1] Started' . ($dryRun ? ' (dry-run)' : ''));

        // 1. Expired posters
        $expiredResult = $this->expiredAction->execute($dryRun);

        // 2. Expiring soon posters (1 day)
        $expiringResult = $this->expiringAction->execute($dryRun);

        // 3. View milestones
        $milestonesResult = $this->milestonesAction->execute($dryRun);

        $totalSent = ($expiredResult['sent'] ?? 0)
            + ($expiringResult['sent'] ?? 0)
            + ($milestonesResult['sent'] ?? 0);

        $totalSkipped = ($expiredResult['skipped'] ?? 0)
            + ($expiringResult['skipped'] ?? 0)
            + ($milestonesResult['skipped'] ?? 0);

        $durationMs = (int) round((microtime(true) - $startTime) * 1000);

        if (!$dryRun) {
            BatchRunLog::create([
                'batch_name'         => self::BATCH_NAME,
                'ran_at'             => now(),
                'status'             => 'success',
                'dry_run'            => false,
                'day_before_jobs'    => $expiringResult['found'] ?? 0,
                'day_before_offers'  => 0,
                'on_expiry_jobs'     => $expiredResult['found'] ?? 0,
                'on_expiry_offers'   => 0,
                'notifications_sent' => $totalSent,
                'skipped_no_token'   => $totalSkipped,
                'duration_ms'        => $durationMs,
            ]);
        }

        Log::info("[Batch:B1] Completed in {$durationMs}ms. Sent={$totalSent}, Skipped={$totalSkipped}");

        return [
            'batch'       => self::BATCH_NAME,
            'expired'     => $expiredResult,
            'expiring'    => $expiringResult,
            'milestones'  => $milestonesResult,
            'total_sent'  => $totalSent,
            'total_skipped' => $totalSkipped,
            'duration_ms' => $durationMs,
        ];
    }
}
