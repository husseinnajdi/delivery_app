<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class notifications extends Model
{
    protected $table = 'notification_messages';
    protected $fillable = [
        'type',
        'sender_user_id',
        'order_id',
        'message',
        'title',
        'created_at',
    ];
    protected $casts = [
        'is_read' => 'boolean',
    ];
}
