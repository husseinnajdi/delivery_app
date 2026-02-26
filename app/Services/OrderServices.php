<?php
namespace App\Services;
use App\Models\orders;
use App\Models\User;
Use App\Models\status;
use App\Http\Resources\OrderResource;
use App\Models\OrderExchange;
class OrderServices
{
    public function formatOrder(orders $order): array
    {
        $user = User::find($order->customer_id);
        $status = status::find($order->status_id);
        $shop=User::find($order->shop_id);
        $orderResourse=new OrderResource($order);
        $orderData=$orderResourse->toArray(request());
        $orderData['customer']['customer_name'] = $user->username ?? 'Unknown Customer';
        $orderData['pickup']['shop_name']=$shop->username ??'';
        $orderData['customer']['phone'] = $user->phone ?? 'Unknown Phone';
        $orderData['status'] = $status->label ?? 'Unknown Status';
        $orderData['deliveryLocation']['address'] = $order->delivery_city ?? 'Unknown Phone';
        $orderData['deliveryLocation']['link'] = $order->pickup_location_url ?? 'Unknown Link';
        if($order->type=="exchange"){
            $orderData['total']=$this->orderexchange($order->id,$orderData['total']);
        }
        return $orderData;
    }

    public function orderexchange($order_id,$total_cost){
        $orderexchange= OrderExchange::where('replacement_order',$order_id)->first();
        if(!$orderexchange){
            return null;
        }
        $original_order=orders::find($orderexchange->original_order);
        $final_cost=$total_cost-$original_order->product_cost;
        return $final_cost;
    }

public function orderstatus($order_status)
{
    switch ($order_status) {
        case 'Pickup Assigned':
            return 3;
        case 'Picked Up':
            return 4;
        case 'At Warehouse':
            return 5;
        case 'Delivery Assigned':
            return 6;
        case 'Out for Delivery':
            return 7;
        case 'Delivered':
            return 8;
        case 'Canceled':
            return 9;
        case 'Delivered with exchange':
            return 10;
        case 'Exchange Collected':
            return 15;
        case 'Exchange Delivery Assigned':
            return 16;
        case 'Exchange Delivered':
            return 17;

        default:
            return null;
    }
}

    public function getorderbyid($order_id)
    {
        $order = orders::find($order_id);
        if (!$order) {
            return null;
        }
        return $order;
    }

    public function getorderbynumber($order_number)
    {
        $order = orders::where('order_number', $order_number)->first();
        if (!$order) {
            return null;
        }
        return $order;
    }
    public function getorderbystatuses($status_id,$driverid){
        return Orders::select('id','order_number','status_id','type',
        'priority','payment_status','estimated_delivery','actual_delivery',
        'created_at','product_cost','delivery_fee','customer_id','shop_id',
        'delivery_city','pickup_location_url','package_description',
        'package_weight','special_instructions','pickup_phone','pickup_address')
        ->where(function($query) use ($driverid) {
                $query->where('delivery_driver_id', $driverid)
                      ->orWhere('pickup_driver_id', $driverid);
            })
            ->whereIn('status_id', $status_id)->orderByRaw("FIELD(priority, 'urgent', 'high', 'normal', 'low')")
            ->paginate(10);
    }

}