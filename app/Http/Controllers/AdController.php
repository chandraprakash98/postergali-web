<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ad;

class AdController extends Controller
{
    // CREATE
    public function store(Request $request)
{
    try {
        // Manually map fields (IMPORTANT for form-data)
        $data = [
            'device_id' => $request->input('device_id'),
            'device_os' => $request->input('device_os'),
            'master_category' => $request->input('master_category'),
            'ad_subcategory' => $request->input('ad_subcategory'),
            'business_name' => $request->input('business_name'),
            'ad_description' => $request->input('ad_description'),
            'mobile' => $request->input('mobile'),
            'location' => $request->input('location'),
            'city' => $request->input('city'),
            'ad_range' => $request->input('ad_range'),
            'add_duration' => $request->input('add_duration'),
            'status' => $request->input('status', 'pending'),
            'ad_steal_deal' => $request->input('ad_steal_deal', false),
            'ad_boost_poster' => $request->input('ad_boost_poster'),
        ];

        // FILE UPLOAD
        if ($request->hasFile('ad_media')) {
            $file = $request->file('ad_media');

            if ($file->isValid()) {
                $path = $file->store('ads', 'public');
                $data['ad_media'] = $path;
            } else {
                return response()->json(['error' => 'Invalid file'], 400);
            }
        } else {
            return response()->json(['error' => 'File not received'], 400);
        }

        $ad = Ad::create($data);

        return response()->json($ad, 201);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
}

    // READ ALL
    public function index()
    {
        return response()->json(Ad::all());
    }

    // READ SINGLE
    public function show($id)
    {
        return response()->json(Ad::findOrFail($id));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $ad = Ad::findOrFail($id);

        $data = $request->all();

        if ($request->hasFile('ad_media')) {
            $file = $request->file('ad_media');
            $path = $file->store('ads', 'public');
            $data['ad_media'] = $path;
        }

        $ad->update($data);

        return response()->json($ad);
    }

    // DELETE
    public function destroy($id)
    {
        $ad = Ad::findOrFail($id);
        $ad->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}