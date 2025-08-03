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
        Schema::table('users', function (Blueprint $table) {
            // Ajoute une colonne 'status' avec une valeur par défaut 'active'
            $table->string('status')->default('active')->after('role');
            
            // Ajoute une colonne 'ban_reason' qui peut être nulle
            $table->text('ban_reason')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Supprime les colonnes en cas d'annulation de la migration
            $table->dropColumn('status');
            $table->dropColumn('ban_reason');
        });
    }
};