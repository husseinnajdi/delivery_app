<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class customers extends Model
{
    protected $table = 'customers';
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'street',
        'building',
        'apartment',
        'floor',
        'location_url',
        'notes',
        'status',
        'created_by',
    ];
}
