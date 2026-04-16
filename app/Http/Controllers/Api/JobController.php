<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
 use App\Models\Job;
class JobController extends Controller
{


            public function store(Request $request)
            {
                
                $request->validate([
                    'biz_name' => 'required',
                    'title' => 'required',
                    'phone' => 'required'
                ]);

                $job = Job::create([
                    'temp_id' => $request->temp_id,
                    'device_id' => $request->device_id,
                    'device_os' => $request->device_os,
                    'biz_name' => $request->biz_name,
                    'title' => $request->title,
                    'description' => $request->description,
                    'job_type' => $request->job_type,
                    'salary' => $request->salary,
                    'phone' => $request->phone,
                    'city' => $request->city,
                    'lat_long' => $request->lat_long,
                    'category' => $request->category,
                    'sub_category' => $request->sub_category,
                    'duration_days' => $request->duration_days,
                    'status' => 'pending'
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Job created successfully',
                    'data' => $job
                ]);
            }

            public function index()
            {
                $jobs = Job::latest()->get();

                return response()->json([
                    'status' => true,
                    'data' => $jobs
                ]);
            }
            
            public function getPrice(Request $request)
            {
                $request->validate([
                    'coverage' => 'required',
                    'duration_days' => 'required|integer'
                ]);

                // Pricing table
                $pricing = [
                    '0-3km' => [
                        1 => 20,
                        3 => 50,
                        7 => 100,
                        15 => 180,
                        30 => 300
                    ],
                    '3-10km' => [
                        1 => 30,
                        3 => 75,
                        7 => 150,
                        15 => 270,
                        30 => 450
                    ],
                    'city' => [
                        1 => 60,
                        3 => 150,
                        7 => 300,
                        15 => 540,
                        30 => 900
                    ]
                ];

                $coverage = $request->coverage;
                $days = $request->duration_days;

                // Check if valid
                if (!isset($pricing[$coverage]) || !isset($pricing[$coverage][$days])) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid coverage or duration'
                    ], 400);
                }

                $price = $pricing[$coverage][$days];

                return response()->json([
                    'status' => true,
                    'coverage' => $coverage,
                    'duration_days' => $days,
                    'price' => $price
                ]);
            }
}
