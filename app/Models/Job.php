<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $table = 'jobs';
    protected $primaryKey = 'ad_id';

    protected $fillable = [
        'device_id',
        'device_os',
        'ad_creation_timestamp',
        'master_category',
        'ad_subcategory',
        'business_name',
        'ad_description',
        'ad_job_type',
        'ad_job_salary',
        'phone_number',
        'location_latlong',
        'city',
        'ad_area_coverage',
        'ad_days',
        'ad_approval_timestamp',
        'ad_expiry_timestamp',
        'ad_status',
        'ad_status_comments',
        'ad_reviewed_by',
        'ad_boost_poster',
    ];

    public $timestamps = true; // includes created_at and updated_at
}