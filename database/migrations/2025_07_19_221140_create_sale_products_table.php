<?php

// database/migrations/xxxx_xx_xx_create_sale_products_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleProductsTable extends Migration
{
    public function up()
    {
        Schema::create('sale_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 10, 2); // prix au moment de la vente
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sale_products');
    }
}
