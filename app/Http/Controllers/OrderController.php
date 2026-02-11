<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\orders;
use App\Http\Resources\OrderResource;
use App\Models\User;
use App\Models\status;
use App\Services\NotificationService;
use Log;
use App\Http\Controllers\account_balances;
use App\Models\Addresse;

class OrderController extends Controller
{
    public function __construct(private NotificationService $service)
    {
    }
    private function formatOrder(orders $order)
    {
        $orderResource = new OrderResource($order);
        $orderData = $orderResource->toArray(request());
        $address = Addresse::where('customer_id', $order->customer_id)->first();
        $user = User::find($order->customer_id);
        $status = status::find($order->status_id);

        $orderData['customer']['customer_name'] = $user->username ?? 'Unknown Customer';
        $orderData['customer']['phone'] = $user->phone ?? 'Unknown Phone';
        $orderData['status'] = $status->label ?? 'Unknown Status';
        $orderData['deliveryLocation']['address'] = $order->delivery_city ?? 'Unknown Phone';
        $orderData['deliveryLocation']['link'] = $order->pickup_location_url ?? 'Unknown Link';
        return $orderData;
    }

    public function index()
    {
        $orders = orders::all();
        $ordersArray = $orders->map(fn($order) => $this->formatOrder($order));
        return response()->json($ordersArray);
    }

    public function show(Request $request)
    {
        $order = orders::find($request->order_id);
        if (!$order)
            return response()->json(['message' => 'Order not found'], 404);

        return response()->json($this->formatOrder($order));
    }

    public function store(Request $request)
    {
        $order = orders::create($request->all());
        return response()->json($this->formatOrder($order), 201);
    }

    public function assigndriver(Request $request, )
    {
        $order = orders::find($request->order_id);
        if (!$order)
            return response()->json(['message' => 'Order not found'], 404);
        $order->delivered_by = $request->delivered_by;
        $order->save();
        if ($request->delivered_by == $request->auth_user->id) {
            return response()->json([
                'message' => 'Driver assigned successfully',
                'order' => $this->formatOrder($order)
            ]);
        }
        try {
            $this->service->send(
                [$request->delivered_by],
                "New Order Assigned",
                "You have been assigned a new order with ID: " . $order->id,
                $order->id,
                $request->auth_user->id
            );
        } catch (\Exception $e) {
            Log::error('Driver assign but Failed to send notification: ' . $e->getMessage());
            return response()->json([
                'message' => 'Driver assigned but failed to send notification',
                'order' => $this->formatOrder($order)
            ], 500);
        }

        return response()->json([
            'message' => 'Driver assigned and notification sent successfully',
            'order' => $this->formatOrder($order)
        ]);
    }
    public function showallbydriver(Request $request)
    {
        $driverid = $request->auth_user->id;
        $orders = orders::where('delivery_driver_id', $driverid)->get();
        $ordersArray = $orders->map(fn($order) => $this->formatOrder($order));
        return response()->json($ordersArray);
    }


    public function showdriverarchive(Request $request)
    {
        $status = [8,10,11,12,13];
        $driverid = $request->auth_user->id;
        $orders = orders::where('delivery_driver_id', $driverid)->whereIn('status_id', $status)->paginate(10);
        $ordersArray = $orders->map(fn($order) => $this->formatOrder($order));
        return response()->json($ordersArray);
    }
    public function showbydriver(Request $request)
    {
        $status = [3,5,6,7,12];
        $driverid = $request->auth_user->id;

        $orders = orders::where('delivery_driver_id', $driverid)
            ->whereIn('status_id', $status)
            ->get();
        $ordersArray = $orders->map(fn($order) => $this->formatOrder($order));
        return response()->json($ordersArray);
    }

    // public function update(Request $request, $id)
    // {
    //     $order = orders::find($id);
    //     if (!$order)
    //         return response()->json(['message' => 'Order not found'], 404);

    //     $order->update($request->all());
    //     return response()->json($this->formatOrder($order));
    // }

    public function updatestatus(Request $request)
    {
        $order = orders::where('order_number',$request->order_number);
        if (!$order)
            return response()->json(['message' => 'Order not found', $order], 404);
        switch($request->status){
            case 'On the way':
                $status=7;
                break;
            case 'Delivered':
                $status=8;
                break;
            case 'Canceled':
                $status=9;
                break;
            case 'Delivered with exchange':
                $status=10;
                break;  
        }
        $order->status_id = $status;
        $order->save();
        if ($status == 8) {
                $accountbalance = new account_balances();
                // dd([
                //     'userId' => $request->auth_user->id,
                //     'type' => gettype($request->auth_user->id),
                //     'order_cost' => $order->product_cost,
                //     'order_cost_type' => gettype($order->product_cost)
                // ]);
                try{$accountbalance->statusupdatebalance($request->auth_user->id, $order->product_cost);
                }catch(\Exception $e){
                    Log::error('Failed to update account balance: ' . $e->getMessage());
                    return response()->json(['message' => 'Failed to update account balance'], 500);
                }
        }

        return response()->json($this->formatOrder($order));
    }

    public function destroy($id)
    {
        $order = orders::find($id);
        if (!$order)
            return response()->json(['message' => 'Order not found'], 404);

        $order->delete();
        return response()->json(['message' => 'Order deleted successfully']);
    }
}
