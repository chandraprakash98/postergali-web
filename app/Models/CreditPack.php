<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CreditPack extends Model
{
    use HasFactory;

    protected $table = 'credit_packs';

    protected $fillable = [
        'name',
        'pack_code',
        'product_id',
        'credits',
        'price',
        'currency',
        'is_active',
        'sort_order',
    ];

    protected $appends = [
        'plan_title',
        'plan_code',
    ];

    protected $casts = [
        'credits' => 'integer',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope for active packages.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Backward-compatibility accessor for plan_title.
     */
    public function getPlanTitleAttribute(): string
    {
        return (string) ($this->attributes['name'] ?? '');
    }

    /**
     * Backward-compatibility accessor for plan_code.
     */
    public function getPlanCodeAttribute(): string
    {
        return (string) ($this->attributes['pack_code'] ?? '');
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class, 'credit_pack_id');
    }
}
