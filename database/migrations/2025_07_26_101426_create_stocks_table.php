<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('initial_stock');
            $table->string('label');
            $table->integer('quantity');
            $table->integer('final_stock');
            $table->unsignedBigInteger('product_id');
            $table->string('product_name');
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->string('seller_name')->nullable();
            $table->timestamps();

            // Optionnel : clés étrangères
            // $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            // $table->foreign('sale_id')->references('id')->on('sales')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stocks');
    }
};
