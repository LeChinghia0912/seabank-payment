<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// --- SIMULATION SCRIPT ---
use App\Models\Order;
use App\Services\SePayService;
use Illuminate\Support\Facades\Http;

echo "--- Simulating SePay IPN Webhook ---\n";

// 1. Get the latest pending order
$order = Order::where('status', 'WAIT_FOR_PAYMENT')->latest()->first();

if (!$order) {
    die("No pending order found to simulate!\n");
}

echo "Found Pending Order ID: {$order->id} - Amount: {$order->amount}\n";

$service = new SePayService();

// 2. Prepare Callback Data (Transaction Notification)
$callbackData = [
    "id" => 92704,
    "gateway" => "Vietcombank",
    "transactionDate" => date('Y-m-d H:i:s'),
    "accountNumber" => "96247ENRQO",
    "code" => null,
    "content" => "Thanh toan DH" . $order->id, // Content contains Order ID matches PaymentController Regex /DH(\d+)/
    "transferType" => "in",
    "transferAmount" => $order->amount,
    "accumulated" => 19077000,
    "subAccount" => null,
    "referenceCode" => "MBVCB." . time(),
    "description" => "Test Simulation"
];

echo "Generated Payload:\n";
print_r($callbackData);

// 3. Send Request to Localhost
$response = Http::withHeaders([
    'Authorization' => 'Apikey ' . env('SEPAY_API_KEY', 'TEST_API_KEY'),
    'Content-Type' => 'application/json'
])->post('http://localhost:8000/payment/callback', $callbackData);

echo "\n--- Response ---\n";
echo "Status: " . $response->status() . "\n";
echo "Body: " . $response->body() . "\n";

if ($response->successful()) {
    echo "SUCCESS! Order {$order->id} should be PAID now.\n";
} else {
    echo "FAILED! Check logs.\n";
}
