<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UgcReport extends Model
{
    protected $fillable = [
        'content_id',
        'content_type',
        'reason',
        'details',
        'author_id',
    ];
}
