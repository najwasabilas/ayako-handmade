<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fabric;

class FabricSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Kanvas', 'Jeans', 'Songket', 'Batik', 'Goni'];

        for ($i = 1; $i <= 20; $i++) {
            Fabric::create([
                'nama' => 'Fabric ' . $i,
                'kategori' => $categories[array_rand($categories)],
                'gambar' => 'fabric_' . $i . '.png', 
            ]);
        }
    }
}
