<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('returnable_products_list', function (Blueprint $table) {
            $table->id();

            $table->foreignId('returnable_product_id')
                ->constrained('returnable_products')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            // Quantité remise décimale
            $table->decimal('quantity_given', 10, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('returnable_products_list');
    }
};
