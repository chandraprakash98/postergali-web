<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Services\GeocodeService;
use Illuminate\Http\Request;

class AdAdminController extends Controller
{

public function updateAll(Request $request, $id)
{
    $ad = Ad::find($id);

    $ad->update($request->all());

    return back()->with('success', 'Ad updated successfully');
}
    // Dashboard
    public function index(Request $request)
    {
        $query = Ad::query();

        if ($request->city) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->range) {
            $query->where('ad_range', 'like', '%' . $request->range . '%');
        }

        $ads = $query->latest()->get();

        // COUNTS
        $total = Ad::count();
        $approved = Ad::where('status', 'approved')->count();
        $rejected = Ad::where('status', 'rejected')->count();

        // 💰 Example revenue (you can change logic)
        $revenue = Ad::sum('ad_boost_poster');
        
        $filter = null;

        return view('admin.ads.index', compact('ads', 'total', 'approved', 'rejected', 'revenue', 'filter'));
    }

    // Pending Ads
    public function pending(Request $request)
    {
        $query = Ad::where('status', 'pending');

        if ($request->city) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->range) {
            $query->where('ad_range', 'like', '%' . $request->range . '%');
        }

        $ads = $query->latest()->get();

        // COUNTS
        $total = Ad::count();
        $approved = Ad::where('status', 'approved')->count();
        $rejected = Ad::where('status', 'rejected')->count();
        
        $filter = 'pending';

        return view('admin.ads.index', compact('ads', 'total', 'approved', 'rejected', 'filter'));
    }

    // Approved Ads
    public function approved(Request $request)
    {
        $query = Ad::where('status', 'approved');

        if ($request->city) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->range) {
            $query->where('ad_range', 'like', '%' . $request->range . '%');
        }

        $ads = $query->latest()->get();

        // COUNTS
        $total = Ad::count();
        $approved = Ad::where('status', 'approved')->count();
        $rejected = Ad::where('status', 'rejected')->count();
        
        $filter = 'approved';

        return view('admin.ads.index', compact('ads', 'total', 'approved', 'rejected', 'filter'));
    }

    // Expired Ads
    public function expired(Request $request)
    {
        $query = Ad::where('status', 'expired');

        if ($request->city) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->range) {
            $query->where('ad_range', 'like', '%' . $request->range . '%');
        }

        $ads = $query->latest()->get();

        // COUNTS
        $total = Ad::count();
        $approved = Ad::where('status', 'approved')->count();
        $rejected = Ad::where('status', 'rejected')->count();
        
        $filter = 'expired';

        return view('admin.ads.index', compact('ads', 'total', 'approved', 'rejected', 'filter'));
    }

    // View Single
    public function show($id)
    {
        $ad = Ad::findOrFail($id);
        
        // Get address from coordinates
        $address = null;
        if ($ad->location) {
            $coords = explode(',', $ad->location);
            if (count($coords) == 2) {
                $latitude = trim($coords[0]);
                $longitude = trim($coords[1]);
                $address = GeocodeService::getAddress($latitude, $longitude);
            }
        }
        
        return view('admin.ads.show', compact('ad', 'address'));
    }

    // Approve
    public function approve($id)
    {
        $ad = Ad::findOrFail($id);

        // Determine the reference started date: created_t is custom field, fallback to created_at
        if ($ad->created_t) {
            $createdAt = \Carbon\Carbon::parse($ad->created_t);
        } else {
            $createdAt = $ad->created_at ?: now();
        }

        $expiryAt = (clone $createdAt)->addHours(24);

        $ad->update([
            'status' => 'approved',
            'approved_at' => now(),
            'expired_at' => $expiryAt,
            'ad_status_comment' => 'Approved'
        ]);

        return redirect()->back()->with('success', 'Ad Approved, expiry set 24h from creation.');
    }

    // Reject
    public function reject(Request $request, $id)
    {
        $ad = Ad::findOrFail($id);

        $ad->update([
            'status' => 'rejected',
            'ad_status_comment' => $request->rejection_reason ?? $request->reason
        ]);

        return redirect()->back()->with('error', 'Ad Rejected');
    }

    // Update field
    public function updateField(Request $request, $id)
    {
        $ad = Ad::findOrFail($id);
        $field = $request->field;
        $value = $request->value;

        // Validate field name to prevent mass assignment attacks
        $allowed = ['business_name', 'master_category', 'ad_subcategory', 'mobile', 'location', 'ad_description'];
        
        if (!in_array($field, $allowed)) {
            return response()->json(['success' => false, 'message' => 'Invalid field'], 400);
        }

        $ad->update([$field => $value]);

        return response()->json(['success' => true, 'message' => 'Updated successfully']);
    }

    public function updateRange(Request $request, $id)
    {
        $ad = Ad::findOrFail($id);
        $ad->ad_range = $request->ad_range;
        $ad->save();

        return redirect()->back()->with('success', 'Range Updated');
    }

}