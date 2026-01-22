<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'adress',
        'email',
        'phone',
        'city',
        'user_id',
    ];
}
