<?php
namespace App\Services;

use App\Models\order_payment;
use Illuminate\Support\Facades\DB;
use App\Models\orders;
use App\Models\account_balances;
class PaymentService
{
    public function getorderpayment($id){
    return order_payment::find($id);
}
    public function createpayment($request){
        return DB::transaction(function () use($request){
            $order=orders::where('order_number',$request->order_number)->firstOrFail();
            $payment=order_payment::create([
                'order_number'=>$request->order_number,
                'order_id'=>$order->id,
                'amount'=>$request->amount,
                'currency_id'=>1,
                'amount_usd'=>$request->amount_usd  ,
                'collected_at'=>now(),
                'collected_by'=>$request->auth_user->id,
            ]);
            $status_id=$order->estimated_delivery<now() ? 10 : 8;
            $status_id=8;
            $order->update([
                'payment_status'=>'paid',
                'actual_delivery'=>now(),
                'status_id'=>$status_id
            ]);
            $account_balance=account_balances::where('user_id',$order->delivery_driver_id)->first();
            if($account_balance){
                $account_balance->total_balance += $request->amount;
                $account_balance->save();
            }else{
                account_balances::create([
                    'user_id'=>$order->delivery_driver_id,
                    'total_balance'=>$request->amount
                ]);
            }
            return $payment;
        });
    }

}
