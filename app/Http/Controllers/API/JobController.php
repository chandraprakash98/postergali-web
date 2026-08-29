<?php

namespace App\Http\Controllers\API;

use App\DTOs\PaymentData;
use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Services\FilterService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class JobController extends Controller
{
    public function __construct(
        protected FilterService $filterService = new FilterService(),
        protected PaymentService $paymentService = new PaymentService(),
    ) {}

    /**
     * Get all jobs within a specified radius of user location
     * 
     * @route GET /api/v1/jobs
     * @param latitude (required): User's latitude
     * @param longitude (required): User's longitude
     * @param radius (optional): Search radius in km (default: 5)
     * @param per_page (optional): Results per page (default: 50, max: 200)
     * @param page (optional): Page number (default: 1)
     */
    public function index(Request $request)
    {
        $this->filterService->rejectUnsupportedParams($request, FilterService::ALLOWED_JOB_INDEX_PARAMS);

        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'sometimes|numeric|min:0.1|max:100',
            'distance' => 'sometimes|numeric|min:0.1|max:100',
            'per_page' => 'sometimes|integer|min:1|max:200',
            'page' => 'sometimes|integer|min:1',
            'sub_categories' => 'sometimes',
            'sub_category' => 'sometimes|string',
            'category' => 'sometimes|string',
            'categories' => 'sometimes',
            'is_expiry' => 'sometimes|string',
            'expiry' => 'sometimes|string',
            'job_type' => 'sometimes|string',
            'job_types' => 'sometimes',
            'salary' => 'sometimes|string',
            'min_salary' => 'sometimes|numeric|min:0',
            'max_salary' => 'sometimes|numeric|min:0',
        ]);

        $userLat = (float) $validated['latitude'];
        $userLng = (float) $validated['longitude'];
        $radius = (float) ($validated['radius'] ?? $validated['distance'] ?? 5);
        $perPage = min((int) ($validated['per_page'] ?? 50), 200);

        $subCategories = $this->filterService->normalizeSubCategories($request);
        $expiryWindow = $this->filterService->normalizeExpiryWindow($request->input('is_expiry', $request->input('expiry')));
        $jobTypes = $this->filterService->normalizeJobTypes($request);
        $salaryRange = $this->filterService->normalizeSalaryRange(
            $request->input('salary'),
            $request->input('min_salary'),
            $request->input('max_salary')
        );

        $query = Job::nearby($userLat, $userLng, $radius)->active();

        $this->filterService->applySubCategoryFilter($query, $subCategories);

        if (!empty($jobTypes)) {
            $this->filterService->applyJobTypeFilter($query, $jobTypes);
        }

        if ($salaryRange) {
            $this->filterService->applySalaryFilter($query, $salaryRange);
        }

        if ($expiryWindow) {
            $this->filterService->applyExpiryFilter($query, $expiryWindow);
        }

        $jobs = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $jobs->items(),
            'pagination' => [
                'total' => $jobs->total(),
                'per_page' => $jobs->perPage(),
                'current_page' => $jobs->currentPage(),
                'last_page' => $jobs->lastPage(),
                'from' => $jobs->firstItem(),
                'to' => $jobs->lastItem(),
            ],
            'radius_km' => $radius,
        ]);
    }

    /**
     * Search jobs by device_id or phone_number
     * Also supports location-based search if latitude/longitude provided
     */
    public function search(Request $request)
    {
        $this->filterService->rejectUnsupportedParams($request, FilterService::ALLOWED_JOB_SEARCH_PARAMS);

        $validated = $request->validate([
            'device_id' => 'sometimes|string',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'radius' => 'sometimes|numeric|min:0.1|max:100',
            'distance' => 'sometimes|numeric|min:0.1|max:100',
            'phone_number' => 'sometimes|string',
            'mobile_number' => 'sometimes|string',
            'per_page' => 'sometimes|integer|min:1|max:200',
            'page' => 'sometimes|integer|min:1',
            'sub_categories' => 'sometimes',
            'sub_category' => 'sometimes|string',
            'category' => 'sometimes|string',
            'categories' => 'sometimes',
            'is_expiry' => 'sometimes|string',
            'expiry' => 'sometimes|string',
            'job_type' => 'sometimes|string',
            'job_types' => 'sometimes',
            'salary' => 'sometimes|string',
            'min_salary' => 'sometimes|numeric|min:0',
            'max_salary' => 'sometimes|numeric|min:0',
        ]);

        $deviceId = $validated['device_id'] ?? null;
        $phone = $validated['phone_number'] ?? $validated['mobile_number'] ?? null;
        $latitude = isset($validated['latitude']) ? (float) $validated['latitude'] : null;
        $longitude = isset($validated['longitude']) ? (float) $validated['longitude'] : null;
        $radius = (float) ($validated['radius'] ?? $validated['distance'] ?? 5);
        $perPage = min((int) ($validated['per_page'] ?? 50), 200);

        $subCategories = $this->filterService->normalizeSubCategories($request);
        $expiryWindow = $this->filterService->normalizeExpiryWindow($request->input('is_expiry', $request->input('expiry')));
        $jobTypes = $this->filterService->normalizeJobTypes($request);
        $salaryRange = $this->filterService->normalizeSalaryRange(
            $request->input('salary'),
            $request->input('min_salary'),
            $request->input('max_salary')
        );

        $query = Job::withCommonFields()->active();

        if ($latitude !== null && $longitude !== null) {
            $query = $query->nearby($latitude, $longitude, $radius);
        }

        $this->filterService->applySubCategoryFilter($query, $subCategories);

        if (!empty($jobTypes)) {
            $this->filterService->applyJobTypeFilter($query, $jobTypes);
        }

        if ($salaryRange) {
            $this->filterService->applySalaryFilter($query, $salaryRange);
        }

        if ($expiryWindow) {
            $this->filterService->applyExpiryFilter($query, $expiryWindow);
        }

        if ($deviceId || $phone) {
            $query->where(function ($q) use ($deviceId, $phone) {
                if ($deviceId) {
                    $q->where('device_id', $deviceId);
                }
                if ($phone) {
                    $q->orWhere('phone_number', $phone);
                }
            });
        }

        $jobs = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $jobs->items(),
            'pagination' => [
                'total' => $jobs->total(),
                'per_page' => $jobs->perPage(),
                'current_page' => $jobs->currentPage(),
                'last_page' => $jobs->lastPage(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'temp_id' => 'required|string',
            'device_id' => 'required',
            'device_os' => 'required',
            'master_category' => 'required',
            'business_name' => 'required',
            'job_role' => 'required',
            'job_type' => 'required',
            'salary' => 'nullable|numeric|min:0',
            'amount' => 'nullable|numeric|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'razorpay_amount' => 'nullable|numeric|min:0',
            'credit_amount' => 'nullable|numeric|min:0',
            'phone_number' => 'required',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'city' => 'required',
            'plan_id' => 'required',
            'transaction_id' => 'nullable|string',
            'razorpay_order_id' => 'nullable|string',
            'razorpay_payment_id' => 'nullable|string',
            'payment_status' => 'nullable|string',
            'payment_type' => ['nullable', 'string', 'in:full_upi,semi,full_credit,FULL_UPI,SEMI,FULL_CREDIT'],
            'credit_mode' => ['nullable', 'string', 'in:full_upi,semi,full_credit,FULL_UPI,SEMI,FULL_CREDIT'],
            'customer_id' => ['nullable', 'string', 'exists:customers,customer_id'],
        ]);

        return DB::transaction(function () use ($data) {
            if (!empty($data['amount']) && empty($data['salary'])) {
                $data['salary'] = $data['amount'];
            }

            $jobAttributes = array_intersect_key($data, array_flip((new Job())->getFillable()));
            $jobAttributes['status'] = 'pending';

            $job = Job::create($jobAttributes);

            $hasPaymentInfo = !empty($data['transaction_id'])
                || !empty($data['payment_type'])
                || !empty($data['credit_mode'])
                || !empty($data['razorpay_payment_id'])
                || !empty($data['amount'])
                || !empty($data['total_amount']);

            if ($hasPaymentInfo) {
                $paymentData = PaymentData::fromArray($data, itemType: 'job', jobOrOfferId: $job->id);
                $this->paymentService->processPayment($paymentData);
            }

            return response()->json($job, 201);
        });
    }

    public function show($id)
    {
        $job = Job::select([
            'id',
            'temp_id',
            'business_name',
            'job_role',
            'job_type',
            'salary',
            'phone_number',
            'city',
            'latitude',
            'longitude',
            'status',
            'view_count',
            'created_at',
            'expires_at',
        ])->findOrFail($id);

        $job->increment('view_count');

        return $job;
    }

    public function update(Request $request, $id)
    {
        $job = Job::findOrFail($id);

        $job->update($request->only([
            'temp_id',
            'business_name',
            'job_role',
            'job_type',
            'salary',
            'phone_number',
            'city',
            'status'
        ]));

        return $job;
    }

    public function destroy($id)
    {
        $job = Job::findOrFail($id);
        $job->delete();

        return response()->json([
            'message' => 'Job deleted successfully'
        ]);
    }
}