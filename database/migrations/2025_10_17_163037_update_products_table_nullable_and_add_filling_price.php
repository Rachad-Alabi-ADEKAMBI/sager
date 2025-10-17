<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Rendre les champs de prix optionnels (nullable)
            $table->decimal('price_detail', 10, 2)->nullable()->change();
            $table->decimal('price_semi_bulk', 10, 2)->nullable()->change();
            $table->decimal('price_bulk', 10, 2)->nullable()->change();

            // Ajouter la colonne filling_price (nullable)
            $table->decimal('filling_price', 10, 2)->nullable()->after('deposit_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Supprimer la colonne ajoutée
            $table->dropColumn('filling_price');

            // Rétablir les contraintes NOT NULL
            $table->decimal('price_detail', 10, 2)->nullable(false)->change();
            $table->decimal('price_semi_bulk', 10, 2)->nullable(false)->change();
            $table->decimal('price_bulk', 10, 2)->nullable(false)->change();
        });
    }
};
