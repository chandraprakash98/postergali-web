<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        $query = Plan::query();

        if ($request->filled('type')) {
            $type = strtoupper($request->input('type'));
            $query->where('plan_type', $type);

            if ($type === Plan::TYPE_CONSUMABLE) {
                return $query->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('price')
                    ->get();
            }
        }

        // Backward compatibility: default returns duration tiers ordered by duration
        return $query
            ->whereNotNull('duration')
            ->orderByRaw('CAST(duration AS SIGNED) ASC')
            ->orderBy('id')
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'plan_title' => 'required',
            'duration' => 'nullable',
            'price' => 'required|numeric',
            'plan_code' => 'nullable|string|unique:plans,plan_code',
            'product_id' => 'nullable|string|unique:plans,product_id',
            'credits' => 'nullable|integer',
            'currency' => 'nullable|string|size:3',
            'plan_type' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        return Plan::create($data);
    }

    public function show($id)
    {
        return Plan::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $plan = Plan::findOrFail($id);
        $plan->update($request->all());

        return $plan;
    }

    public function destroy($id)
    {
        Plan::destroy($id);

        return response()->json(['message' => 'Plan deleted']);
    }
}