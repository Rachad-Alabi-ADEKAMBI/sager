<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimsPayment extends Model
{
    use HasFactory;

    protected $fillable = ['claim_id', 'amount', 'comment', 'payment_method'];

    // Relation vers la claim
    public function claim()
    {
        return $this->belongsTo(Claim::class);
    }
}
