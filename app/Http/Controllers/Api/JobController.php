<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    // Get all jobs
    public function index()
    {
        $jobs = Job::all();
        return response()->json($jobs, 200);
    }

    // Create a new job
    public function store(Request $request)
    {
        $job = Job::create($request->all());
        return response()->json([
            'message' => 'Job created successfully',
            'job' => $job
        ], 201);
    }

    // Show a specific job
    public function show($id)
    {
        $job = Job::find($id);
        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }
        return response()->json($job, 200);
    }

    // Update a job
    public function update(Request $request, $id)
    {
        $job = Job::find($id);
        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }
        $job->update($request->all());
        return response()->json([
            'message' => 'Job updated successfully',
            'job' => $job
        ], 200);
    }

    // Delete a job
    public function destroy($id)
    {
        $job = Job::find($id);
        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }
        $job->delete();
        return response()->json(['message' => 'Job deleted successfully'], 200);
    }
}