<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const TYPE_FULL_UPI = 'FULL_UPI';
    public const TYPE_SEMI = 'SEMI';
    public const TYPE_FULL_CREDIT = 'FULL_CREDIT';

    public const STATUS_PENDING = 'PENDING';
    public const STATUS_COMPLETED = 'COMPLETED';
    public const STATUS_SUCCESS = 'SUCCESS';
    public const STATUS_FAILED = 'FAILED';

    protected $fillable = [
        'customer_id',
        'transaction_id',
        'job_or_offer_id',
        'item_type',
        'credit_mode',
        'amount',
        'payment_type',
        'total_amount',
        'razorpay_amount',
        'credit_amount',
        'razorpay_order_id',
        'razorpay_payment_id',
        'payment_status',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'razorpay_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_or_offer_id');
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class, 'job_or_offer_id');
    }
}
