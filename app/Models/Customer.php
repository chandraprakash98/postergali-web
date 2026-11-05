<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    public const LANG_EN = 'en';
    public const LANG_HI = 'hi';
    public const ALLOWED_LANGUAGES = ['en', 'hi'];

    protected $fillable = [
        'customer_id',
        'mobile',
        'fcm',
        'language',
        'influencer_bonus_coupon_id',
    ];

    /**
     * Normalize language string to supported languages ('en' or 'hi'), defaulting to 'en'.
     */
    public static function normalizeLanguage(?string $lang): string
    {
        $lang = strtolower(trim((string) $lang));
        return in_array($lang, self::ALLOWED_LANGUAGES, true) ? $lang : self::LANG_EN;
    }

    /**
     * Resolve customer by phone number variants.
     */
    public static function resolveByPhone(?string $phoneNumber): ?self
    {
        if (empty($phoneNumber)) {
            return null;
        }
        $raw = trim((string) $phoneNumber);
        $normalized = preg_replace('/[^0-9]/', '', $raw);

        $query = self::where('mobile', $raw);
        if ($normalized !== '') {
            $query->orWhere('mobile', $normalized)
                  ->orWhere('mobile', '+' . $normalized);
            if (strlen($normalized) >= 10) {
                $last10 = substr($normalized, -10);
                $query->orWhere('mobile', $last10)
                      ->orWhere('mobile', '+91' . $last10)
                      ->orWhere('mobile', '91' . $last10)
                      ->orWhere('mobile', '0' . $last10);
            }
        }
        return $query->first();
    }

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
