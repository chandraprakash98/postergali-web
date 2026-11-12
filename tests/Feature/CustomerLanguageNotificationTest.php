<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Job;
use App\Models\Offer;
use App\Models\Referral;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerLanguageNotificationTest extends TestCase
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

    public function test_customer_check_creates_new_customer_with_specified_language(): void
    {
        $response = $this->postJson('/api/v1/customers/check', [
            'mobile' => '9876500001',
            'fcm' => 'fcm_hi_token',
            'language' => 'hi',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'created' => true,
                'mobile' => '9876500001',
                'language' => 'hi',
            ]);

        $customer = Customer::where('mobile', '9876500001')->first();
        $this->assertNotNull($customer);
        $this->assertSame('hi', $customer->language);
    }

    public function test_customer_check_defaults_to_en_when_language_not_provided(): void
    {
        $response = $this->getJson('/api/v1/customers/check?mobile=9876500002&fcm=fcm_en_token');

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'created' => true,
                'language' => 'en',
            ]);

        $customer = Customer::where('mobile', '9876500002')->first();
        $this->assertNotNull($customer);
        $this->assertSame('en', $customer->language);
    }

    public function test_customer_check_updates_language_for_existing_customer(): void
    {
        $customer = Customer::create([
            'mobile' => '9876500003',
            'fcm' => 'fcm_initial',
            'language' => 'en',
        ]);

        $response = $this->postJson('/api/v1/customers/check', [
            'mobile' => '9876500003',
            'language' => 'hi',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'created' => false,
                'language' => 'hi',
            ]);

        $customer->refresh();
        $this->assertSame('hi', $customer->language);
    }

    public function test_admin_approval_sends_hindi_notification_when_customer_language_is_hi(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        Customer::create([
            'customer_id' => 'CUST_HI_01',
            'mobile' => '9876500004',
            'fcm' => 'fcm_token_hi_user',
            'language' => 'hi',
        ]);

        $job = Job::create([
            'temp_id' => 'temp-job-hi-1',
            'device_id' => 'device-hi-1',
            'device_os' => 'android',
            'master_category' => 'Services',
            'subcategory' => 'Food and Hospitality',
            'business_name' => 'Sharma Sweets',
            'phone_number' => '9876500004',
            'status' => 'pending',
            'plan_id' => 'plan-1',
            'job_role' => 'Cook',
            'city' => 'Delhi',
            'latitude' => 28.6139,
            'longitude' => 77.2090,
        ]);

        $this->actingAs($admin)
            ->post("/admin/ads/job/{$job->id}/status", [
                'action' => 'status',
                'status' => 'approved',
            ])
            ->assertRedirect();

        $sent = FirebaseNotificationService::getSentMessages();
        $this->assertCount(1, $sent);

        $msg = $sent[0];
        $this->assertSame('fcm_token_hi_user', $msg['token']);
        $this->assertSame('विज्ञापन स्वीकृत ✓', $msg['title']);
        $this->assertSame('आपका विज्ञापन स्वीकृत हो गया है और अब लाइव है!', $msg['body']);
    }

    public function test_admin_rejection_sends_hindi_notification_when_customer_language_is_hi(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        Customer::create([
            'customer_id' => 'CUST_HI_02',
            'mobile' => '9876500005',
            'fcm' => 'fcm_token_hi_user_2',
            'language' => 'hi',
        ]);

        $offer = Offer::create([
            'temp_id' => 'temp-offer-hi-1',
            'device_id' => 'device-hi-2',
            'device_os' => 'android',
            'master_category' => 'Retail',
            'subcategory' => 'Fashion',
            'business_name' => 'Gupta Footwear',
            'mobile_number' => '9876500005',
            'status' => 'pending',
            'plan_id' => 'plan-1',
            'offer_details' => 'Sale',
            'city' => 'Delhi',
            'latitude' => 28.6139,
            'longitude' => 77.2090,
        ]);

        $this->actingAs($admin)
            ->post("/admin/ads/offer/{$offer->id}/status", [
                'action' => 'status',
                'status' => 'rejected',
                'comment' => 'तस्वीर स्पष्ट नहीं है',
            ])
            ->assertRedirect();

        $sent = FirebaseNotificationService::getSentMessages();
        $this->assertCount(1, $sent);

        $msg = $sent[0];
        $this->assertSame('fcm_token_hi_user_2', $msg['token']);
        $this->assertSame('विज्ञापन अस्वीकृत ✕', $msg['title']);
        $this->assertStringContainsString('तस्वीर स्पष्ट नहीं है', $msg['body']);
    }

    public function test_admin_approval_sends_english_notification_when_customer_language_is_en(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        Customer::create([
            'customer_id' => 'CUST_EN_01',
            'mobile' => '9876500006',
            'fcm' => 'fcm_token_en_user',
            'language' => 'en',
        ]);

        $job = Job::create([
            'temp_id' => 'temp-job-en-1',
            'device_id' => 'device-en-1',
            'device_os' => 'android',
            'master_category' => 'Services',
            'subcategory' => 'Retail and Sales',
            'business_name' => 'Metro Retail',
            'phone_number' => '9876500006',
            'status' => 'pending',
            'plan_id' => 'plan-1',
            'job_role' => 'Sales',
            'city' => 'Delhi',
            'latitude' => 28.6139,
            'longitude' => 77.2090,
        ]);

        $this->actingAs($admin)
            ->post("/admin/ads/job/{$job->id}/status", [
                'action' => 'status',
                'status' => 'approved',
            ])
            ->assertRedirect();

        $sent = FirebaseNotificationService::getSentMessages();
        $this->assertCount(1, $sent);

        $msg = $sent[0];
        $this->assertSame('fcm_token_en_user', $msg['token']);
        $this->assertSame('Ad Approved ✓', $msg['title']);
        $this->assertSame('Your ad has been approved and is now live!', $msg['body']);
    }

    public function test_referral_reward_notification_sent_in_hindi_to_referrer_with_hi_language(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        // Referrer customer has language = 'hi'
        $referrer = Customer::create([
            'customer_id' => 'CUST_REF_HI',
            'mobile' => '9876500007',
            'fcm' => 'fcm_referrer_hi',
            'language' => 'hi',
        ]);

        // Referred user customer has language = 'en'
        $referred = Customer::create([
            'customer_id' => 'CUST_REF_EN',
            'mobile' => '9876500008',
            'fcm' => 'fcm_referred_en',
            'language' => 'en',
        ]);

        Referral::create([
            'referrer_name' => 'Referrer User',
            'referrer_mobile' => '9876500007',
            'referral_name' => 'Referred User',
            'referral_mobile' => '9876500008',
            'status' => 'IN PROGRESS',
        ]);

        $job = Job::create([
            'temp_id' => 'temp-job-ref-1',
            'device_id' => 'device-ref-1',
            'device_os' => 'android',
            'master_category' => 'Services',
            'subcategory' => 'IT and Software',
            'business_name' => 'Referred Business',
            'phone_number' => '9876500008',
            'status' => 'pending',
            'plan_id' => 'plan-1',
            'job_role' => 'Staff',
            'city' => 'Delhi',
            'latitude' => 28.6139,
            'longitude' => 77.2090,
        ]);

        $this->actingAs($admin)
            ->post("/admin/ads/job/{$job->id}/status", [
                'action' => 'status',
                'status' => 'approved',
            ])
            ->assertRedirect();

        $sent = FirebaseNotificationService::getSentMessages();
        // 2 messages: 1 for referral reward (Hindi for referrer), 1 for ad approved (English for referred user)
        $this->assertCount(2, $sent);

        $referralRewardMsg = $sent[0];
        $this->assertSame('fcm_referrer_hi', $referralRewardMsg['token']);
        $this->assertSame('🎉 रेफरल इनाम!', $referralRewardMsg['title']);
        $this->assertStringContainsString('100 पोस्टर क्रेडिट जोड़ दिए गए हैं', $referralRewardMsg['body']);

        $adApprovedMsg = $sent[1];
        $this->assertSame('fcm_referred_en', $adApprovedMsg['token']);
        $this->assertSame('Ad Approved ✓', $adApprovedMsg['title']);
    }
}
