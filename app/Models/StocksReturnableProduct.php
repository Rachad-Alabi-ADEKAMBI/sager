<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StocksReturnableProduct extends Model
{
    use HasFactory;

    protected $table = 'stocks_returnable_products';

    protected $fillable = [
        'sale_id',
        'product_id',
        'buyer_name',
        'quantity_purchased',
        'quantity_returned',
    ];
}
