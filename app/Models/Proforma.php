<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proforma extends Model
{
    use HasFactory;


    protected $guarded = [
        'seller_name',
        'buyer_name',
        'buyer_phone',
        'total',
    ];
}
