<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonusCoupon extends Model
{
    protected $fillable = [
        'influencer_bonus_coupon_id',
        'bonus_coupon_poster_credit',
        'bonus_coupon_status',
        'influencer_name',
    ];
}