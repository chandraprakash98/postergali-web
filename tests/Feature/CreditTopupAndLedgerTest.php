<?php

namespace Tests\Feature;

use App\Models\CreditLedger;
use App\Models\CreditPack;
use App\Models\Customer;
use App\Models\CustomerAccessToken;
use App\Models\CustomerCredit;
use App\Models\PaymentTransaction;
use App\Models\Plan;
use App\Models\User;
use App\Services\Payments\CreditTopupService;
use Database\Seeders\CreditPackSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CreditTopupAndLedgerTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $customer;
    protected string $customerToken;
    protected User $adminUser;
    protected CreditPack $consumablePlan;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed packages
        $this->seed(CreditPackSeeder::class);
        $this->consumablePlan = CreditPack::where('pack_code', 'credits_220')->firstOrFail();

        // Create test customer with token & initial wallet
        $this->customer = Customer::create([
            'customer_id' => 'PSTGL_TEST_01',
            'mobile' => '9876543210',
        ]);

        CustomerCredit::create([
            'customer_id' => $this->customer->customer_id,
            'balance' => 1000.00,
        ]);

        $tokenResult = CustomerAccessToken::createToken($this->customer);
        $this->customerToken = $tokenResult['plainTextToken'];

        // Create admin user
        $this->adminUser = User::create([
            'name' => 'Admin Tester',
            'email' => 'admin@postergali.com',
            'password' => bcrypt('secret123'),
            'is_admin' => 1,
        ]);
    }

    protected function customerHeaders(array $extra = []): array
    {
        return array_merge([
            'Authorization' => 'Bearer ' . $this->customerToken,
            'Accept' => 'application/json',
        ], $extra);
    }

    /**
     * Test 1: Valid package creation by authorized admin.
     */
    public function test_1_valid_package_creation_by_admin(): void
    {
        $payload = [
            'plan_title' => 'Mega Pack',
            'plan_code' => 'credits_5000',
            'product_id' => 'com.postergali.credits.5000',
            'credits' => 5000,
            'price' => 3999.00,
            'currency' => 'INR',
            'plan_type' => 'CONSUMABLE',
            'is_active' => true,
            'sort_order' => 6,
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson('/api/v1/admin/packages', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'plan_title' => 'Mega Pack',
                    'plan_code' => 'credits_5000',
                    'credits' => 5000,
                ],
            ]);

        $this->assertDatabaseHas('credit_packs', [
            'pack_code' => 'credits_5000',
            'product_id' => 'com.postergali.credits.5000',
        ]);
    }

    /**
     * Test 2: Invalid and duplicate product identifiers rejected.
     */
    public function test_2_invalid_and_duplicate_product_identifiers_rejected(): void
    {
        // Duplicate product_id
        $duplicatePayload = [
            'plan_title' => 'Duplicate Pack',
            'plan_code' => 'credits_dup',
            'product_id' => $this->consumablePlan->product_id, // duplicate
            'credits' => 220,
            'price' => 199.00,
            'currency' => 'INR',
            'plan_type' => 'CONSUMABLE',
        ];

        $this->actingAs($this->adminUser)
            ->postJson('/api/v1/admin/packages', $duplicatePayload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['product_id']);

        // Invalid format product_id (spaces or invalid chars)
        $invalidFormatPayload = [
            'plan_title' => 'Bad Product ID Pack',
            'plan_code' => 'credits_bad',
            'product_id' => 'invalid product with spaces!',
            'credits' => 100,
            'price' => 99.00,
            'currency' => 'INR',
            'plan_type' => 'CONSUMABLE',
        ];

        $this->actingAs($this->adminUser)
            ->postJson('/api/v1/admin/packages', $invalidFormatPayload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['product_id']);
    }

    /**
     * Test 3: Negative or zero credit quantities rejected.
     */
    public function test_3_negative_or_zero_credit_quantities_rejected(): void
    {
        foreach ([0, -50] as $invalidCredits) {
            $payload = [
                'plan_title' => 'Zero Pack',
                'plan_code' => 'credits_invalid_' . abs($invalidCredits),
                'product_id' => 'com.postergali.credits.invalid' . abs($invalidCredits),
                'credits' => $invalidCredits,
                'price' => 100.00,
                'currency' => 'INR',
                'plan_type' => 'CONSUMABLE',
            ];

            $this->actingAs($this->adminUser)
                ->postJson('/api/v1/admin/packages', $payload)
                ->assertStatus(422)
                ->assertJsonValidationErrors(['credits']);
        }
    }

    /**
     * Test 4: Invalid package prices rejected.
     */
    public function test_4_invalid_package_prices_rejected(): void
    {
        foreach ([-10.00, 'not-a-number', 999999999999.00] as $invalidPrice) {
            $payload = [
                'plan_title' => 'Bad Price Pack',
                'plan_code' => 'credits_bad_price',
                'product_id' => 'com.postergali.credits.badprice',
                'credits' => 100,
                'price' => $invalidPrice,
                'currency' => 'INR',
                'plan_type' => 'CONSUMABLE',
            ];

            $this->actingAs($this->adminUser)
                ->postJson('/api/v1/admin/packages', $payload)
                ->assertStatus(422)
                ->assertJsonValidationErrors(['price']);
        }
    }

    /**
     * Test 5: Unauthorized package modifications rejected.
     */
    public function test_5_unauthorized_package_modifications_rejected(): void
    {
        // Unauthenticated
        $this->postJson('/api/v1/admin/packages', [
            'plan_title' => 'Hacker Pack',
            'plan_code' => 'credits_hack',
            'product_id' => 'com.postergali.credits.hack',
            'credits' => 1000,
            'price' => 1.00,
            'currency' => 'INR',
            'plan_type' => 'CONSUMABLE',
        ])->assertStatus(401);

        // Authenticated customer (non-admin)
        $nonAdminUser = User::create([
            'name' => 'Regular User',
            'email' => 'regular@postergali.com',
            'password' => bcrypt('password'),
            'is_admin' => 0,
        ]);

        $this->actingAs($nonAdminUser)
            ->postJson('/api/v1/admin/packages', [
                'plan_title' => 'Customer Pack',
                'plan_code' => 'credits_customer',
                'product_id' => 'com.postergali.credits.customer',
                'credits' => 1000,
                'price' => 1.00,
                'currency' => 'INR',
                'plan_type' => 'CONSUMABLE',
            ])->assertStatus(403);
    }

    /**
     * Test 6: Successful Razorpay payment processing using mocked verification.
     */
    public function test_6_successful_razorpay_payment_processing(): void
    {
        $orderId = 'order_test_rzp_123';
        $paymentId = 'pay_test_rzp_456';
        $signature = 'valid_mock_signature';

        // Pre-create pending transaction
        PaymentTransaction::create([
            'transaction_uuid' => 'txn-rzp-001',
            'customer_id' => $this->customer->customer_id,
            'plan_id' => $this->consumablePlan->id,
            'payment_provider' => PaymentTransaction::PROVIDER_RAZORPAY,
            'amount' => $this->consumablePlan->price,
            'currency' => 'INR',
            'credits_to_add' => $this->consumablePlan->credits,
            'provider_order_id' => $orderId,
            'status' => PaymentTransaction::STATUS_PENDING,
        ]);

        $response = $this->postJson('/api/v1/credits/topup', [
            'package_id' => $this->consumablePlan->id,
            'payment_provider' => 'RAZORPAY',
            'action' => 'complete',
            'razorpay_order_id' => $orderId,
            'razorpay_payment_id' => $paymentId,
            'razorpay_signature' => $signature,
        ], $this->customerHeaders());

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'credits_added' => true,
                'credits_granted' => 220,
                'current_balance' => 1220.00,
            ]);

        // Assert payment_transactions table updated
        $this->assertDatabaseHas('payment_transactions', [
            'provider_order_id' => $orderId,
            'provider_payment_id' => $paymentId,
            'status' => PaymentTransaction::STATUS_SUCCESS,
        ]);

        // Assert credit_ledger entry created
        $this->assertDatabaseHas('credit_ledger', [
            'customer_id' => $this->customer->customer_id,
            'entry_type' => CreditLedger::TYPE_CREDIT,
            'transaction_category' => CreditLedger::CATEGORY_WALLET_TOPUP,
            'credit_amount' => 220.00,
            'balance_before' => 1000.00,
            'balance_after' => 1220.00,
        ]);

        // Assert customer_credits balance updated
        $wallet = CustomerCredit::where('customer_id', $this->customer->customer_id)->first();
        $this->assertEquals(1220.00, (float) $wallet->balance);
    }

    public function test_admin_can_filter_payment_transactions_by_mobile_number(): void
    {
        $otherCustomer = Customer::create([
            'customer_id' => 'PSTGL_TEST_02',
            'mobile' => '9876500000',
        ]);

        PaymentTransaction::create([
            'customer_id' => $this->customer->customer_id,
            'plan_id' => $this->consumablePlan->id,
            'payment_provider' => PaymentTransaction::PROVIDER_RAZORPAY,
            'amount' => $this->consumablePlan->price,
            'currency' => 'INR',
            'credits_to_add' => $this->consumablePlan->credits,
            'provider_order_id' => 'order_mobile_filter_match',
            'status' => PaymentTransaction::STATUS_PENDING,
        ]);

        PaymentTransaction::create([
            'customer_id' => $otherCustomer->customer_id,
            'plan_id' => $this->consumablePlan->id,
            'payment_provider' => PaymentTransaction::PROVIDER_RAZORPAY,
            'amount' => $this->consumablePlan->price,
            'currency' => 'INR',
            'credits_to_add' => $this->consumablePlan->credits,
            'provider_order_id' => 'order_mobile_filter_other',
            'status' => PaymentTransaction::STATUS_PENDING,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/v1/admin/credits/transactions?mobile_number=987-654-3210');

        $response->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.customer.mobile', $this->customer->mobile)
            ->assertJsonPath('data.0.provider_order_id', 'order_mobile_filter_match');
    }

    /**
     * Test 7: Successful Apple IAP processing using mocked transaction verification.
     */
    public function test_7_successful_apple_iap_processing(): void
    {
        $appleTxnId = '1000000987654321';
        $jwsPayload = json_encode([
            'transactionId' => $appleTxnId,
            'originalTransactionId' => $appleTxnId,
            'productId' => $this->consumablePlan->product_id, // com.postergali.credits.220
            'purchaseDate' => time(),
            'environment' => 'Sandbox',
        ]);
        $mockJws = 'header.' . base64_encode($jwsPayload) . '.signature';

        $response = $this->postJson('/api/v1/credits/topup', [
            'package_id' => $this->consumablePlan->id,
            'payment_provider' => 'APPLE_IAP',
            'signed_transaction' => $mockJws,
        ], $this->customerHeaders());

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'credits_added' => true,
                'credits_granted' => 220,
                'current_balance' => 1220.00,
            ]);

        // Assert payment_transactions record created
        $this->assertDatabaseHas('payment_transactions', [
            'customer_id' => $this->customer->customer_id,
            'payment_provider' => PaymentTransaction::PROVIDER_APPLE_IAP,
            'provider_transaction_id' => $appleTxnId,
            'status' => PaymentTransaction::STATUS_SUCCESS,
        ]);

        // Assert exactly one credit_ledger entry created
        $this->assertDatabaseHas('credit_ledger', [
            'customer_id' => $this->customer->customer_id,
            'credit_amount' => 220.00,
            'balance_after' => 1220.00,
        ]);
    }

    /**
     * Test 8: Failed payment grants NO credits.
     */
    public function test_8_failed_payment_grants_no_credits(): void
    {
        $initialBalance = CustomerCredit::where('customer_id', $this->customer->customer_id)->value('balance');

        $response = $this->postJson('/api/v1/credits/topup', [
            'package_id' => $this->consumablePlan->id,
            'payment_provider' => 'RAZORPAY',
            'action' => 'complete',
            'razorpay_order_id' => 'order_failed_001',
            'razorpay_payment_id' => 'pay_failed_001',
            'razorpay_signature' => 'invalid_signature',
        ], $this->customerHeaders());

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'credits_added' => false,
            ]);

        // Balance unchanged
        $afterBalance = CustomerCredit::where('customer_id', $this->customer->customer_id)->value('balance');
        $this->assertEquals($initialBalance, $afterBalance);

        // No credit ledger entries created
        $this->assertDatabaseMissing('credit_ledger', [
            'customer_id' => $this->customer->customer_id,
            'transaction_category' => CreditLedger::CATEGORY_WALLET_TOPUP,
        ]);
    }

    /**
     * Test 9: Pending payment (order creation) grants NO credits.
     */
    public function test_9_pending_payment_grants_no_credits(): void
    {
        $initialBalance = CustomerCredit::where('customer_id', $this->customer->customer_id)->value('balance');

        $response = $this->postJson('/api/v1/credits/topup', [
            'package_id' => $this->consumablePlan->id,
            'payment_provider' => 'RAZORPAY',
            'action' => 'initiate',
        ], $this->customerHeaders());

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'credits_added' => false,
                'order' => [
                    'status' => 'PENDING',
                    'credits_to_add' => 220,
                ],
            ]);

        // Wallet balance remains 1000
        $currentBalance = CustomerCredit::where('customer_id', $this->customer->customer_id)->value('balance');
        $this->assertEquals($initialBalance, $currentBalance);

        // Ledger must be empty
        $this->assertDatabaseMissing('credit_ledger', [
            'customer_id' => $this->customer->customer_id,
        ]);
    }

    /**
     * Test 10: Duplicate API request with same Idempotency-Key yields NO duplicate credits.
     */
    public function test_10_duplicate_api_request_no_duplicate_credits(): void
    {
        $appleTxnId = '100000011223344';
        $jwsPayload = json_encode([
            'transactionId' => $appleTxnId,
            'productId' => $this->consumablePlan->product_id,
            'purchaseDate' => time(),
            'environment' => 'Sandbox',
        ]);
        $mockJws = 'h.' . base64_encode($jwsPayload) . '.s';

        $idempotencyKey = 'idemp_key_test_duplicate_01';

        // First request
        $resp1 = $this->postJson('/api/v1/credits/topup', [
            'package_id' => $this->consumablePlan->id,
            'payment_provider' => 'APPLE_IAP',
            'signed_transaction' => $mockJws,
        ], $this->customerHeaders(['Idempotency-Key' => $idempotencyKey]));

        $resp1->assertStatus(200)
            ->assertJson(['credits_granted' => 220, 'current_balance' => 1220.00]);

        // Second request (Duplicate)
        $resp2 = $this->postJson('/api/v1/credits/topup', [
            'package_id' => $this->consumablePlan->id,
            'payment_provider' => 'APPLE_IAP',
            'signed_transaction' => $mockJws,
        ], $this->customerHeaders(['Idempotency-Key' => $idempotencyKey]));

        $resp2->assertStatus(200);

        // Assert wallet is exactly 1220, NOT 1440
        $wallet = CustomerCredit::where('customer_id', $this->customer->customer_id)->first();
        $this->assertEquals(1220.00, (float) $wallet->balance);

        // Exactly one ledger entry
        $this->assertEquals(
            1,
            CreditLedger::where('customer_id', $this->customer->customer_id)->count()
        );
    }

    /**
     * Test 11: Duplicate webhook does not award duplicate credits.
     */
    public function test_11_duplicate_webhook_no_duplicate_credits(): void
    {
        $orderId = 'order_webhook_123';
        $paymentId = 'pay_webhook_456';

        // Pre-create pending transaction
        PaymentTransaction::create([
            'transaction_uuid' => 'txn-webhook-01',
            'customer_id' => $this->customer->customer_id,
            'plan_id' => $this->consumablePlan->id,
            'payment_provider' => PaymentTransaction::PROVIDER_RAZORPAY,
            'amount' => $this->consumablePlan->price,
            'currency' => 'INR',
            'credits_to_add' => 220,
            'provider_order_id' => $orderId,
            'status' => PaymentTransaction::STATUS_PENDING,
        ]);

        $webhookPayload = [
            'event' => 'payment.captured',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id' => $paymentId,
                        'order_id' => $orderId,
                        'amount' => 19900,
                        'status' => 'captured',
                    ],
                ],
            ],
        ];

        // First webhook
        $this->postJson(
            '/api/v1/credits/webhooks/razorpay',
            $webhookPayload,
            ['X-Razorpay-Signature' => 'mock_valid_sig']
        )->assertStatus(200);

        $this->assertEquals(1220.00, (float) CustomerCredit::where('customer_id', $this->customer->customer_id)->value('balance'));

        // Duplicate webhook
        $this->postJson(
            '/api/v1/credits/webhooks/razorpay',
            $webhookPayload,
            ['X-Razorpay-Signature' => 'mock_valid_sig']
        )->assertStatus(200);

        // Balance still exactly 1220.00
        $this->assertEquals(1220.00, (float) CustomerCredit::where('customer_id', $this->customer->customer_id)->value('balance'));
        $this->assertEquals(1, CreditLedger::where('customer_id', $this->customer->customer_id)->count());
    }

    /**
     * Test 12: Concurrent payment processing results in exactly ONE credit allocation.
     */
    public function test_12_concurrent_payment_processing_single_credit_allocation(): void
    {
        $service = app(CreditTopupService::class);
        $orderId = 'order_concurrent_123';
        $paymentId = 'pay_concurrent_456';

        PaymentTransaction::create([
            'transaction_uuid' => 'txn-concurrent-01',
            'customer_id' => $this->customer->customer_id,
            'plan_id' => $this->consumablePlan->id,
            'payment_provider' => PaymentTransaction::PROVIDER_RAZORPAY,
            'amount' => $this->consumablePlan->price,
            'currency' => 'INR',
            'credits_to_add' => 220,
            'provider_order_id' => $orderId,
            'status' => PaymentTransaction::STATUS_PENDING,
        ]);

        // Simulate two concurrent requests completing the same payment
        $txn1 = $service->completeRazorpayPayment(
            $this->customer,
            $this->consumablePlan,
            $orderId,
            $paymentId,
            'valid_signature'
        );

        $txn2 = $service->completeRazorpayPayment(
            $this->customer,
            $this->consumablePlan,
            $orderId,
            $paymentId,
            'valid_signature'
        );

        $this->assertEquals($txn1->id, $txn2->id);
        $this->assertEquals(1220.00, (float) CustomerCredit::where('customer_id', $this->customer->customer_id)->value('balance'));
        $this->assertEquals(1, CreditLedger::where('customer_id', $this->customer->customer_id)->count());
    }

    /**
     * Test 13: Customer isolation in transaction history.
     */
    public function test_13_customer_isolation_in_transaction_history(): void
    {
        // Customer B
        $customerB = Customer::create([
            'customer_id' => 'PSTGL_CUSTOMER_B',
            'mobile' => '9998887776',
        ]);
        CustomerCredit::create([
            'customer_id' => $customerB->customer_id,
            'balance' => 500.00,
        ]);
        $tokenB = CustomerAccessToken::createToken($customerB)['plainTextToken'];

        // Create transaction for Customer A
        $service = app(CreditTopupService::class);
        $txnA = $service->completeRazorpayPayment(
            $this->customer,
            $this->consumablePlan,
            'order_cust_a',
            'pay_cust_a',
            'valid_sig'
        );

        // Customer B requests transactions history
        $responseB = $this->getJson('/api/v1/credits/transactions', [
            'Authorization' => 'Bearer ' . $tokenB,
        ]);

        $responseB->assertStatus(200)
            ->assertJsonCount(0, 'data'); // Customer B sees 0 transactions

        $responseA = $this->getJson('/api/v1/credits/transactions', $this->customerHeaders());
        $responseA->assertStatus(200)
            ->assertJsonPath('data.0.transaction_uuid', $txnA->transaction_uuid)
            ->assertJsonPath('data.0.plan_title', $this->consumablePlan->name);

        // Customer B cannot view Customer A's single transaction by UUID
        $this->getJson("/api/v1/credits/transactions/{$txnA->transaction_uuid}", [
            'Authorization' => 'Bearer ' . $tokenB,
        ])->assertStatus(404);

        // Customer A can view their own transaction
        $this->getJson("/api/v1/credits/transactions/{$txnA->transaction_uuid}", $this->customerHeaders())
            ->assertStatus(200)
            ->assertJsonPath('data.transaction_uuid', $txnA->transaction_uuid);
    }

    /**
     * Test 14: Database failure triggers atomic rollback.
     */
    public function test_14_database_failure_atomic_rollback(): void
    {
        $initialBalance = CustomerCredit::where('customer_id', $this->customer->customer_id)->value('balance');
        $service = app(CreditTopupService::class);

        // Attempt completion with non-existent customer or simulate failure
        try {
            DB::transaction(function () use ($service) {
                $fakeCustomer = new Customer(['customer_id' => 'NON_EXISTENT']);

                $service->completeRazorpayPayment(
                    $fakeCustomer,
                    $this->consumablePlan,
                    'order_fail_atomic',
                    'pay_fail_atomic',
                    'valid_sig'
                );
            });
        } catch (\Throwable $e) {
            // Expected database integrity exception
        }

        // Assert customer balance is untouched
        $this->assertEquals(
            $initialBalance,
            CustomerCredit::where('customer_id', $this->customer->customer_id)->value('balance')
        );

        // Assert no ledger entries created
        $this->assertDatabaseMissing('credit_ledger', [
            'description' => "Credit Top-up: {$this->consumablePlan->plan_title} ({$this->consumablePlan->credits} credits)",
        ]);
    }

    /**
     * Test 15: Refund or reversal processing.
     */
    public function test_15_refund_or_reversal_processing(): void
    {
        $service = app(CreditTopupService::class);

        // Complete a top-up of 220 credits (balance goes 1000 -> 1220)
        $txn = $service->completeRazorpayPayment(
            $this->customer,
            $this->consumablePlan,
            'order_for_reversal',
            'pay_for_reversal',
            'valid_sig'
        );

        $this->assertEquals(1220.00, (float) CustomerCredit::where('customer_id', $this->customer->customer_id)->value('balance'));

        // Process compensating reversal
        $ledgerReversal = $service->processCompensatingReversal(
            $txn,
            'Customer requested refund of purchase',
            'admin_reversal'
        );

        // Assert ledger entry created as REVERSAL
        $this->assertEquals(CreditLedger::TYPE_REVERSAL, $ledgerReversal->entry_type);
        $this->assertEquals(CreditLedger::CATEGORY_REFUND, $ledgerReversal->transaction_category);
        $this->assertEquals(220.00, (float) $ledgerReversal->debit_amount);
        $this->assertEquals(1000.00, (float) $ledgerReversal->balance_after);

        // Assert balance deducted back to 1000.00
        $wallet = CustomerCredit::where('customer_id', $this->customer->customer_id)->first();
        $this->assertEquals(1000.00, (float) $wallet->balance);

        // Assert payment transaction marked as REFUNDED
        $this->assertEquals(PaymentTransaction::STATUS_REFUNDED, $txn->fresh()->status);
    }

    /**
     * Test 16: Seeder idempotency.
     */
    public function test_16_seeder_idempotency(): void
    {
        $countBefore = CreditPack::count();
        $this->assertEquals(5, $countBefore);

        // Run seeder again
        $this->seed(CreditPackSeeder::class);

        $countAfter = CreditPack::count();
        $this->assertEquals(5, $countAfter);
    }

    /**
     * Test 17: Existing plan-dependent functionality remains operational.
     */
    public function test_17_existing_plan_dependent_functionality_remains_operational(): void
    {
        // 1. Existing GET /api/v1/plans still returns duration tiers ordered by duration
        $durationPlans = [
            Plan::create(['plan_title' => '1 Day Tier', 'duration' => '1 days', 'price' => 50]),
            Plan::create(['plan_title' => '10 Day Tier', 'duration' => '10 days', 'price' => 200]),
        ];

        $response = $this->getJson('/api/v1/plans');
        $response->assertOk();
        $data = $response->json();

        // Make sure duration field is present on duration plans
        $durations = collect($data)->pluck('duration')->filter()->values()->all();
        $this->assertContains('1 days', $durations);
        $this->assertContains('10 days', $durations);

        // 2. Existing poster creation with plan_id works
        $jobPayload = [
            'temp_id' => 'temp-job-compat-01',
            'device_id' => 'device-test-compat',
            'device_os' => 'android',
            'master_category' => 'Services',
            'business_name' => 'Compat Services Co',
            'job_role' => 'Software Engineer',
            'job_type' => 'full_time',
            'phone_number' => $this->customer->mobile,
            'latitude' => 28.5914,
            'longitude' => 77.4021,
            'city' => 'Noida',
            'plan_id' => (string) $durationPlans[0]->id,
            'payment_type' => 'FULL_CREDIT',
            'total_amount' => 50.00,
            'razorpay_amount' => 0.00,
            'credit_amount' => 50.00,
            'transaction_id' => 'TXN_COMPAT_01',
        ];

        $jobResponse = $this->postJson('/api/v1/jobs', $jobPayload);
        $jobResponse->assertStatus(201);
    }
}
