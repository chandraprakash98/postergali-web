<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerCredit;
use App\Models\Job;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerInvoicePdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_rejects_missing_mobile_number(): void
    {
        $response = $this->getJson('/api/v1/customers/invoice');
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['mobile']);

        $postResponse = $this->postJson('/api/v1/customers/invoice', []);
        $postResponse->assertStatus(422)
            ->assertJsonValidationErrors(['mobile']);
    }

    public function test_invoice_rejects_invalid_mobile_format(): void
    {
        $response = $this->getJson('/api/v1/customers/invoice?mobile=invalid-num');
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['mobile']);
    }

    public function test_invoice_returns_404_when_customer_not_found(): void
    {
        $response = $this->getJson('/api/v1/customers/invoice?mobile=9999999999');
        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Customer not found for the provided mobile number.',
            ]);
    }

    public function test_invoice_returns_404_when_customer_has_no_payments(): void
    {
        $customer = Customer::create([
            'customer_id' => 'PSTGL_NOPAY',
            'mobile' => '9888877777',
        ]);

        $response = $this->getJson('/api/v1/customers/invoice?mobile=9888877777');
        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'No payment records found for this customer.',
            ]);
    }

    public function test_invoice_generates_and_streams_pdf_for_valid_customer_and_payments(): void
    {
        $customer = Customer::create([
            'customer_id' => 'PSTGL_INV_01',
            'mobile' => '9876543210',
        ]);

        CustomerCredit::create([
            'customer_id' => $customer->customer_id,
            'balance' => 450.00,
        ]);

        $job = Job::create([
            'temp_id' => 'temp-inv-job-1',
            'device_id' => 'device-inv-1',
            'device_os' => 'android',
            'master_category' => 'Services',
            'business_name' => 'Poster Studio Noida',
            'job_role' => 'Graphic Designer',
            'job_type' => 'full_time',
            'salary' => '1200.00',
            'phone_number' => '9876543210',
            'latitude' => 28.5355,
            'longitude' => 77.3910,
            'city' => 'Noida',
            'plan_id' => 'plan-1',
            'status' => 'active',
        ]);

        Payment::create([
            'customer_id' => $customer->customer_id,
            'transaction_id' => 'TXN_INV_TEST_01',
            'job_or_offer_id' => $job->id,
            'item_type' => 'job',
            'credit_mode' => 'semi',
            'payment_type' => 'SEMI',
            'total_amount' => 1200.00,
            'razorpay_amount' => 700.00,
            'credit_amount' => 500.00,
            'razorpay_order_id' => 'order_inv_123',
            'razorpay_payment_id' => 'pay_inv_456',
            'payment_status' => 'COMPLETED',
        ]);

        Payment::create([
            'customer_id' => $customer->customer_id,
            'transaction_id' => 'TXN_INV_TEST_02',
            'job_or_offer_id' => $job->id,
            'item_type' => 'job',
            'credit_mode' => 'full_upi',
            'payment_type' => 'FULL_UPI',
            'total_amount' => 300.00,
            'razorpay_amount' => 300.00,
            'credit_amount' => 0.00,
            'razorpay_order_id' => 'order_inv_789',
            'razorpay_payment_id' => 'pay_inv_999',
            'payment_status' => 'COMPLETED',
        ]);

        // Test GET request
        $response = $this->get('/api/v1/customers/invoice?mobile=9876543210');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('attachment;', $response->headers->get('content-disposition'));
        $this->assertStringContainsString('invoice_PSTGL_INV_01_', $response->headers->get('content-disposition'));

        // Verify PDF binary header starts with %PDF-
        $content = $response->getContent();
        $this->assertStringStartsWith('%PDF-', $content);

        // Test POST request with formatted mobile number
        $postResponse = $this->postJson('/api/v1/customers/invoice', [
            'mobile' => '+91 9876543210',
        ]);

        $postResponse->assertStatus(200);
        $postResponse->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('attachment;', $postResponse->headers->get('content-disposition'));
        $this->assertStringStartsWith('%PDF-', $postResponse->getContent());

        // Test payments alias endpoint
        $aliasResponse = $this->get('/api/v1/payments/invoice?mobile=9876543210');
        $aliasResponse->assertStatus(200);
        $aliasResponse->assertHeader('content-type', 'application/pdf');
    }
}
