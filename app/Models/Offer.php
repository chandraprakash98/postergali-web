<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = [
    'temp_id',   // 👈 MUST BE HERE
    'device_id',
    'device_os',
    'master_category',
    'business_name',
    'offer_details',
    'offer_type',
    'media',
    'mobile_number',
    'latitude',
    'longitude',
    'city',
    'status',
    'view_count',
    'plan_id'
];

    protected $casts = [
        'media' => 'array',
        'approved_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
