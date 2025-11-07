<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('price_detail', 10, 2);
            $table->decimal('price_semi_bulk', 10, 2);
            $table->decimal('price_bulk', 10, 2);
            $table->integer('quantity');
            $table->string('photo')->nullable(); // Chemin vers l'image
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
