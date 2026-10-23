<?php

namespace App\Services\Payments;

use App\Models\CreditLedger;
use App\Models\Customer;
use App\Models\CustomerCredit;
use App\Models\PaymentTransaction;
use App\Models\Plan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class CreditTopupService
{
    public function __construct(
        protected RazorpayPaymentService $razorpayService = new RazorpayPaymentService(),
        protected AppleIapPaymentService $appleIapService = new AppleIapPaymentService(),
    ) {}

    /**
     * Create a Razorpay order and register a PENDING payment transaction.
     * Note: Order creation must NOT grant credits.
     */
    public function initiateRazorpayOrder(
        Customer $customer,
        Plan $plan,
        ?string $idempotencyKey = null
    ): array {
        $this->ensurePlanIsActiveConsumable($plan);

        // Check if an existing pending or successful transaction exists for this idempotency key
        if ($idempotencyKey) {
            $existing = PaymentTransaction::where('customer_id', $customer->customer_id)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing) {
                return [
                    'transaction_uuid' => $existing->transaction_uuid,
                    'order_id' => $existing->provider_order_id,
                    'amount' => (float) $existing->amount,
                    'currency' => $existing->currency,
                    'status' => $existing->status,
                    'credits_to_add' => $existing->credits_to_add,
                    'is_existing' => true,
                ];
            }
        }

        $order = $this->razorpayService->createOrder($customer, $plan);

        $transaction = PaymentTransaction::create([
            'transaction_uuid' => (string) Str::uuid(),
            'customer_id' => $customer->customer_id,
            'plan_id' => $plan->id,
            'payment_provider' => PaymentTransaction::PROVIDER_RAZORPAY,
            'amount' => $plan->price,
            'currency' => $plan->currency ?? 'INR',
            'credits_to_add' => (int) $plan->credits,
            'provider_order_id' => $order['id'] ?? null,
            'provider_product_id' => $plan->product_id,
            'status' => PaymentTransaction::STATUS_PENDING,
            'verification_status' => PaymentTransaction::VERIFICATION_UNVERIFIED,
            'idempotency_key' => $idempotencyKey,
            'raw_payload' => $order,
        ]);

        return [
            'transaction_uuid' => $transaction->transaction_uuid,
            'order_id' => $transaction->provider_order_id,
            'amount' => (float) $transaction->amount,
            'currency' => $transaction->currency,
            'status' => $transaction->status,
            'credits_to_add' => $transaction->credits_to_add,
            'is_existing' => false,
        ];
    }

    /**
     * Verify and process a Razorpay payment completion.
     */
    public function completeRazorpayPayment(
        Customer $customer,
        Plan $plan,
        string $orderId,
        string $paymentId,
        string $signature,
        ?string $idempotencyKey = null
    ): PaymentTransaction {
        $this->ensurePlanIsActiveConsumable($plan);

        // 1. Verify Razorpay cryptographic signature
        if (!$this->razorpayService->verifyPaymentSignature($orderId, $paymentId, $signature)) {
            $this->recordFailedTransaction(
                $customer,
                $plan,
                PaymentTransaction::PROVIDER_RAZORPAY,
                $orderId,
                $paymentId,
                'INVALID_SIGNATURE',
                'Razorpay payment signature verification failed.',
                $idempotencyKey
            );

            throw new RuntimeException('Invalid payment signature.');
        }

        // 2. Process database transaction with strict row locking & concurrency protection
        return DB::transaction(function () use ($customer, $plan, $orderId, $paymentId, $idempotencyKey) {
            // Check if payment already processed by provider_payment_id
            $existing = PaymentTransaction::where('provider_payment_id', $paymentId)
                ->lockForUpdate()
                ->first();

            if ($existing && $existing->status === PaymentTransaction::STATUS_SUCCESS) {
                return $existing;
            }

            // Find pending transaction for this order if exists
            $transaction = PaymentTransaction::where('customer_id', $customer->customer_id)
                ->where('provider_order_id', $orderId)
                ->lockForUpdate()
                ->first();

            if ($transaction && $transaction->status === PaymentTransaction::STATUS_SUCCESS) {
                return $transaction;
            }

            if (!$transaction) {
                $transaction = new PaymentTransaction([
                    'transaction_uuid' => (string) Str::uuid(),
                    'customer_id' => $customer->customer_id,
                    'plan_id' => $plan->id,
                    'payment_provider' => PaymentTransaction::PROVIDER_RAZORPAY,
                    'amount' => $plan->price,
                    'currency' => $plan->currency ?? 'INR',
                    'credits_to_add' => (int) $plan->credits,
                    'provider_order_id' => $orderId,
                    'idempotency_key' => $idempotencyKey,
                ]);
            }

            $transaction->provider_payment_id = $paymentId;
            $transaction->provider_product_id = $plan->product_id;
            $transaction->status = PaymentTransaction::STATUS_SUCCESS;
            $transaction->verification_status = PaymentTransaction::VERIFICATION_VERIFIED;
            $transaction->paid_at = now();
            $transaction->save();

            // Grant credits atomically
            $this->allocateCreditsToCustomer(
                $customer,
                $transaction,
                $plan,
                "Credit Top-up: {$plan->plan_title} ({$plan->credits} credits)",
                PaymentTransaction::PROVIDER_RAZORPAY
            );

            return $transaction;
        });
    }

    /**
     * Verify and process Apple In-App Purchase.
     */
    public function completeAppleIapPurchase(
        Customer $customer,
        Plan $plan,
        string $transactionReceiptOrJws,
        ?string $idempotencyKey = null
    ): PaymentTransaction {
        $this->ensurePlanIsActiveConsumable($plan);

        // 1. Verify Apple IAP transaction data
        $verifiedData = $this->appleIapService->verifyTransaction($transactionReceiptOrJws, $plan);
        $providerTxnId = $verifiedData['transaction_id'];
        $originalTxnId = $verifiedData['original_transaction_id'] ?? null;

        // 2. Start database transaction with row locking
        return DB::transaction(function () use (
            $customer,
            $plan,
            $providerTxnId,
            $originalTxnId,
            $verifiedData,
            $idempotencyKey
        ) {
            // Check if provider_transaction_id already processed
            $existing = PaymentTransaction::where('provider_transaction_id', $providerTxnId)
                ->lockForUpdate()
                ->first();

            if ($existing && $existing->status === PaymentTransaction::STATUS_SUCCESS) {
                return $existing;
            }

            $transaction = $existing ?? new PaymentTransaction();
            $transaction->fill([
                'transaction_uuid' => $transaction->transaction_uuid ?: (string) Str::uuid(),
                'customer_id' => $customer->customer_id,
                'plan_id' => $plan->id,
                'payment_provider' => PaymentTransaction::PROVIDER_APPLE_IAP,
                'amount' => $plan->price,
                'currency' => $plan->currency ?? 'INR',
                'credits_to_add' => (int) $plan->credits,
                'provider_transaction_id' => $providerTxnId,
                'provider_original_transaction_id' => $originalTxnId,
                'provider_product_id' => $plan->product_id,
                'status' => PaymentTransaction::STATUS_SUCCESS,
                'verification_status' => PaymentTransaction::VERIFICATION_VERIFIED,
                'idempotency_key' => $idempotencyKey,
                'raw_payload' => $verifiedData,
                'paid_at' => now(),
            ]);
            $transaction->save();

            // Grant credits atomically
            $this->allocateCreditsToCustomer(
                $customer,
                $transaction,
                $plan,
                "Credit Top-up: {$plan->plan_title} ({$plan->credits} credits)",
                PaymentTransaction::PROVIDER_APPLE_IAP
            );

            return $transaction;
        });
    }

    /**
     * Process Razorpay verified Webhook.
     */
    public function processRazorpayWebhook(array $payload, string $rawBody, string $signature): ?PaymentTransaction
    {
        if (!$this->razorpayService->verifyWebhookSignature($rawBody, $signature)) {
            Log::warning('Razorpay webhook signature mismatch.');
            throw new RuntimeException('Invalid webhook signature.');
        }

        $event = $payload['event'] ?? null;

        if ($event === 'payment.captured' || $event === 'order.paid') {
            $paymentEntity = $payload['payload']['payment']['entity'] ?? [];
            $orderId = $paymentEntity['order_id'] ?? null;
            $paymentId = $paymentEntity['id'] ?? null;

            if (!$orderId || !$paymentId) {
                return null;
            }

            return DB::transaction(function () use ($orderId, $paymentId, $paymentEntity) {
                $transaction = PaymentTransaction::where('provider_order_id', $orderId)
                    ->lockForUpdate()
                    ->first();

                if (!$transaction) {
                    Log::info("No internal transaction found for Razorpay order: {$orderId}");
                    return null;
                }

                if ($transaction->status === PaymentTransaction::STATUS_SUCCESS) {
                    return $transaction; // Already processed
                }

                $customer = Customer::where('customer_id', $transaction->customer_id)->first();
                $plan = Plan::find($transaction->plan_id);

                if (!$customer || !$plan) {
                    return null;
                }

                $transaction->provider_payment_id = $paymentId;
                $transaction->status = PaymentTransaction::STATUS_SUCCESS;
                $transaction->verification_status = PaymentTransaction::VERIFICATION_VERIFIED;
                $transaction->paid_at = now();
                $transaction->raw_payload = array_merge($transaction->raw_payload ?? [], ['webhook' => $paymentEntity]);
                $transaction->save();

                $this->allocateCreditsToCustomer(
                    $customer,
                    $transaction,
                    $plan,
                    "Credit Top-up: {$plan->plan_title} ({$plan->credits} credits) [Webhook]",
                    PaymentTransaction::PROVIDER_RAZORPAY
                );

                return $transaction;
            });
        }

        if ($event === 'payment.failed') {
            $paymentEntity = $payload['payload']['payment']['entity'] ?? [];
            $orderId = $paymentEntity['order_id'] ?? null;
            $paymentId = $paymentEntity['id'] ?? null;

            if ($orderId) {
                $transaction = PaymentTransaction::where('provider_order_id', $orderId)->first();
                if ($transaction && $transaction->status === PaymentTransaction::STATUS_PENDING) {
                    $transaction->update([
                        'provider_payment_id' => $paymentId,
                        'status' => PaymentTransaction::STATUS_FAILED,
                        'failure_code' => $paymentEntity['error_code'] ?? 'PAYMENT_FAILED',
                        'failure_reason' => $paymentEntity['error_description'] ?? 'Payment failed via webhook',
                    ]);
                }
            }
        }

        return null;
    }

    /**
     * Process Apple App Store Server Notification V2 (Webhook).
     */
    public function processAppleWebhook(array $payload, string $rawBody): ?PaymentTransaction
    {
        $decoded = $this->appleIapService->decodeNotificationPayload($payload);
        $notificationType = $decoded['notificationType'] ?? null;
        $subtype = $decoded['subtype'] ?? null;

        Log::info("Apple Webhook received: type={$notificationType}, subtype={$subtype}");

        // Handle REFUND / REVOCATION
        if ($notificationType === 'REFUND' || $notificationType === 'REVOKE') {
            $transactionData = $decoded['data']['signedTransactionInfo'] ?? null;
            if ($transactionData) {
                $info = $this->appleIapService->decodeTransactionPayload($transactionData);
                $txnId = $info['transaction_id'] ?? null;

                if ($txnId) {
                    $transaction = PaymentTransaction::where('provider_transaction_id', $txnId)->first();
                    if ($transaction && $transaction->status === PaymentTransaction::STATUS_SUCCESS) {
                        $this->processCompensatingReversal(
                            $transaction,
                            "Apple IAP refund: {$notificationType} ({$subtype})",
                            'apple_webhook'
                        );
                        return $transaction;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Atomically allocate credits, append ledger record, and update balance.
     */
    public function allocateCreditsToCustomer(
        Customer $customer,
        PaymentTransaction $transaction,
        Plan $plan,
        string $description,
        string $createdBy
    ): CreditLedger {
        // Ensure exactly one ledger entry exists for this payment transaction
        $existingEntry = CreditLedger::where('payment_transaction_id', $transaction->id)
            ->where('transaction_category', CreditLedger::CATEGORY_WALLET_TOPUP)
            ->first();

        if ($existingEntry) {
            return $existingEntry;
        }

        // Row-level lock on CustomerCredit
        $creditWallet = CustomerCredit::where('customer_id', $customer->customer_id)
            ->lockForUpdate()
            ->first();

        if (!$creditWallet) {
            $creditWallet = CustomerCredit::create([
                'customer_id' => $customer->customer_id,
                'balance' => 0.00,
            ]);
        }

        $balanceBefore = number_format((float) $creditWallet->balance, 2, '.', '');
        $creditsToAdd = number_format((float) $transaction->credits_to_add, 2, '.', '');
        $balanceAfter = bcadd($balanceBefore, $creditsToAdd, 2);

        // Append-only ledger record
        $ledgerEntry = CreditLedger::create([
            'ledger_uuid' => (string) Str::uuid(),
            'customer_id' => $customer->customer_id,
            'payment_transaction_id' => $transaction->id,
            'entry_type' => CreditLedger::TYPE_CREDIT,
            'transaction_category' => CreditLedger::CATEGORY_WALLET_TOPUP,
            'credit_amount' => $creditsToAdd,
            'debit_amount' => 0.00,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'reference_type' => 'payment_transaction',
            'reference_id' => $transaction->transaction_uuid,
            'description' => $description,
            'idempotency_key' => $transaction->idempotency_key ? 'ledger_' . $transaction->idempotency_key : 'ledger_txn_' . $transaction->transaction_uuid,
            'created_by' => $createdBy,
            'created_at' => now(),
        ]);

        // Update balance
        $creditWallet->balance = (float) $balanceAfter;
        $creditWallet->save();

        return $ledgerEntry;
    }

    /**
     * Process compensating reversal (append-only ledger entry) when a transaction is refunded.
     */
    public function processCompensatingReversal(
        PaymentTransaction $transaction,
        string $reason,
        ?string $createdBy = null
    ): CreditLedger {
        return DB::transaction(function () use ($transaction, $reason, $createdBy) {
            $customer = Customer::where('customer_id', $transaction->customer_id)->firstOrFail();

            $creditWallet = CustomerCredit::where('customer_id', $customer->customer_id)
                ->lockForUpdate()
                ->firstOrFail();

            $balanceBefore = number_format((float) $creditWallet->balance, 2, '.', '');
            $debitAmount = number_format((float) $transaction->credits_to_add, 2, '.', '');

            // Deduct credits or floor at 0
            $balanceAfter = bcsub($balanceBefore, $debitAmount, 2);
            if (bccomp($balanceAfter, '0.00', 2) < 0) {
                $balanceAfter = '0.00';
            }

            $ledgerEntry = CreditLedger::create([
                'ledger_uuid' => (string) Str::uuid(),
                'customer_id' => $customer->customer_id,
                'payment_transaction_id' => $transaction->id,
                'entry_type' => CreditLedger::TYPE_REVERSAL,
                'transaction_category' => CreditLedger::CATEGORY_REFUND,
                'credit_amount' => 0.00,
                'debit_amount' => $debitAmount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'reference_type' => 'payment_transaction',
                'reference_id' => $transaction->transaction_uuid,
                'description' => "Compensating Reversal: {$reason}",
                'created_by' => $createdBy ?? 'system',
                'created_at' => now(),
            ]);

            $creditWallet->balance = (float) $balanceAfter;
            $creditWallet->save();

            $transaction->status = PaymentTransaction::STATUS_REFUNDED;
            $transaction->save();

            return $ledgerEntry;
        });
    }

    protected function recordFailedTransaction(
        Customer $customer,
        Plan $plan,
        string $provider,
        ?string $orderId,
        ?string $paymentId,
        string $errorCode,
        string $reason,
        ?string $idempotencyKey
    ): PaymentTransaction {
        return PaymentTransaction::create([
            'transaction_uuid' => (string) Str::uuid(),
            'customer_id' => $customer->customer_id,
            'plan_id' => $plan->id,
            'payment_provider' => $provider,
            'amount' => $plan->price,
            'currency' => $plan->currency ?? 'INR',
            'credits_to_add' => (int) $plan->credits,
            'provider_order_id' => $orderId,
            'provider_payment_id' => $paymentId,
            'status' => PaymentTransaction::STATUS_FAILED,
            'verification_status' => PaymentTransaction::VERIFICATION_FAILED,
            'failure_code' => $errorCode,
            'failure_reason' => $reason,
            'idempotency_key' => $idempotencyKey,
        ]);
    }

    protected function ensurePlanIsActiveConsumable(Plan $plan): void
    {
        if ($plan->plan_type !== Plan::TYPE_CONSUMABLE) {
            throw new InvalidArgumentException("Selected plan is not a consumable credit package.");
        }

        if (!$plan->is_active) {
            throw new InvalidArgumentException("Selected credit package is currently inactive.");
        }

        if (empty($plan->credits) || $plan->credits < 1) {
            throw new InvalidArgumentException("Selected credit package has no valid credit allocation.");
        }
    }
}
