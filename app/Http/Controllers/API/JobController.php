<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Job;
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
        ]);

        $userLat = $validated['latitude'];
        $userLng = $validated['longitude'];
        $radius = $validated['radius'] ?? 5; // Default 5 km
        $perPage = min($validated['per_page'] ?? 50, 200); // Max 200 per page

        try {
            $jobs = Job::nearby($userLat, $userLng, $radius)
                ->active()
                ->paginate($perPage);

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
            'device_id' => 'required_without_all:latitude,longitude|string',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'radius' => 'sometimes|numeric|min:0.1|max:50',
            'phone_number' => 'sometimes|string',
            'mobile_number' => 'sometimes|string',
            'per_page' => 'sometimes|integer|min:1|max:200',
            'page' => 'sometimes|integer|min:1',
        ]);

        $deviceId = $validated['device_id'] ?? null;
        $phone = $validated['phone_number'] ?? $validated['mobile_number'] ?? null;
        $latitude = $validated['latitude'] ?? null;
        $longitude = $validated['longitude'] ?? null;
        $radius = $validated['radius'] ?? 5;
        $perPage = min($validated['per_page'] ?? 50, 200);

        try {
            $query = Job::withCommonFields();

            // Location-based search
            if ($latitude && $longitude) {
                $query = $query->nearby($latitude, $longitude, $radius);
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
            'salary' => 'required',
            'phone_number' => 'required',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'city' => 'required',
            'plan_id' => 'required',
        ]);

        return Job::create($data);
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