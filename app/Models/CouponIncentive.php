<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponIncentive extends Model
{
    protected $table = 'coupon_incentive_table';

    protected $fillable = [
        'influencer_bonus_coupon_id',
        'ad_id',
        'customer_id',
        'mobile_number',
        'usage_date',
        'payment_method',
        'poster_count',
        'incentive_processed_date',
    ];

    protected $casts = [
        'usage_date' => 'date',
        'incentive_processed_date' => 'date',
    ];
}