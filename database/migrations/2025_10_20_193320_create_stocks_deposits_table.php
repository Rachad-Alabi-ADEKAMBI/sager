<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stocks_deposits', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_id');
            $table->decimal('initial_stock', 10, 2);
            $table->decimal('quantity', 10, 2);
            $table->decimal('final_stock', 10, 2);
            $table->string('comment')->nullable();

            $table->timestamps();

            // Clé étrangère
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks_deposits');
    }
};
