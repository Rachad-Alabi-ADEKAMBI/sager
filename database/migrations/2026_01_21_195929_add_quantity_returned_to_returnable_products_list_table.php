<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('returnable_products_list', function (Blueprint $table) {
            $table->decimal('quantity_returned', 10, 2)
                ->default(0)
                ->after('quantity_given');
        });
    }

    public function down(): void
    {
        Schema::table('returnable_products_list', function (Blueprint $table) {
            $table->dropColumn('quantity_returned');
        });
    }
};
