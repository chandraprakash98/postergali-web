<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\CustomerCredit;
use App\Models\Job;
use App\Models\Payment;
use App\Services\LocationService;

class JobController extends Controller
{
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
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'sometimes|numeric|min:0.1|max:50',
            'per_page' => 'sometimes|integer|min:1|max:200',
            'page' => 'sometimes|integer|min:1',
            'sub_categories' => 'sometimes',
            'sub_category' => 'sometimes|string',
            'is_expiry' => 'sometimes|string',
            'job_type' => 'sometimes|string',
            'job_types' => 'sometimes',
            'salary' => 'sometimes|numeric|min:0',
        ]);

        $userLat = $validated['latitude'];
        $userLng = $validated['longitude'];
        $radius = $validated['radius'] ?? 5; // Default 5 km
        $perPage = min($validated['per_page'] ?? 50, 200); // Max 200 per page
        $subCategories = $this->normalizeSubCategories($request);
        $expiryWindow = $this->normalizeExpiryWindow($request->input('is_expiry'));
        $jobTypes = $this->normalizeJobTypes($request);
        $salaryRange = $this->normalizeSalaryRange($request);

        try {
            $query = Job::nearby($userLat, $userLng, $radius)->active();

            $this->applySubCategoryFilter($query, $subCategories);

            if (!empty($jobTypes)) {
                $this->applyJobTypeFilter($query, $jobTypes);
            }

            if ($salaryRange) {
                $this->applySalaryFilter($query, $salaryRange);
            }

            if ($expiryWindow) {
                $query->whereNotNull('expires_at')
                    ->whereBetween('expires_at', [$expiryWindow['from'], $expiryWindow['to']]);
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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching nearby jobs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search jobs by device_id or phone_number
     * Also supports location-based search if latitude/longitude provided
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'sometimes|string',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'radius' => 'sometimes|numeric|min:0.1|max:50',
            'phone_number' => 'sometimes|string',
            'mobile_number' => 'sometimes|string',
            'per_page' => 'sometimes|integer|min:1|max:200',
            'page' => 'sometimes|integer|min:1',
            'sub_categories' => 'sometimes',
            'sub_category' => 'sometimes|string',
            'is_expiry' => 'sometimes|string',
            'job_type' => 'sometimes|string',
            'job_types' => 'sometimes',
            'salary' => 'sometimes|numeric|min:0',
        ]);

        $deviceId = $validated['device_id'] ?? null;
        $phone = $validated['phone_number'] ?? $validated['mobile_number'] ?? null;
        $latitude = $validated['latitude'] ?? null;
        $longitude = $validated['longitude'] ?? null;
        $radius = $validated['radius'] ?? 5;
        $perPage = min($validated['per_page'] ?? 50, 200);
        $subCategories = $this->normalizeSubCategories($request);
        $expiryWindow = $this->normalizeExpiryWindow($request->input('is_expiry'));
        $jobTypes = $this->normalizeJobTypes($request);
        $salaryRange = $this->normalizeSalaryRange($request);

        try {
            $query = Job::withCommonFields()->active();

            // Location-based search
            if ($latitude && $longitude) {
                $query = $query->nearby($latitude, $longitude, $radius);
            }

            // Subcategory filtering
            $this->applySubCategoryFilter($query, $subCategories);

            if (!empty($jobTypes)) {
                $this->applyJobTypeFilter($query, $jobTypes);
            }

            if ($salaryRange) {
                $this->applySalaryFilter($query, $salaryRange);
            }

            // Expiry window filtering
            if ($expiryWindow) {
                $query->whereNotNull('expires_at')
                    ->whereBetween('expires_at', [$expiryWindow['from'], $expiryWindow['to']]);
            }

            // Device/Phone based search
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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error searching jobs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    protected function normalizeSubCategories(Request $request): array
    {
        $raw = $request->input('sub_categories', $request->input('sub_category'));

        if ($raw === null || $raw === '') {
            return [];
        }

        if (is_array($raw)) {
            return array_values(array_filter(array_map(static fn ($value) => trim((string) $value), $raw)));
        }

        $parts = preg_split('/[,\s]+/', (string) $raw) ?: [];

        return array_values(array_filter(array_map(static fn ($value) => trim((string) $value), $parts)));
    }

    protected function applySubCategoryFilter($query, array $subCategories): void
    {
        if (empty($subCategories)) {
            return;
        }

        $query->where(function ($q) use ($subCategories) {
            foreach ($subCategories as $subcategory) {
                $normalized = strtolower(trim((string) $subcategory));

                if ($normalized === '') {
                    continue;
                }

                $q->orWhere(DB::raw('LOWER(subcategory)'), 'like', '%' . $normalized . '%');
            }
        });
    }

    protected function normalizeJobTypes(Request $request): array
    {
        $raw = $request->input('job_types', $request->input('job_type'));

        if ($raw === null || $raw === '') {
            return [];
        }

        if (is_array($raw)) {
            return array_values(array_filter(array_map(static fn ($value) => trim((string) $value), $raw)));
        }

        $parts = preg_split('/[,\s]+/', (string) $raw) ?: [];

        return array_values(array_filter(array_map(static fn ($value) => trim((string) $value), $parts)));
    }

    protected function normalizeSalaryRange(Request $request): ?array
    {
        $salary = $request->input('salary');

        if ($salary === null || $salary === '') {
            return null;
        }

        return [
            'max' => (float) $salary,
        ];
    }

    protected function applyJobTypeFilter($query, array $jobTypes): void
    {
        $query->where(function ($q) use ($jobTypes) {
            foreach ($jobTypes as $jobType) {
                $normalized = strtolower(trim((string) $jobType));

                if ($normalized === '') {
                    continue;
                }

                $q->orWhere(DB::raw('LOWER(job_type)'), 'like', '%' . $normalized . '%');
            }
        });
    }

    protected function applySalaryFilter($query, array $salaryRange): void
    {
        if (isset($salaryRange['max']) && $salaryRange['max'] !== null) {
            $query->where('salary', '<=', $salaryRange['max']);
        }
    }

    protected function normalizeExpiryWindow(?string $value): ?array
    {
        if (!$value) {
            return null;
        }

        $normalized = strtolower(trim($value));
        $normalized = preg_replace('/[\s\-_]+/', '_', $normalized);
        $dayMap = [
            'within_a_day' => 1,
            'withinaday' => 1,
            'within_1_day' => 1,
            'within1day' => 1,
            'within_3_days' => 3,
            'within3days' => 3,
            'within3_days' => 3,
            'within_3_day' => 3,
            'within3day' => 3,
            'within_a_week' => 7,
            'withinaweek' => 7,
            'within_week' => 7,
            'within1week' => 7,
        ];

        if (isset($dayMap[$normalized])) {
            return [
                'from' => now(),
                'to' => now()->addDays($dayMap[$normalized]),
            ];
        }

        if (preg_match('/^within(?:\s+|_)?(?:a|an)?(?:\s+|_)?(\d+)?(?:\s+|_)?(day|days|week|weeks)?$/', $normalized, $matches)) {
            $amount = (int) ($matches[1] ?? 1);
            $unit = $matches[2] ?? 'day';

            if ($unit === 'week' || $unit === 'weeks') {
                $days = 7 * $amount;
            } else {
                $days = $amount;
            }

            if ($days > 0) {
                return [
                    'from' => now(),
                    'to' => now()->addDays($days),
                ];
            }
        }

        return null;
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
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
                'phone_number' => 'required',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'city' => 'required',
                'plan_id' => 'required',
                'transaction_id' => 'nullable|string',
                'credit_mode' => ['nullable', 'string', 'in:full_upi,semi,full_credit'],
                'customer_id' => ['nullable', 'string', 'exists:customers,customer_id'],
            ]);

            if (!empty($data['amount']) && empty($data['salary'])) {
                $data['salary'] = $data['amount'];
            }

            $data['status'] = 'IN PROGRESS';

            $job = Job::create($data);

            if (!empty($data['transaction_id'])) {
                Payment::create([
                    'transaction_id' => $data['transaction_id'],
                    'job_or_offer_id' => $job->id,
                    'item_type' => 'job',
                    'credit_mode' => $data['credit_mode'] ?? 'full_upi',
                    'amount' => $data['amount'] ?? $data['salary'] ?? null,
                ]);
            }

            if (!empty($data['customer_id']) && in_array($data['credit_mode'] ?? '', ['semi', 'full_credit'], true)) {
                $amount = (float) ($data['amount'] ?? $data['salary'] ?? 0);
                $deduction = $amount;
                $credit = CustomerCredit::where('customer_id', $data['customer_id'])->first();

                if (!$credit) {
                    $credit = CustomerCredit::create([
                        'customer_id' => $data['customer_id'],
                        'balance' => 1000,
                    ]);
                }

                $credit->balance = max(0, (float) $credit->balance - $deduction);
                $credit->save();
            }

            DB::commit();

            return $job;
        } catch (ValidationException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Unable to create job.',
                'error' => $e->getMessage(),
            ], 400);
        }
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
        Job::destroy($id);

        return [
            'message' => 'Job deleted successfully'
        ];
    }
}