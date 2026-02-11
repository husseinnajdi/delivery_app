<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class account_balances extends Model
{
    protected $table = 'account_balances';
    protected $fillable=[
        'user_id',
        'order_number', 
        'currency_id',
        'total_balance',    
        'due_balance',
        'paid_balance',
    ];
}
