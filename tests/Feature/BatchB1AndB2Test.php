<?php

namespace Tests\Feature;

use App\Models\BatchRunLog;
use App\Models\Customer;
use App\Models\Job;
use App\Models\Offer;
use App\Services\Batches\BatchMonitorService;
use App\Services\FirebaseNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BatchB1AndB2Test extends TestCase
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

    public function test_batch_b1_executes_expired_expiring_soon_and_milestones_in_one_run(): void
    {
        // 1. Customer for Expired Job
        Customer::create([
            'customer_id' => 'PSTGL_B1_01',
            'mobile'      => '9876540001',
            'fcm'         => 'fcm_b1_expired',
        ]);

        $expiredJob = Job::create([
            'temp_id'         => 'temp-b1-exp-1',
            'device_id'       => 'device-b1-1',
            'device_os'       => 'android',
            'master_category' => 'Services',
            'business_name'   => 'Expired Bakery',
            'job_role'        => 'Baker',
            'phone_number'    => '9876540001',
            'latitude'        => 28.5914,
            'longitude'       => 77.4021,
            'city'            => 'Noida',
            'plan_id'         => 'plan-1',
            'status'          => 'approved',
            'expires_at'      => now()->subMinutes(10), // Expired
        ]);

        // 2. Customer for Expiring Soon Job (expires in 4 hours)
        Customer::create([
            'customer_id' => 'PSTGL_B1_02',
            'mobile'      => '9876540002',
            'fcm'         => 'fcm_b1_expiring',
        ]);

        $expiringJob = Job::create([
            'temp_id'         => 'temp-b1-soon-1',
            'device_id'       => 'device-b1-2',
            'device_os'       => 'android',
            'master_category' => 'Services',
            'business_name'   => 'Expiring Tech',
            'job_role'        => 'Engineer',
            'phone_number'    => '9876540002',
            'latitude'        => 28.5914,
            'longitude'       => 77.4021,
            'city'            => 'Noida',
            'plan_id'         => 'plan-1',
            'status'          => 'approved',
            'expires_at'      => now()->addHours(4), // Expiring soon
        ]);

        // 3. Customer for View Milestone Offer
        Customer::create([
            'customer_id' => 'PSTGL_B1_03',
            'mobile'      => '9876540003',
            'fcm'         => 'fcm_b1_milestone',
        ]);

        $milestoneOffer = Offer::create([
            'temp_id'         => 'temp-b1-mile-1',
            'device_id'       => 'device-b1-3',
            'device_os'       => 'android',
            'master_category' => 'Retail',
            'business_name'   => 'Popular Fashion',
            'offer_details'   => 'Super Sale',
            'mobile_number'   => '9876540003',
            'latitude'        => 28.5914,
            'longitude'       => 77.4021,
            'city'            => 'Noida',
            'plan_id'         => 'plan-1',
            'status'          => 'approved',
            'view_count'      => 105,
            'last_view_milestone_notified' => 0,
        ]);

        // Execute Batch B1 command
        $this->artisan('batch:b1')->assertExitCode(0);

        // Verify all 3 distinct notifications dispatched via Firebase fake
        $sent = FirebaseNotificationService::getSentMessages();
        $this->assertCount(3, $sent);

        $tokens = array_column($sent, 'token');
        $this->assertContains('fcm_b1_expired', $tokens);
        $this->assertContains('fcm_b1_expiring', $tokens);
        $this->assertContains('fcm_b1_milestone', $tokens);

        // Verify database records updated
        $expiredJob->refresh();
        $this->assertNotNull($expiredJob->expired_notified_at);
        $this->assertSame('expired', $expiredJob->status);

        $expiringJob->refresh();
        $this->assertNotNull($expiringJob->day_before_expiry_notified_at);

        $milestoneOffer->refresh();
        $this->assertSame(100, (int) $milestoneOffer->last_view_milestone_notified);

        // Verify BatchRunLog saved with batch_name = 'B1'
        $log = BatchRunLog::where('batch_name', 'B1')->first();
        $this->assertNotNull($log);
        $this->assertSame('success', $log->status);
        $this->assertSame(3, (int) $log->notifications_sent);
    }

    public function test_batch_b2_executes_view_milestones_and_logs_as_b2(): void
    {
        Customer::create([
            'customer_id' => 'PSTGL_B2_01',
            'mobile'      => '9876540004',
            'fcm'         => 'fcm_b2_milestone',
        ]);

        $job = Job::create([
            'temp_id'         => 'temp-b2-mile-1',
            'device_id'       => 'device-b2-1',
            'device_os'       => 'android',
            'master_category' => 'Services',
            'business_name'   => 'Evening Milestone Cafe',
            'job_role'        => 'Barista',
            'phone_number'    => '9876540004',
            'latitude'        => 28.5914,
            'longitude'       => 77.4021,
            'city'            => 'Noida',
            'plan_id'         => 'plan-1',
            'status'          => 'approved',
            'view_count'      => 210,
            'last_view_milestone_notified' => 100,
        ]);

        // Execute Batch B2 command
        $this->artisan('batch:b2')->assertExitCode(0);

        $sent = FirebaseNotificationService::getSentMessages();
        $this->assertCount(1, $sent);
        $this->assertSame('fcm_b2_milestone', $sent[0]['token']);
        $this->assertStringContainsString('crossed 200 views', $sent[0]['body']);

        $job->refresh();
        $this->assertSame(200, (int) $job->last_view_milestone_notified);

        // Verify BatchRunLog saved with batch_name = 'B2'
        $log = BatchRunLog::where('batch_name', 'B2')->first();
        $this->assertNotNull($log);
        $this->assertSame('success', $log->status);
        $this->assertSame(1, (int) $log->notifications_sent);
    }

    public function test_batch_monitor_service_returns_accurate_reporting_data(): void
    {
        $monitorService = new BatchMonitorService();
        $data = $monitorService->getBatchStatuses();

        $this->assertArrayHasKey('batches', $data);
        $this->assertCount(2, $data['batches']);

        $b1 = $data['batches'][0];
        $b2 = $data['batches'][1];

        $this->assertSame('B1', $b1['name']);
        $this->assertSame('Active', $b1['status']);
        $this->assertNotEmpty($b1['upcomingFormatted']);

        $this->assertSame('B2', $b2['name']);
        $this->assertSame('Active', $b2['status']);
        $this->assertNotEmpty($b2['upcomingFormatted']);
    }
}
