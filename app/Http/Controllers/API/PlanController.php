<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;

class PlanController extends Controller
{
    public function index()
    {
        return Plan::latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'plan_title' => 'required',
            'duration' => 'required',
            'price' => 'required|numeric'
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