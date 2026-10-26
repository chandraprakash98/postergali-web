<?php

namespace App\Services\Payments;

use App\Models\CreditLedger;
use App\Models\CreditPack;
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
        CreditPack|Plan $pack,
        ?string $idempotencyKey = null
    ): array {
        $this->ensurePackIsActiveConsumable($pack);

        // Check if an existing pending or successful transaction exists for this idempotency key
        if ($idempotencyKey) {
            $existing = PaymentTransaction::where('customer_id', $customer->customer_id)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing) {
                Log::info('Razorpay order initiation reused existing transaction', [
                    'customer_id' => $customer->customer_id,
                    'credit_pack_id' => $pack->id,
                    'transaction_uuid' => $existing->transaction_uuid,
                    'order_id' => $existing->provider_order_id,
                    'status' => $existing->status,
                ]);

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

        $order = $this->razorpayService->createOrder($customer, $pack);

        $transaction = PaymentTransaction::create([
            'transaction_uuid' => (string) Str::uuid(),
            'customer_id' => $customer->customer_id,
            'credit_pack_id' => $pack->id,
            'plan_id' => $pack->id,
            'payment_provider' => PaymentTransaction::PROVIDER_RAZORPAY,
            'amount' => $pack->price,
            'currency' => $pack->currency ?? 'INR',
            'credits_to_add' => (int) $pack->credits,
            'provider_order_id' => $order['id'] ?? null,
            'provider_product_id' => $pack->product_id,
            'status' => PaymentTransaction::STATUS_PENDING,
            'verification_status' => PaymentTransaction::VERIFICATION_UNVERIFIED,
            'idempotency_key' => $idempotencyKey,
            'raw_payload' => $order,
        ]);

        Log::info('Razorpay order initiation recorded', [
            'customer_id' => $customer->customer_id,
            'credit_pack_id' => $pack->id,
            'transaction_uuid' => $transaction->transaction_uuid,
            'order_id' => $transaction->provider_order_id,
            'amount' => (float) $transaction->amount,
            'currency' => $transaction->currency,
            'credits_to_add' => $transaction->credits_to_add,
            'status' => $transaction->status,
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
        CreditPack|Plan $pack,
        string $orderId,
        string $paymentId,
        string $signature,
        ?string $idempotencyKey = null
    ): PaymentTransaction {
        $this->ensurePackIsActiveConsumable($pack);

        Log::info('Razorpay payment completion received', [
            'customer_id' => $customer->customer_id,
            'credit_pack_id' => $pack->id,
            'order_id' => $orderId,
            'payment_id' => $paymentId,
        ]);

        // 1. Verify Razorpay cryptographic signature
        if (!$this->razorpayService->verifyPaymentSignature($orderId, $paymentId, $signature)) {
            Log::warning('Razorpay payment signature verification failed', [
                'customer_id' => $customer->customer_id,
                'credit_pack_id' => $pack->id,
                'order_id' => $orderId,
                'payment_id' => $paymentId,
            ]);

            $this->recordFailedTransaction(
                $customer,
                $pack,
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
        $transaction = DB::transaction(function () use ($customer, $pack, $orderId, $paymentId, $idempotencyKey) {
            // Check if payment already processed by provider_payment_id
            $existing = PaymentTransaction::where('provider_payment_id', $paymentId)
                ->lockForUpdate()
                ->first();

            if ($existing && $existing->status === PaymentTransaction::STATUS_SUCCESS) {
                Log::info('Razorpay payment already processed', [
                    'customer_id' => $customer->customer_id,
                    'transaction_uuid' => $existing->transaction_uuid,
                    'order_id' => $existing->provider_order_id ?? $orderId,
                    'payment_id' => $existing->provider_payment_id ?? $paymentId,
                    'status' => $existing->status,
                ]);

                return $existing;
            }

            // Find pending transaction for this order if exists
            $transaction = PaymentTransaction::where('customer_id', $customer->customer_id)
                ->where('provider_order_id', $orderId)
                ->lockForUpdate()
                ->first();

            if ($transaction && $transaction->status === PaymentTransaction::STATUS_SUCCESS) {
                Log::info('Razorpay order already completed', [
                    'customer_id' => $customer->customer_id,
                    'transaction_uuid' => $transaction->transaction_uuid,
                    'order_id' => $orderId,
                    'payment_id' => $paymentId,
                    'status' => $transaction->status,
                ]);

                return $transaction;
            }

            if (!$transaction) {
                $transaction = new PaymentTransaction([
                    'transaction_uuid' => (string) Str::uuid(),
                    'customer_id' => $customer->customer_id,
                    'credit_pack_id' => $pack->id,
                    'plan_id' => $pack->id,
                    'payment_provider' => PaymentTransaction::PROVIDER_RAZORPAY,
                    'amount' => $pack->price,
                    'currency' => $pack->currency ?? 'INR',
                    'credits_to_add' => (int) $pack->credits,
                    'provider_order_id' => $orderId,
                    'idempotency_key' => $idempotencyKey,
                ]);
            }

            $packTitle = $pack->name ?? $pack->plan_title ?? 'Credit Pack';
            $transaction->credit_pack_id = $pack->id;
            $transaction->provider_payment_id = $paymentId;
            $transaction->provider_product_id = $pack->product_id;
            $transaction->status = PaymentTransaction::STATUS_SUCCESS;
            $transaction->verification_status = PaymentTransaction::VERIFICATION_VERIFIED;
            $transaction->paid_at = now();
            $transaction->save();

            // Grant credits atomically
            $this->allocateCreditsToCustomer(
                $customer,
                $transaction,
                $pack,
                "Credit Top-up: {$packTitle} ({$pack->credits} credits)",
                PaymentTransaction::PROVIDER_RAZORPAY
            );

            return $transaction;
        });

        Log::info('Razorpay payment completed and credits allocated', [
            'customer_id' => $customer->customer_id,
            'credit_pack_id' => $pack->id,
            'transaction_uuid' => $transaction->transaction_uuid,
            'order_id' => $orderId,
            'payment_id' => $paymentId,
            'amount' => (float) $transaction->amount,
            'currency' => $transaction->currency,
            'credits_granted' => $transaction->credits_to_add,
            'status' => $transaction->status,
        ]);

        return $transaction;
    }

    /**
     * Verify and process Apple In-App Purchase.
     */
    public function completeAppleIapPurchase(
        Customer $customer,
        CreditPack|Plan $pack,
        string $transactionReceiptOrJws,
        ?string $idempotencyKey = null
    ): PaymentTransaction {
        $this->ensurePackIsActiveConsumable($pack);

        // 1. Verify Apple IAP transaction data
        $verifiedData = $this->appleIapService->verifyTransaction($transactionReceiptOrJws, $pack);
        $providerTxnId = $verifiedData['transaction_id'];
        $originalTxnId = $verifiedData['original_transaction_id'] ?? null;

        // 2. Start database transaction with row locking
        return DB::transaction(function () use (
            $customer,
            $pack,
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
                'credit_pack_id' => $pack->id,
                'plan_id' => $pack->id,
                'payment_provider' => PaymentTransaction::PROVIDER_APPLE_IAP,
                'amount' => $pack->price,
                'currency' => $pack->currency ?? 'INR',
                'credits_to_add' => (int) $pack->credits,
                'provider_transaction_id' => $providerTxnId,
                'provider_original_transaction_id' => $originalTxnId,
                'provider_product_id' => $pack->product_id,
                'status' => PaymentTransaction::STATUS_SUCCESS,
                'verification_status' => PaymentTransaction::VERIFICATION_VERIFIED,
                'idempotency_key' => $idempotencyKey,
                'raw_payload' => $verifiedData,
                'paid_at' => now(),
            ]);
            $transaction->save();

            $packTitle = $pack->name ?? $pack->plan_title ?? 'Credit Pack';

            // Grant credits atomically
            $this->allocateCreditsToCustomer(
                $customer,
                $transaction,
                $pack,
                "Credit Top-up: {$packTitle} ({$pack->credits} credits)",
                PaymentTransaction::PROVIDER_APPLE_IAP
            );

            return $transaction;
        });
    }

    /**
     * Process asynchronous Razorpay Webhook.
     */
    public function processRazorpayWebhook(array $payload, string $rawBody, string $signature): ?PaymentTransaction
    {
        if (!$this->razorpayService->verifyWebhookSignature($rawBody, $signature)) {
            Log::warning('Razorpay webhook signature verification failed.');
            throw new RuntimeException('Invalid webhook signature.');
        }

        $event = $payload['event'] ?? '';
        $paymentEntity = $payload['payload']['payment']['entity'] ?? [];
        $orderId = $paymentEntity['order_id'] ?? null;
        $paymentId = $paymentEntity['id'] ?? null;
        $status = $paymentEntity['status'] ?? '';

        if ($event === 'payment.captured' || ($event === 'order.paid' && $status === 'captured')) {
            if (!$orderId || !$paymentId) {
                return null;
            }

            return DB::transaction(function () use ($orderId, $paymentId, $paymentEntity) {
                $transaction = PaymentTransaction::where('provider_order_id', $orderId)
                    ->orWhere('provider_payment_id', $paymentId)
                    ->lockForUpdate()
                    ->first();

                if (!$transaction) {
                    Log::info("No internal transaction found for Razorpay order: {$orderId}");
                    return null;
                }

                if ($transaction->status === PaymentTransaction::STATUS_SUCCESS) {
                    return $transaction;
                }

                $customer = Customer::where('customer_id', $transaction->customer_id)->first();
                $pack = CreditPack::find($transaction->credit_pack_id ?? $transaction->plan_id);

                if (!$customer || !$pack) {
                    Log::error('Razorpay webhook missing customer or credit pack context', [
                        'order_id' => $orderId,
                        'payment_id' => $paymentId,
                    ]);
                    return null;
                }

                $transaction->credit_pack_id = $pack->id;
                $transaction->provider_payment_id = $paymentId;
                $transaction->provider_product_id = $pack->product_id;
                $transaction->status = PaymentTransaction::STATUS_SUCCESS;
                $transaction->verification_status = PaymentTransaction::VERIFICATION_VERIFIED;
                $transaction->paid_at = now();
                $transaction->raw_payload = array_merge($transaction->raw_payload ?? [], ['webhook' => $paymentEntity]);
                $transaction->save();

                $packTitle = $pack->name ?? $pack->plan_title ?? 'Credit Pack';
                $this->allocateCreditsToCustomer(
                    $customer,
                    $transaction,
                    $pack,
                    "Credit Top-up: {$packTitle} ({$pack->credits} credits)",
                    'razorpay_webhook'
                );

                return $transaction;
            });
        }

        if ($event === 'payment.failed') {
            if ($orderId) {
                $transaction = PaymentTransaction::where('provider_order_id', $orderId)->first();
                if ($transaction && $transaction->status === PaymentTransaction::STATUS_PENDING) {
                    $transaction->update([
                        'provider_payment_id' => $paymentId,
                        'status' => PaymentTransaction::STATUS_FAILED,
                        'failure_code' => $paymentEntity['error_code'] ?? 'PAYMENT_FAILED',
                        'failure_reason' => $paymentEntity['error_description'] ?? 'Payment failed via webhook',
                    ]);
                    return $transaction;
                }
            }
        }

        return null;
    }

    public function handleRazorpayWebhook(array $payload, string $rawBody, string $signature): array
    {
        $txn = $this->processRazorpayWebhook($payload, $rawBody, $signature);
        return [
            'status' => $txn ? 'processed' : 'ignored',
            'transaction_id' => $txn?->transaction_uuid,
        ];
    }

    /**
     * Process Apple Server-to-Server V2 Webhook / Refund notification.
     */
    public function processAppleWebhook(array $payload, string $rawBody): ?PaymentTransaction
    {
        $notificationType = $payload['notificationType'] ?? '';
        $subtype = $payload['subtype'] ?? '';
        $signedTransactionInfo = $payload['data']['signedTransactionInfo'] ?? null;

        if (!$signedTransactionInfo) {
            return null;
        }

        $decoded = $this->appleIapService->decodeTransactionPayload($signedTransactionInfo);
        $transactionId = $decoded['transaction_id'] ?? null;

        if (!$transactionId) {
            return null;
        }

        if (in_array($notificationType, ['REVOKE', 'REFUND'], true)) {
            $transaction = PaymentTransaction::where('provider_transaction_id', $transactionId)->first();

            if ($transaction && $transaction->status === PaymentTransaction::STATUS_SUCCESS) {
                $reason = "Apple StoreKit {$notificationType} " . ($subtype ? "({$subtype})" : '');
                $this->processRefundAndReversal($transaction, $reason, 'apple_webhook');

                return $transaction;
            }
        }

        return null;
    }

    public function handleAppleWebhook(array $payload): array
    {
        $txn = $this->processAppleWebhook($payload, '');
        return [
            'status' => $txn ? 'refunded' : 'ignored',
            'transaction_uuid' => $txn?->transaction_uuid,
        ];
    }

    /**
     * Atomically increment customer wallet and append ledger entry.
     */
    protected function allocateCreditsToCustomer(
        Customer $customer,
        PaymentTransaction $transaction,
        CreditPack|Plan $pack,
        string $description,
        string $provider
    ): CreditLedger {
        // Lock customer credits row
        $creditWallet = CustomerCredit::where('customer_id', $customer->customer_id)
            ->lockForUpdate()
            ->first();

        if (!$creditWallet) {
            $creditWallet = CustomerCredit::create([
                'customer_id' => $customer->customer_id,
                'balance' => 0.00,
            ]);
            $creditWallet = CustomerCredit::where('customer_id', $customer->customer_id)
                ->lockForUpdate()
                ->first();
        }

        $balanceBefore = (float) $creditWallet->balance;
        $creditsToAdd = (float) $pack->credits;
        $balanceAfter = $balanceBefore + $creditsToAdd;

        // Append to immutable ledger
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
            'created_by' => $provider,
            'created_at' => now(),
        ]);

        // Update wallet balance
        $creditWallet->balance = (float) $balanceAfter;
        $creditWallet->save();

        return $ledgerEntry;
    }

    /**
     * Process refund and append compensating debit reversal.
     */
    public function processRefundAndReversal(
        PaymentTransaction $transaction,
        string $reason,
        ?string $createdBy = null
    ): CreditLedger {
        return DB::transaction(function () use ($transaction, $reason, $createdBy) {
            $customer = Customer::where('customer_id', $transaction->customer_id)->firstOrFail();

            $creditWallet = CustomerCredit::where('customer_id', $customer->customer_id)
                ->lockForUpdate()
                ->firstOrFail();

            $balanceBefore = (float) $creditWallet->balance;
            $debitAmount = (float) $transaction->credits_to_add;
            $balanceAfter = max(0.00, $balanceBefore - $debitAmount);

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

    public function processCompensatingReversal(
        PaymentTransaction $transaction,
        string $reason,
        ?string $createdBy = null
    ): CreditLedger {
        return $this->processRefundAndReversal($transaction, $reason, $createdBy);
    }

    protected function recordFailedTransaction(
        Customer $customer,
        CreditPack|Plan $pack,
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
            'credit_pack_id' => $pack->id,
            'plan_id' => $pack->id,
            'payment_provider' => $provider,
            'amount' => $pack->price,
            'currency' => $pack->currency ?? 'INR',
            'credits_to_add' => (int) $pack->credits,
            'provider_order_id' => $orderId,
            'provider_payment_id' => $paymentId,
            'status' => PaymentTransaction::STATUS_FAILED,
            'verification_status' => PaymentTransaction::VERIFICATION_FAILED,
            'failure_code' => $errorCode,
            'failure_reason' => $reason,
            'idempotency_key' => $idempotencyKey,
        ]);
    }

    public function ensurePackIsActiveConsumable(CreditPack|Plan $pack): void
    {
        if (isset($pack->is_active) && !$pack->is_active) {
            throw new InvalidArgumentException("Selected credit package is currently inactive.");
        }

        if (empty($pack->credits) || $pack->credits < 1) {
            throw new InvalidArgumentException("Selected credit package has no valid credit allocation.");
        }
    }

    public function ensurePlanIsActiveConsumable(CreditPack|Plan $pack): void
    {
        $this->ensurePackIsActiveConsumable($pack);
    }
}
