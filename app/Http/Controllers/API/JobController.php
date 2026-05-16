<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Job;

class JobController extends Controller
{
    public function index()
    {
        return Job::select([
            'id',
            'temp_id',
            'business_name',
            'job_role',
            'job_type',
            'salary',
            'city',
            'latitude',
            'longitude',
            'status',
            'view_count',
            'created_at'
        ])
        ->latest()
        ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'temp_id' => 'required|string', // ✅ STRICT: must come from Postman

            'device_id' => 'required',
            'device_os' => 'required',
            'master_category' => 'required',
            'business_name' => 'required',
            'job_role' => 'required',
            'phone_number' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
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
            'created_at'
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