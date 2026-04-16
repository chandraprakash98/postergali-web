<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
     protected $primaryKey = 'job_id';

    protected $fillable = [
        'temp_id',
        'device_id',
        'device_os',
        'biz_name',
        'title',
        'description',
        'job_type',
        'salary',
        'phone',
        'city',
        'lat_long',
        'category',
        'sub_category',
        'duration_days',
        'status'
    ];
}
