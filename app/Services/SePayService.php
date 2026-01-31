<?php

namespace App\Services;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class SePayService
{
    protected $merchantId;
    protected $secretKey;
    protected $bankAccount;
    protected $bankCode;

    public function __construct()
    {
        $this->merchantId = env('SEPAY_MERCHANT_ID');
        $this->secretKey = env('SEPAY_SECRET_KEY');
        $this->bankAccount = '96247ENRQO'; // From User Request
        $this->bankCode = 'BIDV';          // From User Request
    }

    public function getQrUrl($amount, $description)
    {
        // https://qr.sepay.vn/img?acc=96247ENRQO&bank=BIDV&amount=100000&des=DH102969
        $query = http_build_query([
            'acc' => $this->bankAccount,
            'bank' => $this->bankCode,
            'amount' => $amount,
            'des' => $description,
        ]);
        return "https://qr.sepay.vn/img?" . $query;
    }




    public function createPaymentLink($amount, $orderId, $description = 'Payment for order')
    {
        // $url = 'https://pay-sandbox.sepay.vn/v1/checkout/init';
        
        $data = [
            'merchant' => $this->merchantId,
            'currency' => 'VND',
            'order_amount' => (string)intval($amount),
            'operation' => 'PURCHASE',
            'order_description' => $description,
            'order_invoice_number' => (string)$orderId,
            'success_url' => route('dashboard'),
            'error_url' => route('dashboard'),
            'cancel_url' => route('dashboard'),
        ];
        
        $signature = $this->createSignature($data);
        $data['signature'] = $signature;
        
        return $data;
    }


    public function createSignature($data)
    {
        $rawData = [];
        foreach ($data as $key => $value) {
            if ($key !== 'signature' && $value !== null && $value !== '') {
                $rawData[] = $key . '=' . $value;
            }
        }
        $payload = implode('&', $rawData);
        
        return hash_hmac('sha256', $payload, $this->secretKey);
    }

    public function verifyCallback($data)
    {
        if (!isset($data['signature'])) {
            return false;
        }

        $receivedSignature = $data['signature'];
        unset($data['signature']);
        
        ksort($data);
        
        $rawData = [];
        foreach ($data as $key => $value) {
             $rawData[] = $key . '=' . $value;
        }
        $payload = implode('&', $rawData);
        
        $calculatedSignature = hash_hmac('sha256', $payload, $this->secretKey);

        return hash_equals($calculatedSignature, $receivedSignature);
    }
}
