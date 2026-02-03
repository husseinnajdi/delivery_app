<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class notification_users extends Model
{
    protected $table = 'notification_users';

    protected $fillable = [
        'notification_id',
        'user_id',
        'is_read',
    ];
}
