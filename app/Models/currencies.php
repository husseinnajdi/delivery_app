<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class currencies extends Model
{
    protected $table = 'currencies';

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'rate_to_usd',
        'is_base',
        'status',
    ];
}
