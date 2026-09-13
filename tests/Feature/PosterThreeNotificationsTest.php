<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Job;
use App\Models\Offer;
use App\Services\FirebaseNotificationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosterThreeNotificationsTest extends TestCase
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

    /**
     * Case 1: Ad view milestone crossed (100, 200, 300...)
     */
    public function test_case_1_ad_view_crossing_100_and_200_sends_milestone_notifications(): void
    {
        Customer::create([
            'customer_id' => 'PSTGL_VIEW_01',
            'mobile' => '9876543001',
            'fcm' => 'fcm_view_token_1',
        ]);

        $job = Job::create([
            'temp_id' => 'temp-job-view-1',
            'device_id' => 'device-view-1',
            'device_os' => 'android',
            'master_category' => 'Services',
            'business_name' => 'Royal Cafe',
            'job_role' => 'Chef',
            'phone_number' => '9876543001',
            'latitude' => 28.5914,
            'longitude' => 77.4021,
            'city' => 'Noida',
            'plan_id' => 'plan-1',
            'view_count' => 99,
            'last_view_milestone_notified' => 0,
        ]);

        // 100th view via show API
        $response = $this->getJson("/api/v1/jobs/{$job->id}");
        $response->assertStatus(200);

        $sent = FirebaseNotificationService::getSentMessages();
        $this->assertCount(1, $sent);

        $msg100 = $sent[0];
        $this->assertSame('fcm_view_token_1', $msg100['token']);
        $this->assertStringContainsString('Royal Cafe', $msg100['body']);
        $this->assertStringContainsString('crossed 100 views', $msg100['body']);
        $this->assertStringContainsString('100 व्यूज पूरे कर लिए हैं', $msg100['body']);

        $job->refresh();
        $this->assertSame(100, (int) $job->view_count);
        $this->assertSame(100, (int) $job->last_view_milestone_notified);

        // 101st view - should NOT dispatch duplicate notification
        $this->getJson("/api/v1/jobs/{$job->id}")->assertStatus(200);
        $this->assertCount(1, FirebaseNotificationService::getSentMessages());

        // Fast-forward view count to 199
        $job->update(['view_count' => 199]);

        // 200th view via show API
        $this->getJson("/api/v1/jobs/{$job->id}")->assertStatus(200);

        $sentAfter200 = FirebaseNotificationService::getSentMessages();
        $this->assertCount(2, $sentAfter200);

        $msg200 = $sentAfter200[1];
        $this->assertStringContainsString('crossed 200 views', $msg200['body']);
        $this->assertStringContainsString('200 व्यूज पूरे कर लिए हैं', $msg200['body']);

        $job->refresh();
        $this->assertSame(200, (int) $job->last_view_milestone_notified);
    }

    /**
     * Case 1: Offer views crossing 100 sends milestone notification
     */
    public function test_case_1_offer_view_crossing_100_sends_milestone_notification(): void
    {
        Customer::create([
            'customer_id' => 'PSTGL_VIEW_02',
            'mobile' => '9876543002',
            'fcm' => 'fcm_offer_token_1',
        ]);

        $offer = Offer::create([
            'temp_id' => 'temp-offer-view-1',
            'device_id' => 'device-view-2',
            'device_os' => 'android',
            'master_category' => 'Retail',
            'business_name' => 'Fashion Hub',
            'offer_details' => 'Flat 50% discount',
            'mobile_number' => '9876543002',
            'latitude' => 28.5914,
            'longitude' => 77.4021,
            'city' => 'Noida',
            'plan_id' => 'plan-1',
            'view_count' => 99,
            'last_view_milestone_notified' => 0,
        ]);

        $this->getJson("/api/v1/offers/{$offer->id}")->assertStatus(200);

        $sent = FirebaseNotificationService::getSentMessages();
        $this->assertCount(1, $sent);
        $this->assertStringContainsString('Fashion Hub', $sent[0]['body']);
        $this->assertStringContainsString('crossed 100 views', $sent[0]['body']);

        $offer->refresh();
        $this->assertSame(100, (int) $offer->last_view_milestone_notified);
    }

    /**
     * Case 2: On poster expiry - notify customer using Batch
     */
    public function test_case_2_on_poster_expiry_sends_distinct_notification(): void
    {
        Customer::create([
            'customer_id' => 'PSTGL_EXP_01',
            'mobile' => '9876543003',
            'fcm' => 'fcm_expired_token',
        ]);

        $job = Job::create([
            'temp_id' => 'temp-job-exp-1',
            'device_id' => 'device-exp-1',
            'device_os' => 'android',
            'master_category' => 'Services',
            'business_name' => 'Express Logistics',
            'job_role' => 'Driver',
            'phone_number' => '9876543003',
            'latitude' => 28.5914,
            'longitude' => 77.4021,
            'city' => 'Noida',
            'plan_id' => 'plan-1',
            'status' => 'approved',
            'expires_at' => now()->subMinutes(15), // Expired 15 mins ago
        ]);

        $this->artisan('posters:notify-expired')->assertExitCode(0);

        $sent = FirebaseNotificationService::getSentMessages();
        $this->assertCount(1, $sent);

        $msg = $sent[0];
        $this->assertSame('fcm_expired_token', $msg['token']);
        $this->assertStringContainsString('Poster Expired', $msg['title']);
        $this->assertStringContainsString('Express Logistics', $msg['body']);
        $this->assertStringContainsString('has expired', $msg['body']);
        $this->assertStringContainsString('की अवधि समाप्त हो गई', $msg['body']);

        $job->refresh();
        $this->assertNotNull($job->expired_notified_at);
        $this->assertSame('expired', $job->status);

        // Next batch run: no duplicate notification
        $this->artisan('posters:notify-expired')->assertExitCode(0);
        $this->assertCount(1, FirebaseNotificationService::getSentMessages());
    }

    /**
     * Case 3: Day before poster expiry - notify customer using Batch
     */
    public function test_case_3_day_before_poster_expiry_sends_distinct_notification(): void
    {
        Customer::create([
            'customer_id' => 'PSTGL_DAY_01',
            'mobile' => '9876543004',
            'fcm' => 'fcm_day_before_token',
        ]);

        $offer = Offer::create([
            'temp_id' => 'temp-offer-day-1',
            'device_id' => 'device-day-1',
            'device_os' => 'android',
            'master_category' => 'Retail',
            'business_name' => 'City Bakery',
            'offer_details' => 'Weekend Cakes',
            'mobile_number' => '9876543004',
            'latitude' => 28.5914,
            'longitude' => 77.4021,
            'city' => 'Noida',
            'plan_id' => 'plan-1',
            'status' => 'approved',
            'expires_at' => now()->addHours(20), // Expiring in 20 hours (tomorrow)
        ]);

        $this->artisan('posters:notify-expiring')->assertExitCode(0);

        $sent = FirebaseNotificationService::getSentMessages();
        $this->assertCount(1, $sent);

        $msg = $sent[0];
        $this->assertSame('fcm_day_before_token', $msg['token']);
        $this->assertStringContainsString('Poster Expires Tomorrow', $msg['title']);
        $this->assertStringContainsString('City Bakery', $msg['body']);
        $this->assertStringContainsString('expires tomorrow', $msg['body']);
        $this->assertStringContainsString('कल समाप्त हो रहा है', $msg['body']);

        $offer->refresh();
        $this->assertNotNull($offer->day_before_expiry_notified_at);
        $this->assertNull($offer->expired_notified_at);

        // Next batch run: no duplicate notification
        $this->artisan('posters:notify-expiring')->assertExitCode(0);
        $this->assertCount(1, FirebaseNotificationService::getSentMessages());
    }

    /**
     * Combined lifecycle test: Day Before notification first, then On Expiry notification later
     */
    public function test_poster_receives_day_before_and_then_on_expiry_notifications_sequentially(): void
    {
        Customer::create([
            'customer_id' => 'PSTGL_SEQ_01',
            'mobile' => '9876543005',
            'fcm' => 'fcm_seq_token',
        ]);

        $job = Job::create([
            'temp_id' => 'temp-job-seq-1',
            'device_id' => 'device-seq-1',
            'device_os' => 'android',
            'master_category' => 'Services',
            'business_name' => 'Sequential Business',
            'job_role' => 'Manager',
            'phone_number' => '9876543005',
            'latitude' => 28.5914,
            'longitude' => 77.4021,
            'city' => 'Noida',
            'plan_id' => 'plan-1',
            'status' => 'approved',
            'expires_at' => now()->addHours(12), // Expiring in 12 hours
        ]);

        // 1st batch run: receives "Day Before Expiry"
        $this->artisan('posters:notify-expiring')->assertExitCode(0);

        $sent = FirebaseNotificationService::getSentMessages();
        $this->assertCount(1, $sent);
        $this->assertStringContainsString('expires tomorrow', $sent[0]['body']);

        $job->refresh();
        $this->assertNotNull($job->day_before_expiry_notified_at);
        $this->assertNull($job->expired_notified_at);

        // Advance poster to expired state (24 hours later)
        $job->update(['expires_at' => now()->subHour()]);

        // 2nd batch run: receives "On Expiry"
        $this->artisan('posters:notify-expired')->assertExitCode(0);

        $sentAfterExpiry = FirebaseNotificationService::getSentMessages();
        $this->assertCount(2, $sentAfterExpiry);

        $msgOnExpiry = $sentAfterExpiry[1];
        $this->assertStringContainsString('has expired', $msgOnExpiry['body']);

        $job->refresh();
        $this->assertNotNull($job->expired_notified_at);
        $this->assertSame('expired', $job->status);
    }
}
