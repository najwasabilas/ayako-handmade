<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\DokuService;
use Illuminate\Http\Request;

class DokuPaymentController extends Controller
{
    public function create($orderId, DokuService $doku)
    {
        $order = Order::findOrFail($orderId);

        // Generate invoice number (wajib unik)
        $order->invoice_number = "INV-" . time() . "-" . $order->id;
        $order->save();

        $response = $doku->createPayment($order);

        if (!isset($response['response']['payment']['url'])) {
            return back()->with('error', 'Gagal membuat pembayaran.');
        }

        $order->payment_url       = $response['response']['payment']['url'];
        $order->payment_token     = $response['response']['payment']['token_id'];
        $order->payment_expired_at= now()->addMinutes(60);
        $order->save();

        // Arahkan ke halaman bayar
        return redirect()->route('checkout.payment', $order->id);
    }


    // CALLBACK dari DOKU
    public function callback(Request $request)
    {
        Log::info("=== DOKU CALLBACK RECEIVED ===");

        $headers = getallheaders();
        $secretKey = env('DOKU_SECRET_KEY');
        $notificationPath = "/payment/callback";

        // Raw body dari DOKU
        $body = file_get_contents("php://input");
        Log::info("CALLBACK RAW BODY", ['body' => $body]);

        // Generate Digest
        $digest = base64_encode(hash('sha256', $body, true));

        // Susun raw signature sesuai dokumentasi
        $rawSignature =
            "Client-Id:" . ($headers['Client-Id'] ?? '') . "\n" .
            "Request-Id:" . ($headers['Request-Id'] ?? '') . "\n" .
            "Request-Timestamp:" . ($headers['Request-Timestamp'] ?? '') . "\n" .
            "Request-Target:" . $notificationPath . "\n" .
            "Digest:" . $digest;

        // HMAC SHA256
        $generatedSignature = "HMACSHA256=" . base64_encode(
            hash_hmac('sha256', $rawSignature, $secretKey, true)
        );

        // VALIDASI SIGNATURE
        if (!isset($headers['Signature']) || $generatedSignature !== $headers['Signature']) {

            Log::error("INVALID SIGNATURE", [
                "expected" => $generatedSignature,
                "received" => $headers['Signature'] ?? null
            ]);

            return response("Invalid Signature", 400)
                ->header("Content-Type", "text/plain");
        }

        Log::info("VALID SIGNATURE ✓");

        // WAJIB → respon 200 dulu agar DOKU tidak retry
        $response = response("OK", 200)->header("Content-Type", "text/plain");
        $response->send();

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }


        // -------- PROSES NOTIFICATION ----------
        $data = json_decode($body, true);

        $invoice = $data['order']['invoice_number'] ?? null;
        $status  = $data['transaction']['status'] ?? null;

        if (!$invoice) {
            Log::error("Invoice not found in callback.");
            return;
        }

        $order = Order::where('invoice_number', $invoice)->first();

        if (!$order) {
            Log::error("Order not found: " . $invoice);
            return;
        }

        // Idempotent → jangan proses ulang
        if ($order->payment_status === "PAID") {
            Log::info("Order already processed → ignore duplicate event", ['order_id' => $order->id]);
            return;
        }

        if ($status === "SUCCESS") {
            $order->payment_status = "PAID";
            $order->status = "Dikemas";
        }
        elseif ($status === "FAILED") {
            Log::info("FAILED ignored (Checkout Spec)");
            return;
        }
        else {
            $order->payment_status = "PENDING";
        }

        $order->save();

        Log::info("ORDER UPDATED SUCCESSFULLY", [
            "order_id" => $order->id,
            "new_status" => $order->payment_status
        ]);
    }
    public function afterPayment(Request $request)
    {
        return redirect()->route('orders.index')->with('success', 'Pembayaran berhasil!');
    }
}
