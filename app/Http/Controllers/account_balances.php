<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\account_balances as AccountBalance;
use Illuminate\Support\Facades\Log;
use App\Services\ActivityLog;
class account_balances extends Controller
{
    public function __construct(private ActivityLog $activityLog)
    {
    }
    public function index()
    {
        $balances = AccountBalance::all();
        return response()->json($balances);
    }

    public function statusupdatebalance($user_id, $amount)
    {
        log::info("Updating balance for user_id: $user_id, amount: $amount");
        $balance = \DB::table('account_balances')
            ->where('user_id', $user_id)
            ->first();

        if (!$balance) {
            log::info("Account balance not found for user_id: $user_id");
            \DB::table('account_balances')->insert([
                'user_id' => $user_id,
                'currency_id'   => 1,
                'total_balance' => $amount,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        try {
            \DB::table('account_balances')
                ->where('user_id', '=', (int) $user_id)
                ->update([
                    'total_balance' => $balance->total_balance + $amount
                ]);
            log::info("Updated balance for user_id: $user_id, amount: $amount, new_balance: " . ($balance->total_balance + $amount));
        } catch (\Exception $e) {
            log::error("Failed to update balance for user_id: $user_id, amount: $amount, error: " . $e->getMessage());
            return response()->json(['message' => 'Failed to update balance', 'error' => $e->getMessage()], 500);
        }
        $this->activityLog->log($user_id, 'balance update', "User balance updated by $amount", '1');
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
