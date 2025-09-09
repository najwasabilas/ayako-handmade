<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder {
    public function run(): void {
        User::updateOrCreate(
            ['email' => 'admin@ayako.test'],
            [
                'name'=>'Admin Ayako',
                'password'=>Hash::make('admin123'),
                'role'=>'admin',
                'phone'=>'081234567890'
            ]
        );
    }
}
