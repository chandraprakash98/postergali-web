<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = [
        'referrer_name',
        'referrer_mobile',
        'referral_name',
        'referral_mobile',
        'status',
    ];
}
