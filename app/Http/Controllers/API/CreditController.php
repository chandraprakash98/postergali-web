<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CreditLedger;
use App\Models\Customer;
use App\Models\CustomerCredit;
use App\Models\PaymentTransaction;
use App\Models\Plan;
use App\Services\Payments\CreditTopupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class CreditController extends Controller
{
    public function __construct(
        protected CreditTopupService $topupService = new CreditTopupService()
    ) {}

    /**
     * List active consumable credit packages for purchase.
     * 
     * @route GET /api/v1/credits/packages
     */
    public function packages(): JsonResponse
    {
        $packages = Plan::consumable()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get([
                'id',
                'plan_title',
                'plan_code',
                'product_id',
                'credits',
                'price',
                'currency',
                'plan_type',
                'is_active',
                'sort_order',
            ]);

        return response()->json([
            'success' => true,
            'data' => $packages,
        ]);
    }

    /**
     * Get single package details.
     * 
     * @route GET /api/v1/credits/packages/{id}
     */
    public function package(int|string $id): JsonResponse
    {
        $package = Plan::consumable()->where(function ($query) use ($id) {
            if (is_numeric($id)) {
                $query->where('id', (int) $id);
            } else {
                $query->where('plan_code', $id)->orWhere('product_id', $id);
            }
        })->first();

        if (!$package) {
            return response()->json([
                'success' => false,
                'message' => 'Credit package not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $package,
        ]);
    }

    /**
     * Top-up credits via Razorpay or Apple IAP.
     * 
     * @route POST /api/v1/credits/topup
     */
    public function topup(Request $request): JsonResponse
    {
        /** @var Customer $customer */
        $customer = $request->attributes->get('customer') ?? $request->user();

        if (!$customer instanceof Customer) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated customer.',
            ], 401);
        }

        $validated = $request->validate([
            'mobile_number' => ['sometimes', 'nullable', 'string'],
            'package_id' => ['required_without:plan_code', 'nullable'],
            'plan_code' => ['required_without:package_id', 'nullable', 'string'],
            'payment_provider' => ['required', 'string', 'in:RAZORPAY,APPLE_IAP,razorpay,apple_iap'],
            'action' => ['sometimes', 'string', 'in:initiate,create_order,complete,verify'],

            // Razorpay completion fields
            'razorpay_order_id' => ['sometimes', 'nullable', 'string'],
            'razorpay_payment_id' => ['sometimes', 'nullable', 'string'],
            'razorpay_signature' => ['sometimes', 'nullable', 'string'],

            // Apple IAP fields
            'transaction_receipt' => ['sometimes', 'nullable', 'string'],
            'signed_transaction' => ['sometimes', 'nullable', 'string'],
            'transaction_id' => ['sometimes', 'nullable', 'string'],
        ]);

        // Security check: verify that client is not attempting to topup someone else's account
        if (!empty($validated['mobile_number'])) {
            $normalizedPassed = preg_replace('/[^0-9]/', '', $validated['mobile_number']);
            $normalizedCustomer = preg_replace('/[^0-9]/', '', $customer->mobile);

            if ($normalizedPassed !== $normalizedCustomer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden: You are not authorized to purchase credits for another customer.',
                ], 403);
            }
        }

        // Resolve plan
        $plan = $this->resolveConsumablePlan(
            $validated['package_id'] ?? null,
            $validated['plan_code'] ?? null
        );

        if (!$plan) {
            return response()->json([
                'success' => false,
                'message' => 'The selected credit package was not found or is inactive.',
            ], 404);
        }

        $provider = strtoupper($validated['payment_provider']);
        $idempotencyKey = $request->header('Idempotency-Key');

        try {
            if ($provider === PaymentTransaction::PROVIDER_RAZORPAY) {
                $isCompletion = !empty($validated['razorpay_payment_id']) && !empty($validated['razorpay_signature']);
                $action = strtolower($validated['action'] ?? ($isCompletion ? 'complete' : 'initiate'));

                if ($action === 'initiate' || $action === 'create_order' || !$isCompletion) {
                    $orderData = $this->topupService->initiateRazorpayOrder($customer, $plan, $idempotencyKey);

                    return response()->json([
                        'success' => true,
                        'message' => 'Payment order created. Complete payment to receive credits.',
                        'credits_added' => false,
                        'order' => $orderData,
                    ], 200);
                }

                // Completion flow
                if (empty($validated['razorpay_order_id']) || empty($validated['razorpay_payment_id']) || empty($validated['razorpay_signature'])) {
                    throw ValidationException::withMessages([
                        'razorpay_signature' => ['Razorpay order ID, payment ID, and signature are required for payment verification.'],
                    ]);
                }

                $transaction = $this->topupService->completeRazorpayPayment(
                    $customer,
                    $plan,
                    $validated['razorpay_order_id'],
                    $validated['razorpay_payment_id'],
                    $validated['razorpay_signature'],
                    $idempotencyKey
                );

                $currentBalance = CustomerCredit::where('customer_id', $customer->customer_id)->value('balance') ?? 0;

                return response()->json([
                    'success' => true,
                    'message' => 'Payment verified successfully and credits granted.',
                    'credits_added' => true,
                    'credits_granted' => $transaction->credits_to_add,
                    'current_balance' => (float) $currentBalance,
                    'transaction' => [
                        'transaction_uuid' => $transaction->transaction_uuid,
                        'status' => $transaction->status,
                        'amount' => (float) $transaction->amount,
                        'currency' => $transaction->currency,
                        'paid_at' => $transaction->paid_at?->toIso8601String(),
                    ],
                ], 200);
            }

            if ($provider === PaymentTransaction::PROVIDER_APPLE_IAP) {
                $receiptData = $validated['signed_transaction']
                    ?? $validated['transaction_receipt']
                    ?? $validated['transaction_id']
                    ?? null;

                if (empty($receiptData)) {
                    throw ValidationException::withMessages([
                        'signed_transaction' => ['Apple IAP transaction data or receipt is required.'],
                    ]);
                }

                $transaction = $this->topupService->completeAppleIapPurchase(
                    $customer,
                    $plan,
                    $receiptData,
                    $idempotencyKey
                );

                $currentBalance = CustomerCredit::where('customer_id', $customer->customer_id)->value('balance') ?? 0;

                return response()->json([
                    'success' => true,
                    'message' => 'Apple IAP verified successfully and credits granted.',
                    'credits_added' => true,
                    'credits_granted' => $transaction->credits_to_add,
                    'current_balance' => (float) $currentBalance,
                    'transaction' => [
                        'transaction_uuid' => $transaction->transaction_uuid,
                        'status' => $transaction->status,
                        'provider_transaction_id' => $transaction->provider_transaction_id,
                        'amount' => (float) $transaction->amount,
                        'currency' => $transaction->currency,
                        'paid_at' => $transaction->paid_at?->toIso8601String(),
                    ],
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Unsupported payment provider.',
            ], 422);

        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Topup failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'credits_added' => false,
            ], 400);
        }
    }

    /**
     * Get authenticated customer's current credit balance.
     * 
     * @route GET /api/v1/credits/balance
     */
    public function balance(Request $request): JsonResponse
    {
        /** @var Customer $customer */
        $customer = $request->attributes->get('customer') ?? $request->user();

        $credit = CustomerCredit::where('customer_id', $customer->customer_id)->first();

        return response()->json([
            'success' => true,
            'data' => [
                'customer_id' => $customer->customer_id,
                'mobile' => $customer->mobile,
                'balance' => (float) ($credit?->balance ?? 0.00),
            ],
        ]);
    }

    /**
     * Paginated credit ledger history for authenticated customer.
     * 
     * @route GET /api/v1/credits/ledger
     */
    public function ledger(Request $request): JsonResponse
    {
        /** @var Customer $customer */
        $customer = $request->attributes->get('customer') ?? $request->user();

        $perPage = min((int) $request->input('per_page', 20), 100);

        $entries = CreditLedger::byCustomer($customer->customer_id)
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $entries->items(),
            'meta' => [
                'current_page' => $entries->currentPage(),
                'per_page' => $entries->perPage(),
                'total' => $entries->total(),
                'last_page' => $entries->lastPage(),
            ],
        ]);
    }

    /**
     * Paginated payment transaction history for authenticated customer.
     * 
     * @route GET /api/v1/credits/transactions
     */
    public function transactions(Request $request): JsonResponse
    {
        /** @var Customer $customer */
        $customer = $request->attributes->get('customer') ?? $request->user();

        $perPage = min((int) $request->input('per_page', 20), 100);

        $transactions = PaymentTransaction::byCustomer($customer->customer_id)
            ->with(['plan:id,plan_title,plan_code,credits'])
            ->orderByDesc('id')
            ->paginate($perPage);

        // Map cleanly without exposing provider secrets or internal json blobs
        $transformed = collect($transactions->items())->map(function (PaymentTransaction $txn) {
            return [
                'transaction_uuid' => $txn->transaction_uuid,
                'plan_title' => $txn->plan?->plan_title,
                'payment_provider' => $txn->payment_provider,
                'amount' => (float) $txn->amount,
                'currency' => $txn->currency,
                'credits_to_add' => $txn->credits_to_add,
                'status' => $txn->status,
                'paid_at' => $txn->paid_at?->toIso8601String(),
                'created_at' => $txn->created_at?->toIso8601String(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $transformed,
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
                'last_page' => $transactions->lastPage(),
            ],
        ]);
    }

    /**
     * View single payment transaction details for authenticated customer.
     * 
     * @route GET /api/v1/credits/transactions/{uuid}
     */
    public function transaction(Request $request, string $uuid): JsonResponse
    {
        /** @var Customer $customer */
        $customer = $request->attributes->get('customer') ?? $request->user();

        $txn = PaymentTransaction::byCustomer($customer->customer_id)
            ->where('transaction_uuid', $uuid)
            ->with(['plan', 'ledgerEntries'])
            ->first();

        if (!$txn) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'transaction_uuid' => $txn->transaction_uuid,
                'plan_title' => $txn->plan?->plan_title,
                'payment_provider' => $txn->payment_provider,
                'amount' => (float) $txn->amount,
                'currency' => $txn->currency,
                'credits_to_add' => $txn->credits_to_add,
                'status' => $txn->status,
                'verification_status' => $txn->verification_status,
                'failure_reason' => $txn->failure_reason,
                'paid_at' => $txn->paid_at?->toIso8601String(),
                'created_at' => $txn->created_at?->toIso8601String(),
                'ledger_entries' => $txn->ledgerEntries->map(fn (CreditLedger $l) => [
                    'ledger_uuid' => $l->ledger_uuid,
                    'entry_type' => $l->entry_type,
                    'transaction_category' => $l->transaction_category,
                    'credit_amount' => (float) $l->credit_amount,
                    'debit_amount' => (float) $l->debit_amount,
                    'balance_after' => (float) $l->balance_after,
                    'description' => $l->description,
                    'created_at' => $l->created_at?->toIso8601String(),
                ]),
            ],
        ]);
    }

    /**
     * Razorpay Webhook Endpoint.
     * 
     * @route POST /api/v1/credits/webhooks/razorpay
     */
    public function razorpayWebhook(Request $request): JsonResponse
    {
        $signature = $request->header('X-Razorpay-Signature', '');
        $rawBody = $request->getContent();
        $payload = $request->all();

        try {
            $transaction = $this->topupService->processRazorpayWebhook($payload, $rawBody, $signature);

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully.',
                'transaction_uuid' => $transaction?->transaction_uuid,
            ]);
        } catch (Throwable $e) {
            Log::warning('Razorpay webhook processing rejected', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Apple App Store Server Notifications Endpoint.
     * 
     * @route POST /api/v1/credits/webhooks/apple
     */
    public function appleWebhook(Request $request): JsonResponse
    {
        $payload = $request->all();
        $rawBody = $request->getContent();

        try {
            $this->topupService->processAppleWebhook($payload, $rawBody);

            return response()->json([
                'success' => true,
                'message' => 'Apple notification processed successfully.',
            ]);
        } catch (Throwable $e) {
            Log::warning('Apple notification processing error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    protected function resolveConsumablePlan(mixed $packageId, ?string $planCode): ?Plan
    {
        $query = Plan::consumable()->active();

        if (!empty($packageId)) {
            if (is_numeric($packageId)) {
                return $query->where('id', (int) $packageId)->first();
            }

            return $query->where(function ($q) use ($packageId) {
                $q->where('plan_code', $packageId)
                  ->orWhere('product_id', $packageId);
            })->first();
        }

        if (!empty($planCode)) {
            return $query->where('plan_code', $planCode)->first();
        }

        return null;
    }
}
