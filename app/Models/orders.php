<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class orders extends Model
{
    protected $fillable = [
        'order_number',
        'shop_id',
        'customer_id',
        'pickup_address',
        'pickup_phone',
        'customer_address',
        'customer_phone',
        'package_description',
        'package_weight',
        'order_cost',
        'estimated_delivery',
        'location_link',
        'special_instructions',
        'actual_delivery',
        'priority',
        'status_id',
        'payment_status',
        'delivery_fee',
        'assigned_to',
        'created_by',
        'confirmed_by',
    ];
}
