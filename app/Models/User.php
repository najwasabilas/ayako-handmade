<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable {
    use HasFactory;
    use Notifiable;

    // Menambahkan kolom yang bisa diisi
    protected $fillable = [
        'name', 'email', 'password', 'phone', 'profile_picture', 'role', 
        'verification_code', 'verification_expires_at', 'is_verified'
    ];

    // Menambahkan kolom yang harus disembunyikan (tidak akan ditampilkan di array atau JSON)
    protected $hidden = ['password', 'remember_token'];

    // Menambahkan cast untuk kolom yang memiliki tipe data khusus
    protected $casts = [
        'email_verified_at' => 'datetime',  // Pastikan email_verified_at adalah datetime
        'verification_expires_at' => 'datetime', // Mengubah verification_expires_at menjadi instansi Carbon
        'is_verified' => 'boolean', // Pastikan is_verified di-cast ke boolean
    ];

    // Relasi dengan model Order
    public function orders() {
        return $this->hasMany(Order::class);
    }

    // Relasi dengan model Address
    public function addresses() {
        return $this->hasMany(Address::class);
    }

    // Relasi dengan alamat utama
    public function mainAddress() {
        return $this->hasOne(Address::class)->where('utama', true);
    }
}
