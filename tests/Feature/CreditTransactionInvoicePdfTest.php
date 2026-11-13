<?php

namespace Tests\Feature;

use App\Models\CreditPack;
use App\Models\CreditLedger;
use App\Models\Customer;
use App\Models\CustomerCredit;
use App\Models\PaymentTransaction;
use Database\Seeders\CreditPackSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreditTransactionInvoicePdfTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $customer;
    protected CreditPack $pack;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(CreditPackSeeder::class);
        $this->pack = CreditPack::where('pack_code', 'credits_220')->firstOrFail();

        $this->customer = Customer::create([
            'customer_id' => 'PSTGL88888',
            'mobile' => '9876543210',
        ]);

        CustomerCredit::create([
            'customer_id' => $this->customer->customer_id,
            'balance' => 1220.00,
        ]);
    }

    public function test_credit_invoice_rejects_missing_identifiers(): void
    {
        $response = $this->getJson('/api/v1/credits/invoice');
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['mobile']);
    }

    public function test_credit_invoice_returns_404_when_customer_not_found(): void
    {
        $response = $this->getJson('/api/v1/credits/invoice?customer_id=PSTGL_NONEXISTENT');
        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Customer not found for the provided PosterGali ID.',
            ]);
    }

    public function test_credit_invoice_returns_404_when_no_successful_transactions_exist(): void
    {
        $response = $this->getJson('/api/v1/credits/invoice?customer_id=' . $this->customer->customer_id);
        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'No successful Razorpay or Apple IAP payment transactions found for this customer.',
            ]);
    }

    public function test_credit_invoice_generates_and_directly_opens_razorpay_pdf_by_mobile(): void
    {
        $txn = PaymentTransaction::create([
            'transaction_uuid' => 'txn-rzp-inv-001',
            'customer_id' => $this->customer->customer_id,
            'credit_pack_id' => $this->pack->id,
            'payment_provider' => PaymentTransaction::PROVIDER_RAZORPAY,
            'amount' => 199.00,
            'currency' => 'INR',
            'credits_to_add' => 220,
            'provider_order_id' => 'order_rzp_inv_123',
            'provider_payment_id' => 'pay_rzp_inv_456',
            'status' => PaymentTransaction::STATUS_SUCCESS,
            'verification_status' => PaymentTransaction::VERIFICATION_VERIFIED,
            'paid_at' => now(),
        ]);

        // Direct open (default: inline)
        $response = $this->get('/api/v1/credits/invoice?mobile=' . $this->customer->mobile);

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('inline;', $response->headers->get('content-disposition'));
        $this->assertStringContainsString('invoice_PSTGL88888_razorpay_', $response->headers->get('content-disposition'));

        // Verify PDF binary output
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_credit_invoice_generates_and_directly_opens_apple_iap_pdf_by_postergaliid(): void
    {
        $txn = PaymentTransaction::create([
            'transaction_uuid' => 'txn-apple-inv-002',
            'customer_id' => $this->customer->customer_id,
            'credit_pack_id' => $this->pack->id,
            'payment_provider' => PaymentTransaction::PROVIDER_APPLE_IAP,
            'amount' => 199.00,
            'currency' => 'INR',
            'credits_to_add' => 220,
            'provider_transaction_id' => '1000000987654321',
            'provider_product_id' => $this->pack->product_id,
            'status' => PaymentTransaction::STATUS_SUCCESS,
            'verification_status' => PaymentTransaction::VERIFICATION_VERIFIED,
            'paid_at' => now(),
        ]);

        // Using postergaliid parameter
        $response = $this->get('/api/v1/credits/invoice?postergaliid=' . $this->customer->customer_id);

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('inline;', $response->headers->get('content-disposition'));
        $this->assertStringContainsString('invoice_PSTGL88888_apple_iap_', $response->headers->get('content-disposition'));
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_credit_invoice_supports_post_request_and_forced_download(): void
    {
        PaymentTransaction::create([
            'transaction_uuid' => 'txn-rzp-inv-003',
            'customer_id' => $this->customer->customer_id,
            'credit_pack_id' => $this->pack->id,
            'payment_provider' => PaymentTransaction::PROVIDER_RAZORPAY,
            'amount' => 199.00,
            'currency' => 'INR',
            'credits_to_add' => 220,
            'provider_order_id' => 'order_rzp_dl_123',
            'status' => PaymentTransaction::STATUS_SUCCESS,
            'paid_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/credits/invoice', [
            'postergali_id' => $this->customer->customer_id,
            'download' => true,
        ]);

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('attachment;', $response->headers->get('content-disposition'));
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_credit_invoice_targets_specific_transaction(): void
    {
        $txn1 = PaymentTransaction::create([
            'transaction_uuid' => 'txn-first-001',
            'customer_id' => $this->customer->customer_id,
            'credit_pack_id' => $this->pack->id,
            'payment_provider' => PaymentTransaction::PROVIDER_RAZORPAY,
            'amount' => 199.00,
            'currency' => 'INR',
            'credits_to_add' => 220,
            'provider_order_id' => 'order_first_001',
            'status' => PaymentTransaction::STATUS_SUCCESS,
            'paid_at' => now()->subDays(2),
        ]);

        $txn2 = PaymentTransaction::create([
            'transaction_uuid' => 'txn-second-002',
            'customer_id' => $this->customer->customer_id,
            'credit_pack_id' => $this->pack->id,
            'payment_provider' => PaymentTransaction::PROVIDER_APPLE_IAP,
            'amount' => 199.00,
            'currency' => 'INR',
            'credits_to_add' => 220,
            'provider_transaction_id' => 'apple_txn_specific_002',
            'status' => PaymentTransaction::STATUS_SUCCESS,
            'paid_at' => now(),
        ]);

        // Request specific first transaction
        $response = $this->get('/api/v1/credits/invoice?customer_id=' . $this->customer->customer_id . '&transaction_uuid=txn-first-001');

        $response->assertStatus(200);
        $this->assertStringContainsString('invoice_PSTGL88888_razorpay_', $response->headers->get('content-disposition'));
    }

    public function test_credit_invoice_can_be_generated_from_mobile_and_credit_ledger_id(): void
    {
        $transaction = PaymentTransaction::create([
            'transaction_uuid' => 'txn-ledger-invoice-001',
            'customer_id' => $this->customer->customer_id,
            'credit_pack_id' => $this->pack->id,
            'payment_provider' => PaymentTransaction::PROVIDER_RAZORPAY,
            'amount' => 199.00,
            'currency' => 'INR',
            'credits_to_add' => 220,
            'provider_order_id' => 'order-ledger-invoice-001',
            'status' => PaymentTransaction::STATUS_SUCCESS,
            'verification_status' => PaymentTransaction::VERIFICATION_VERIFIED,
            'paid_at' => now(),
        ]);
        $ledgerEntry = CreditLedger::create([
            'customer_id' => $this->customer->customer_id,
            'payment_transaction_id' => $transaction->id,
            'entry_type' => CreditLedger::TYPE_CREDIT,
            'transaction_category' => CreditLedger::CATEGORY_WALLET_TOPUP,
            'credit_amount' => 220,
            'debit_amount' => 0,
            'balance_before' => 1000,
            'balance_after' => 1220,
            'reference_type' => 'payment_transaction',
            'reference_id' => $transaction->transaction_uuid,
            'description' => 'Credit Top-up invoice test',
        ]);

        $response = $this->get('/api/v1/credits/invoice?' . http_build_query([
            'mobile' => $this->customer->mobile,
            'credit_ledger_id' => $ledgerEntry->id,
        ]));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('invoice_PSTGL88888_razorpay_', $response->headers->get('content-disposition'));
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_credit_ledger_invoice_rejects_non_purchase_ledger_entries(): void
    {
        $ledgerEntry = CreditLedger::create([
            'customer_id' => $this->customer->customer_id,
            'entry_type' => CreditLedger::TYPE_CREDIT,
            'transaction_category' => CreditLedger::CATEGORY_BONUS,
            'credit_amount' => 100,
            'debit_amount' => 0,
            'balance_before' => 1000,
            'balance_after' => 1100,
            'reference_type' => 'referral',
            'reference_id' => 'referral-1',
            'description' => 'Referral bonus',
        ]);

        $this->getJson('/api/v1/credits/invoice?' . http_build_query([
            'mobile' => $this->customer->mobile,
            'credit_ledger_id' => $ledgerEntry->id,
        ]))
            ->assertNotFound()
            ->assertJsonPath('message', 'The selected credit ledger entry is not a credit purchase.');
    }

    public function test_credit_ledger_invoice_requires_a_mobile_number(): void
    {
        $this->getJson('/api/v1/credits/invoice?credit_ledger_id=1')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['mobile']);
    }
}
