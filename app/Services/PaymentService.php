<?php
namespace App\Services;

use App\Models\order_payment;
use Illuminate\Support\Facades\DB;
use App\Models\Orders;
class PaymentService
{
    public function createpayment($request){
        return DB::transaction(function () use($request){
            $order=orders::where('order_number',$request->order_number)->firstOrFail();
            $payment=order_payment::create([
                'order_number'=>$request->order_number,
                'amount'=>$request->amount,
                'currency_id'=>1,
                'amount_usd'=>$request->amount_usd  ,
                'collected_at'=>now(),
                'collected_by'=>$request->auth_user->id,
            ]);
            $status_id=$order->estimated_delivery<now() ? 9 : 8;
            $order->update([
                'payment_status'=>'paid',
                'actual_delivery'=>now(),
                'status_id'=>$status_id
            ]);
            return $payment;
        });
    }

}
