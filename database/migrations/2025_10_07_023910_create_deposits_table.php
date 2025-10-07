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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();

            // Relations (facultatives pour l'historique)
            $table->unsignedBigInteger('sale_id')->nullable();     // Vente concernée
            $table->unsignedBigInteger('product_id')->nullable();  // Produit consigné

            // Sauvegarde des infos du produit pour l'historique
            $table->string('product_name');                        // Nom du produit au moment de la vente
            $table->decimal('deposit_price_at_sale', 10, 2);       // Prix de consignation au moment de la vente

            // Détails de la consignation
            $table->decimal('quantity', 10, 2);                   // Quantité consignée
            $table->decimal('total', 10, 2);                      // Total du dépôt (deposit_price * quantity)
            $table->enum('status', ['en_cours', 'annule', 'retourne'])
                ->default('en_cours');                          // Statut de la consigne

            $table->timestamps();

            // Clés étrangères sans suppression automatique
            $table->foreign('sale_id')->references('id')->on('sales');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    public function down()
    {
        Schema::dropIfExists('deposits');
    }
};
