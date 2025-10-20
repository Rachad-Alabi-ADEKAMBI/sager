<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();

            // Clé étrangère vers le produit
            $table->unsignedBigInteger('product_id');

            // Nom du produit au moment du dépôt
            $table->string('product_name');

            // Quantités
            $table->decimal('initial_quantity', 10, 2)->default(0);
            $table->decimal('quantity', 10, 2)->default(0);
            $table->decimal('final_quantity', 10, 2)->default(0);

            // Commentaire libre
            $table->text('comment')->nullable();

            $table->timestamps();

            // Relation avec products
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
