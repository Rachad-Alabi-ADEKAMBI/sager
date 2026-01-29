<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnableProductsList extends Model
{
    use HasFactory;

    protected $table = 'returnable_products_list'; // attention au pluriel

    protected $fillable = [
        'returnable_product_id',
        'product_id',
        'quantity_given',
        'quantity_returned',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function returnableProduct()
    {
        return $this->belongsTo(ReturnableProduct::class);
    }
}
