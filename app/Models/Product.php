<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'purchase_price',
        'price_detail',
        'price_semi_bulk',
        'price_bulk',
        'quantity',
        'stock',
        'photo',
        'is_depositable',
        'deposit_price',
        'filling_price',
    ];

    // Indique à Eloquent de gérer automatiquement la colonne deleted_at
    protected $dates = ['deleted_at'];

    public function sales()
    {
        return $this->belongsToMany(Sale::class, 'sale_products')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }
}
