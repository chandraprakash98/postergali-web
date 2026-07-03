<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\LocationService;

class Job extends Model
{
    protected $fillable = [
        'temp_id','device_id','device_os',
        'master_category','subcategory',
        'business_name','job_role','job_type',
        'salary','phone_number',
        'latitude','longitude','city',
        'approved_at','expires_at',
        'status','status_comment',
        'view_count','reviewed_by',
        'boost_hours','plan_id'
    ];

    protected $casts = [
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
     * Scope to find jobs nearby a location
     * Automatically selects common fields plus distance
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param float $latitude
     * @param float $longitude
     * @param float $radiusKm
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNearby($query, float $latitude, float $longitude, float $radiusKm = 5)
    {
        $query = $query->select([
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
            'created_at',
            'expires_at',
        ]);
        
        $query = LocationService::withBoundingBox($query, $latitude, $longitude, $radiusKm);
        return LocationService::nearbyQuery($query, $latitude, $longitude, $radiusKm);
    }

    /**
     * Scope to filter approved and not expired jobs
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
            'created_at',
            'expires_at',
        ]);
    }
}
