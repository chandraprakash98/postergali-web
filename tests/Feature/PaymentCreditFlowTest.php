<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerCredit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentCreditFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_posting_with_semi_credit_reduces_customer_balance_and_creates_payment(): void
    {
        $customer = Customer::create([
            'customer_id' => 'PSTGL00001',
            'mobile' => '0501234567',
        ]);

        CustomerCredit::create([
            'customer_id' => $customer->customer_id,
            'balance' => 1000,
        ]);

        $payload = [
            'temp_id' => 'temp-job-1',
            'device_id' => 'device-job-1',
            'device_os' => 'android',
            'master_category' => 'Services',
            'business_name' => 'Test Business',
            'job_role' => 'Developer',
            'job_type' => 'full_time',
            'amount' => 1200,
            'phone_number' => '0505555555',
            'latitude' => 24.4539,
            'longitude' => 54.3773,
            'city' => 'Abu Dhabi',
            'plan_id' => 'plan-1',
            'customer_id' => $customer->customer_id,
            'transaction_id' => 'txn-001',
            'credit_mode' => 'semi',
        ];

        $response = $this->postJson('/api/v1/jobs', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('jobs', [
            'temp_id' => 'temp-job-1',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('payments', [
            'transaction_id' => 'txn-001',
            'customer_id' => $customer->customer_id,
            'item_type' => 'job',
            'credit_mode' => 'semi',
        ]);

        $credit = CustomerCredit::where('customer_id', $customer->customer_id)->first();
        $this->assertSame(0.0, (float) $credit->balance);
    }

    public function test_offer_posting_with_semi_credit_reduces_customer_balance_and_creates_payment(): void
    {
        $customer = Customer::create([
            'customer_id' => 'PSTGL00002',
            'mobile' => '0507654321',
        ]);

        CustomerCredit::create([
            'customer_id' => $customer->customer_id,
            'balance' => 1000,
        ]);

        $payload = [
            'temp_id' => 'temp-offer-1',
            'device_id' => 'device-offer-1',
            'device_os' => 'android',
            'master_category' => 'Services',
            'business_name' => 'Offer Business',
            'offer_details' => 'Special discount offer',
            'offer_type' => 'discount',
            'amount' => 250,
            'mobile_number' => '0507654321',
            'latitude' => 24.4539,
            'longitude' => 54.3773,
            'city' => 'Abu Dhabi',
            'plan_id' => 'plan-1',
            'customer_id' => $customer->customer_id,
            'transaction_id' => 'txn-offer-001',
            'credit_mode' => 'semi',
        ];

        $response = $this->postJson('/api/v1/offers', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('payments', [
            'transaction_id' => 'txn-offer-001',
            'customer_id' => $customer->customer_id,
            'item_type' => 'offer',
            'credit_mode' => 'semi',
        ]);

        $credit = CustomerCredit::where('customer_id', $customer->customer_id)->first();
        $this->assertSame(750.0, (float) $credit->balance);
    }

    public function test_offer_posting_with_semi_credit_creates_missing_credit_row_and_deducts_balance(): void
    {
        $customer = Customer::create([
            'customer_id' => 'PSTGL00003',
            'mobile' => '0501122334',
        ]);

        $payload = [
            'temp_id' => 'temp-offer-2',
            'device_id' => 'device-offer-2',
            'device_os' => 'android',
            'master_category' => 'Services',
            'business_name' => 'Offer Business 2',
            'offer_details' => 'Another special discount offer',
            'offer_type' => 'discount',
            'amount' => 300,
            'mobile_number' => '0501122334',
            'latitude' => 24.4539,
            'longitude' => 54.3773,
            'city' => 'Abu Dhabi',
            'plan_id' => 'plan-1',
            'customer_id' => $customer->customer_id,
            'transaction_id' => 'txn-offer-002',
            'credit_mode' => 'semi',
        ];

        $response = $this->postJson('/api/v1/offers', $payload);

        $response->assertStatus(201);

        $credit = CustomerCredit::where('customer_id', $customer->customer_id)->first();
        $this->assertNotNull($credit);
        $this->assertSame(700.0, (float) $credit->balance);

        $this->assertDatabaseHas('offers', [
            'temp_id' => 'temp-offer-2',
            'amount' => 300,
        ]);
    }
}
