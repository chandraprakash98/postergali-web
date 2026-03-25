<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $primaryKey = 'ad_id';

    protected $fillable = [
        'device_id',
        'device_os',
        'created_t',
        'master_category',
        'ad_subcategory',
        'business_name',
        'ad_description',
        'ad_media',
        'mobile',
        'location',
        'city',
        'ad_range',
        'add_duration',
        'approved_at',
        'expired_at',
        'status',
        'ad_status_comment',
        'reviewed_by',
        'ad_steal_deal',
        'ad_boost_poster'
    ];
}