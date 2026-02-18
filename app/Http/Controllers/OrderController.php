<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\orders;
use App\Services\NotificationService;
use Log;
use App\Http\Controllers\account_balances;
use App\Services\OrderServices;
use App\Services\ActivityLog;
use App\Services\PaymentService;
class OrderController extends Controller
{
    public function __construct(private account_balances $account_balances,private OrderServices $orderservice,private NotificationService $service, private ActivityLog $activityLog, private PaymentService $paymentService)
    {
    }
    public function index()
    {
        $orders = orders::all();
        $ordersArray = $orders->map(fn($order) => $this->orderservice->formatOrder($order));
        return response()->json($ordersArray);
    }



    public function show(Request $request)
    {
        $order=$this->orderservice->getorderbyid($request->order_id);
        if (!$order)
            return response()->json(['message' => 'Order not found'], 404);

        return response()->json($this->orderservice->formatOrder($order));
    }



    public function store(Request $request)
    {
        $order = orders::create($request->all());
        return response()->json($this->orderservice->formatOrder($order), 201);
    }




    public function assigndriver(Request $request, )
    {
        $order = $this->orderservice->getorderbyid($request->order_id);
        if (!$order)
            return response()->json(['message' => 'Order not found'], 404);
        $order->delivered_by = $request->delivered_by;
        $order->save();
        if ($request->delivered_by == $request->auth_user->id) {
            return response()->json([
                'message' => 'Driver assigned successfully',
                'order' => $this->orderservice->formatOrder($order)
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
                'order' => $this->orderservice->formatOrder($order)
            ], 500);
        }

        return response()->json([
            'message' => 'Driver assigned and notification sent successfully',
            'order' => $this->orderservice->formatOrder($order)
        ]);
    }



    public function showdriverarchive(Request $request)
    {
        $status = [8, 10, 11, 12, 13];
        $driverid = $request->auth_user->id;
        $orders = orders::where('delivery_driver_id', $driverid)->whereIn('status_id', $status)->paginate(10);
        $ordersArray = $orders->map(fn($order) => $this->orderservice->formatOrder($order));
        return response()->json($ordersArray);
    }



    public function showbydriver(Request $request)
    {
        $status = [3, 5, 6, 7, 12];
        $driverid = $request->auth_user->id;

        $orders = orders::where('delivery_driver_id', $driverid)
            ->whereIn('status_id', $status)
            ->paginate(10);
        $ordersArray = $orders->map(fn($order) => $this->orderservice->formatOrder($order));
        return response()->json($ordersArray);
    }



    public function updatestatus(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
            'status' => 'required|string|in:On the way,Delivered,Canceled,Delivered with exchange'
        ]);
        $order = orders::where('order_number', $request->order_number)->first();
        if (!$order)
            return response()->json(['message' => 'Order not found', $order], 404);
        switch ($request->status) {
            case 'On the way':
                $status = 7;
                break;
            case 'Delivered':
                $status = 8;
                break;
            case 'Canceled':
                $status = 9;
                break;
            case 'Delivered with exchange':
                $status = 10;
                break;
        }
        if(today()->greaterThan($order->estimated_delivery)){
            $status = 10; 
        }
        $order->actual_delivery = now();
        $order->status_id = $status;
        $order->save();
        $this->activityLog->log($request->auth_user->id, 
        'update_status', 'Status of order with number: '. $request->order_number .' updated to ' . $request->status, 
        '1');   
        if ($status == 8 || $status == 10) {
            try {
                $this->paymentService->createpayment(new Request([
                    'order_number' => $order->order_number,
                    'amount' => $order->amount_lbp,
                    'currency_id' => 1,
                    'amount_usd' => $order->amount_usd,
                    'auth_user' => $request->auth_user
                ]));
                $this->account_balances->statusupdatebalance($request->auth_user->id, $order->product_cost);
            } catch (\Exception $e) {
                Log::error('Failed to update account balance: ' . $e->getMessage());
                return response()->json(['message' => 'Failed to update account balance'], 500);
            }
        }
        return response()->json($this->orderservice->formatOrder($order));
    }




    public function destroy($id)
    {
        $order =$this->orderservice->getorderbyid($id);
        if (!$order)
            return response()->json(['message' => 'Order not found'], 404);

        $order->delete();
        return response()->json(['message' => 'Order deleted successfully']);
    }


}
