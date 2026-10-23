<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'customer_id',
        'mobile',
        'fcm',
        'influencer_bonus_coupon_id',
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'customer_id', 'customer_id');
    }

    public function creditWallet(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CustomerCredit::class, 'customer_id', 'customer_id');
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class, 'customer_id', 'customer_id');
    }

    public function creditLedger(): HasMany
    {
        return $this->hasMany(CreditLedger::class, 'customer_id', 'customer_id');
    }

    public function accessTokens(): HasMany
    {
        return $this->hasMany(CustomerAccessToken::class, 'customer_id', 'customer_id');
    }

    protected static function booted(): void
    {
        static::creating(function (Customer $customer): void {
            if (empty($customer->customer_id)) {
                do {
                    $sequence = random_int(1, 999999);
                    $customer->customer_id = 'PSTGL' . str_pad((string) $sequence, 5, '0', STR_PAD_LEFT);
                } while (self::where('customer_id', $customer->customer_id)->exists());
            }
        });
    }
}
