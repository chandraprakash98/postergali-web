<?php

namespace Tests\Feature;

use App\Models\CouponIncentive;
use App\Models\Customer;
use App\Models\CustomerCredit;
use App\Models\Job;
use App\Models\Offer;
use App\Models\Payment;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPosterApprovalCouponIncentiveTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        FirebaseNotificationService::fake();
        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    protected function tearDown(): void
    {
        FirebaseNotificationService::resetFake();
        parent::tearDown();
    }

    private function createJob(array $attributes = []): Job
    {
        return Job::create(array_merge([
            'temp_id' => 'job-test-' . uniqid(),
            'device_id' => 'device-' . uniqid(),
            'device_os' => 'android',
            'master_category' => 'Services',
            'business_name' => 'Tech Corp',
            'job_role' => 'Developer',
            'phone_number' => '9876543210',
            'status' => 'pending',
            'latitude' => 28.6139,
            'longitude' => 77.2090,
            'city' => 'Delhi',
            'plan_id' => 'plan-1',
        ], $attributes));
    }

    private function createOffer(array $attributes = []): Offer
    {
        return Offer::create(array_merge([
            'temp_id' => 'offer-test-' . uniqid(),
            'device_id' => 'device-' . uniqid(),
            'device_os' => 'android',
            'master_category' => 'Services',
            'business_name' => 'Cafe Delight',
            'offer_details' => '50% off',
            'mobile_number' => '9876543210',
            'status' => 'pending',
            'latitude' => 28.6139,
            'longitude' => 77.2090,
            'city' => 'Delhi',
            'plan_id' => 'plan-1',
        ], $attributes));
    }

    public function test_approving_poster_creates_coupon_incentive_record_when_customer_used_coupon(): void
    {
        $customer = Customer::create([
            'mobile' => '9876543210',
            'influencer_bonus_coupon_id' => 'INFLUENCER100',
        ]);

        $job = $this->createJob([
            'phone_number' => $customer->mobile,
        ]);

        Payment::create([
            'customer_id' => $customer->customer_id,
            'transaction_id' => 'TXN_TEST_01',
            'job_or_offer_id' => $job->id,
            'item_type' => 'job',
            'payment_type' => Payment::TYPE_FULL_CREDIT,
            'credit_mode' => 'full_credit',
            'total_amount' => 100,
            'credit_amount' => 100,
            'payment_status' => Payment::STATUS_COMPLETED,
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson('/admin/ads/job/' . $job->id . '/status', [
                'status' => 'approved',
                'comment' => 'Approved poster',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'status' => 'approved',
        ]);

        $this->assertDatabaseHas('coupon_incentive_table', [
            'influencer_bonus_coupon_id' => 'INFLUENCER100',
            'ad_id' => $job->id,
            'customer_id' => $customer->customer_id,
            'mobile_number' => $customer->mobile,
            'payment_method' => 'free',
            'poster_count' => 1,
            'incentive_processed_date' => null,
        ]);

        $incentive = CouponIncentive::where('ad_id', $job->id)->first();
        $this->assertNotNull($incentive);
        $this->assertEquals(now()->toDateString(), $incentive->usage_date->toDateString());
    }

    public function test_approving_poster_skips_incentive_if_customer_has_no_coupon(): void
    {
        $customer = Customer::create([
            'mobile' => '9876543211',
            'influencer_bonus_coupon_id' => null,
        ]);

        $job = $this->createJob([
            'phone_number' => $customer->mobile,
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson('/admin/ads/job/' . $job->id . '/status', [
                'status' => 'approved',
                'comment' => 'Approved poster',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'status' => 'approved',
        ]);

        $this->assertDatabaseCount('coupon_incentive_table', 0);
    }

    public function test_rejecting_poster_does_not_create_incentive_record(): void
    {
        $customer = Customer::create([
            'mobile' => '9876543212',
            'influencer_bonus_coupon_id' => 'INFLUENCER100',
        ]);

        CustomerCredit::create([
            'customer_id' => $customer->customer_id,
            'balance' => 0,
        ]);

        $job = $this->createJob([
            'phone_number' => $customer->mobile,
        ]);

        Payment::create([
            'customer_id' => $customer->customer_id,
            'transaction_id' => 'TXN_REJECT_TEST',
            'job_or_offer_id' => $job->id,
            'item_type' => 'job',
            'payment_type' => Payment::TYPE_FULL_CREDIT,
            'credit_mode' => 'full_credit',
            'total_amount' => 100,
            'credit_amount' => 100,
            'payment_status' => Payment::STATUS_COMPLETED,
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson('/admin/ads/job/' . $job->id . '/status', [
                'status' => 'rejected',
                'comment' => 'Invalid content',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'status' => 'rejected',
        ]);

        // Existing rejection logic (credit refund) executes
        $this->assertDatabaseHas('customer_credits', [
            'customer_id' => $customer->customer_id,
            'balance' => 100,
        ]);

        // No coupon incentive created on rejection
        $this->assertDatabaseCount('coupon_incentive_table', 0);
    }

    public function test_poster_count_increments_per_customer_across_multiple_approved_posters(): void
    {
        $customerA = Customer::create([
            'mobile' => '9111111111',
            'influencer_bonus_coupon_id' => 'BONUS_A',
        ]);

        $customerB = Customer::create([
            'mobile' => '9222222222',
            'influencer_bonus_coupon_id' => 'BONUS_B',
        ]);

        // Customer A: Poster 1
        $jobA1 = $this->createJob(['phone_number' => $customerA->mobile]);

        $this->actingAs($this->admin)
            ->postJson('/admin/ads/job/' . $jobA1->id . '/status', ['status' => 'approved']);

        $incentiveA1 = CouponIncentive::where('ad_id', $jobA1->id)->first();
        $this->assertNotNull($incentiveA1);
        $this->assertEquals(1, $incentiveA1->poster_count);
        $this->assertEquals($customerA->customer_id, $incentiveA1->customer_id);

        // Customer A: Poster 2
        $jobA2 = $this->createJob(['phone_number' => $customerA->mobile]);

        $this->actingAs($this->admin)
            ->postJson('/admin/ads/job/' . $jobA2->id . '/status', ['status' => 'approved']);

        $incentiveA2 = CouponIncentive::where('ad_id', $jobA2->id)->first();
        $this->assertNotNull($incentiveA2);
        $this->assertEquals(2, $incentiveA2->poster_count);
        $this->assertEquals($customerA->customer_id, $incentiveA2->customer_id);

        // Customer B: Poster 1 (should start at 1, independent of Customer A)
        $jobB1 = $this->createJob(['phone_number' => $customerB->mobile]);

        $this->actingAs($this->admin)
            ->postJson('/admin/ads/job/' . $jobB1->id . '/status', ['status' => 'approved']);

        $incentiveB1 = CouponIncentive::where('ad_id', $jobB1->id)->first();
        $this->assertNotNull($incentiveB1);
        $this->assertEquals(1, $incentiveB1->poster_count);
        $this->assertEquals($customerB->customer_id, $incentiveB1->customer_id);

        // Customer A: Poster 3 (should increment from 2 to 3)
        $jobA3 = $this->createJob(['phone_number' => $customerA->mobile]);

        $this->actingAs($this->admin)
            ->postJson('/admin/ads/job/' . $jobA3->id . '/status', ['status' => 'approved']);

        $incentiveA3 = CouponIncentive::where('ad_id', $jobA3->id)->first();
        $this->assertNotNull($incentiveA3);
        $this->assertEquals(3, $incentiveA3->poster_count);
        $this->assertEquals($customerA->customer_id, $incentiveA3->customer_id);
    }

    public function test_payment_method_is_paid_for_full_gateway_payment(): void
    {
        $customer = Customer::create([
            'mobile' => '9333333333',
            'influencer_bonus_coupon_id' => 'COUPON_UPI',
        ]);

        $job = $this->createJob(['phone_number' => $customer->mobile]);

        Payment::create([
            'customer_id' => $customer->customer_id,
            'transaction_id' => 'TXN_UPI_TEST',
            'job_or_offer_id' => $job->id,
            'item_type' => 'job',
            'payment_type' => Payment::TYPE_FULL_UPI,
            'credit_mode' => 'full_upi',
            'total_amount' => 500,
            'razorpay_amount' => 500,
            'credit_amount' => 0,
            'payment_status' => Payment::STATUS_COMPLETED,
        ]);

        $this->actingAs($this->admin)
            ->postJson('/admin/ads/job/' . $job->id . '/status', ['status' => 'approved']);

        $this->assertDatabaseHas('coupon_incentive_table', [
            'ad_id' => $job->id,
            'customer_id' => $customer->customer_id,
            'payment_method' => 'paid',
        ]);
    }

    public function test_payment_method_is_paid_for_semi_gateway_payment(): void
    {
        $customer = Customer::create([
            'mobile' => '9444444444',
            'influencer_bonus_coupon_id' => 'COUPON_SEMI',
        ]);

        $offer = $this->createOffer(['mobile_number' => $customer->mobile]);

        Payment::create([
            'customer_id' => $customer->customer_id,
            'transaction_id' => 'TXN_SEMI_TEST',
            'job_or_offer_id' => $offer->id,
            'item_type' => 'offer',
            'payment_type' => Payment::TYPE_SEMI,
            'credit_mode' => 'semi',
            'total_amount' => 1000,
            'razorpay_amount' => 600,
            'credit_amount' => 400,
            'payment_status' => Payment::STATUS_COMPLETED,
        ]);

        $this->actingAs($this->admin)
            ->postJson('/admin/ads/offer/' . $offer->id . '/status', ['status' => 'approved']);

        $this->assertDatabaseHas('coupon_incentive_table', [
            'ad_id' => $offer->id,
            'customer_id' => $customer->customer_id,
            'payment_method' => 'paid',
        ]);
    }

    public function test_payment_method_is_free_when_poster_credit_used(): void
    {
        $customer = Customer::create([
            'mobile' => '9555555555',
            'influencer_bonus_coupon_id' => 'COUPON_CREDIT',
        ]);

        $offer = $this->createOffer(['mobile_number' => $customer->mobile]);

        Payment::create([
            'customer_id' => $customer->customer_id,
            'transaction_id' => 'TXN_CREDIT_TEST',
            'job_or_offer_id' => $offer->id,
            'item_type' => 'offer',
            'payment_type' => Payment::TYPE_FULL_CREDIT,
            'credit_mode' => 'full_credit',
            'total_amount' => 200,
            'razorpay_amount' => 0,
            'credit_amount' => 200,
            'payment_status' => Payment::STATUS_COMPLETED,
        ]);

        $this->actingAs($this->admin)
            ->postJson('/admin/ads/offer/' . $offer->id . '/status', ['status' => 'approved']);

        $this->assertDatabaseHas('coupon_incentive_table', [
            'ad_id' => $offer->id,
            'customer_id' => $customer->customer_id,
            'payment_method' => 'free',
        ]);
    }

    public function test_duplicate_protection_prevents_multiple_records_on_re_approval_or_retry(): void
    {
        $customer = Customer::create([
            'mobile' => '9666666666',
            'influencer_bonus_coupon_id' => 'RETRY_COUPON',
        ]);

        $job = $this->createJob(['phone_number' => $customer->mobile]);

        // First approval call
        $this->actingAs($this->admin)
            ->postJson('/admin/ads/job/' . $job->id . '/status', ['status' => 'approved']);

        $this->assertEquals(1, CouponIncentive::where('ad_id', $job->id)->count());

        // Second approval call (admin retry or page refresh)
        $this->actingAs($this->admin)
            ->postJson('/admin/ads/job/' . $job->id . '/status', ['status' => 'approved']);

        $this->assertEquals(1, CouponIncentive::where('ad_id', $job->id)->count());

        // Third approval call with subcategory update
        $this->actingAs($this->admin)
            ->postJson('/admin/ads/job/' . $job->id . '/status', [
                'status' => 'approved',
                'comment' => 'Already approved update',
            ]);

        $this->assertEquals(1, CouponIncentive::where('ad_id', $job->id)->count());
    }

    public function test_admin_approval_sends_notification_to_customer_fcm_token_and_not_poster_device_id(): void
    {
        $customer = Customer::create([
            'mobile' => '9777777777',
            'fcm' => 'real_customer_fcm_token_999',
        ]);

        $job = $this->createJob([
            'phone_number' => $customer->mobile,
            'device_id' => 'samsung_galaxy_s23_ultra_model_sm_s918b',
        ]);

        $this->actingAs($this->admin)
            ->postJson('/admin/ads/job/' . $job->id . '/status', [
                'status' => 'approved',
                'comment' => 'Approved ad',
            ])
            ->assertRedirect();

        $sentMessages = FirebaseNotificationService::getSentMessages();
        $this->assertCount(1, $sentMessages);
        $this->assertEquals('real_customer_fcm_token_999', $sentMessages[0]['token']);
        $this->assertNotEquals('samsung_galaxy_s23_ultra_model_sm_s918b', $sentMessages[0]['token']);
        $this->assertEquals('Ad Approved ✓', $sentMessages[0]['title']);
    }

    public function test_admin_rejection_sends_notification_to_customer_fcm_token_and_not_poster_device_id(): void
    {
        $customer = Customer::create([
            'mobile' => '9888888888',
            'fcm' => 'customer_fcm_for_rejected_ad_123',
        ]);

        $offer = $this->createOffer([
            'mobile_number' => $customer->mobile,
            'device_id' => 'apple_iphone_15_pro_a3102',
        ]);

        $this->actingAs($this->admin)
            ->postJson('/admin/ads/offer/' . $offer->id . '/status', [
                'status' => 'rejected',
                'comment' => 'Inappropriate images',
            ])
            ->assertRedirect();

        $sentMessages = FirebaseNotificationService::getSentMessages();
        $this->assertCount(1, $sentMessages);
        $this->assertEquals('customer_fcm_for_rejected_ad_123', $sentMessages[0]['token']);
        $this->assertNotEquals('apple_iphone_15_pro_a3102', $sentMessages[0]['token']);
        $this->assertEquals('Ad Rejected ✕', $sentMessages[0]['title']);
        $this->assertStringContainsString('Inappropriate images', $sentMessages[0]['body']);
    }

    public function test_admin_approval_gracefully_handles_missing_customer_fcm(): void
    {
        $customer = Customer::create([
            'mobile' => '9999999999',
            'fcm' => null,
        ]);

        $job = $this->createJob([
            'phone_number' => $customer->mobile,
            'device_id' => 'device_hardware_id_no_fcm',
        ]);

        $this->actingAs($this->admin)
            ->postJson('/admin/ads/job/' . $job->id . '/status', [
                'status' => 'approved',
            ])
            ->assertRedirect();

        $sentMessages = FirebaseNotificationService::getSentMessages();
        $this->assertCount(0, $sentMessages);
    }
}
