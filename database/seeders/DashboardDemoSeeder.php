<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DashboardDemoSeeder extends Seeder
{
    public function run()
    {
        // Buat beberapa user pelanggan
        $customers = User::factory()->count(8)->create([
            'role' => 'customer',
        ]);

        // Ambil semua produk dari ProductSeeder
        $products = Product::all();

        if ($products->isEmpty()) {
            $this->command->warn("⚠️  Tidak ada produk di database! Jalankan ProductSeeder terlebih dahulu.");
            return;
        }

        // Buat pesanan acak untuk tiap user
        foreach ($customers as $customer) {
            $jumlahOrder = rand(2, 4);

            for ($i = 0; $i < $jumlahOrder; $i++) {
                $order = Order::create([
                    'user_id' => $customer->id,
                    'status' => collect(['menunggu', 'diproses', 'selesai'])->random(),
                    'total' => 0,
                    'alamat' => 'Jl. Contoh No. ' . rand(1, 100) . ', Pekanbaru',
                    'created_at' => Carbon::now()->subMonths(rand(0, 5)),
                ]);

                $total = 0;

                // Pilih produk acak dari list produk kamu
                $produkTerpilih = $products->random(rand(1, 3));
                foreach ($produkTerpilih as $produk) {
                    $qty = rand(1, 3);
                    $subtotal = $produk->harga * $qty;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $produk->id,
                        'qty' => $qty,
                        'harga' => $produk->harga,
                    ]);

                    $total += $subtotal;
                }

                $order->update(['total' => $total]);
            }
        }

        $this->command->info("✅ Dashboard demo data berhasil dibuat!");
    }
}
