<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderListController extends Controller
{
    /**
     * Menampilkan halaman Pesanan Saya (semua order user)
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Filter status jika ada parameter ?status=
        $status = $request->get('status', 'Belum Dibayar');

        $orders = Order::with(['items.product'])
            ->where('user_id', $user->id)
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
        $statuses = ['Belum Dibayar', 'Dikemas', 'Dikirim', 'Selesai'];

        return view('orders.index', compact('orders', 'statuses', 'status'));
    }

    /**
     * Menampilkan detail pesanan (opsional)
     */
    public function show($id)
    {
        $order = Order::with(['items.product'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('orders.show', compact('order'));
    }

    /**
     * Hapus pesanan (jika belum dibayar)
     */
    public function destroy($id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        if ($order->status === 'Belum Dibayar') {
            $order->delete();
            return redirect()->back()->with('success', 'Pesanan berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'Pesanan tidak dapat dihapus.');
    }
}
