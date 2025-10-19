<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama' => fake()->word(),
            'deskripsi' => fake()->paragraph(),
            'harga' => fake()->numberBetween(10000, 500000),
            'stok' => fake()->numberBetween(0, 100),
            'kategori' => fake()->randomElement(['Elektronik', 'Fashion', 'Makanan', 'Lainnya']),
        ];
    }
}
