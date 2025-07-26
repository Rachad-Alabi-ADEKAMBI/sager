<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = [
        'date',
        'initial_stock',
        'label',
        'quantity',
        'final_stock',
        'product_id',
        'product_name',
        'sale_id',
        'seller_name',
    ];
}
