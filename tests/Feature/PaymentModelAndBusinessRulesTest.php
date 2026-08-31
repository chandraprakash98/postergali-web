<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerCredit;
use App\Models\Payment;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentModelAndBusinessRulesTest extends TestCase
{
    use RefreshDatabase;

    private function baseJobPayload(array $overrides = []): array
    {
        return array_merge([
            'temp_id' => 'temp-job-' . uniqid(),
            'device_id' => 'device-test-01',
            'device_os' => 'android',
            'master_category' => 'Services',
            'business_name' => 'Tech Services Co',
            'job_role' => 'Software Engineer',
            'job_type' => 'full_time',
            'phone_number' => '9876543210',
            'latitude' => 28.5914,
            'longitude' => 77.4021,
            'city' => 'Noida',
            'plan_id' => 'plan-1',
        ], $overrides);
    }

    public function test_full_upi_payment_creates_valid_payment_record(): void
    {
        $payload = $this->baseJobPayload([
            'payment_type' => 'FULL_UPI',
            'total_amount' => 500.00,
            'razorpay_amount' => 500.00,
            'credit_amount' => 0.00,
            'razorpay_order_id' => 'order_upi_111',
            'razorpay_payment_id' => 'pay_upi_222',
            'transaction_id' => 'TXN_UPI_TEST_01',
        ]);

        $response = $this->postJson('/api/v1/jobs', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('payments', [
            'transaction_id' => 'TXN_UPI_TEST_01',
            'payment_type' => 'FULL_UPI',
            'total_amount' => '500.00',
            'razorpay_amount' => '500.00',
            'credit_amount' => '0.00',
            'razorpay_order_id' => 'order_upi_111',
            'razorpay_payment_id' => 'pay_upi_222',
            'payment_status' => 'COMPLETED',
        ]);
    }

    public function test_full_upi_payment_rejects_mismatched_razorpay_amount(): void
    {
        $payload = $this->baseJobPayload([
            'payment_type' => 'FULL_UPI',
            'total_amount' => 500.00,
            'razorpay_amount' => 450.00,
            'credit_amount' => 0.00,
        ]);

        $response = $this->postJson('/api/v1/jobs', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['razorpay_amount']);
    }

    public function test_full_upi_payment_rejects_positive_credit_amount(): void
    {
        $payload = $this->baseJobPayload([
            'payment_type' => 'FULL_UPI',
            'total_amount' => 500.00,
            'razorpay_amount' => 500.00,
            'credit_amount' => 50.00,
        ]);

        $response = $this->postJson('/api/v1/jobs', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['credit_amount']);
    }

    public function test_semi_payment_valid_amounts_and_credit_deduction(): void
    {
        $customer = Customer::create([
            'customer_id' => 'CUST_SEMI_01',
            'mobile' => '9876543210',
        ]);

        CustomerCredit::create([
            'customer_id' => $customer->customer_id,
            'balance' => 1000.00,
        ]);

        $payload = $this->baseJobPayload([
            'customer_id' => $customer->customer_id,
            'payment_type' => 'SEMI',
            'total_amount' => 1200.00,
            'razorpay_amount' => 700.00,
            'credit_amount' => 500.00,
            'razorpay_order_id' => 'order_semi_123',
            'razorpay_payment_id' => 'pay_semi_456',
            'transaction_id' => 'TXN_SEMI_001',
        ]);

        $response = $this->postJson('/api/v1/jobs', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('payments', [
            'transaction_id' => 'TXN_SEMI_001',
            'customer_id' => $customer->customer_id,
            'payment_type' => 'SEMI',
            'total_amount' => '1200.00',
            'razorpay_amount' => '700.00',
            'credit_amount' => '500.00',
            'payment_status' => 'COMPLETED',
        ]);

        $payment = Payment::where('transaction_id', 'TXN_SEMI_001')->first();
        $this->assertNotNull($payment->customer);
        $this->assertSame($customer->id, $payment->customer->id);
        $this->assertTrue($customer->payments->contains($payment));

        $credit = CustomerCredit::where('customer_id', $customer->customer_id)->first();
        $this->assertSame(500.0, (float) $credit->balance);
    }

    public function test_semi_payment_rejects_sum_mismatch(): void
    {
        $payload = $this->baseJobPayload([
            'payment_type' => 'SEMI',
            'total_amount' => 1000.00,
            'razorpay_amount' => 600.00,
            'credit_amount' => 300.00, // 600 + 300 = 900 != 1000
        ]);

        $response = $this->postJson('/api/v1/jobs', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['total_amount']);
    }

    public function test_semi_payment_rejects_zero_razorpay_amount(): void
    {
        $payload = $this->baseJobPayload([
            'payment_type' => 'SEMI',
            'total_amount' => 1000.00,
            'razorpay_amount' => 0.00,
            'credit_amount' => 1000.00,
        ]);

        $response = $this->postJson('/api/v1/jobs', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['razorpay_amount']);
    }

    public function test_semi_payment_rejects_zero_credit_amount(): void
    {
        $payload = $this->baseJobPayload([
            'payment_type' => 'SEMI',
            'total_amount' => 1000.00,
            'razorpay_amount' => 1000.00,
            'credit_amount' => 0.00,
        ]);

        $response = $this->postJson('/api/v1/jobs', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['credit_amount']);
    }

    public function test_full_credit_payment_valid_and_deducts_full_amount(): void
    {
        $customer = Customer::create([
            'customer_id' => 'CUST_FULL_CREDIT_01',
            'mobile' => '9876543211',
        ]);

        CustomerCredit::create([
            'customer_id' => $customer->customer_id,
            'balance' => 800.00,
        ]);

        $payload = $this->baseJobPayload([
            'customer_id' => $customer->customer_id,
            'payment_type' => 'FULL_CREDIT',
            'total_amount' => 300.00,
            'razorpay_amount' => 0.00,
            'credit_amount' => 300.00,
            'transaction_id' => 'TXN_CREDIT_001',
        ]);

        $response = $this->postJson('/api/v1/jobs', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('payments', [
            'transaction_id' => 'TXN_CREDIT_001',
            'payment_type' => 'FULL_CREDIT',
            'total_amount' => '300.00',
            'razorpay_amount' => '0.00',
            'credit_amount' => '300.00',
        ]);

        $credit = CustomerCredit::where('customer_id', $customer->customer_id)->first();
        $this->assertSame(500.0, (float) $credit->balance);
    }

    public function test_full_credit_payment_rejects_positive_razorpay_amount(): void
    {
        $payload = $this->baseJobPayload([
            'payment_type' => 'FULL_CREDIT',
            'total_amount' => 300.00,
            'razorpay_amount' => 100.00,
            'credit_amount' => 300.00,
        ]);

        $response = $this->postJson('/api/v1/jobs', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['razorpay_amount']);
    }

    public function test_decimal_precision_exact_cents(): void
    {
        $payload = $this->baseJobPayload([
            'payment_type' => 'SEMI',
            'total_amount' => '199.99',
            'razorpay_amount' => '129.50',
            'credit_amount' => '70.49', // 129.50 + 70.49 = 199.99
            'transaction_id' => 'TXN_DECIMAL_001',
        ]);

        $response = $this->postJson('/api/v1/jobs', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('payments', [
            'transaction_id' => 'TXN_DECIMAL_001',
            'total_amount' => '199.99',
            'razorpay_amount' => '129.50',
            'credit_amount' => '70.49',
        ]);
    }

    public function test_payment_fetches_customer_id_by_mobile_number_when_saving_payment(): void
    {
        $customer = Customer::create([
            'customer_id' => 'PSTGL_AUTO_001',
            'mobile' => '9988776655',
        ]);

        $payload = $this->baseJobPayload([
            'phone_number' => '9988776655',
            'payment_type' => 'FULL_UPI',
            'total_amount' => 600.00,
            'razorpay_amount' => 600.00,
            'credit_amount' => 0.00,
            'transaction_id' => 'TXN_MOBILE_LOOKUP_01',
        ]);
        unset($payload['customer_id']);

        $response = $this->postJson('/api/v1/jobs', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('payments', [
            'transaction_id' => 'TXN_MOBILE_LOOKUP_01',
            'customer_id' => 'PSTGL_AUTO_001',
            'total_amount' => '600.00',
        ]);
    }
}
