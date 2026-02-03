<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\orders;
use App\Http\Resources\OrderResource;
use App\Models\User;
use App\Models\status;
use App\Http\Controllers\NotificationController;
use Log;
use App\Http\Controllers\account_balances;
class OrderController extends Controller
{
    public function index()
    {
        $orders = orders::all();

        $ordersArray = $orders->map(function ($order) {
            $orderResource = new OrderResource($order);
            $orderData = $orderResource->toArray(request());

            $user = User::find($order->customer_id);
            $status = status::find($order->status_id);

            $orderData['customer']['customer_name'] = $user ? $user->username : null;
            $orderData['status'] = $status ? $status->name : null;

            return $orderData;
        });

        return response()->json($ordersArray);
    }

    public function show($id)
    {
        $order = orders::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        $user = User::find($order->customer_id);
        $status = status::find($order->status_id);

        $orderResource = new OrderResource($order);
        $orderArray = $orderResource->toArray(request());
        $orderArray['customer']['customer_name'] = $user->username;
        $orderArray['status'] = $status->name;
        return response()->json($orderArray);
    }

    public function store(Request $request)
    {
        $order = orders::create($request->all());
        return new OrderResource($order);
    }

    public function assigndriver(Request $request, $id)
    {
        $order = orders::find($id);
        if ($order == null) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        $order->delivered_by = $request->delivered_by;
        $order->save();
        try {
            $notificationController = new NotificationController();
            $notificationController->notificationsend(
                [$request->delivered_by],
                "New Order Assigned",
                "You have been assigned a new order with ID: " . $order->id
            );
        } catch (\Exception $e) {
            Log::error('driver assign but Failed to send notification: ' . $e->getMessage());
        }
        return response()->json(['message' => 'Driver assigned and notification sent successfully', 'order' => new OrderResource($order)]);
    }

    public function showbystatus($status)
    {
        $orders = orders::where('status_id', $status)->get();
        return OrderResource::collection($orders);
    }

    public function showallbydriver(Request $request){
        $driverid = $request->auth_user->id;
        $orders = orders::where('delivered_by', $driverid)->get();
        return OrderResource::collection($orders);
    }
    public function showbydriver(Request $request)
    {
        $status=[2,3,4,5,6,7];
        $driverid = $request->auth_user->id;
        $orders = orders::where('delivered_by', $driverid)->whereIn('status_id',$status)->get();
        return OrderResource::collection($orders);
    }

    public function update(Request $request, $id)
    {
        $order = orders::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        $order->update($request->all());
        return new OrderResource($order);
    }
    public function updatestatus(Request $request, $id)
    {
        $order = orders::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        $order->status = $request->status;
        $order->save();
        $accountbalance=new account_balances();
        if($request->status==6){
            $accountbalance->statusupdatebaance($order->deliverd_by,-$order->order_cost);
        }
        return new OrderResource($order);
    }
    public function destroy($id)
    {
        $order = orders::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        $order->delete();
        return response()->json(['message' => 'Order deleted successfully']);
    }
}
