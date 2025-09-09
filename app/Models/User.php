<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable {
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name','email','password','phone','profile_picture','role'
    ];

    protected $hidden = ['password','remember_token'];

    public function orders() {
        return $this->hasMany(Order::class);
    }
}