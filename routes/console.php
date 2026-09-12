<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use App\Services\PosterExpiryNotificationService;

// Centralized configurable poster expiry check batch (Default: runs everyday at 6:00 AM India Standard Time)
$expirySchedule = PosterExpiryNotificationService::getSchedule();
$expiryTimezone = PosterExpiryNotificationService::getTimezone();

Schedule::command('posters:check-expiry')
    ->cron($expirySchedule)
    ->timezone($expiryTimezone)
    ->withoutOverlapping()
    ->runInBackground();
