<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AdSubcategory;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * Return all active subcategories grouped by ad type.
     *
     * GET /api/v1/categories
     *
     * Response shape:
     * {
     *   "job":   ["Shop/Office/School Staff", "Delivery & Logistics", ...],
     *   "offer": ["Local Shop Promotion", "Local Service", ...]
     * }
     */
    public function index(): JsonResponse
    {
        $categories = AdSubcategory::where('is_active', true)
            ->orderBy('type')
            ->orderBy('sort_order')
            ->get(['type', 'name']);

        $grouped = $categories
            ->groupBy('type')
            ->map(fn($items) => $items->pluck('name')->values());

        return response()->json([
            'job'   => $grouped->get('job',   collect())->all(),
            'offer' => $grouped->get('offer', collect())->all(),
        ]);
    }
}
