<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class status extends Model
{
    protected $table = 'statuses';
    protected $fillable = [
        'id',
        'name',
        'lsbel',
        'badge_class',
        'color',
        'sort_order',
        'is_active',
    ];
}
