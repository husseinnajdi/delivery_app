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
    private function formatOrder(orders $order)
    {
        $orderResource = new OrderResource($order);
        $orderData = $orderResource->toArray(request());

        $user = User::find($order->customer_id);
        $status = status::find($order->status_id);

        $orderData['customer']['customer_name'] = $user ? $user->username : null;
        $orderData['status'] = $status ? $status->name : "hefwijfiewj";

        return $orderData;
    }

    public function index()
    {
        $orders = orders::all();
        $ordersArray = $orders->map(fn($order) => $this->formatOrder($order));
        return response()->json($ordersArray);
    }

    public function show($id)
    {
        $order = orders::find($id);
        if (!$order) return response()->json(['message' => 'Order not found'], 404);

        return response()->json($this->formatOrder($order));
    }

    public function store(Request $request)
    {
        $order = orders::create($request->all());
        return response()->json($this->formatOrder($order), 201);
    }

    public function assigndriver(Request $request, $id)
    {
        $order = orders::find($id);
        if (!$order) return response()->json(['message' => 'Order not found'], 404);

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
            Log::error('Driver assign but Failed to send notification: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Driver assigned and notification sent successfully',
            'order' => $this->formatOrder($order)
        ]);
    }

    public function showbystatus($status)
    {
        $orders = orders::where('status_id', $status)->get();
        $ordersArray = $orders->map(fn($order) => $this->formatOrder($order));
        return response()->json($ordersArray);
    }

    public function showallbydriver(Request $request)
    {
        $driverid = $request->auth_user->id;
        $orders = orders::where('delivered_by', $driverid)->get();
        $ordersArray = $orders->map(fn($order) => $this->formatOrder($order));
        return response()->json($ordersArray);
    }


    public function showdriverarchive(Request $request){
        $status=[6,8,10,13];
        $driverid=$request->auth_user->id;
        $orders=orders::where('delivered_by',$driverid)->whereIn('status_id',$status)->get();
        $ordersArray = $orders->map(fn($order) => $this->formatOrder($order));
        return response()->json($ordersArray);
    }
    public function showbydriver(Request $request)
    {
        $status = [2,4,5,12];
        $driverid = $request->auth_user->id;

        $orders = orders::where('delivered_by', $driverid)
                        ->whereIn('status_id', $status)
                        ->get();
        $ordersArray = $orders->map(fn($order) => $this->formatOrder($order));
        return response()->json($ordersArray);
    }

    public function update(Request $request, $id)
    {
        $order = orders::find($id);
        if (!$order) return response()->json(['message' => 'Order not found'], 404);

        $order->update($request->all());
        return response()->json($this->formatOrder($order));
    }

    public function updatestatus(Request $request, $id)
    {
        $order = orders::find($id);
        if (!$order) return response()->json(['message' => 'Order not found'], 404);

        $order->status = $request->status;
        $order->save();

        $accountbalance = new account_balances();
        if ($request->status == 6) {
            $accountbalance->statusupdatebaance($order->delivered_by, -$order->order_cost);
        }

        return response()->json($this->formatOrder($order));
    }

    public function destroy($id)
    {
        $order = orders::find($id);
        if (!$order) return response()->json(['message' => 'Order not found'], 404);

        $order->delete();
        return response()->json(['message' => 'Order deleted successfully']);
    }
}
