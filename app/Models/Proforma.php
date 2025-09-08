<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proforma extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_name',
        'buyer_name',
        'buyer_phone',
        'total',
    ];

    // Relation avec les produits de la proforma
    public function products()
    {
        return $this->hasMany(ProformaProduct::class);
    }
}
