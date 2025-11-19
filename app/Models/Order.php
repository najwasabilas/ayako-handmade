<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model 
{
    protected $fillable = [
        'user_id',
        'status',
        'total',
        'alamat',

        // Tambahan untuk DOKU
        'invoice_number',
        'payment_url',
        'payment_token',
        'payment_expired_at',
        'payment_status',
    ];

    protected $casts = [
        'payment_expired_at' => 'datetime',
    ];

    public function items() {
        return $this->hasMany(OrderItem::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
