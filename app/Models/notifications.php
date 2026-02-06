<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class notifications extends Model
{
    protected $table = 'notifications';
    protected $fillable = [
        'type',
        'order_id',
        'body',
        'title',
        'created_at',
    ];
    protected $casts = [
        'is_read' => 'boolean',
    ];
}
