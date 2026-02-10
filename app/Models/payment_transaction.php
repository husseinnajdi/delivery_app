<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class payment_transaction extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'currency_id',
        'transaction_type',
        'reference_type',
        'reference_id',
        'description',
        'amount',
    ];
}
