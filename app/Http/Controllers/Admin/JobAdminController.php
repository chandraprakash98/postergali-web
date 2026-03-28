<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;

class JobAdminController extends Controller
{

    public function updateAll(Request $request, $id)
    {
        $job = Job::find($id);

        $job->update($request->all());

        return back()->with('success', 'Job updated successfully');
    }

    // Dashboard
    public function index(Request $request)
    {
        $query = Job::query();

        if ($request->title) {
            $query->where('business_name', 'like', '%' . $request->title . '%');
        }

        if ($request->company) {
            $query->where('business_name', 'like', '%' . $request->company . '%');
        }

        $jobs = $query->latest()->get();

        // COUNTS
        $total = Job::count();
        $active = Job::where('ad_status', 'Approved')->count();
        $expired = Job::where('ad_status', 'Expired')->count();

        return view('admin.jobs.index', compact('jobs', 'total', 'active', 'expired'));
    }

    // View Single
    public function show($id)
    {
        $job = Job::findOrFail($id);
        return view('admin.jobs.show', compact('job'));
    }

    // Approve
    public function approve($id)
    {
        $job = Job::findOrFail($id);

        // Calculate expiry date: 30 days from creation
        $createdAt = $job->ad_creation_timestamp ?? $job->created_at;
        $expiryAt = $createdAt->addDays(30);

        $job->update([
            'ad_status' => 'Approved',
            'ad_approval_timestamp' => now(),
            'ad_expiry_timestamp' => $expiryAt,
            'ad_status_comments' => 'Approved'
        ]);

        return redirect()->back()->with('success', 'Job Approved Successfully! Expiry set to 30 days from creation.');
    }

    // Reject
    public function reject(Request $request, $id)
    {
        $job = Job::findOrFail($id);

        $job->update([
            'ad_status' => 'Rejected',
            'ad_status_comments' => $request->reason
        ]);

        return redirect()->back()->with('error', 'Job Rejected');
    }

}