<?php

namespace Tests\Feature;

use App\Models\BatchRunLog;
use App\Models\Customer;
use App\Models\Job;
use App\Models\Notification;
use App\Models\Offer;
use App\Services\FirebaseNotificationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosterExpiryBatchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        FirebaseNotificationService::fake();
    }

    protected function tearDown(): void
    {
        FirebaseNotificationService::resetFake();
        parent::tearDown();
    }

    public function test_batch_expiring_detects_expiring_posters_and_sends_notification(): void
    {
        Customer::create([
            'customer_id' => 'PSTGL_TEST_01',
            'mobile' => '9876543210',
            'fcm' => 'fcm_token_test_123',
        ]);

        $job = Job::create([
            'temp_id' => 'temp-job-1',
            'device_id' => 'device-1',
            'device_os' => 'android',
            'master_category' => 'Services',
            'business_name' => 'Acme Corp',
            'job_role' => 'Developer',
            'job_type' => 'full_time',
            'phone_number' => '9876543210',
            'latitude' => 28.5914,
            'longitude' => 77.4021,
            'city' => 'Noida',
            'plan_id' => 'plan-1',
            'status' => 'approved',
            'approved_at' => now()->subDays(29),
            'expires_at' => now()->addHours(4),
        ]);

        $this->artisan('posters:notify-expiring')
            ->assertExitCode(0);

        $sent = FirebaseNotificationService::getSentMessages();
        $this->assertCount(1, $sent);

        $this->assertSame('fcm_token_test_123', $sent[0]['token']);
        $this->assertStringContainsString('expires tomorrow', $sent[0]['body']);
        $this->assertStringContainsString('कल समाप्त हो रहा है', $sent[0]['body']);

        $job->refresh();
        $this->assertNotNull($job->day_before_expiry_notified_at);

        // Check log recorded with batch_name
        $log = BatchRunLog::where('batch_name', 'notify-expiring')->first();
        $this->assertNotNull($log);
        $this->assertSame('success', $log->status);
        $this->assertSame(1, $log->notifications_sent);
    }

    public function test_batch_expired_detects_expired_posters_and_sends_notification(): void
    {
        Customer::create([
            'customer_id' => 'PSTGL_TEST_02',
            'mobile' => '9876543211',
            'fcm' => 'fcm_token_test_456',
        ]);

        $offer = Offer::create([
            'temp_id' => 'temp-offer-1',
            'device_id' => 'device-2',
            'device_os' => 'android',
            'master_category' => 'Retail',
            'business_name' => 'Mega Mart',
            'offer_details' => 'Mega sale',
            'mobile_number' => '9876543211',
            'latitude' => 28.5914,
            'longitude' => 77.4021,
            'city' => 'Noida',
            'plan_id' => 'plan-1',
            'status' => 'approved',
            'approved_at' => now()->subDays(30),
            'expires_at' => now()->subHours(2),
        ]);

        $this->artisan('posters:notify-expired')
            ->assertExitCode(0);

        $sent = FirebaseNotificationService::getSentMessages();
        $this->assertCount(1, $sent);

        $this->assertSame('fcm_token_test_456', $sent[0]['token']);
        $this->assertStringContainsString('has expired', $sent[0]['body']);
        $this->assertStringContainsString('की अवधि समाप्त हो गई', $sent[0]['body']);

        $offer->refresh();
        $this->assertNotNull($offer->expired_notified_at);
        $this->assertSame('expired', $offer->status);

        // Check log recorded with batch_name
        $log = BatchRunLog::where('batch_name', 'notify-expired')->first();
        $this->assertNotNull($log);
        $this->assertSame('success', $log->status);
        $this->assertSame(1, $log->notifications_sent);
    }

    public function test_subsequent_runs_do_not_send_duplicate_notifications(): void
    {
        Customer::create([
            'customer_id' => 'PSTGL_TEST_03',
            'mobile' => '9876543212',
            'fcm' => 'fcm_token_test_789',
        ]);

        Job::create([
            'temp_id' => 'temp-job-2',
            'device_id' => 'device-2',
            'device_os' => 'android',
            'master_category' => 'Services',
            'business_name' => 'Tech Corp',
            'job_role' => 'Designer',
            'phone_number' => '9876543212',
            'latitude' => 28.5914,
            'longitude' => 77.4021,
            'city' => 'Noida',
            'plan_id' => 'plan-1',
            'status' => 'approved',
            'expires_at' => now()->addHours(2),
        ]);

        // Run 1st time
        $this->artisan('posters:notify-expiring')->assertExitCode(0);
        $this->assertCount(1, FirebaseNotificationService::getSentMessages());

        // Run 2nd time
        $this->artisan('posters:notify-expiring')->assertExitCode(0);

        // Total count should still be 1 (NO duplicate notification)
        $this->assertCount(1, FirebaseNotificationService::getSentMessages());
    }

    public function test_posters_expiring_outside_window_are_not_notified(): void
    {
        Customer::create([
            'customer_id' => 'PSTGL_TEST_04',
            'mobile' => '9876543213',
            'fcm' => 'fcm_token_test_999',
        ]);

        $job = Job::create([
            'temp_id' => 'temp-job-3',
            'device_id' => 'device-3',
            'device_os' => 'android',
            'master_category' => 'Services',
            'business_name' => 'Design Studio',
            'job_role' => 'Architect',
            'phone_number' => '9876543213',
            'latitude' => 28.5914,
            'longitude' => 77.4021,
            'city' => 'Noida',
            'plan_id' => 'plan-1',
            'status' => 'approved',
            'expires_at' => now()->addDays(7), // Expiring next week
        ]);

        $this->artisan('posters:notify-expiring')->assertExitCode(0);

        $this->assertCount(0, FirebaseNotificationService::getSentMessages());
        $job->refresh();
        $this->assertNull($job->day_before_expiry_notified_at);
    }

    public function test_fcm_token_resolution_from_notifications_table(): void
    {
        // No Customer record, but Notification record exists
        Notification::create([
            'mobile' => '9876543215',
            'device_id' => 'device-fallback',
            'fcm_tocken' => 'fcm_token_from_notification_table',
        ]);

        Job::create([
            'temp_id' => 'temp-job-6',
            'device_id' => 'device-fallback',
            'device_os' => 'android',
            'master_category' => 'Services',
            'business_name' => 'Fallback Co',
            'job_role' => 'Support',
            'phone_number' => '9876543215',
            'latitude' => 28.5914,
            'longitude' => 77.4021,
            'city' => 'Noida',
            'plan_id' => 'plan-1',
            'status' => 'approved',
            'expires_at' => now()->addHours(2),
        ]);

        $this->artisan('posters:notify-expiring')->assertExitCode(0);

        $sent = FirebaseNotificationService::getSentMessages();
        $this->assertCount(1, $sent);
        $this->assertSame('fcm_token_from_notification_table', $sent[0]['token']);
    }
}
