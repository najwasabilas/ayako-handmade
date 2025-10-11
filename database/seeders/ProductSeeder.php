<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductImage;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            ['Tote Bag Goni Kombinasi Songket', 'Tas etnik kombinasi goni dan songket khas Riau.', 350000, 'Totebag'],
            ['Hobo Bag Kombinasi Songket', 'Tas bahu elegan dengan sentuhan songket.', 300000, 'Hand Bag'],
            ['Totebag Songket', 'Totebag cantik berbahan dasar songket.', 275000, 'Totebag'],
            ['Puan Totebag', 'Totebag minimalis untuk aktivitas sehari-hari.', 200000, 'Totebag'],
            ['Tenun Totebag', 'Tas tenun elegan dengan warna klasik.', 750000, 'Totebag'],
            ['Ayako Mini Tote', 'Tas kecil praktis dan menawan.', 135000, 'Totebag'],
            ['Feminime Clutch', 'Clutch feminin dengan bahan kombinasi batik.', 175000, 'Dompet'],
            ['Simple SlingBag', 'Tas selempang etnik untuk gaya santai.', 125000, 'Sling Bag'],
            ['Laptop Sleeve', 'Pelindung laptop berbahan songket tenun.', 165000, 'Tas Laptop'],
            ['Pouch Tenun Riau', 'Pouch kecil dengan motif khas Riau.', 145000, 'Dompet'],
            ['Connie Slingbag', 'Slingbag simpel dan berwarna cerah.', 150000, 'Sling Bag'],
            ['Pouch Songket Riau', 'Pouch etnik berbahan songket tradisional.', 65000, 'Dompet'],
            ['Classic Handbag', 'Tas tangan klasik bergaya elegan.', 400000, 'Hand Bag'],
            ['Mini Crossbody Bag', 'Tas kecil selempang untuk aktivitas ringan.', 180000, 'Sling Bag'],
            ['Tenun Pouch Premium', 'Pouch premium dari tenun asli Riau.', 210000, 'Dompet'],
            ['Backpack Songket', 'Ransel etnik dengan motif songket.', 500000, 'Backpack'],
            ['Tote Eco Bag', 'Totebag ramah lingkungan dengan desain stylish.', 120000, 'Totebag'],
            ['Ayako Laptop Bag', 'Tas laptop dengan bahan premium.', 480000, 'Tas Laptop'],
            ['Modern SlingBag', 'Tas selempang modern dengan sentuhan tradisional.', 195000, 'Sling Bag'],
            ['Exclusive Tenun Bag', 'Tas eksklusif hasil kolaborasi pengrajin lokal.', 650000, 'Hand Bag'],
        ];

        foreach ($products as $index => $data) {
            $product = Product::create([
                'nama' => $data[0],
                'deskripsi' => $data[1],
                'harga' => $data[2],
                'stok' => rand(5, 20),
                'kategori' => $data[3],
            ]);

            
            ProductImage::create([
                'product_id' => $product->id,
                'gambar' => 'produk' . ($index + 1) . '.png',
            ]);
        }
    }
    public function down()
    {
        // Menghapus data produk dan gambar terkait
        ProductImage::truncate();  // Menghapus semua gambar produk
        Product::truncate();       // Menghapus semua produk
    }
}
