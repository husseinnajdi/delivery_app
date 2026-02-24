<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderExchange extends Model
{
    protected $table = 'order_exchange';
    protected $fillable = [
        'order_id',
        'exchange_order_id',
        'reason',
        'customer_note',
        'internal_note',
    ];
}
