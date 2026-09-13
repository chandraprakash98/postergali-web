<?php

namespace App\Services\Batches;

use App\Models\BatchRunLog;
use Carbon\Carbon;

class BatchMonitorService
{
    /**
     * Get simplified status report for all batches (B1 & B2).
     *
     * @return array
     */
    public function getBatchStatuses(): array
    {
        $timezone = (string) config('posters.batches.timezone', 'Asia/Kolkata');

        $batchDefs = [
            'b1' => [
                'name'        => 'B1',
                'label'       => '⚡ Batch B1 (High Frequency)',
                'description' => 'Checks expired posters, posters expiring in 1 day, and view milestones',
                'tasks'       => [
                    '⏰ Posters expiring in 1 day (reminder FCM)',
                    '⚠️ Posters expired (expiration FCM & status update)',
                    '🎉 View milestones crossed (100, 200, 300...)',
                ],
                'schedule'    => (string) config('posters.batches.b1.schedule', BatchB1Service::DEFAULT_SCHEDULE),
                'enabled'     => (bool) config('posters.batches.b1.enabled', true),
            ],
            'b2' => [
                'name'        => 'B2',
                'label'       => '🌙 Batch B2 (Evening 7 PM)',
                'description' => 'Checks view milestones and extensible evening notification tasks',
                'tasks'       => [
                    '🎉 View milestones check & celebration alerts',
                    '➕ Extensible for additional evening checks',
                ],
                'schedule'    => (string) config('posters.batches.b2.schedule', BatchB2Service::DEFAULT_SCHEDULE),
                'enabled'     => (bool) config('posters.batches.b2.enabled', true),
            ],
        ];

        $report = [];

        foreach ($batchDefs as $id => $def) {
            $name     = $def['name'];
            $cron     = $def['schedule'];
            $enabled  = $def['enabled'];

            // Query last execution from batch_run_logs
            $lastRunLog = BatchRunLog::where('batch_name', $name)
                ->latest('ran_at')
                ->first();

            $lastRunIst = $lastRunLog && $lastRunLog->ran_at
                ? $lastRunLog->ran_at->copy()->timezone($timezone)
                : null;

            // Calculate upcoming run using CronExpression
            $upcomingIst = $enabled ? BatchHelper::getNextRun($cron, $timezone) : null;

            $report[] = [
                'id'            => $id,
                'name'          => $name,
                'label'         => $def['label'],
                'description'   => $def['description'],
                'tasks'         => $def['tasks'],
                'schedule'      => $cron,
                'scheduleHuman' => BatchHelper::getScheduleHuman($cron),
                'timezone'      => $timezone,
                'enabled'       => $enabled,
                'status'        => $enabled ? 'Active' : 'Disabled',
                'totalRuns'     => BatchRunLog::where('batch_name', $name)->count(),
                'totalSent'     => (int) BatchRunLog::where('batch_name', $name)->sum('notifications_sent'),

                // Last run details
                'lastRunFormatted' => $lastRunIst ? $lastRunIst->format('d M Y, h:i A') . ' IST' : 'Never ran',
                'lastRunHuman'     => $lastRunLog && $lastRunLog->ran_at ? $lastRunLog->ran_at->diffForHumans() : null,
                'lastRunStatus'    => $lastRunLog ? ucfirst($lastRunLog->status) : null,
                'lastRunSent'      => $lastRunLog ? (int) $lastRunLog->notifications_sent : 0,
                'lastRunSkipped'   => $lastRunLog ? (int) $lastRunLog->skipped_no_token : 0,

                // Upcoming run details
                'upcomingFormatted' => $upcomingIst ? $upcomingIst->format('d M Y, h:i A') . ' IST' : 'N/A',
                'upcomingHuman'     => $upcomingIst ? $upcomingIst->diffForHumans() : 'Disabled',
            ];
        }

        return [
            'batches'  => $report,
            'timezone' => $timezone,
        ];
    }
}
