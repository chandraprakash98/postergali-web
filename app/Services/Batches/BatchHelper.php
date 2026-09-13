<?php

namespace App\Services\Batches;

use Carbon\Carbon;
use Cron\CronExpression;

class BatchHelper
{
    /**
     * Compute the next upcoming run Carbon instance in the specified timezone for a given cron string.
     */
    public static function getNextRun(string $cronExpression, string $timezone = 'Asia/Kolkata'): Carbon
    {
        try {
            $cron = new CronExpression($cronExpression);
            $nextDateTime = $cron->getNextRunDate('now', 0, false, $timezone);
            return Carbon::instance($nextDateTime)->timezone($timezone);
        } catch (\Throwable) {
            return Carbon::now($timezone)->addMinutes(2);
        }
    }

    /**
     * Return human-friendly description for cron expressions.
     */
    public static function getScheduleHuman(string $cronExpression): string
    {
        $map = [
            '*/2 * * * *' => 'Every 2 minutes',
            '*/5 * * * *' => 'Every 5 minutes',
            '*/15 * * * *' => 'Every 15 minutes',
            '0 19 * * *'  => 'Every evening at 7:00 PM IST',
            '0 6 * * *'   => 'Everyday at 6:00 AM IST',
            '* * * * *'   => 'Every minute',
        ];

        return $map[$cronExpression] ?? $cronExpression;
    }
}
