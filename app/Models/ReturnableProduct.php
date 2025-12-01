<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnableProduct extends Model
{
    protected $fillable = [
        'product_id',
        'sale_id',
        'quantity_purchased',
        'quantity_returned',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
