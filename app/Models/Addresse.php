<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Addresse extends Model
{
    protected $table = 'customer_locations';

    protected $fillable = [
        'customer_id',
        'street',
        'city',
        'street',
        'building',
        'apartment',
        'floor',
        'location_url',
        'is_default',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
