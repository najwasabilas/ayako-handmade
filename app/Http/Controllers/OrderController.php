<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class OrderController extends Controller
{
    /**
     * Tambahkan produk ke keranjang (status: cart)
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $user = Auth::user();

        if ($request->qty > $product->stok) {
            return response()->json(['error' => 'Jumlah melebihi stok tersedia'], 400);
        }

        // Cek apakah user sudah punya keranjang aktif
        $order = Order::where('user_id', $user->id)
                      ->where('status', 'cart')
                      ->first();

        if (!$order) {
            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'cart',
                'total' => 0,
                'alamat' => null,
            ]);
        }

        // Tambahkan item ke order
        $item = $order->items()->where('product_id', $product->id)->first();
        if ($item) {
            $item->qty += $request->qty;
            if ($item->qty > $product->stok) {
                return response()->json(['error' => 'Jumlah melebihi stok tersedia'], 400);
            }
            $item->save();
        } else {
            $order->items()->create([
                'product_id' => $product->id,
                'qty' => $request->qty,
                'harga' => $product->harga,
            ]);
        }

        // Update total order
        $order->total = $order->items->sum(fn($i) => $i->qty * $i->harga);
        $order->save();

        return response()->json(['success' => 'Produk berhasil dimasukkan ke keranjang']);
    }

    /**
     * Arahkan ke halaman checkout untuk produk tertentu
     */
    public function checkoutNow(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($request->qty > $product->stok) {
            return redirect()->back()->with('error', 'Jumlah melebihi stok tersedia.');
        }

        // Simpan data sementara ke session agar bisa dibaca di halaman checkout
        session([
            'checkout_items' => [
                'product_id' => $product->id,
                'nama' => $product->nama,
                'harga' => $product->harga,
                'qty' => $request->qty,
                'total' => $product->harga * $request->qty,
                'image' => $product->images->first()->gambar ?? 'no-image.jpg',  
            ]
        ]);

        return redirect()->route('checkout.page');
    }
}
