<?php

namespace App\Models;

use Database\Factories\PlanFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    /** @use HasFactory<PlanFactory> */
    use HasFactory;

    public const TYPE_CONSUMABLE = 'CONSUMABLE';
    public const TYPE_POSTER_DURATION = 'POSTER_DURATION';

    public const SUPPORTED_TYPES = [
        self::TYPE_CONSUMABLE,
        self::TYPE_POSTER_DURATION,
    ];

    protected $fillable = [
        'plan_title',
        'plan_code',
        'product_id',
        'credits',
        'price',
        'currency',
        'plan_type',
        'duration',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'credits' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class, 'plan_id');
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'plan_id');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class, 'plan_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeConsumable(Builder $query): Builder
    {
        return $query->where('plan_type', self::TYPE_CONSUMABLE);
    }

    public function scopeDurationTiers(Builder $query): Builder
    {
        return $query->where('plan_type', self::TYPE_POSTER_DURATION);
    }
}