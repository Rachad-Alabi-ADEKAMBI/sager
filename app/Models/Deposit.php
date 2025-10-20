<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_name',
        'initial_quantity',
        'quantity',
        'final_quantity',
        'comment',
    ];

    // Relation : un dépôt appartient à un produit
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
