<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\DokuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        return response('OK', 200)->header('Content-Type', 'text/plain');
        $headers = $request->headers->all();

        $body = $request->getContent();

        $data = json_decode($body, true);

        Log::info("DOKU callback body", $data ?? []);
        Log::info("DOKU callback headers", $headers);

        $invoice = $data['order']['invoice_number'] ?? null;
        $status  = $data['transaction']['status'] ?? null;

        if (!$invoice) {
            Log::error("Invoice missing in callback");
            return;
        }

        $order = Order::where('invoice_number', $invoice)->first();
        if (!$order) {
            Log::error("Order not found: $invoice");
            return;
        }

        if ($order->payment_status === "PAID") {
            Log::info("Payment already processed → ignore duplicate");
            return;
        }
        if ($status === "SUCCESS") {
            $order->payment_status = "PAID";
            $order->status = "Dikemas";
        } elseif ($status === "FAILED") {
            Log::info("FAILED ignored");
            return;
        } else {
            $order->payment_status = "PENDING";
        }

        $order->save();
        return response('OK', 200)->header('Content-Type', 'text/plain');
    }
    public function afterPayment(Request $request)
    {
        return redirect()->route('orders.index')->with('success', 'Pembayaran berhasil!');
    }
}
