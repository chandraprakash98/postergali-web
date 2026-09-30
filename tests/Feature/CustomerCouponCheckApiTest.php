<?php

namespace Tests\Feature;

use App\Models\BonusCoupon;
use App\Models\Customer;
use App\Models\CustomerCredit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerCouponCheckApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_customer_receives_active_coupon_credit_and_code_is_recorded(): void
    {
        BonusCoupon::create([
            'influencer_bonus_coupon_id' => 'INFLUENCER25',
            'bonus_coupon_poster_credit' => 250.50,
            'bonus_coupon_status' => 'active',
            'influencer_name' => 'Test Influencer',
        ]);

        $response = $this->getJson('/api/v1/customers/check?' . http_build_query([
            'mobile' => '1111111119',
            'coupon_code' => 'INFLUENCER25',
            'fcm' => 'test-fcm-token',
        ]));

        $response->assertCreated()
            ->assertJsonPath('created', true)
            ->assertJsonPath('coupon_applied', true)
            ->assertJsonPath('coupon_code_status', 'active');

        $this->assertEquals(250.50, (float) $response->json('balance'));
        $this->assertDatabaseHas('customers', [
            'mobile' => '1111111119',
            'influencer_bonus_coupon_id' => 'INFLUENCER25',
            'fcm' => 'test-fcm-token',
        ]);
        $this->assertDatabaseHas('customer_credits', [
            'customer_id' => $response->json('customer_id'),
            'balance' => 250.50,
        ]);
    }

    public function test_new_customer_gets_default_credit_and_invalid_coupon_is_reported(): void
    {
        $response = $this->getJson('/api/v1/customers/check?' . http_build_query([
            'mobile' => '1111111118',
            'couponcode' => 'DOESNOTEXIST',
        ]));

        $response->assertCreated()
            ->assertJsonPath('created', true)
            ->assertJsonPath('balance', 1000)
            ->assertJsonPath('coupon_applied', false)
            ->assertJsonPath('coupon_code_status', 'invalid')
            ->assertJsonPath('message', 'Coupon code is invalid or inactive. Default credit assigned.');
    }

    public function test_new_customer_gets_default_credit_for_inactive_coupon(): void
    {
        BonusCoupon::create([
            'influencer_bonus_coupon_id' => 'INACTIVE25',
            'bonus_coupon_poster_credit' => 250,
            'bonus_coupon_status' => 'inactive',
            'influencer_name' => 'Test Influencer',
        ]);

        $response = $this->getJson('/api/v1/customers/check?' . http_build_query([
            'mobile' => '1111111117',
            'coupon_code' => 'INACTIVE25',
        ]));

        $response->assertCreated()
            ->assertJsonPath('balance', 1000)
            ->assertJsonPath('coupon_applied', false)
            ->assertJsonPath('coupon_code_status', 'inactive');

        $this->assertDatabaseHas('customers', [
            'mobile' => '1111111117',
            'influencer_bonus_coupon_id' => null,
        ]);
    }

    public function test_existing_customer_only_gets_fcm_updated_and_current_credit_returned(): void
    {
        $customer = Customer::create([
            'mobile' => '1111111116',
            'fcm' => 'old-token',
        ]);
        CustomerCredit::create([
            'customer_id' => $customer->customer_id,
            'balance' => 425,
        ]);
        BonusCoupon::create([
            'influencer_bonus_coupon_id' => 'IGNORED25',
            'bonus_coupon_poster_credit' => 250,
            'bonus_coupon_status' => 'active',
            'influencer_name' => 'Test Influencer',
        ]);

        $response = $this->getJson('/api/v1/customers/check?' . http_build_query([
            'mobile' => '1111111116',
            'coupon_code' => 'IGNORED25',
            'fcm' => 'new-token',
        ]));

        $response->assertOk()
            ->assertJsonPath('created', false)
            ->assertJsonPath('fcm', 'new-token');

        $this->assertEquals(425, (float) $response->json('balance'));
        $this->assertDatabaseHas('customers', [
            'customer_id' => $customer->customer_id,
            'influencer_bonus_coupon_id' => null,
            'fcm' => 'new-token',
        ]);
        $this->assertDatabaseHas('customer_credits', [
            'customer_id' => $customer->customer_id,
            'balance' => 425,
        ]);
    }
}
