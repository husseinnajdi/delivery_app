<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class order_payment extends Model
{
    protected $fillable = [
        'order_number',
        'currency_id',
        'amount',
        'amount_usd',
        'collected_at',
        'collected_by'
    ];
}
