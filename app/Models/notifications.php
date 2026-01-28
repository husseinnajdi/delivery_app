<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class notifications extends Model
{
    protected $table = 'notifications';
    protected $fillable = [
        'id',
        'user_id',
        'type',
        'body',
        'is_read',
        'title',
        'created_at',
    ];
}
