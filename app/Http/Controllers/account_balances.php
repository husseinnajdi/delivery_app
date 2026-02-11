<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\account_balances as AccountBalance;
use Illuminate\Support\Facades\Log;
class account_balances extends Controller
{
    public function index()
    {
        $balances = AccountBalance::all();
        return response()->json($balances);
    }

    public function statusupdatebalance($user_id, $amount)
    {
        // dd([
        //     'user_id' => $user_id,
        //     'user_id_type' => gettype($user_id),
        //     'amount' => $amount,
        //     'amount_type' => gettype($amount)
        // ]);
        $balance = \DB::table('account_balances')
        ->where('user_id',  $user_id)
        ->first();
    
    if(!$balance){
        return response()->json(['message'=>'Account balance not found'], 404);
    }
    
    // Update using DB query
    \DB::table('account_balances')
        ->where('user_id', '=', (int)$user_id)
        ->update([
            'total_balance' => $balance->total_balance + $amount
        ]);
        return $balance;
    }
    public function update(Request $request, $user_id)
    {
        $balance = AccountBalance::where('user_id', $user_id)->first();
        if (!$balance) {
            return response()->json(['message' => 'Account balance not found'], 404);
        }
        $balance->total_balance = $request->input('total_balance', $balance->total_balance);
        $balance->save();
        return response()->json($balance);
    }
}
