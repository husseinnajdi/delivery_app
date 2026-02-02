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
}
