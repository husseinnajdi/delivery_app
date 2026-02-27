<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\order_payment as orderpayment;
use App\Services\PaymentService;
use App\Http\Controllers\account_balances;
class Order_Payment extends Controller
{
    private PaymentService $paymentService;
    private account_balances $accountBalances;

    public function __construct(PaymentService $paymentService, account_balances $account_balances)
    {
        $this->paymentService = $paymentService;
        $this->accountBalances = $account_balances;
    }

    public function show($id)
    {
        return $this->paymentService->getorderpayment($id);
    }
    public function update(Request $request)
    {
        $payment = $this->paymentService->getorderpayment($request->id);
        if ($payment) {
            $payment->update([
                'amount' => $request->amount,
                'currency' => $request->currency,
                'status' => $request->status,
                'transaction_id' => $request->transaction_id
            ]);
            return response()->json(['message' => 'Payment updated successfully']);
        } else {
            return response()->json(['message' => 'Payment not found'], 404);
        }
    }
    public function store(Request $request)
    {
        $payment = $this->paymentService->createpayment($request);
        if ($payment) {
            try {
                log::info("Payment created successfully, updating balance for user_id: $request->user_id, amount: $request->amount_usd, amount_lbp: $request->amount_lbp");
                $this->accountBalances->statusupdatebalance(
                    $request->auth_user->id,
                    $request->amount_usd,
                    $request->amount_lbp
                );
            } catch (\Exception $e) {
                return response()->json(['message' => 'Payment created but failed to update balance', 'error' => $e->getMessage()], 500);
            }
        }

        return response()->json(['message' => 'Payment created successfully', 'data' => $payment]);
    }
}
