<?php

namespace Tests\Feature;

use App\Services\PostergaliAlphaBatchService;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Tests\TestCase;

class BatchScheduleConfigurationTest extends TestCase
{
    public function test_postergali_alpha_constants_are_defined(): void
    {
        $this->assertSame('postergali-alpha', PostergaliAlphaBatchService::BATCH_NAME);
        $this->assertSame('0 6 * * *', PostergaliAlphaBatchService::DEFAULT_SCHEDULE);
        $this->assertSame('Asia/Kolkata', PostergaliAlphaBatchService::DEFAULT_TIMEZONE);
    }

    public function test_postergali_alpha_computes_human_schedule_and_future_runs(): void
    {
        $this->assertSame('Everyday at 6:00 AM IST', PostergaliAlphaBatchService::getScheduleHuman('0 6 * * *'));
        $this->assertSame('Every 2 minutes', PostergaliAlphaBatchService::getScheduleHuman('*/2 * * * *'));
        $this->assertSame('Everyday at 07:30 PM', PostergaliAlphaBatchService::getScheduleHuman('30 19 * * *'));

        $nextAlpha = PostergaliAlphaBatchService::getNextRun('0 6 * * *', 'Asia/Kolkata');
        $this->assertTrue($nextAlpha->isFuture());
        $this->assertSame('Asia/Kolkata', $nextAlpha->timezoneName);
    }

    public function test_batch_schedule_is_customizable_via_config(): void
    {
        config(['posters.batch.alpha.schedule' => '30 9 * * *']);
        $this->assertSame('30 9 * * *', PostergaliAlphaBatchService::getSchedule());

        $nextRun = PostergaliAlphaBatchService::getNextRun(PostergaliAlphaBatchService::getSchedule(), 'Asia/Kolkata');
        $this->assertTrue($nextRun->isFuture());
    }

    public function test_laravel_scheduler_registers_postergali_alpha_batch_with_india_timezone(): void
    {
        $schedule = $this->app->make(Schedule::class);
        $events = collect($schedule->events());

        $alphaEvent = $events->first(function ($event) {
            return str_contains($event->command, 'postergali-alpha');
        });

        $this->assertNotNull($alphaEvent, 'Expected postergali-alpha command to be registered in scheduler.');
        $this->assertSame(PostergaliAlphaBatchService::getSchedule(), $alphaEvent->expression);
        $this->assertSame('Asia/Kolkata', $alphaEvent->timezone);
    }
}
