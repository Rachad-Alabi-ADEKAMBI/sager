<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claims_payments', function (Blueprint $table) {
            $table->id(); // ID auto-increment
            $table->foreignId('claim_id')->constrained('claims')->onDelete('cascade'); // référence à la claim
            $table->decimal('amount', 10, 2); // montant du paiement
            $table->text('comment')->nullable(); // commentaire optionnel
            $table->string('payment_method')->nullable(); // méthode de paiement (ex: cash, card)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims_payments');
    }
};
