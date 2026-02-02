<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class account_balances extends Model
{
    protected $table=[
        'user_id',
        'currency_id',
        'total_balance',    
        'due_balance',
        'paid_balance',
    ];
}
