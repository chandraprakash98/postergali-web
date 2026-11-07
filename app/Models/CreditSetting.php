<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditSetting extends Model
{
    use HasFactory;

    protected $table = 'credit_settings';

    protected $fillable = [
        'signup_credit',
        'otp_credit',
        'referral_credit',
        'updated_by',
    ];

    protected $casts = [
        'signup_credit'   => 'float',
        'otp_credit'      => 'float',
        'referral_credit' => 'float',
    ];

    /**
     * Retrieve the singleton settings record or initialize with defaults.
     */
    public static function getSettings(): self
    {
        try {
            $setting = static::query()->latest('id')->first();
            if (!$setting) {
                $setting = static::create([
                    'signup_credit'   => 1000.00,
                    'otp_credit'      => 1000.00,
                    'referral_credit' => 100.00,
                ]);
            }
            return $setting;
        } catch (\Throwable $e) {
            return new static([
                'signup_credit'   => 1000.00,
                'otp_credit'      => 1000.00,
                'referral_credit' => 100.00,
            ]);
        }
    }

    /**
     * Scenario 1: New Customer Sign-Up / Check-in (API)
     */
    public static function getSignupCredit(): float
    {
        try {
            $val = static::query()->latest('id')->value('signup_credit');
            return $val !== null ? (float) $val : 1000.00;
        } catch (\Throwable $e) {
            return 1000.00;
        }
    }

    /**
     * Scenario 2: OTP Verification / First Login
     */
    public static function getOtpCredit(): float
    {
        try {
            $val = static::query()->latest('id')->value('otp_credit');
            return $val !== null ? (float) $val : 1000.00;
        } catch (\Throwable $e) {
            return 1000.00;
        }
    }

    /**
     * Scenario 5: Referral Bonus Upon Poster Approval
     */
    public static function getReferralCredit(): float
    {
        try {
            $val = static::query()->latest('id')->value('referral_credit');
            return $val !== null ? (float) $val : 100.00;
        } catch (\Throwable $e) {
            return 100.00;
        }
    }
}
