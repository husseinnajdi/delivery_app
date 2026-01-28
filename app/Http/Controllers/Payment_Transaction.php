<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\payment_transaction as paymenttransaction;

class Payment_Transaction extends Controller
{
    public function show($id){
        return paymenttransaction::where('order_id',$id)->get();
    }
    public function update(Request $request, $id){
        $payment = paymenttransaction::where('order_id',$id)->first();
        if($payment){
            $payment->update($request->all());
            return response()->json(['message' => 'Payment transaction updated successfully', 'payment_transaction' => $payment], 200);
        }else{
            return response()->json(['message' => 'Payment transaction not found'], 404);
        }
    }
}
