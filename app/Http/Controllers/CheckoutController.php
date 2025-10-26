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
        $user = auth()->user();
        $addresses = $user->addresses()
            ->orderByDesc('utama') // alamat utama paling atas
            ->get();

        $items = session('checkout_items', []);
        if (empty($items)) {
            return redirect()->route('cart.index')->with('error', 'Tidak ada produk yang dipilih untuk checkout.');
        }

        if (isset($items['product_id'])) {
            $items = [$items]; // jika single item, ubah ke array of array
        }

        $subtotal = collect($items)->sum(fn($i) => $i['harga'] * $i['qty']);
        $total = $subtotal;

        return view('checkout.index', compact('items', 'total', 'addresses'));
    }

    /**
     * Membuat pesanan baru
     */
    public function placeOrder(Request $request)
    {
        $user = Auth::user();

        $items = session('checkout_items', []);
        if (empty($items)) {
            return redirect()->route('cart.index')->with('error', 'Tidak ada produk yang dipilih untuk checkout.');
        }
        if (isset($items['product_id'])) {
            $items = [$items]; // jika single item, ubah ke array of array
        }

        // Jika user isi alamat baru
        if ($request->filled('nama_penerima_baru') && $request->filled('telepon_baru') && $request->filled('alamat_lengkap_baru')) {
            // Jika alamat baru dijadikan utama, set yang lama jadi false dulu
            if ($user->addresses()->count() >= 5) {
                return back()->with('error', 'Maksimal 5 alamat pengiriman.');
            }
            if ($request->has('is_default_baru')) {
                $user->addresses()->update(['utama' => false]);
            }

            $address = $user->addresses()->create([
                'nama_penerima' => $request->nama_penerima_baru,
                'no_hp' => $request->telepon_baru,
                'alamat_lengkap' => $request->alamat_lengkap_baru,
                'utama' => $request->has('is_default_baru'),
            ]);
        } else {
            // Jika user pilih alamat dari list
            $address = $user->addresses()->find($request->address_id);

            // Kalau belum punya sama sekali, validasi minimal
            if (!$address) {
                return back()->with('error', 'Silakan pilih atau tambahkan alamat terlebih dahulu.');
            }
        }

        // Buat string alamat gabungan untuk order
        $alamatGabung = "{$address->nama_penerima} | {$address->telepon} | {$address->alamat_lengkap}";

        // Hitung total
        $subtotal = collect($items)->sum(fn($i) => $i['harga'] * $i['qty']);
        $total = $subtotal; // tanpa ongkir

        // Buat order baru
        $order = Order::create([
            'user_id' => $user->id,
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
                $product->stok = max(0, $product->stok - $item['qty']);
                $product->save();
            }
        }

        // Hapus dari cart
        $cartOrder = Order::where('user_id', $user->id)
            ->where('status', 'cart')
            ->first();

        if ($cartOrder) {
            $cartOrder->items()
                ->whereIn('product_id', array_column($items, 'product_id'))
                ->delete();

            if ($cartOrder->items()->count() === 0) {
                $cartOrder->delete();
            }
        }

        session()->forget('checkout_items');

        return redirect()->route('checkout.payment', $order->id)->with('success', 'Pesanan berhasil dibuat!');
    }


    /**
     * Menampilkan halaman pembayaran
     */
    public function payment($id)
    {
        $order = Order::with('items.product')->findOrFail($id);

        $items = $order->items->map(function ($item) {
            return [
                'nama' => $item->product->nama ?? 'Produk tidak ditemukan',
                'harga' => $item->harga,
                'qty' => $item->qty,
                'subtotal' => $item->harga * $item->qty,
            ];
        });

    $total = $items->sum('subtotal');
        return view('checkout.payment', compact('order', 'items', 'total'));
    }
}