<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReferralApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_save_referrer_and_referrals_in_a_single_table(): void
    {
        $payload = [
            'referrer_name' => 'John Doe',
            'referrer_mobile' => '0501234567',
            'status' => 'active',
            'referrals' => [
                ['referral_name' => 'Alice', 'referral_mobile' => '0501111111', 'status' => 'active'],
                ['referral_name' => 'Bob', 'referral_mobile' => '0502222222', 'status' => 'inactive'],
            ],
        ];

        $response = $this->postJson('/api/v1/referrals', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Referrals saved successfully.',
            ]);

        $this->assertDatabaseHas('referrals', [
            'referrer_name' => 'John Doe',
            'referrer_mobile' => '0501234567',
            'referral_name' => 'Alice',
            'referral_mobile' => '0501111111',
            'status' => 'IN PROGRESS',
        ]);

        $this->assertDatabaseHas('referrals', [
            'referrer_name' => 'John Doe',
            'referrer_mobile' => '0501234567',
            'referral_name' => 'Bob',
            'referral_mobile' => '0502222222',
            'status' => 'IN PROGRESS',
        ]);
    }

    public function test_it_returns_referrer_customer_id_in_check_response(): void
    {
        $customer = Customer::create([
            'mobile' => '0501234567',
        ]);

        $referral = \App\Models\Referral::create([
            'referrer_name' => 'John Doe',
            'referrer_mobile' => '0501234567',
            'referral_name' => 'Alice',
            'referral_mobile' => '0501111111',
            'status' => 'IN PROGRESS',
        ]);

        $response = $this->getJson('/api/v1/referrals/check?mobile=' . $referral->referral_mobile);

        $response->assertOk()
            ->assertJsonPath('found', true)
            ->assertJsonPath('customer_id', $customer->customer_id)
            ->assertJsonPath('customerId', $customer->customer_id);
    }

    public function test_it_rejects_more_than_five_referrals_and_duplicate_mobiles(): void
    {
        $payload = [
            'referrer_name' => 'Jane Doe',
            'referrer_mobile' => '0509876543',
            'referrals' => [
                ['referral_name' => 'A', 'referral_mobile' => '0500000001'],
                ['referral_name' => 'B', 'referral_mobile' => '0500000002'],
                ['referral_name' => 'C', 'referral_mobile' => '0500000003'],
                ['referral_name' => 'D', 'referral_mobile' => '0500000004'],
                ['referral_name' => 'E', 'referral_mobile' => '0500000005'],
                ['referral_name' => 'F', 'referral_mobile' => '0500000005'],
            ],
        ];

        $response = $this->postJson('/api/v1/referrals', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([ 'referrals' ]);
    }
}
