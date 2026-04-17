<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ad;

class AdController extends Controller
{
    // CREATE ADD by App //Admin consume this too for managing ads lifecyle
    public function store(Request $request)
    {
        try {
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

            // ✅ OPTIONAL MEDIA UPLOAD
            if ($request->hasFile('ad_media')) {
                $files = $request->file('ad_media');
                $files = is_array($files) ? $files : [$files];
                $paths = [];
                foreach ($files as $file) {
                    if ($file->isValid()) {
                        $paths[] = $file->store('ads', 'public');
                    }
                }
                if (!empty($paths)) {
                    $data['ad_media'] = implode(',', $paths);
                }
            }
            // ✅ CREATE EVEN WITHOUT MEDIA
            $ad = Ad::create($data);
            // Format response
            $ad->ad_media = $this->formatMediaUrls($ad->ad_media ?? null);
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
        $ads = Ad::all();
        foreach ($ads as $ad) {
            $ad->ad_media = $this->formatMediaUrls($ad->ad_media);
        }
        return response()->json($ads);
    }

    // READ SINGLE
    public function show($id)
    {
        $ad = Ad::findOrFail($id);
        $ad->ad_media = $this->formatMediaUrls($ad->ad_media);
        return response()->json($ad);
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $ad = Ad::findOrFail($id);
        $data = $request->all();
        // Handle multiple file upload in update also
        if ($request->hasFile('ad_media')) {
            $files = $request->file('ad_media');
            $files = is_array($files) ? $files : [$files];
            $paths = [];
            foreach ($files as $file) {
                if ($file->isValid()) {
                    $paths[] = $file->store('ads', 'public');
                }
            }
            if (!empty($paths)) {
                $data['ad_media'] = implode(',', $paths);
            }
        }
        $ad->update($data);
        // Format response
        $ad->ad_media = $this->formatMediaUrls($ad->ad_media);
        return response()->json($ad);
    }

    // DELETE
    public function destroy($id)
    {
        $ad = Ad::findOrFail($id);
        $ad->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    private function formatMediaUrls($media)
    {
        if (!$media) {
            return []; // important
        }
        $paths = explode(',', $media);
        return array_map(function ($path) {
            return asset('storage/' . $path);
        }, $paths);
    }

    public function listBasicAds()
    {
        $ads = \App\Models\Ad::select(
            'ad_id',
            'business_name',
            'city',
            'ad_range',
            'add_duration',
            'status',
            'location',
            'expired_at as expiry'
        )->get();
        return response()->json($ads);
    }

    public function getAdById($id)
    {
        $ad = \App\Models\Ad::find($id);
        if (!$ad) {
            return response()->json([
                'message' => 'Ad not found'
            ], 404);
        }
        // format media (reuse your existing function)
        $ad->ad_media = $this->formatMediaUrls($ad->ad_media);
        return response()->json($ad);
    }
}