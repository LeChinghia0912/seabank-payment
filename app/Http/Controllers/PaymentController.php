<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Services\SePayService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $sePayService;

    public function __construct(SePayService $sePayService)
    {
        $this->sePayService = $sePayService;
    }

    public function showTopUpForm()
    {
        return view('payment.topup');
    }

    public function initiatePayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
        ]);

        $amount = $request->amount;
        $user = Auth::user();

        // Create Order
        $order = Order::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'status' => 'WAIT_FOR_PAYMENT',
            'payment_method' => 'SEPAY',
        ]);



        // Generate SePay QR
        $description = 'DH' . $order->id; // Short description as per example
        try {
            $qrUrl = $this->sePayService->getQrUrl($amount, $description);
            return view('payment.qa', ['qrUrl' => $qrUrl, 'order' => $order]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }


    }

    public function callback(Request $request)
    {
        Log::info('SePay Webhook Received', $request->all());

        // 1. Validate Authentication (SePay sends "Authorization: Apikey API_KEY")
        $authHeader = $request->header('Authorization');
        // Extract Key from "Apikey KEY"
        $apiKey = null;
        if ($authHeader && preg_match('/Apikey\s+(.*)/i', $authHeader, $matches)) {
            $apiKey = trim($matches[1]);
        }
        
        $content = $request->input('content');
        $transferAmount = $request->input('transferAmount');
        
        if (!preg_match('/DH(\d+)/i', $content, $matches)) {
            Log::error('SePay Webhook: Undefined Order ID in content: ' . $content);
            return response()->json(['success' => false, 'message' => 'No Order ID found'], 400); 
        }
        
        $orderId = $matches[1];
        
        // 3. Find Order
        $order = Order::find($orderId);
        
        if (!$order) {
            Log::error('SePay Webhook: Order not found: ' . $orderId);
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }
        
        // 4. Verify Amount
        if (intval($transferAmount) < intval($order->amount)) {
            Log::error("SePay Webhook: Amount mismatch. Received: $transferAmount, Order: {$order->amount}");
             return response()->json(['success' => false, 'message' => 'Amount invalid'], 400);
        }

        // 5. Update Status
        if ($order->status != 'PAID') {
            $order->update([
                'status' => 'PAID',
                'transaction_id' => $request->input('referenceCode'), // e.g. MBVCB.3278907687
            ]);

            $user = User::find($order->user_id);
            if ($user) {
                $user->balance += $order->amount;
                $user->save();
            }
        }

        return response()->json(['success' => true]);
    }
}
