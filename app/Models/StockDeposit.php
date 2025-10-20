<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockDeposit extends Model
{
    use HasFactory;

    // Nom exact de la table
    protected $table = 'stocks_deposits';

    protected $fillable = [
        'product_id',
        'initial_stock',
        'quantity',
        'final_stock',
        'comment',
    ];
}
