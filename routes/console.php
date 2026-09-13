<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\Batches\BatchB1Service;
use App\Services\Batches\BatchB2Service;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

$batchTimezone = (string) config('posters.batches.timezone', 'Asia/Kolkata');
$b1Schedule    = (string) config('posters.batches.b1.schedule', BatchB1Service::DEFAULT_SCHEDULE);
$b2Schedule    = (string) config('posters.batches.b2.schedule', BatchB2Service::DEFAULT_SCHEDULE);

// ── Batch B1: High Frequency (Every 2 mins) ──────────────────────────────────
// Checks: expired posters, posters expiring in 1 day, view milestones
Schedule::command('batch:b1')
    ->cron($b1Schedule)
    ->timezone($batchTimezone)
    ->withoutOverlapping()
    ->runInBackground();

// ── Batch B2: Evening Digest (Everyday at 7:00 PM IST) ────────────────────────
// Checks: view milestones (extensible for more evening tasks)
Schedule::command('batch:b2')
    ->cron($b2Schedule)
    ->timezone($batchTimezone)
    ->withoutOverlapping()
    ->runInBackground();
