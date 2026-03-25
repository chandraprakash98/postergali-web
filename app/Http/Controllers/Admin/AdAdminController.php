<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
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

        return view('admin.ads.index', compact('ads', 'total', 'approved', 'rejected', 'revenue'));
    }

    // View Single
    public function show($id)
    {
        $ad = Ad::findOrFail($id);
        return view('admin.ads.show', compact('ad'));
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
            'ad_status_comment' => $request->reason
        ]);

        return redirect()->back()->with('error', 'Ad Rejected');
    }

    public function updateRange(Request $request, $id)
    {
        $ad = Ad::findOrFail($id);
        $ad->ad_range = $request->ad_range;
        $ad->save();

        return redirect()->back()->with('success', 'Range Updated');
    }

}