<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'seller_name',
        'buyer_name',
        'total',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'sale_products')
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }
}
