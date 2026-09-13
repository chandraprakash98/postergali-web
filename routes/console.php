<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\PosterExpiryNotificationService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

$expiryTimezone = PosterExpiryNotificationService::getTimezone();
$expirySchedule = PosterExpiryNotificationService::getSchedule();

// ── Batch 1: Expiring posters notification (expires within 1 day) ───────────
Schedule::command('posters:notify-expiring')
    ->cron($expirySchedule)
    ->timezone($expiryTimezone)
    ->withoutOverlapping()
    ->runInBackground();

// ── Batch 2: Expired posters notification ─────────────────────────────────────
Schedule::command('posters:notify-expired')
    ->cron($expirySchedule)
    ->timezone($expiryTimezone)
    ->withoutOverlapping()
    ->runInBackground();

