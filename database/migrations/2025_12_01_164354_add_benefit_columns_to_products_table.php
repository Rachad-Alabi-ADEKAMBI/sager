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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('benefit_deposit', 10, 2)->nullable()->after('isReturnable');
            $table->decimal('benefit_refill', 10, 2)->nullable()->after('benefit_deposit');
            $table->decimal('benefit_deposit_refill', 10, 2)->nullable()->after('benefit_refill');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'benefit_deposit',
                'benefit_refill',
                'benefit_deposit_refill'
            ]);
        });
    }
};
