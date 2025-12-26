<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stocks_returnable_products', function (Blueprint $table) {
            $table->id();

            // référence à l'opération de remise
            $table->foreignId('returnable_product_id')
                ->constrained('returnable_products')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedInteger('quantity_returned');
            $table->date('date');
            $table->string('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks_returnable_products');
    }
};
