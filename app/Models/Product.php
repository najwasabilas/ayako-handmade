<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {
    protected $fillable = ['nama','deskripsi','harga','stok','kategori'];

    public function images() {
        return $this->hasMany(ProductImage::class);
    }

    public function orderItems() {
        return $this->hasMany(OrderItem::class);
    }
}
