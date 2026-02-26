<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\order_payment as orderpayment;
use App\Services\PaymentService;
class Order_Payment extends Controller
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
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
        return response()->json(['message' => 'Payment created successfully', 'data' => $payment]);
    }
}
