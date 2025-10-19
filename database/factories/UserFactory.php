<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'), // default password
            'phone' => fake()->phoneNumber(),
            'profile_picture' => fake()->imageUrl(200, 200, 'people'),
            'role' => fake()->randomElement(['user', 'admin']),
            'remember_token' => Str::random(10),
        ];
    }
}
