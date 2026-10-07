<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\Offer;
use App\Models\Plan;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPosterApprovalExpiryTest extends TestCase
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

    public function test_approving_job_sets_expiry_at_end_of_day(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $plan = Plan::create([
            'plan_title' => 'Basic Plan',
            'duration'   => '30 days',
            'price'      => 499,
        ]);

        $job = Job::create([
            'temp_id'         => 'temp-job-1',
            'device_id'       => 'device-job-1',
            'device_os'       => 'android',
            'master_category' => 'Services',
            'business_name'   => 'Test Business',
            'job_role'        => 'Developer',
            'job_type'        => 'full_time',
            'salary'          => 50000,
            'phone_number'    => '9876543210',
            'latitude'        => 28.6139,
            'longitude'       => 77.2090,
            'city'            => 'New Delhi',
            'status'          => 'pending',
            'plan_id'         => (string) $plan->id,
        ]);

        $fixedNow = Carbon::parse('2026-10-07 10:30:15');
        Carbon::setTestNow($fixedNow);

        $response = $this->actingAs($admin)->post('/admin/ads/job/' . $job->id . '/status', [
            'status' => 'approved',
        ]);

        $response->assertRedirect();

        $job->refresh();
        $this->assertSame('approved', $job->status);
        $this->assertNotNull($job->approved_at);
        $this->assertNotNull($job->expires_at);

        // Expiry date must be 30 days later, and time must be exactly 23:59:59 (end of day)
        $expectedExpiry = Carbon::parse('2026-11-06 23:59:59');
        $this->assertSame($expectedExpiry->toDateTimeString(), $job->expires_at->toDateTimeString());

        Carbon::setTestNow();
    }

    public function test_approving_poster_with_duration_today_expires_at_end_of_current_day(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $plan = Plan::create([
            'plan_title' => 'Today Plan',
            'duration'   => 'today',
            'price'      => 99,
        ]);

        $offer = Offer::create([
            'temp_id'         => 'temp-offer-1',
            'device_id'       => 'device-offer-1',
            'device_os'       => 'android',
            'master_category' => 'Retail',
            'business_name'   => 'Offer Mart',
            'offer_details'   => '50% off today only',
            'offer_type'      => 'Discount',
            'mobile_number'   => '9876543211',
            'latitude'        => 28.6139,
            'longitude'       => 77.2090,
            'city'            => 'New Delhi',
            'status'          => 'pending',
            'plan_id'         => (string) $plan->id,
        ]);

        $fixedNow = Carbon::parse('2026-10-07 14:15:00');
        Carbon::setTestNow($fixedNow);

        $response = $this->actingAs($admin)->post('/admin/ads/offer/' . $offer->id . '/status', [
            'status' => 'approved',
        ]);

        $response->assertRedirect();

        $offer->refresh();
        $this->assertSame('approved', $offer->status);

        // If expiring today, expires_at must be end of today: 2026-10-07 23:59:59
        $expectedExpiry = Carbon::parse('2026-10-07 23:59:59');
        $this->assertSame($expectedExpiry->toDateTimeString(), $offer->expires_at->toDateTimeString());

        Carbon::setTestNow();
    }

    public function test_approving_poster_with_custom_expires_at_sets_to_end_of_specified_day(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $plan = Plan::create([
            'plan_title' => 'Default Plan',
            'duration'   => '30 days',
            'price'      => 499,
        ]);

        $job = Job::create([
            'temp_id'         => 'temp-job-custom-exp',
            'device_id'       => 'device-job-custom-exp',
            'device_os'       => 'android',
            'master_category' => 'Services',
            'business_name'   => 'Custom Exp Business',
            'job_role'        => 'Manager',
            'job_type'        => 'full_time',
            'salary'          => 40000,
            'phone_number'    => '9876543212',
            'latitude'        => 28.6139,
            'longitude'       => 77.2090,
            'city'            => 'New Delhi',
            'status'          => 'pending',
            'plan_id'         => (string) $plan->id,
        ]);

        $response = $this->actingAs($admin)->post('/admin/ads/job/' . $job->id . '/status', [
            'status'     => 'approved',
            'expires_at' => '2026-10-15',
        ]);

        $response->assertRedirect();

        $job->refresh();
        $this->assertSame('approved', $job->status);

        // Custom date expires at end of that day: 2026-10-15 23:59:59
        $this->assertSame('2026-10-15 23:59:59', $job->expires_at->toDateTimeString());
    }

    public function test_rejecting_poster_clears_approved_at_and_expires_at(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $plan = Plan::create([
            'plan_title' => 'Default Plan',
            'duration'   => '30 days',
            'price'      => 499,
        ]);

        $job = Job::create([
            'temp_id'         => 'temp-job-reject',
            'device_id'       => 'device-job-reject',
            'device_os'       => 'android',
            'master_category' => 'Services',
            'business_name'   => 'Rejected Business',
            'job_role'        => 'Cashier',
            'job_type'        => 'part_time',
            'salary'          => 15000,
            'phone_number'    => '9876543213',
            'latitude'        => 28.6139,
            'longitude'       => 77.2090,
            'city'            => 'New Delhi',
            'status'          => 'approved',
            'plan_id'         => (string) $plan->id,
            'approved_at'     => now(),
            'expires_at'      => now()->addDays(30)->endOfDay(),
        ]);

        $response = $this->actingAs($admin)->post('/admin/ads/job/' . $job->id . '/status', [
            'status'  => 'rejected',
            'comment' => 'Inappropriate content',
        ]);

        $response->assertRedirect();

        $job->refresh();
        $this->assertSame('rejected', $job->status);
        $this->assertNull($job->approved_at);
        $this->assertNull($job->expires_at);
    }
}
