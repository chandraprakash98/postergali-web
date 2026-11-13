<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosterSetting extends Model
{
    protected $fillable = [
        'daily_limit_per_phone',
        'updated_by',
    ];

    protected $casts = [
        'daily_limit_per_phone' => 'integer',
    ];
}
