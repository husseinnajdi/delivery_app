<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'orderId' => $this->id,
            'orderNumber' => $this->order_number,
            'orderCost' => $this->order_cost,
            'status' => $this->status,
            'priority' => $this->priority,
            'paymentStatus' => $this->payment_status,
            'createdAt' => $this->created_at,
            'deliveryFee' => $this->delivery_fee,   
            'total' => $this->order_cost + $this->delivery_fee,
            'estimated_delivery_time' => $this->estimated_delivery,
            'actual_delivery_time' => $this->actual_delivery,
            'customer' => [
                'id' => $this->customer_id,
                'customer_name' => $this->customer_name,
                'phone' => $this->customer_phone,
            ],
            'pickup' => [
                'address' => $this->pickup_address,
                'phone' => $this->pickup_phone,
            ],
            'deliveryLocation' => [
                'address' => $this->customer_address,
                'link' => $this->location_link,
            ],
            'package' => [
                'description' => $this->package_description,
                'weight' => $this->package_weight,
                'specialInstructions' => $this->special_instructions,
            ],
        ];
    }
}
