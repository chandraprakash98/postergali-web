<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_title',
        'duration',
        'price',
    ];

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'plan_id');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class, 'plan_id');
    }
}