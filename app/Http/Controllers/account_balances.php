<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\account_balances as AccountBalance;
class account_balances extends Controller
{
    public function index(){
        $balances = AccountBalance::all();
        return response()->json($balances);
    }

    public function statusupdatebaance($user_id,$amount){
        $balance = AccountBalance::where('user_id',$user_id)->first();
        if(!$balance){
            return response()->json(['message'=>'Account balance not found'],404);
        }
        $balance->total_balance += $amount;
        $balance->save();
        return response()->json($balance);
    }
    public function update(Request $request,$user_id){
        $balance = AccountBalance::where('user_id',$user_id)->first();
        if(!$balance){
            return response()->json(['message'=>'Account balance not found'],404);
        }
        $balance->total_balance = $request->input('total_balance',$balance->total_balance);
        $balance->save();
        return response()->json($balance);
    }
}
