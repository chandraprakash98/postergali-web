<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'customer_id',
        'mobile',
        'fcm',
    ];

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
