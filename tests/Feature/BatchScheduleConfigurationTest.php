<?php

namespace Tests\Feature;

use App\Services\Batches\BatchB1Service;
use App\Services\Batches\BatchB2Service;
use App\Services\Batches\BatchHelper;
use App\Services\PosterExpiryNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Tests\TestCase;

class BatchScheduleConfigurationTest extends TestCase
{
    public function test_batch_b1_and_b2_constants_are_defined(): void
    {
        $this->assertSame('*/2 * * * *', BatchB1Service::DEFAULT_SCHEDULE);
        $this->assertSame('0 19 * * *', BatchB2Service::DEFAULT_SCHEDULE);
        $this->assertSame('Asia/Kolkata', BatchB1Service::DEFAULT_TIMEZONE);
    }

    public function test_batch_helper_computes_human_schedule_and_future_runs(): void
    {
        $this->assertSame('Every 2 minutes', BatchHelper::getScheduleHuman('*/2 * * * *'));
        $this->assertSame('Every evening at 7:00 PM IST', BatchHelper::getScheduleHuman('0 19 * * *'));

        $nextB1 = BatchHelper::getNextRun('*/2 * * * *', 'Asia/Kolkata');
        $this->assertTrue($nextB1->isFuture());
        $this->assertSame('Asia/Kolkata', $nextB1->timezoneName);

        $nextB2 = BatchHelper::getNextRun('0 19 * * *', 'Asia/Kolkata');
        $this->assertTrue($nextB2->isFuture());
        $this->assertSame('Asia/Kolkata', $nextB2->timezoneName);
    }

    public function test_batch_schedule_is_configurable_via_config(): void
    {
        config(['posters.batches.b1.schedule' => '*/5 * * * *']);
        $this->assertSame('*/5 * * * *', config('posters.batches.b1.schedule'));

        $nextRun = BatchHelper::getNextRun(config('posters.batches.b1.schedule'), 'Asia/Kolkata');
        $this->assertTrue($nextRun->isFuture());
    }

    public function test_laravel_scheduler_registers_b1_and_b2_batches_with_india_timezone(): void
    {
        $schedule = $this->app->make(Schedule::class);
        $events = collect($schedule->events());

        $b1Event = $events->first(function ($event) {
            return str_contains($event->command, 'batch:b1');
        });

        $b2Event = $events->first(function ($event) {
            return str_contains($event->command, 'batch:b2');
        });

        $this->assertNotNull($b1Event, 'Expected batch:b1 command to be registered in scheduler.');
        $this->assertSame(config('posters.batches.b1.schedule', '*/2 * * * *'), $b1Event->expression);
        $this->assertSame('Asia/Kolkata', $b1Event->timezone);

        $this->assertNotNull($b2Event, 'Expected batch:b2 command to be registered in scheduler.');
        $this->assertSame(config('posters.batches.b2.schedule', '0 19 * * *'), $b2Event->expression);
        $this->assertSame('Asia/Kolkata', $b2Event->timezone);
    }
}
