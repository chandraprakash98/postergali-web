<?php

namespace Tests\Feature;

use App\Services\PosterExpiryNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Tests\TestCase;

class BatchScheduleConfigurationTest extends TestCase
{
    public function test_batch_schedule_constants_are_defined_for_everyday_6am_ist(): void
    {
        $this->assertSame('0 6 * * *', PosterExpiryNotificationService::DEFAULT_SCHEDULE);
        $this->assertSame('Asia/Kolkata', PosterExpiryNotificationService::DEFAULT_TIMEZONE);
    }

    public function test_get_schedule_returns_configured_schedule_or_constant_default(): void
    {
        $this->assertSame('0 6 * * *', PosterExpiryNotificationService::getSchedule());
        $this->assertSame('Asia/Kolkata', PosterExpiryNotificationService::getTimezone());
        $this->assertSame('Everyday at 6:00 AM IST', PosterExpiryNotificationService::getScheduleHuman());
    }

    public function test_get_next_run_ist_calculates_next_6am_india_time(): void
    {
        $nextRun = PosterExpiryNotificationService::getNextRunIst();

        $this->assertSame('Asia/Kolkata', $nextRun->timezoneName);
        $this->assertSame(6, (int) $nextRun->hour);
        $this->assertSame(0, (int) $nextRun->minute);
        $this->assertSame(0, (int) $nextRun->second);
        $this->assertTrue($nextRun->isFuture());
    }

    public function test_batch_schedule_is_configurable_via_config(): void
    {
        config(['posters.expiry_notification.schedule' => '*/15 * * * *']);

        $this->assertSame('*/15 * * * *', PosterExpiryNotificationService::getSchedule());
        $this->assertSame('Every 15 mins', PosterExpiryNotificationService::getScheduleHuman());

        $nextRun = PosterExpiryNotificationService::getNextRunIst();
        $this->assertTrue($nextRun->isFuture());
    }

    public function test_laravel_scheduler_registers_expiry_batch_with_india_timezone(): void
    {
        $schedule = $this->app->make(Schedule::class);
        $events = collect($schedule->events());

        $expiryEvent = $events->first(function ($event) {
            return str_contains($event->command, 'posters:check-expiry');
        });

        $this->assertNotNull($expiryEvent, 'Expected posters:check-expiry command to be registered in scheduler.');
        $this->assertSame(PosterExpiryNotificationService::getSchedule(), $expiryEvent->expression);
        $this->assertSame('Asia/Kolkata', $expiryEvent->timezone);
    }
}
