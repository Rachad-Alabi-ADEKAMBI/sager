<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ReturnableProduct extends Model
{
    protected $fillable = [
        'client_id',
        'date',
        'comment',
        'status'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $year = now()->year;

            $lastNumber = DB::table('returnable_products')
                ->whereYear('created_at', $year)
                ->max(DB::raw('CAST(SUBSTRING_INDEX(reference, "-", -1) AS UNSIGNED)'));

            $nextNumber = str_pad(($lastNumber ?? 0) + 1, 4, '0', STR_PAD_LEFT);

            $model->reference = "REM-{$year}-{$nextNumber}";
        });
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function products()
    {
        return $this->hasMany(
            ReturnableProductsList::class,
            'returnable_product_id'
        );
    }
}
