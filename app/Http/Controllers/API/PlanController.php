<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * List poster duration plans for PosterMan chatbot and ad publishing.
     */
    public function index()
    {
        return Plan::query()
            ->orderByRaw('CAST(duration AS SIGNED) ASC')
            ->orderBy('id')
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'plan_title' => 'required|string|max:100',
            'duration' => 'required|string|max:30',
            'price' => 'required|numeric',
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