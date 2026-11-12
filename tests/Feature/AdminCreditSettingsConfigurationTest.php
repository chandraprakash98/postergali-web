<?php

namespace Tests\Feature;

use App\Models\CreditSetting;
use App\Models\BonusCoupon;
use App\Models\Customer;
use App\Models\CustomerCredit;
use App\Models\Job;
use App\Models\Offer;
use App\Models\OtpVerification;
use App\Models\Referral;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use App\Services\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class AdminCreditSettingsConfigurationTest extends TestCase
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

    public function test_guest_cannot_access_credit_settings_page(): void
    {
        $this->get('/admin/credit-settings')
            ->assertRedirect('/admin/login');
    }

    public function test_non_admin_cannot_access_credit_settings_page(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get('/admin/credit-settings')
            ->assertRedirect('/admin/login')
            ->assertSessionHas('error');
    }

    public function test_admin_can_view_credit_settings_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        CreditSetting::create([
            'signup_credit'   => 1200.00,
            'otp_credit'      => 800.00,
            'referral_credit' => 150.00,
            'updated_by'      => 'Super Admin',
        ]);

        $response = $this->actingAs($admin)->get('/admin/credit-settings');

        $response->assertStatus(200);
        $response->assertSee('Credit Settings');
        $response->assertSee('Default Customer Check Credits');
        $response->assertDontSee('OTP First Login Credits');
        $response->assertSee('value="1200"', false);
        $response->assertSee('value="150"', false);
    }

    public function test_admin_can_update_credit_settings(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'name' => 'Admin Boss']);

        $response = $this->actingAs($admin)->post('/admin/credit-settings', [
            'signup_credit'   => 1500,
            'referral_credit' => 200,
        ]);

        $response->assertRedirect('/admin/credit-settings');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('credit_settings', [
            'signup_credit'   => 1500.00,
            'referral_credit' => 200.00,
            'updated_by'      => 'Admin Boss',
        ]);

        $this->assertSame(1500.0, CreditSetting::getSignupCredit());
        $this->assertSame(200.0, CreditSetting::getReferralCredit());
    }

    public function test_public_credit_settings_api_returns_default_customer_and_referral_credit(): void
    {
        CreditSetting::create([
            'signup_credit' => 1350.00,
            'otp_credit' => 800.00,
            'referral_credit' => 175.00,
        ]);

        $expected = [
            'success' => true,
            'data' => [
                'default_customer_credit' => 1350,
                'referral_credit' => 175,
            ],
        ];

        $this->getJson('/api/v1/credits/settings')
            ->assertOk()
            ->assertJson($expected);

        $this->getJson('/api/v1/credits/setting')
            ->assertOk()
            ->assertJson($expected);
    }

    public function test_credit_settings_validation_rejects_negative_or_missing_values(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post('/admin/credit-settings', [
            'signup_credit'   => -50,
            'referral_credit' => '',
        ]);

        $response->assertSessionHasErrors(['signup_credit', 'referral_credit']);
    }

    public function test_scenario_1_new_customer_checkin_uses_configured_signup_credit(): void
    {
        CreditSetting::create([
            'signup_credit'   => 1750.00,
            'otp_credit'      => 1000.00,
            'referral_credit' => 100.00,
        ]);

        $response = $this->postJson('/api/v1/customers/check', [
            'mobile' => '9876543210',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('created', true)
            ->assertJsonPath('balance', 1750);

        $customer = Customer::where('mobile', '9876543210')->first();
        $this->assertNotNull($customer);

        $credit = CustomerCredit::where('customer_id', $customer->customer_id)->first();
        $this->assertSame(1750.0, (float) $credit->balance);
    }

    public function test_customer_check_after_otp_verification_applies_coupon_credit_once(): void
    {
        CreditSetting::create([
            'signup_credit'   => 1750.00,
            'otp_credit'      => 2500.00,
            'referral_credit' => 100.00,
        ]);
        BonusCoupon::create([
            'influencer_bonus_coupon_id' => 'AFTEROTP',
            'bonus_coupon_poster_credit' => 325.00,
            'bonus_coupon_status' => 'active',
            'influencer_name' => 'OTP Influencer',
        ]);

        $mobile = '9876543219';

        $otpMock = Mockery::mock(OtpService::class);
        $otpMock->shouldReceive('verify')->with($mobile, '1234')->once()->andReturn(true);
        $this->app->instance(OtpService::class, $otpMock);

        $response = $this->postJson('/api/v1/auth/otp/verify', [
            'mobile' => $mobile,
            'otp'    => '1234',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $customer = Customer::where('mobile', $mobile)->first();
        $this->assertNotNull($customer);
        $this->assertDatabaseMissing('customer_credits', ['customer_id' => $customer->customer_id]);

        $this->postJson('/api/v1/customers/check', [
            'mobile' => $mobile,
            'coupon_code' => 'AFTEROTP',
        ])
            ->assertOk()
            ->assertJsonPath('created', false)
            ->assertJsonPath('coupon_applied', true)
            ->assertJsonPath('coupon_code_status', 'active')
            ->assertJsonPath('balance', 325);

        $credit = CustomerCredit::where('customer_id', $customer->customer_id)->first();
        $this->assertSame(325.0, (float) $credit->balance);
        $this->assertSame('AFTEROTP', $customer->fresh()->influencer_bonus_coupon_id);

        $this->postJson('/api/v1/customers/check', [
            'mobile' => $mobile,
            'coupon_code' => 'AFTEROTP',
        ])->assertJsonPath('balance', 325);
        $this->assertSame(325.0, (float) $credit->fresh()->balance);
    }

    public function test_customer_check_after_otp_uses_configured_default_without_coupon(): void
    {
        CreditSetting::create([
            'signup_credit' => 1777.00,
            'otp_credit' => 2500.00,
            'referral_credit' => 100.00,
        ]);

        $mobile = '9876543218';
        $otpMock = Mockery::mock(OtpService::class);
        $otpMock->shouldReceive('verify')->with($mobile, '1234')->once()->andReturn(true);
        $this->app->instance(OtpService::class, $otpMock);

        $this->postJson('/api/v1/auth/otp/verify', [
            'mobile' => $mobile,
            'otp' => '1234',
        ])->assertOk();

        $customer = Customer::where('mobile', $mobile)->firstOrFail();
        $this->assertDatabaseMissing('customer_credits', ['customer_id' => $customer->customer_id]);

        $this->postJson('/api/v1/customers/check', ['mobile' => $mobile])
            ->assertOk()
            ->assertJsonPath('balance', 1777)
            ->assertJsonPath('coupon_applied', false);
    }

    public function test_scenario_5_admin_approval_awards_configured_referral_credit_and_notification(): void
    {
        CreditSetting::create([
            'signup_credit'   => 1000.00,
            'otp_credit'      => 1000.00,
            'referral_credit' => 300.00, // Custom configured referral reward
        ]);

        $admin = User::factory()->create(['is_admin' => true]);

        $referrer = Customer::create([
            'mobile'   => '9560213952',
            'fcm'      => 'fcm_token_referrer_abc',
            'language' => 'en',
        ]);

        CustomerCredit::create([
            'customer_id' => $referrer->customer_id,
            'balance'     => 100.00,
        ]);

        $job = Job::create([
            'temp_id'         => 'temp-job-ref-test',
            'device_id'       => 'device-job-ref',
            'device_os'       => 'android',
            'master_category' => 'Services',
            'subcategory'     => 'IT and Software',
            'business_name'   => 'Referral Enterprise',
            'job_role'        => 'Marketing Lead',
            'job_type'        => 'full_time',
            'salary'          => 2500,
            'phone_number'    => '9560213954',
            'latitude'        => 28.6139,
            'longitude'       => 77.2090,
            'city'            => 'New Delhi',
            'status'          => 'pending',
            'plan_id'         => 'plan-1',
        ]);

        Referral::create([
            'referrer_name'   => 'Chandra Prakash',
            'referrer_mobile' => '9560213952',
            'referral_name'   => 'Ankit Publisher',
            'referral_mobile' => '9560213954',
            'status'          => 'IN PROGRESS',
        ]);

        $this->actingAs($admin)
            ->post('/admin/ads/job/' . $job->id . '/status', [
                'status' => 'approved',
            ])
            ->assertRedirect();

        // Referrer should receive exactly 300 credits: 100 + 300 = 400
        $credit = CustomerCredit::where('customer_id', $referrer->customer_id)->first();
        $this->assertSame(400.0, (float) $credit->balance);

        $referral = Referral::where('referral_mobile', '9560213954')->first();
        $this->assertSame('Success', $referral->status);

        // Check FCM notification body mentions 300 credits
        $sentMessages = FirebaseNotificationService::getSentMessages();
        $this->assertNotEmpty($sentMessages);
        $referralMsg = collect($sentMessages)->firstWhere('token', 'fcm_token_referrer_abc');
        $this->assertNotNull($referralMsg);
        $this->assertStringContainsString('300 Poster Credits', $referralMsg['body']);
        $this->assertSame('300', $referralMsg['data']['credits_added']);
        $this->assertSame('400', $referralMsg['data']['balance']);
    }
}
