<?php

namespace Tests\Feature;

use App\Models\BatchRunLog;
use App\Models\Customer;
use App\Models\Job;
use App\Models\Offer;
use App\Services\FirebaseNotificationService;
use App\Services\PostergaliAlphaBatchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostergaliAlphaBatchTest extends TestCase
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

    public function test_f1_sends_notification_to_posters_expiring_in_one_day(): void
    {
        Customer::create([
            'customer_id' => 'PSTGL_ALPHA_01',
            'mobile'      => '9876541111',
            'fcm'         => 'fcm_alpha_f1_token',
        ]);

        $expiringJob = Job::create([
            'temp_id'         => 'temp-alpha-f1',
            'device_id'       => 'device-alpha-1',
            'device_os'       => 'android',
            'master_category' => 'Services',
            'business_name'   => 'Alpha Tech Repairs',
            'job_role'        => 'Technician',
            'phone_number'    => '9876541111',
            'latitude'        => 28.5914,
            'longitude'       => 77.4021,
            'city'            => 'Noida',
            'plan_id'         => 'plan-1',
            'status'          => 'approved',
            'expires_at'      => now()->addHours(6), // Expiring in 6 hours (within 24h window)
        ]);

        $service = new PostergaliAlphaBatchService();
        $result = $service->sendDayBeforeExpiryNotifications();

        $this->assertSame(1, $result['found']);
        $this->assertSame(1, $result['sent']);
        $this->assertSame(0, $result['skipped']);

        $sent = FirebaseNotificationService::getSentMessages();
        $this->assertCount(1, $sent);
        $this->assertSame('fcm_alpha_f1_token', $sent[0]['token']);
        $this->assertStringContainsString('expires tomorrow', $sent[0]['body']);
        $this->assertStringContainsString('कल समाप्त हो रहा है', $sent[0]['body']);

        $expiringJob->refresh();
        $this->assertNotNull($expiringJob->day_before_expiry_notified_at);

        // Subsequent call must NOT send duplicate
        $secondRun = $service->sendDayBeforeExpiryNotifications();
        $this->assertSame(0, $secondRun['found']);
        $this->assertCount(1, FirebaseNotificationService::getSentMessages());
    }

    public function test_f2_sends_notification_to_posters_expiring_today_and_updates_status(): void
    {
        Customer::create([
            'customer_id' => 'PSTGL_ALPHA_02',
            'mobile'      => '9876542222',
            'fcm'         => 'fcm_alpha_f2_token',
        ]);

        $expiredOffer = Offer::create([
            'temp_id'         => 'temp-alpha-f2',
            'device_id'       => 'device-alpha-2',
            'device_os'       => 'android',
            'master_category' => 'Retail',
            'business_name'   => 'Alpha Sweet House',
            'offer_details'   => 'Diwali Sweets 20% Off',
            'mobile_number'   => '9876542222',
            'latitude'        => 28.5914,
            'longitude'       => 77.4021,
            'city'            => 'Noida',
            'plan_id'         => 'plan-1',
            'status'          => 'approved',
            'expires_at'      => now()->subMinutes(10), // Expired 10 minutes ago
        ]);

        $service = new PostergaliAlphaBatchService();
        $result = $service->sendExpiringTodayNotifications();

        $this->assertSame(1, $result['found']);
        $this->assertSame(1, $result['sent']);
        $this->assertSame(0, $result['skipped']);

        $sent = FirebaseNotificationService::getSentMessages();
        $this->assertCount(1, $sent);
        $this->assertSame('fcm_alpha_f2_token', $sent[0]['token']);
        $this->assertStringContainsString('has expired', $sent[0]['body']);
        $this->assertStringContainsString('की अवधि समाप्त हो गई', $sent[0]['body']);

        $expiredOffer->refresh();
        $this->assertNotNull($expiredOffer->expired_notified_at);
        $this->assertSame('expired', $expiredOffer->status);

        // Subsequent call must NOT send duplicate
        $secondRun = $service->sendExpiringTodayNotifications();
        $this->assertSame(0, $secondRun['found']);
        $this->assertCount(1, FirebaseNotificationService::getSentMessages());
    }

    public function test_unified_postergali_alpha_command_runs_f1_and_f2_and_logs_execution(): void
    {
        Customer::create([
            'customer_id' => 'PSTGL_ALPHA_03',
            'mobile'      => '9876543333',
            'fcm'         => 'fcm_alpha_both_f1',
        ]);

        Customer::create([
            'customer_id' => 'PSTGL_ALPHA_04',
            'mobile'      => '9876544444',
            'fcm'         => 'fcm_alpha_both_f2',
        ]);

        // Job for f1 (expiring in 12h)
        Job::create([
            'temp_id'         => 'temp-alpha-combo-1',
            'device_id'       => 'device-alpha-3',
            'device_os'       => 'android',
            'master_category' => 'Services',
            'business_name'   => 'Expiring Tomorrow Salon',
            'job_role'        => 'Stylist',
            'phone_number'    => '9876543333',
            'latitude'        => 28.5914,
            'longitude'       => 77.4021,
            'city'            => 'Noida',
            'plan_id'         => 'plan-1',
            'status'          => 'approved',
            'expires_at'      => now()->addHours(12),
        ]);

        // Offer for f2 (already expired)
        Offer::create([
            'temp_id'         => 'temp-alpha-combo-2',
            'device_id'       => 'device-alpha-4',
            'device_os'       => 'android',
            'master_category' => 'Retail',
            'business_name'   => 'Expired Shoes',
            'offer_details'   => 'Clearance',
            'mobile_number'   => '9876544444',
            'latitude'        => 28.5914,
            'longitude'       => 77.4021,
            'city'            => 'Noida',
            'plan_id'         => 'plan-1',
            'status'          => 'approved',
            'expires_at'      => now()->subMinutes(30),
        ]);

        // Execute postergali-alpha artisan command
        $this->artisan('postergali-alpha')->assertExitCode(0);

        // Verify notifications dispatched
        $sent = FirebaseNotificationService::getSentMessages();
        $this->assertCount(2, $sent);

        $tokens = array_column($sent, 'token');
        $this->assertContains('fcm_alpha_both_f1', $tokens);
        $this->assertContains('fcm_alpha_both_f2', $tokens);

        // Verify BatchRunLog
        $log = BatchRunLog::where('batch_name', 'postergali-alpha')->first();
        $this->assertNotNull($log);
        $this->assertSame('success', $log->status);
        $this->assertSame(2, (int) $log->notifications_sent);
        $this->assertSame(1, (int) $log->day_before_jobs);
        $this->assertSame(1, (int) $log->on_expiry_offers);
    }

    public function test_postergali_alpha_dry_run_dispatches_no_notifications(): void
    {
        Customer::create([
            'customer_id' => 'PSTGL_ALPHA_05',
            'mobile'      => '9876545555',
            'fcm'         => 'fcm_alpha_dry_run',
        ]);

        $job = Job::create([
            'temp_id'         => 'temp-alpha-dry-1',
            'device_id'       => 'device-alpha-5',
            'device_os'       => 'android',
            'master_category' => 'Services',
            'business_name'   => 'Dry Run Business',
            'job_role'        => 'Tester',
            'phone_number'    => '9876545555',
            'latitude'        => 28.5914,
            'longitude'       => 77.4021,
            'city'            => 'Noida',
            'plan_id'         => 'plan-1',
            'status'          => 'approved',
            'expires_at'      => now()->addHours(3),
        ]);

        $this->artisan('postergali-alpha --dry-run')->assertExitCode(0);

        $this->assertCount(0, FirebaseNotificationService::getSentMessages());
        $job->refresh();
        $this->assertNull($job->day_before_expiry_notified_at);
        $this->assertSame(0, BatchRunLog::where('batch_name', 'postergali-alpha')->count());
    }

    public function test_admin_batch_status_returns_clean_alpha_report(): void
    {
        $service = new PostergaliAlphaBatchService();
        $status = $service->getStatus();

        $this->assertSame('postergali-alpha', $status['batch_name']);
        $this->assertSame('Active', $status['status']);
        $this->assertNotEmpty($status['schedule']);
        $this->assertNotEmpty($status['schedule_human']);
        $this->assertSame('Asia/Kolkata', $status['timezone']);
        $this->assertArrayHasKey('recent_runs', $status);
    }
}
