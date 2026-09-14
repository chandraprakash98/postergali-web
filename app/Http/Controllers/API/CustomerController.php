<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerCredit;
use App\Models\Job;
use App\Models\Offer;
use App\Services\FilterService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(
        protected FilterService $filterService = new FilterService(),
    ) {}

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
        $this->filterService->rejectUnsupportedParams($request, FilterService::ALLOWED_POSTER_ADS_PARAMS);

        $validated = $request->validate([
            'mobile' => ['required_without_all:mobile_number,phone_number,customer_id', 'nullable', 'string', 'max:20', 'regex:/^\+?[0-9\-\s]{7,15}$/'],
            'mobile_number' => ['sometimes', 'string', 'max:20', 'regex:/^\+?[0-9\-\s]{7,15}$/'],
            'phone_number' => ['sometimes', 'string', 'max:20', 'regex:/^\+?[0-9\-\s]{7,15}$/'],
            'customer_id' => ['sometimes', 'string'],
            'device_id' => ['sometimes', 'string'],
            'latitude' => ['sometimes', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'numeric', 'between:-180,180'],
            'radius' => ['sometimes'],
            'distance' => ['sometimes'],
            'min_radius' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'max_radius' => ['sometimes', 'numeric', 'min:0.1', 'max:100'],
            'min_distance' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'max_distance' => ['sometimes', 'numeric', 'min:0.1', 'max:100'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:200'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'sub_categories' => ['sometimes'],
            'sub_category' => ['sometimes', 'string'],
            'category' => ['sometimes', 'string'],
            'categories' => ['sometimes'],
            'is_expiry' => ['sometimes', 'string'],
            'expiry' => ['sometimes', 'string'],
            'job_type' => ['sometimes', 'string'],
            'job_types' => ['sometimes'],
            'salary' => ['sometimes', 'string'],
            'min_salary' => ['sometimes', 'numeric', 'min:0'],
            'max_salary' => ['sometimes', 'numeric', 'min:0'],
            'offer_type' => ['sometimes', 'string'],
            'offer_types' => ['sometimes'],
            'type' => ['sometimes', 'string', 'in:job,jobs,offer,offers,all'],
            'poster_type' => ['sometimes', 'string', 'in:job,jobs,offer,offers,all'],
            'status' => ['sometimes', 'string'],
        ]);

        $rawMobile = $validated['mobile'] ?? $validated['mobile_number'] ?? $validated['phone_number'] ?? null;
        $customerId = $validated['customer_id'] ?? null;

        $normalizedMobile = $rawMobile ? $this->normalizeMobile($rawMobile) : null;

        $customer = null;
        if ($normalizedMobile) {
            $customer = Customer::where('mobile', $normalizedMobile)->first();
        }
        if (!$customer && $customerId) {
            $customer = Customer::where('customer_id', $customerId)->first();
            if ($customer && !$normalizedMobile) {
                $normalizedMobile = $customer->mobile;
            }
        }

        if (!$customer) {
            return response()->json([
                'success' => true,
                'message' => 'No customer found for this mobile number.',
                'jobs' => [],
                'offers' => [],
            ]);
        }

        $targetType = strtolower((string) ($request->input('type', $request->input('poster_type', 'all'))));
        $onlyOffers = in_array($targetType, ['offer', 'offers'], true);
        $onlyJobs = in_array($targetType, ['job', 'jobs'], true);

        // Normalize filters exactly as in JobController and OfferController
        $subCategories = $this->filterService->normalizeSubCategories($request);
        $expiryWindow = $this->filterService->normalizeExpiryWindow($request->input('is_expiry', $request->input('expiry')));
        $jobTypes = $this->filterService->normalizeJobTypes($request);
        $salaryRange = $this->filterService->normalizeSalaryRange(
            $request->input('salary'),
            $request->input('min_salary'),
            $request->input('max_salary')
        );
        $offerTypes = $this->filterService->normalizeOfferTypes($request);

        $hasLocation = $request->filled('latitude') && $request->filled('longitude');
        $distanceRange = null;
        if ($hasLocation) {
            $distanceRange = $this->filterService->normalizeDistanceRange(
                $request->input('distance'),
                $request->input('radius'),
                $request->input('min_distance'),
                $request->input('max_distance'),
                $request->input('min_radius'),
                $request->input('max_radius')
            );
        }

        $jobs = [];
        if (!$onlyOffers) {
            $jobsQuery = Job::where('phone_number', $normalizedMobile);

            if ($request->filled('device_id')) {
                $jobsQuery->where('device_id', $request->input('device_id'));
            }

            if (!empty($subCategories)) {
                $this->filterService->applySubCategoryFilter($jobsQuery, $subCategories);
            }

            if (!empty($jobTypes)) {
                $this->filterService->applyJobTypeFilter($jobsQuery, $jobTypes);
            }

            if ($salaryRange) {
                $this->filterService->applySalaryFilter($jobsQuery, $salaryRange);
            }

            if ($expiryWindow) {
                $this->filterService->applyExpiryFilter($jobsQuery, $expiryWindow);
            }

            if ($request->filled('status')) {
                $status = strtolower(trim((string) $request->input('status')));
                if ($status === 'active' || $status === 'live') {
                    $jobsQuery->active();
                } else {
                    $jobsQuery->where('status', $status);
                }
            }

            if ($hasLocation && $distanceRange) {
                $lat = (float) $request->input('latitude');
                $lng = (float) $request->input('longitude');
                $jobsQuery->nearby($lat, $lng, $distanceRange['max']);
            } else {
                $jobsQuery->orderByDesc('created_at');
            }

            if ($request->filled('per_page') || $request->filled('page')) {
                $perPage = min((int) ($request->input('per_page', 50)), 200);
                $page = max((int) ($request->input('page', 1)), 1);
                $jobs = $jobsQuery->forPage($page, $perPage)->get();
            } else {
                $jobs = $jobsQuery->get();
            }
        }

        $offers = [];
        if (!$onlyJobs) {
            $offersQuery = Offer::where('mobile_number', $normalizedMobile);

            if ($request->filled('device_id')) {
                $offersQuery->where('device_id', $request->input('device_id'));
            }

            if (!empty($subCategories)) {
                $this->filterService->applySubCategoryFilter($offersQuery, $subCategories);
            }

            if (!empty($offerTypes)) {
                $this->filterService->applyOfferTypeFilter($offersQuery, $offerTypes);
            }

            if ($expiryWindow) {
                $this->filterService->applyExpiryFilter($offersQuery, $expiryWindow);
            }

            if ($request->filled('status')) {
                $status = strtolower(trim((string) $request->input('status')));
                if ($status === 'active' || $status === 'live') {
                    $offersQuery->active();
                } else {
                    $offersQuery->where('status', $status);
                }
            }

            if ($hasLocation && $distanceRange) {
                $lat = (float) $request->input('latitude');
                $lng = (float) $request->input('longitude');
                $offersQuery->nearby($lat, $lng, $distanceRange['max'], $distanceRange['min']);
            } else {
                $offersQuery->orderByDesc('created_at');
            }

            if ($request->filled('per_page') || $request->filled('page')) {
                $perPage = min((int) ($request->input('per_page', 50)), 200);
                $page = max((int) ($request->input('page', 1)), 1);
                $offers = $offersQuery->forPage($page, $perPage)->get();
            } else {
                $offers = $offersQuery->get();
            }
        }

        $response = [
            'success' => true,
            'customer_id' => $customer->customer_id,
            'mobile' => $customer->mobile,
            'jobs' => $jobs,
            'offers' => $offers,
        ];

        if ($hasLocation && $distanceRange) {
            $response['radius_km'] = $distanceRange['max'];
            $response['min_distance_km'] = $distanceRange['min'];
            $response['max_distance_km'] = $distanceRange['max'];
        }

        return response()->json($response);
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
