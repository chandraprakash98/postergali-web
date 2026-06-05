<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
