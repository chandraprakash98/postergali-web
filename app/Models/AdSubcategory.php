<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdSubcategory extends Model
{
    protected $fillable = [
        'type',
        'name',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope to only return active subcategories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by ad type ('job' or 'offer').
     */
    public function scopeForType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Return ordered, active subcategory names for a given type.
     *
     * @param  string  $type  'job' or 'offer'
     * @return \Illuminate\Support\Collection<string>
     */
    public static function namesForType(string $type)
    {
        return static::where('type', $type)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('name');
    }
}
