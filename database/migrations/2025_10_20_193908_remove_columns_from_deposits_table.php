<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn(['initial_quantity', 'final_quantity', 'comment']);
        });
    }

    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->decimal('initial_quantity', 10, 2)->default(0);
            $table->decimal('final_quantity', 10, 2)->default(0);
            $table->text('comment')->nullable();
        });
    }
};
