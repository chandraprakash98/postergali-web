<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Models\CreditLedger;
use App\Models\PaymentTransaction;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminPackageController extends Controller
{
    /**
     * List all packages (consumable and duration tiers) with administrative filters.
     * 
     * @route GET /api/v1/admin/packages
     */
    public function index(Request $request): JsonResponse
    {
        $query = Plan::query();

        if ($request->filled('type')) {
            $query->where('plan_type', strtoupper($request->input('type')));
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $packages = $query->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $packages,
        ]);
    }

    /**
     * View single package details.
     * 
     * @route GET /api/v1/admin/packages/{id}
     */
    public function show(int $id): JsonResponse
    {
        $plan = Plan::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $plan,
        ]);
    }

    /**
     * Create a new package (validated with StorePlanRequest).
     * 
     * @route POST /api/v1/admin/packages
     */
    public function store(StorePlanRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $plan = Plan::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Package created successfully.',
            'data' => $plan,
        ], 201);
    }

    /**
     * Update an existing package (validated with UpdatePlanRequest).
     * 
     * @route PUT /api/v1/admin/packages/{id}
     */
    public function update(UpdatePlanRequest $request, int $id): JsonResponse
    {
        $plan = Plan::findOrFail($id);
        $validated = $request->validated();

        $plan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Package updated successfully.',
            'data' => $plan,
        ]);
    }

    /**
     * Activate or deactivate a package.
     * 
     * @route PATCH /api/v1/admin/packages/{id}/status
     */
    public function toggleStatus(Request $request, int $id): JsonResponse
    {
        $plan = Plan::findOrFail($id);

        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $plan->is_active = $validated['is_active'];
        $plan->save();

        return response()->json([
            'success' => true,
            'message' => 'Package status updated.',
            'data' => [
                'id' => $plan->id,
                'is_active' => $plan->is_active,
            ],
        ]);
    }

    /**
     * View all payment transactions for reconciliation and support.
     * 
     * @route GET /api/v1/admin/credits/transactions
     */
    public function transactions(Request $request): JsonResponse
    {
        $query = PaymentTransaction::with(['customer', 'plan']);

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', strtoupper($request->input('status')));
        }

        if ($request->filled('provider')) {
            $query->where('payment_provider', strtoupper($request->input('provider')));
        }

        $perPage = min((int) $request->input('per_page', 25), 100);

        $transactions = $query->orderByDesc('id')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $transactions->items(),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
                'last_page' => $transactions->lastPage(),
            ],
        ]);
    }

    /**
     * View all credit ledger entries for audit and reconciliation.
     * 
     * @route GET /api/v1/admin/credits/ledger
     */
    public function ledger(Request $request): JsonResponse
    {
        $query = CreditLedger::with(['customer', 'paymentTransaction']);

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        if ($request->filled('entry_type')) {
            $query->where('entry_type', strtoupper($request->input('entry_type')));
        }

        if ($request->filled('category')) {
            $query->where('transaction_category', strtoupper($request->input('category')));
        }

        $perPage = min((int) $request->input('per_page', 25), 100);

        $entries = $query->orderByDesc('id')->paginate($perPage);

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
}
