<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Buat user dummy
        $users = [
            ['name' => 'John Carter', 'email' => 'hello@johncarter.com'],
            ['name' => 'Sophie Moore', 'email' => 'contact@sophiemoore.com'],
            ['name' => 'Matt Cannon', 'email' => 'info@mattcannon.com'],
            ['name' => 'Graham Hills', 'email' => 'hi@grahamhills.com'],
            ['name' => 'Sandy Houston', 'email' => 'contact@sandyhouston.com'],
            ['name' => 'Andy Smith', 'email' => 'hello@andysmith.com'],
        ];

        foreach ($users as $u) {
            User::firstOrCreate(['email' => $u['email']], [
                'name' => $u['name'],
                'password' => bcrypt('password'),
            ]);
        }

        // Buat produk dummy
        $produk = Product::firstOrCreate([
            'nama' => 'Nama Produk',
            'deskripsi' => 'Deskripsi produk contoh',
            'harga' => 160000,
            'stok' => 100,
            'kategori' => 'Umum'
        ]);

        // Buat orders
        $dataOrders = [
            ['user_email' => 'hello@johncarter.com', 'status' => 'Dikirim', 'total' => 805000, 'alamat' => 'Pekanbaru', 'qty' => 5],
            ['user_email' => 'contact@sophiemoore.com', 'status' => 'Selesai', 'total' => 200000, 'alamat' => 'Jakarta', 'qty' => 1],
            ['user_email' => 'info@mattcannon.com', 'status' => 'Dikirim', 'total' => 350000, 'alamat' => 'Padang', 'qty' => 2],
            ['user_email' => 'hi@grahamhills.com', 'status' => 'Dikemas', 'total' => 160000, 'alamat' => 'Medan', 'qty' => 1],
            ['user_email' => 'contact@sandyhouston.com', 'status' => 'Dikirim', 'total' => 500000, 'alamat' => 'Bogor', 'qty' => 3],
            ['user_email' => 'hello@andysmith.com', 'status' => 'Dikemas', 'total' => 1000000, 'alamat' => 'Pekanbaru', 'qty' => 10],
        ];

        foreach ($dataOrders as $i => $o) {
            $user = User::where('email', $o['user_email'])->first();
            $order = Order::create([
                'user_id' => $user->id,
                'status' => $o['status'],
                'total' => $o['total'],
                'alamat' => $o['alamat'],
                'created_at' => now()->subDays(10 - $i),
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $produk->id,
                'qty' => $o['qty'],
                'harga' => $produk->harga,
            ]);
        }
    }
}
