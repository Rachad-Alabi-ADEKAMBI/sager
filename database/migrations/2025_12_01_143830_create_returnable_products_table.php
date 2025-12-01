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
        Schema::create('returnable_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id'); // produit vendu
            $table->unsignedBigInteger('sale_id');    // facture associée
            $table->integer('quantity_purchased')->default(0); // quantité vendue
            $table->integer('quantity_returned')->default(0);  // quantité retournée
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('returnable_products');
    }
};
