<?php

namespace App\Services\Batches\Actions;

use App\Services\PosterMilestoneNotificationService;

class CheckViewMilestonesAction
{
    public function __construct(
        protected PosterMilestoneNotificationService $milestoneService = new PosterMilestoneNotificationService()
    ) {}

    /**
     * Finds posters crossing view thresholds and dispatches milestone notifications.
     *
     * @param bool $dryRun
     * @return array ['found' => int, 'sent' => int, 'skipped' => int]
     */
    public function execute(bool $dryRun = false): array
    {
        $result = $this->milestoneService->checkAllPendingMilestones($dryRun);

        $sent = (int) ($result['milestones_sent'] ?? 0);
        $skipped = (int) ($result['milestones_skipped'] ?? 0);
        $found = $sent + $skipped;

        return compact('found', 'sent', 'skipped');
    }
}
