<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Offer;

class OfferController extends Controller
{
   public function index()
{
    return Offer::select([
        'id',
        'business_name',
        'offer_details',
        'offer_type',
        'city',
        'latitude',
        'longitude',
        'status',
        'view_count',
        'temp_id'
    ])
    ->latest()
    ->get();
}

    public function store(Request $request)
    {
        $data = $request->validate([
            'temp_id' => 'nullable|string', // 👈 ADD THIS
            'device_id' => 'required',
            'device_os' => 'required',
            'master_category' => 'required',
            'business_name' => 'required',
            'offer_details' => 'required',
            'mobile_number' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
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