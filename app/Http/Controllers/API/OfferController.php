<?php

namespace App\Http\Controllers\API;

use App\DTOs\PaymentData;
use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Services\FilterService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfferController extends Controller
{
    public function __construct(
        protected FilterService $filterService = new FilterService(),
        protected PaymentService $paymentService = new PaymentService(),
    ) {}

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
        $this->filterService->rejectUnsupportedParams($request, FilterService::ALLOWED_OFFER_INDEX_PARAMS);

        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'sometimes',
            'distance' => 'sometimes',
            'min_radius' => 'sometimes|numeric|min:0|max:100',
            'max_radius' => 'sometimes|numeric|min:0.1|max:100',
            'min_distance' => 'sometimes|numeric|min:0|max:100',
            'max_distance' => 'sometimes|numeric|min:0.1|max:100',
            'per_page' => 'sometimes|integer|min:1|max:200',
            'page' => 'sometimes|integer|min:1',
            'sub_categories' => 'sometimes',
            'sub_category' => 'sometimes|string',
            'category' => 'sometimes|string',
            'categories' => 'sometimes',
            'is_expiry' => 'sometimes|string',
            'expiry' => 'sometimes|string',
            'offer_type' => 'sometimes|string',
            'offer_types' => 'sometimes',
        ]);

        $userLat = (float) $validated['latitude'];
        $userLng = (float) $validated['longitude'];
        $perPage = min((int) ($validated['per_page'] ?? 50), 200);

        $distanceRange = $this->filterService->normalizeDistanceRange(
            $request->input('distance'),
            $request->input('radius'),
            $request->input('min_distance'),
            $request->input('max_distance'),
            $request->input('min_radius'),
            $request->input('max_radius')
        );

        $subCategories = $this->filterService->normalizeSubCategories($request);
        $expiryWindow = $this->filterService->normalizeExpiryWindow($request->input('is_expiry', $request->input('expiry')));
        $offerTypes = $this->filterService->normalizeOfferTypes($request);

        $query = Offer::nearby($userLat, $userLng, $distanceRange['max'], $distanceRange['min'])->active();

        $this->filterService->applySubCategoryFilter($query, $subCategories);

        if (!empty($offerTypes)) {
            $this->filterService->applyOfferTypeFilter($query, $offerTypes);
        }

        if ($expiryWindow) {
            $this->filterService->applyExpiryFilter($query, $expiryWindow);
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
            'radius_km' => $distanceRange['max'],
            'min_distance_km' => $distanceRange['min'],
            'max_distance_km' => $distanceRange['max'],
        ]);
    }

    /**
     * Search offers by device_id or mobile_number
     * Also supports location-based search if latitude/longitude provided
     */
    public function search(Request $request)
    {
        $this->filterService->rejectUnsupportedParams($request, FilterService::ALLOWED_OFFER_SEARCH_PARAMS);

        $validated = $request->validate([
            'device_id' => 'required_without_all:latitude,longitude|string',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'radius' => 'sometimes',
            'distance' => 'sometimes',
            'min_radius' => 'sometimes|numeric|min:0|max:100',
            'max_radius' => 'sometimes|numeric|min:0.1|max:100',
            'min_distance' => 'sometimes|numeric|min:0|max:100',
            'max_distance' => 'sometimes|numeric|min:0.1|max:100',
            'mobile_number' => 'sometimes|string',
            'phone_number' => 'sometimes|string',
            'per_page' => 'sometimes|integer|min:1|max:200',
            'page' => 'sometimes|integer|min:1',
            'sub_categories' => 'sometimes',
            'sub_category' => 'sometimes|string',
            'category' => 'sometimes|string',
            'categories' => 'sometimes',
            'is_expiry' => 'sometimes|string',
            'expiry' => 'sometimes|string',
            'offer_type' => 'sometimes|string',
            'offer_types' => 'sometimes',
        ]);

        $deviceId = $validated['device_id'] ?? null;
        $mobile = $validated['mobile_number'] ?? $validated['phone_number'] ?? null;
        $latitude = isset($validated['latitude']) ? (float) $validated['latitude'] : null;
        $longitude = isset($validated['longitude']) ? (float) $validated['longitude'] : null;
        $perPage = min((int) ($validated['per_page'] ?? 50), 200);

        $distanceRange = $this->filterService->normalizeDistanceRange(
            $request->input('distance'),
            $request->input('radius'),
            $request->input('min_distance'),
            $request->input('max_distance'),
            $request->input('min_radius'),
            $request->input('max_radius')
        );

        $subCategories = $this->filterService->normalizeSubCategories($request);
        $expiryWindow = $this->filterService->normalizeExpiryWindow($request->input('is_expiry', $request->input('expiry')));
        $offerTypes = $this->filterService->normalizeOfferTypes($request);

        $query = Offer::withCommonFields()->active();

        if ($latitude !== null && $longitude !== null) {
            $query = $query->nearby($latitude, $longitude, $distanceRange['max'], $distanceRange['min']);
        }

        $this->filterService->applySubCategoryFilter($query, $subCategories);

        if (!empty($offerTypes)) {
            $this->filterService->applyOfferTypeFilter($query, $offerTypes);
        }

        if ($expiryWindow) {
            $this->filterService->applyExpiryFilter($query, $expiryWindow);
        }

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
            'radius_km' => $distanceRange['max'],
            'min_distance_km' => $distanceRange['min'],
            'max_distance_km' => $distanceRange['max'],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'temp_id' => 'nullable|string',
            'device_id' => 'nullable|string',
            'device_os' => 'nullable|string',
            'master_category' => 'nullable|string',
            'subcategory' => 'nullable|string',
            'business_name' => 'nullable|string',
            'offer_details' => 'nullable|string',
            'offer_type' => 'nullable|string',
            'amount' => 'nullable|numeric|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'razorpay_amount' => 'nullable|numeric|min:0',
            'credit_amount' => 'nullable|numeric|min:0',
            'mobile_number' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'city' => 'nullable|string',
            'plan_id' => 'nullable|string',
            'transaction_id' => 'nullable|string',
            'razorpay_order_id' => 'nullable|string',
            'razorpay_payment_id' => 'nullable|string',
            'payment_status' => 'nullable|string',
            'payment_type' => ['nullable', 'string', 'in:full_upi,semi,full_credit,FULL_UPI,SEMI,FULL_CREDIT'],
            'credit_mode' => ['nullable', 'string', 'in:full_upi,semi,full_credit,FULL_UPI,SEMI,FULL_CREDIT'],
            'customer_id' => ['nullable', 'string'],
        ]);

        return DB::transaction(function () use ($request, $data) {
            // Media handling
            $media = ['images' => [], 'video' => null];

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $img) {
                    $media['images'][] = $img->store('offers/images', 'public');
                }
            }

            if ($request->hasFile('video')) {
                $media['video'] = $request->file('video')->store('offers/videos', 'public');
            }

            $offerAttributes = array_intersect_key($data, array_flip((new Offer())->getFillable()));
            $offerAttributes['media'] = $media;
            $offerAttributes['status'] = 'pending';

            $offer = Offer::create($offerAttributes);

            $hasPaymentInfo = !empty($data['transaction_id'])
                || !empty($data['payment_type'])
                || !empty($data['credit_mode'])
                || !empty($data['razorpay_payment_id'])
                || !empty($data['amount'])
                || !empty($data['total_amount']);

            if ($hasPaymentInfo) {
                $paymentData = PaymentData::fromArray($data, itemType: 'offer', jobOrOfferId: $offer->id);
                $this->paymentService->processPayment($paymentData);
            }

            return response()->json($offer, 201);
        });
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
        $offer = Offer::findOrFail($id);
        $offer->delete();

        return response()->json([
            'message' => 'Offer deleted'
        ]);
    }
}