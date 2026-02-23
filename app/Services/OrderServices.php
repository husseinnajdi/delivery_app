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
        $original_order=orders::find($orderexchange->order_id);
        $final_cost=$total_cost-($original_order->product_cost+$original_order->delivery_fee);
        return $final_cost;
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