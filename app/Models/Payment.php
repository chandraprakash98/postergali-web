<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'transaction_id',
        'job_or_offer_id',
        'item_type',
        'credit_mode',
        'amount',
    ];
}
