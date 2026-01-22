<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class status extends Model
{
    protected $table = 'status';
    protected $fillable = [
        'id',
        'title',
        'description',
    ];
}
