<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnableProductsList extends Model
{
    protected $table = 'returnable_products_list';

    protected $fillable = [
        'returnable_product_id',
        'product_id',
        'quantity_given',
    ];

    protected $casts = [
        'quantity_given' => 'decimal:2',
    ];

    public function operation()
    {
        return $this->belongsTo(\App\Models\ReturnableProduct::class, 'returnable_product_id');
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class);
    }
}
