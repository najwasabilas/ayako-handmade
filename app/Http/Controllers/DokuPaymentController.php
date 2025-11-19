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
        // Log semua callback agar mudah debug
        Log::info('DOKU CALLBACK RECEIVED', $request->all());

        $data = $request->all();

        // Pastikan ada invoice number dari DOKU
        if (!isset($data['order']['invoice_number'])) {
            Log::error('DOKU CALLBACK ERROR: invoice_number tidak ditemukan');
            return response()->json(['message' => 'invoice_number not found'], 400);
        }

        $invoiceNumber = $data['order']['invoice_number'];

        // Cari order berdasarkan invoice_number
        $order = Order::where('invoice_number', $invoiceNumber)->first();

        if (!$order) {
            Log::error('Order tidak ditemukan', ['invoice' => $invoiceNumber]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Status payment dari DOKU
        $transactionStatus = $data['transaction']['status'] ?? null;

        // Update status order
        if ($transactionStatus === 'SUCCESS') {
            $order->payment_status = 'PAID';
            $order->status = 'Dikemas';
        } elseif ($transactionStatus === 'FAILED') {
            $order->payment_status = 'FAILED';
            $order->status = 'Dibatalkan';
        } else {
            $order->payment_status = 'PENDING';
            $order->status = 'pending';
        }

        $order->save();

        Log::info('Order berhasil diupdate', ['order' => $order->id]);

        // Callback harus return JSON 200 agar DOKU menganggap sukses
        return response()->json(['success' => true], 200);
    }
    public function afterPayment(Request $request)
    {
        return redirect()->route('orders.index')->with('success', 'Pembayaran berhasil!');
    }
}
