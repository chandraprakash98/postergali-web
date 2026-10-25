<?php

namespace App\Models;

use Database\Factories\PaymentTransactionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PaymentTransaction extends Model
{
    /** @use HasFactory<PaymentTransactionFactory> */
    use HasFactory;

    public const PROVIDER_RAZORPAY = 'RAZORPAY';
    public const PROVIDER_APPLE_IAP = 'APPLE_IAP';

    public const STATUS_PENDING = 'PENDING';
    public const STATUS_SUCCESS = 'SUCCESS';
    public const STATUS_FAILED = 'FAILED';
    public const STATUS_REFUNDED = 'REFUNDED';

    public const VERIFICATION_UNVERIFIED = 'UNVERIFIED';
    public const VERIFICATION_VERIFIED = 'VERIFIED';
    public const VERIFICATION_FAILED = 'FAILED';

    protected $fillable = [
        'transaction_uuid',
        'customer_id',
        'credit_pack_id',
        'plan_id',
        'payment_provider',
        'amount',
        'currency',
        'credits_to_add',
        'provider_order_id',
        'provider_payment_id',
        'provider_transaction_id',
        'provider_product_id',
        'provider_original_transaction_id',
        'status',
        'verification_status',
        'failure_code',
        'failure_reason',
        'idempotency_key',
        'raw_payload',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'credits_to_add' => 'integer',
        'raw_payload' => 'array',
        'paid_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (PaymentTransaction $txn): void {
            if (empty($txn->transaction_uuid)) {
                $txn->transaction_uuid = (string) Str::uuid();
            }

            if (empty($txn->credit_pack_id) && !empty($txn->plan_id)) {
                $txn->credit_pack_id = $txn->plan_id;
            }

            if (empty($txn->plan_id) && !empty($txn->credit_pack_id)) {
                $txn->plan_id = $txn->credit_pack_id;
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function creditPack(): BelongsTo
    {
        return $this->belongsTo(CreditPack::class, 'credit_pack_id');
    }

    public function plan(): BelongsTo
    {
        return $this->creditPack();
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(CreditLedger::class, 'payment_transaction_id');
    }

    public function scopeSuccessful(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SUCCESS);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeByCustomer(Builder $query, string $customerId): Builder
    {
        return $query->where('customer_id', $customerId);
    }
}
