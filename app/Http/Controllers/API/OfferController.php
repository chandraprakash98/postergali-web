<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Offer;

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
        ]);

        $userLat = $validated['latitude'];
        $userLng = $validated['longitude'];
        $radius = $validated['radius'] ?? 5; // Default 5 km
        $perPage = min($validated['per_page'] ?? 50, 200); // Max 200 per page

        try {
            $offers = Offer::nearby($userLat, $userLng, $radius)
                ->active()
                ->paginate($perPage);

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
        ]);

        $deviceId = $validated['device_id'] ?? null;
        $mobile = $validated['mobile_number'] ?? $validated['phone_number'] ?? null;
        $latitude = $validated['latitude'] ?? null;
        $longitude = $validated['longitude'] ?? null;
        $radius = $validated['radius'] ?? 5;
        $perPage = min($validated['per_page'] ?? 50, 200);

        try {
            $query = Offer::withCommonFields();

            // Location-based search
            if ($latitude && $longitude) {
                $query = $query->nearby($latitude, $longitude, $radius);
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

    public function store(Request $request)
    {
        $data = $request->validate([
            'temp_id' => 'nullable|string',
            'device_id' => 'required',
            'device_os' => 'required',
            'master_category' => 'required',
            'business_name' => 'required',
            'offer_details' => 'required',
            'offer_type' => 'required',
            'mobile_number' => 'required',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'city' => 'required',
            'plan_id' => 'required',
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

        return Offer::create($data);
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