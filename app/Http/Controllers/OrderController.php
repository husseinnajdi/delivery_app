<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\orders;
use App\Http\Resources\OrderResource;
use App\Models\User;
use App\Models\status;
class OrderController extends Controller
{
    public function index()
    {
        return OrderResource::collection(orders::all());
    }
    public function show($id)
    {
        $order = orders::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        $user=User::find($order->customer_id);
        $status=status::find($order->status_id);

        $orderResource=new OrderResource($order);
        $orderArray=$orderResource->toArray(request());
        $orderArray['customer']['customer_name']=$user->username;
        $orderArray['status']=$status->title;
        return response()->json($orderArray);
    }

    public function store(Request $request)
    {
        $order = orders::create($request->all());
        return new OrderResource($order);
    }

    public function assigndriver(Request $request,$id){
        $order=orders::find($id);
        if($order == null){
            return response()->json(['message' => 'Order not found'], 404);
        }
        $order->assigned_to=$request->assigned_to;
        $order->save();
        return new OrderResource($order);
    }

    public function showbystatus ($status)
    {
        $orders=orders::where('status',$status)->get();
        return OrderResource::collection($orders);
    }

    public function showbydriver($driverid)
    {
        $orders=orders::where('assigned_to',$driverid)->get();
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
