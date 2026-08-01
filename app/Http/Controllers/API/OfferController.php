<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\CustomerCredit;
use App\Models\Offer;
use App\Models\Payment;

class OfferController extends Controller
{
    /**
     * Get all offers within a specified radius of user location
     * 
     * @route GET /api/v1/offers
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
        ]);

        $userLat = $validated['latitude'];
        $userLng = $validated['longitude'];
        $radius = $validated['radius'] ?? 5; // Default 5 km
        $perPage = min($validated['per_page'] ?? 50, 200); // Max 200 per page
        $subCategories = $this->normalizeSubCategories($request);
        $expiryWindow = $this->normalizeExpiryWindow($request->input('is_expiry'));

        try {
            $query = Offer::nearby($userLat, $userLng, $radius)->active();

            $this->applySubCategoryFilter($query, $subCategories);

            if ($expiryWindow) {
                $query->whereNotNull('expires_at')
                    ->whereBetween('expires_at', [$expiryWindow['from'], $expiryWindow['to']]);
            }

            $offers = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $offers->items(),
                'pagination' => [
                    'total' => $offers->total(),
                    'per_page' => $offers->perPage(),
                    'current_page' => $offers->currentPage(),
                    'last_page' => $offers->lastPage(),
                    'from' => $offers->firstItem(),
                    'to' => $offers->lastItem(),
                ],
                'radius_km' => $radius,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching nearby offers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search offers by device_id or mobile_number
     * Also supports location-based search if latitude/longitude provided
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required_without_all:latitude,longitude|string',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'radius' => 'sometimes|numeric|min:0.1|max:50',
            'mobile_number' => 'sometimes|string',
            'phone_number' => 'sometimes|string',
            'per_page' => 'sometimes|integer|min:1|max:200',
            'page' => 'sometimes|integer|min:1',
            'sub_categories' => 'sometimes',
            'sub_category' => 'sometimes|string',
            'is_expiry' => 'sometimes|string',
        ]);

        $deviceId = $validated['device_id'] ?? null;
        $mobile = $validated['mobile_number'] ?? $validated['phone_number'] ?? null;
        $latitude = $validated['latitude'] ?? null;
        $longitude = $validated['longitude'] ?? null;
        $radius = $validated['radius'] ?? 5;
        $perPage = min($validated['per_page'] ?? 50, 200);
        $subCategories = $this->normalizeSubCategories($request);
        $expiryWindow = $this->normalizeExpiryWindow($request->input('is_expiry'));

        try {
            $query = Offer::withCommonFields();

            // Location-based search
            if ($latitude && $longitude) {
                $query = $query->nearby($latitude, $longitude, $radius);
            }

            $this->applySubCategoryFilter($query, $subCategories);

            if ($expiryWindow) {
                $query->whereNotNull('expires_at')
                    ->whereBetween('expires_at', [$expiryWindow['from'], $expiryWindow['to']]);
            }

            // Device/Mobile based search
            if ($deviceId || $mobile) {
                $query->where(function ($q) use ($deviceId, $mobile) {
                    if ($deviceId) {
                        $q->where('device_id', $deviceId);
                    }
                    if ($mobile) {
                        $q->orWhere('mobile_number', $mobile);
                    }
                });
            }

            $offers = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $offers->items(),
                'pagination' => [
                    'total' => $offers->total(),
                    'per_page' => $offers->perPage(),
                    'current_page' => $offers->currentPage(),
                    'last_page' => $offers->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error searching offers',
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
                'temp_id' => 'nullable|string',
                'device_id' => 'nullable|string',
                'device_os' => 'nullable|string',
                'master_category' => 'nullable|string',
                'business_name' => 'nullable|string',
                'offer_details' => 'nullable|string',
                'offer_type' => 'nullable|string',
                'amount' => 'nullable|numeric|min:0',
                'mobile_number' => 'nullable|string',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'city' => 'nullable|string',
                'plan_id' => 'nullable|string',
                'transaction_id' => 'nullable|string',
                'credit_mode' => ['nullable', 'string', 'in:full_upi,semi,full_credit'],
                'customer_id' => ['nullable', 'string'],
            ]);

            // MEDIA HANDLING
            $media = ['images' => [], 'video' => null];

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $img) {
                    $media['images'][] = $img->store('offers/images', 'public');
                }
            }

            if ($request->hasFile('video')) {
                $media['video'] = $request->file('video')->store('offers/videos', 'public');
            }

            $data['media'] = $media;

            $offer = Offer::create($data);

            if (!empty($data['transaction_id'])) {
                Payment::create([
                    'transaction_id' => $data['transaction_id'],
                    'job_or_offer_id' => $offer->id,
                    'item_type' => 'offer',
                    'credit_mode' => $data['credit_mode'] ?? 'full_upi',
                    'amount' => $data['amount'] ?? null,
                ]);
            }

            if (!empty($data['customer_id']) && in_array($data['credit_mode'] ?? '', ['semi', 'full_credit'], true)) {
                $amount = (float) ($data['amount'] ?? 0);
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

            return $offer;
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
                'message' => 'Unable to create offer.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function show($id)
    {
        $offer = Offer::findOrFail($id);
        $offer->increment('view_count');
        return $offer;
    }

    public function update(Request $request, $id)
    {
        $offer = Offer::findOrFail($id);
        $offer->update($request->all());
        return $offer;
    }

    public function destroy($id)
    {
        Offer::destroy($id);
        return ['message' => 'Offer deleted'];
    }
}