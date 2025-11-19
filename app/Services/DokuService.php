<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DokuService
{
    private $clientId;
    private $secretKey;
    private $url;

    public function __construct()
    {
        $this->clientId  = env('DOKU_CLIENT_ID');
        $this->secretKey = env('DOKU_SECRET_KEY');
        $this->url       = "https://api-sandbox.doku.com/checkout/v1/payment";
    }

    private function generateSignature($requestId, $timestamp, $body)
    {
        $digest = base64_encode(hash('sha256', $body, true));
        $rawSignature = "Client-Id:$this->clientId\n".
                        "Request-Id:$requestId\n".
                        "Request-Timestamp:$timestamp\n".
                        "Request-Target:/checkout/v1/payment\n".
                        "Digest:$digest";

        return base64_encode(
            hash_hmac('sha256', $rawSignature, $this->secretKey, true)
        );
    }

    public function createPayment($order)
    {
        $requestId = Str::uuid()->toString();
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");

        $body = [
            "order" => [
                "amount"   => (int) $order->total,
                "invoice_number" => $order->invoice_number,
                "currency" => "IDR"
            ],
            "payment" => [
                "payment_due_date" => 60 // 60 minutes
            ]
        ];

        $jsonBody = json_encode($body);

        $signature = $this->generateSignature($requestId, $timestamp, $jsonBody);

        $response = Http::withHeaders([
            "Client-Id"          => $this->clientId,
            "Request-Id"         => $requestId,
            "Request-Timestamp"  => $timestamp,
            "Signature"          => "HMACSHA256=" . $signature,
            "Content-Type"       => "application/json"
        ])->post($this->url, $body);

        return $response->json();
    }
}
