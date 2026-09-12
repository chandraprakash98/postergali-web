<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Configurable poster expiry check batch (Default: runs every 2 minutes)
$expirySchedule = config('posters.expiry_notification.schedule', '*/2 * * * *');
Schedule::command('posters:check-expiry')
    ->cron($expirySchedule)
    ->withoutOverlapping()
    ->runInBackground();
