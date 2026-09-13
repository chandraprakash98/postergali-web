<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchRunLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'batch_name',
        'ran_at',
        'status',
        'dry_run',
        'day_before_jobs',
        'day_before_offers',
        'on_expiry_jobs',
        'on_expiry_offers',
        'notifications_sent',
        'skipped_no_token',
        'duration_ms',
    ];

    protected $casts = [
        'ran_at'  => 'datetime',
        'dry_run' => 'boolean',
    ];
}
