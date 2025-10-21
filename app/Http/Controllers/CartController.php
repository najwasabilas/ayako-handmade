<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class CartController extends Controller
{
    /**
     * Menampilkan semua item keranjang aktif user
     */
    public function index()
    {
        $order = Order::where('user_id', Auth::id())
                      ->where('status', 'cart')
                      ->with('items.product')
                      ->first();

        $items = $order ? $order->items : collect();

        return view('cart.index', compact('items'));
    }

    /**
     * Update jumlah item di keranjang
     */
    public function updateQuantity(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:order_items,id',
            'qty' => 'required|integer|min:1'
        ]);

        $item = OrderItem::with('product')->find($request->item_id);
        if ($request->qty > $item->product->stok) {
            return response()->json(['error' => 'Jumlah melebihi stok tersedia'], 400);
        }

        $item->qty = $request->qty;
        $item->save();

        // Update total order
        $item->order->update([
            'total' => $item->order->items->sum(fn($i) => $i->qty * $i->harga)
        ]);

        return response()->json(['success' => 'Jumlah berhasil diperbarui']);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:order_items,id'
        ]);

        $item = OrderItem::findOrFail($request->id);
        $order = $item->order;

        // Hapus item
        $item->delete();

        // Update total order
        if ($order) {
            $order->update([
                'total' => $order->items->sum(fn($i) => $i->qty * $i->harga)
            ]);
        }

        return response()->json(['success' => true]);
    }


    /**
     * Checkout produk yang dicentang
     */
    public function checkoutSelected(Request $request)
    {
        $request->validate([
            'selected_items' => 'nullable|array',
        ]);

        if (empty($request->selected_items)) {
            return redirect()->back()->with('error', 'Tidak ada produk yang dipilih untuk checkout.');
        }

        $items = OrderItem::whereIn('id', $request->selected_items)
            ->with('product.images')
            ->get();

        $checkoutItems = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'nama' => $item->product->nama,
                'harga' => $item->harga,
                'qty' => $item->qty,
                'gambar' => $item->product->images->first()->gambar ?? null,
            ];
        })->toArray();

        session(['checkout_items' => $checkoutItems]);

        return redirect()->route('checkout.page');
    }

}
