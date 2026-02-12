<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class activity_log extends Model
{
    protected $table = 'activity_logs';
    protected $fillable = [
        'user_id',
        'action',
        'description',
        'ip_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
