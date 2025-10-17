<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            // Supprime la clé étrangère existante si nécessaire
            $table->dropForeign(['product_id']);

            // Recrée la clé étrangère avec cascade
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            // Optionnel : recrée la clé étrangère sans cascade
            $table->foreign('product_id')
                ->references('id')
                ->on('products');
        });
    }
};
