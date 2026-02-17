<?php
namespace App\Services;
use App\Models\orders;
use App\Models\User;
Use App\Models\status;
use App\Http\Resources\OrderResource;
class OrderServices
{
    public function formatOrder(orders $order): array
    {
        $user = User::find($order->customer_id);
        $status = status::find($order->status_id);
        
        $orderResourse=new OrderResource($order);
        $orderData=$orderResourse->toArray(request());
        $orderData['customer']['customer_name'] = $user->username ?? 'Unknown Customer';
        $orderData['customer']['phone'] = $user->phone ?? 'Unknown Phone';
        $orderData['status'] = $status->label ?? 'Unknown Status';
        $orderData['deliveryLocation']['address'] = $order->delivery_city ?? 'Unknown Phone';
        $orderData['deliveryLocation']['link'] = $order->pickup_location_url ?? 'Unknown Link';
        return $orderData;
    }

    public function getorderbyid($order_id)
    {
        $order = orders::find($order_id);
        if (!$order) {
            return null;
        }
        return $order;
    }

}