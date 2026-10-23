<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CustomerAccessToken extends Model
{
    protected $fillable = [
        'customer_id',
        'token_hash',
        'name',
        'last_used_at',
        'expires_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    /**
     * Create a new personal access token for a customer.
     * Returns an array containing the plaintext token and the created model instance.
     *
     * @return array{plainTextToken: string, accessToken: self}
     */
    public static function createToken(Customer $customer, string $name = 'mobile_app', ?\DateTimeInterface $expiresAt = null): array
    {
        $plainTextToken = 'pg_' . Str::random(40);
        $tokenHash = hash('sha256', $plainTextToken);

        $token = self::create([
            'customer_id' => $customer->customer_id,
            'token_hash' => $tokenHash,
            'name' => $name,
            'expires_at' => $expiresAt ?? now()->addDays(90),
        ]);

        return [
            'plainTextToken' => $plainTextToken,
            'accessToken' => $token,
        ];
    }

    /**
     * Find a valid access token by plaintext token.
     */
    public static function findValidToken(string $plainTextToken): ?self
    {
        $tokenHash = hash('sha256', $plainTextToken);

        $token = self::with('customer')
            ->where('token_hash', $tokenHash)
            ->first();

        if (!$token) {
            return null;
        }

        if ($token->expires_at && $token->expires_at->isPast()) {
            return null;
        }

        return $token;
    }
}
