<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
    'name',
    'purchase_price',
    'price_detail',
    'price_semi_bulk',
    'price_bulk',
    'quantity',
    'photo',
];

}
