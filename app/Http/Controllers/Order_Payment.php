<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\order_payment as orderpayment;
class Order_Payment extends Controller
{
    public function show($id){
        return orderpayment::where('order_id',$id)->get();
    }
    public function getbyorderid($id){
        return orderpayment::where('order_id',$id)->first();
    }
    public function update(Request $request){
        $payment = orderpayment::where('order_id',$request->order_id)->first();
        if($payment){
            $payment->update([
                'amount'=>$request->amount,
                'currency'=>$request->currency,
                'status'=>$request->status,
                'transaction_id'=>$request->transaction_id
            ]);
            return response()->json(['message'=>'Payment updated successfully']);
        }
        else{
            return response()->json(['message'=>'Payment not found'],404);
        }
    }
    public function store(Request $request){
        $payment=orderpayment::create([
            'order_id'=>$request->order_id,
            'amount'=>$request->amount,
            'currency_id'=>$request->currency,
            'amount_usd'=>$request->amount_usd  ,
            'collected_at'=>$request->collected_at,
            'collected_by'=>$request->collected_by,
        ]);
        return response()->json(['message'=>'Payment created successfully','payment'=>$payment]);
    }
}
