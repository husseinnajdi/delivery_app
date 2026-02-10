<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class customers extends Model
{
    protected $table = 'customers';
    protected $fillable = [
        'id',
        'shop_id',
        'name',
        'email',
        'phone',
        'notes',
        'address_id',
        'status',
        'created_by',

    ];
}
