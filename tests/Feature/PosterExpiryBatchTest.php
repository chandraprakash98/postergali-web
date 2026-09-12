<?php

namespace Tests\Feature;

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

    public function test_batch_detects_expiring_and_expired_posters_and_sends_firebase_notification(): void
    {
        // 1. Create a Customer with FCM token
        Customer::create([
            'customer_id' => 'PSTGL_TEST_01',
            'mobile' => '9876543210',
            'fcm' => 'fcm_token_test_123',
        ]);

        // 2. Create an expiring Job (expires in 4 hours, status approved)
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

        // 3. Create an expired Offer (expired 2 hours ago, status approved)
        $offer = Offer::create([
            'temp_id' => 'temp-offer-1',
            'device_id' => 'device-2',
            'device_os' => 'android',
            'master_category' => 'Retail',
            'business_name' => 'Mega Mart',
            'offer_details' => 'Mega sale',
            'mobile_number' => '9876543210',
            'latitude' => 28.5914,
            'longitude' => 77.4021,
            'city' => 'Noida',
            'plan_id' => 'plan-1',
            'status' => 'approved',
            'approved_at' => now()->subDays(30),
            'expires_at' => now()->subHours(2),
        ]);

        // Run the batch command
        $this->artisan('posters:check-expiry')
            ->assertExitCode(0);

        // Verify sent messages via fake
        $sent = FirebaseNotificationService::getSentMessages();
        $this->assertCount(2, $sent);

        // Message 0: Day before expiry (Job)
        $this->assertSame('fcm_token_test_123', $sent[0]['token']);
        $this->assertStringContainsString('expires tomorrow', $sent[0]['body']);
        $this->assertStringContainsString('कल समाप्त हो रहा है', $sent[0]['body']);

        // Message 1: On poster expiry (Offer)
        $this->assertSame('fcm_token_test_123', $sent[1]['token']);
        $this->assertStringContainsString('has expired today', $sent[1]['body']);
        $this->assertStringContainsString('की अवधि आज समाप्त हो गई है', $sent[1]['body']);

        // Verify database records updated
        $job->refresh();
        $offer->refresh();

        $this->assertNotNull($job->expiry_notified_at);
        $this->assertNotNull($offer->expiry_notified_at);
        $this->assertSame('expired', $offer->status);
    }

    public function test_subsequent_runs_do_not_send_duplicate_notifications(): void
    {
        Customer::create([
            'customer_id' => 'PSTGL_TEST_02',
            'mobile' => '9876543211',
            'fcm' => 'fcm_token_test_456',
        ]);

        Job::create([
            'temp_id' => 'temp-job-2',
            'device_id' => 'device-2',
            'device_os' => 'android',
            'master_category' => 'Services',
            'business_name' => 'Tech Corp',
            'job_role' => 'Designer',
            'phone_number' => '9876543211',
            'latitude' => 28.5914,
            'longitude' => 77.4021,
            'city' => 'Noida',
            'plan_id' => 'plan-1',
            'status' => 'approved',
            'expires_at' => now()->addHours(2),
        ]);

        // Run 1st time
        $this->artisan('posters:check-expiry')->assertExitCode(0);
        $this->assertCount(1, FirebaseNotificationService::getSentMessages());

        // Run 2nd time (15 mins later simulation)
        $this->artisan('posters:check-expiry')->assertExitCode(0);

        // Total count should still be 1 (NO duplicate notification)
        $this->assertCount(1, FirebaseNotificationService::getSentMessages());
    }

    public function test_posters_expiring_outside_window_are_not_notified(): void
    {
        Customer::create([
            'customer_id' => 'PSTGL_TEST_03',
            'mobile' => '9876543212',
            'fcm' => 'fcm_token_test_789',
        ]);

        $job = Job::create([
            'temp_id' => 'temp-job-3',
            'device_id' => 'device-3',
            'device_os' => 'android',
            'master_category' => 'Services',
            'business_name' => 'Design Studio',
            'job_role' => 'Architect',
            'phone_number' => '9876543212',
            'latitude' => 28.5914,
            'longitude' => 77.4021,
            'city' => 'Noida',
            'plan_id' => 'plan-1',
            'status' => 'approved',
            'expires_at' => now()->addDays(7), // Expiring next week
        ]);

        $this->artisan('posters:check-expiry')->assertExitCode(0);

        $this->assertCount(0, FirebaseNotificationService::getSentMessages());
        $job->refresh();
        $this->assertNull($job->expiry_notified_at);
    }

    public function test_dry_run_does_not_mutate_database_or_dispatch_notifications(): void
    {
        Customer::create([
            'customer_id' => 'PSTGL_TEST_04',
            'mobile' => '9876543213',
            'fcm' => 'fcm_token_dryrun',
        ]);

        $job = Job::create([
            'temp_id' => 'temp-job-4',
            'device_id' => 'device-4',
            'device_os' => 'android',
            'master_category' => 'Services',
            'business_name' => 'DryRun Co',
            'job_role' => 'Tester',
            'phone_number' => '9876543213',
            'latitude' => 28.5914,
            'longitude' => 77.4021,
            'city' => 'Noida',
            'plan_id' => 'plan-1',
            'status' => 'approved',
            'expires_at' => now()->addHours(1),
        ]);

        $this->artisan('posters:check-expiry --dry-run')->assertExitCode(0);

        $this->assertCount(0, FirebaseNotificationService::getSentMessages());
        $job->refresh();
        $this->assertNull($job->expiry_notified_at);
    }

    public function test_disabled_batch_setting_skips_processing(): void
    {
        config(['posters.expiry_notification.enabled' => false]);

        Customer::create([
            'customer_id' => 'PSTGL_TEST_05',
            'mobile' => '9876543214',
            'fcm' => 'fcm_token_disabled',
        ]);

        Job::create([
            'temp_id' => 'temp-job-5',
            'device_id' => 'device-5',
            'device_os' => 'android',
            'master_category' => 'Services',
            'business_name' => 'Disabled Co',
            'job_role' => 'Tester',
            'phone_number' => '9876543214',
            'latitude' => 28.5914,
            'longitude' => 77.4021,
            'city' => 'Noida',
            'plan_id' => 'plan-1',
            'status' => 'approved',
            'expires_at' => now()->addHours(1),
        ]);

        $this->artisan('posters:check-expiry')->assertExitCode(0);
        $this->assertCount(0, FirebaseNotificationService::getSentMessages());
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

        $this->artisan('posters:check-expiry')->assertExitCode(0);

        $sent = FirebaseNotificationService::getSentMessages();
        $this->assertCount(1, $sent);
        $this->assertSame('fcm_token_from_notification_table', $sent[0]['token']);
    }
}
