<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->decimal('quantity', 10, 2)->change();
            $table->decimal('initial_stock', 10, 2)->change();
            $table->decimal('final_stock', 10, 2)->change();
        });
    }

    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->integer('quantity')->change();
            $table->integer('initial_stock')->change();
            $table->integer('final_stock')->change();
        });
    }
};
