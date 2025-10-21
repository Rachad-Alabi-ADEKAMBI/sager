<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id(); // ID auto-increment
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade'); // clé étrangère vers clients
            $table->decimal('amount', 10, 2); // montant de la créance
            $table->text('comment')->nullable(); // commentaire optionnel
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};
