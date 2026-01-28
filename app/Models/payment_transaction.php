<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class payment_transaction extends Model
{
    protected $fillable = [
        'order_id',
        'payment_method',
        'amount',
        'currency',
        'status',
        'payment_details',
    ];
}
