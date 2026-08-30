<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\LocationService;

class Offer extends Model
{
    protected $fillable = [
    'temp_id',
    'device_id',
    'device_os',
    'master_category',
    'subcategory',
    'business_name',
    'offer_details',
    'offer_type',
    'media',
    'amount',
    'mobile_number',
    'latitude',
    'longitude',
    'city',
    'status',
    'view_count',
    'approved_at',
    'expires_at',
    'plan_id'
];

    protected $casts = [
        'media' => 'array',
        'approved_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    /**
     * Scope to find offers nearby a location
     * Automatically selects common fields plus distance
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param float $latitude
     * @param float $longitude
     * @param float $radiusKm
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNearby($query, float $latitude, float $longitude, float $radiusKm = 5, float $minRadiusKm = 0)
    {
        $query = $query->select([
            'id',
            'business_name',
            'offer_details',
            'offer_type',
            'city',
            'latitude',
            'longitude',
            'status',
            'view_count',
            'temp_id',
            'created_at',
            'expires_at',
        ]);
        
        $query = LocationService::withBoundingBox($query, $latitude, $longitude, $radiusKm);
        return LocationService::nearbyQuery($query, $latitude, $longitude, $radiusKm, minRadiusKm: $minRadiusKm);
    }

    /**
     * Scope to filter approved and not expired offers
     */
    public function scopeActive($query)
    {
        return $query
            ->whereNotNull('approved_at')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope to get commonly used fields for list/search
     */
    public function scopeWithCommonFields($query)
    {
        return $query->select([
            'id',
            'business_name',
            'offer_details',
            'offer_type',
            'city',
            'latitude',
            'longitude',
            'status',
            'view_count',
            'temp_id',
            'created_at',
            'expires_at',
        ]);
    }
}
