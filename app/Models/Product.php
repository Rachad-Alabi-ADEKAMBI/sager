<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'purchase_price', 'price_detail', 'price_semi_bulk', 'price_bulk', 'quantity', 'stock', 'photo', 'is_depositable', 'deposit_price'];

    public function sales()
    {
        return $this->belongsToMany(Sale::class, 'sale_products')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }
}
