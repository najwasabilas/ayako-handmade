<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class CheckoutController extends Controller
{
    /**
     * Menampilkan halaman checkout dari session cart
     */
    public function index()
    {
        $items = session('checkout_items', []);
        if (empty($items)) {
            return redirect()->route('cart.index')->with('error', 'Tidak ada produk yang dipilih untuk checkout.');
        }

        $subtotal = collect($items)->sum(fn($i) => $i['harga'] * $i['qty']);
        $total = $subtotal; // tanpa ongkir

        return view('checkout.index', compact('items', 'subtotal', 'total'));
    }

    /**
     * Membuat pesanan baru
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'telepon' => 'required|string|max:20',
            'alamat_lengkap' => 'required|string|max:500',
        ]);

        $items = session('checkout_items', []);
        if (empty($items)) {
            return redirect()->route('cart.index')->with('error', 'Tidak ada produk yang dipilih untuk checkout.');
        }

        $subtotal = collect($items)->sum(fn($i) => $i['harga'] * $i['qty']);
        $total = $subtotal; // tanpa ongkir

        // Gabungkan alamat
        $alamatGabung = "{$request->nama} | {$request->telepon} | {$request->alamat_lengkap}";

        // Buat order baru
        $order = Order::create([
            'user_id' => Auth::id(),
            'status' => 'Belum dibayar',
            'total' => $total,
            'alamat' => $alamatGabung,
        ]);

        // Simpan item order
        foreach ($items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'qty' => $item['qty'],
                'harga' => $item['harga'],
            ]);

            // Kurangi stok produk
            $product = Product::find($item['product_id']);
            if ($product) {
                $product->stok -= $item['qty'];
                $product->save();
            }
        }

        // Hapus item dari order status 'cart'
        $cartOrder = Order::where('user_id', Auth::id())
            ->where('status', 'cart')
            ->first();

        if ($cartOrder) {
            $cartOrder->items()
                ->whereIn('product_id', array_column($items, 'product_id'))
                ->delete();

            // Jika sudah kosong, hapus order cart-nya juga
            if ($cartOrder->items()->count() === 0) {
                $cartOrder->delete();
            }
        }

        // Bersihkan session
        session()->forget('checkout_items');

        // Redirect ke halaman pembayaran
        return redirect()->route('checkout.payment', $order->id)->with('success', 'Pesanan berhasil dibuat!');
    }

    /**
     * Menampilkan halaman pembayaran
     */
    public function payment($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        return view('checkout.payment', compact('order'));
    }
}
