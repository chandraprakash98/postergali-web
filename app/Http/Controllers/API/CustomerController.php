<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerCredit;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function check(Request $request)
    {
        $validated = $request->validate([
            'mobile' => ['required', 'string', 'max:20', 'regex:/^\+?[0-9\-\s]{7,15}$/'],
            'fcm' => ['nullable', 'string', 'max:255'],
        ]);

        $normalizedMobile = $this->normalizeMobile($validated['mobile']);
        $customer = Customer::where('mobile', $normalizedMobile)->first();

        if (!$customer) {
            $customer = Customer::create([
                'mobile' => $normalizedMobile,
                'fcm' => $validated['fcm'] ?? null,
            ]);

            CustomerCredit::create([
                'customer_id' => $customer->customer_id,
                'balance' => 1000,
            ]);

            return response()->json([
                'success' => true,
                'created' => true,
                'customer_id' => $customer->customer_id,
                'mobile' => $customer->mobile,
                'balance' => 1000,
            ], 201);
        }

        if (!empty($validated['fcm'])) {
            $customer->fcm = $validated['fcm'];
            $customer->save();
        }

        $credit = CustomerCredit::where('customer_id', $customer->customer_id)->first();

        return response()->json([
            'success' => true,
            'created' => false,
            'customer_id' => $customer->customer_id,
            'mobile' => $customer->mobile,
            'balance' => $credit?->balance ?? 0,
            'fcm' => $customer->fcm,
        ]);
    }

    public function posterAds(Request $request)
    {
        $validated = $request->validate([
            'mobile' => ['required', 'string', 'max:20', 'regex:/^\+?[0-9\-\s]{7,15}$/'],
        ]);

        $normalizedMobile = $this->normalizeMobile($validated['mobile']);
        $customer = Customer::where('mobile', $normalizedMobile)->first();

        if (!$customer) {
            return response()->json([
                'success' => true,
                'message' => 'No customer found for this mobile number.',
                'jobs' => [],
                'offers' => [],
            ]);
        }

        $jobs = \App\Models\Job::where('phone_number', $normalizedMobile)
            ->orderByDesc('created_at')
            ->get();

        $offers = \App\Models\Offer::where('mobile_number', $normalizedMobile)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'customer_id' => $customer->customer_id,
            'mobile' => $customer->mobile,
            'jobs' => $jobs,
            'offers' => $offers,
        ]);
    }

    public function balance(Request $request, string $customerId)
    {
        $customer = Customer::where('customer_id', $customerId)->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found.',
            ], 404);
        }

        $credit = CustomerCredit::where('customer_id', $customer->customer_id)->first();

        return response()->json([
            'success' => true,
            'customer_id' => $customer->customer_id,
            'balance' => $credit?->balance ?? 0,
        ]);
    }

    protected function normalizeMobile(string $value): string
    {
        return preg_replace('/[^0-9]/', '', $value);
    }
}
