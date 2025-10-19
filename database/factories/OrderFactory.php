<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories.Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status' => fake()->randomElement(['pending', 'processing', 'completed', 'cancelled']),
            'total' => fake()->numberBetween(50000, 1000000),
            'alamat' => fake()->address(),
        ];
    }
}
