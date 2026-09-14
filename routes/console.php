<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\PostergaliAlphaBatchService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

$alphaSchedule = PostergaliAlphaBatchService::getSchedule();
$alphaTimezone = PostergaliAlphaBatchService::getTimezone();

// ── Batch: postergali-alpha ──────────────────────────────────────────────────
// Runs automated expiry notifications:
// f1: Day 1 before poster expiry reminder
// f2: Expiring today / expired notification & status update
if (PostergaliAlphaBatchService::isEnabled()) {
    Schedule::command('postergali-alpha')
        ->cron($alphaSchedule)
        ->timezone($alphaTimezone)
        ->withoutOverlapping()
        ->runInBackground();
}

