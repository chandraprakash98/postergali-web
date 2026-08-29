<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Referral;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'referrer_name' => ['required', 'string', 'max:255'],
            'referrer_mobile' => ['required', 'string', 'max:20', 'regex:/^\+?[0-9\-\s]{7,15}$/'],
            'status' => ['nullable', 'string', 'in:active,inactive,pending'],
            'referrals' => ['required', 'array', 'max:5'],
            'referrals.*.referral_name' => ['required', 'string', 'max:255'],
            'referrals.*.referral_mobile' => ['required', 'string', 'max:20', 'regex:/^\+?[0-9\-\s]{7,15}$/'],
            'referrals.*.status' => ['nullable', 'string', 'in:active,inactive,pending'],
        ]);

        $mobileNumbers = collect($validated['referrals'])
            ->pluck('referral_mobile')
            ->map(fn ($value) => $this->normalizeMobile($value))
            ->all();

        if (count($mobileNumbers) !== count(array_unique($mobileNumbers))) {
            return response()->json([
                'success' => false,
                'message' => 'Referral mobile numbers must be unique within the request.',
            ], 422);
        }

        $referrerMobile = $this->normalizeMobile($validated['referrer_mobile']);

        foreach ($validated['referrals'] as $referralData) {
            Referral::create([
                'referrer_name' => $validated['referrer_name'],
                'referrer_mobile' => $referrerMobile,
                'referral_name' => $referralData['referral_name'],
                'referral_mobile' => $this->normalizeMobile($referralData['referral_mobile']),
                'status' => 'IN PROGRESS',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Referrals saved successfully.',
        ], 201);
    }

    public function check(Request $request)
    {
        $validated = $request->validate([
            'mobile' => ['required', 'string', 'max:20', 'regex:/^\+?[0-9\-\s]{7,15}$/'],
        ]);

        $normalizedMobile = $this->normalizeMobile($validated['mobile']);
        $referral = Referral::where('referral_mobile', $normalizedMobile)->first();

        if (!$referral) {
            return response()->json([
                'success' => true,
                'found' => false,
                'message' => 'No referral found for this mobile number.',
            ]);
        }

        $referrerCustomer = Customer::where('mobile', $this->normalizeMobile($referral->referrer_mobile))->first();
        $referrerCustomerId = $referrerCustomer?->customer_id;

        return response()->json([
            'success' => true,
            'found' => true,
            'referral_name' => $referral->referral_name,
            'referral_mobile' => $referral->referral_mobile,
            'referrer_name' => $referral->referrer_name,
            'referrer_mobile' => $referral->referrer_mobile,
            'status' => $referral->status,
            'customer_id' => $referrerCustomerId,
            'customerId' => $referrerCustomerId,
        ]);
    }

    protected function normalizeMobile(string $value): string
    {
        return preg_replace('/[^0-9]/', '', $value);
    }
}
