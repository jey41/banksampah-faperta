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
        Schema::table('trash_prices', function (Blueprint $table) {
            $table->decimal('carbon_factor', 8, 2)->default(0.00)->after('price_sell');
        });

        Schema::table('deposit_items', function (Blueprint $table) {
            $table->decimal('total_carbon', 10, 2)->default(0.00)->after('total_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposit_items', function (Blueprint $table) {
            $table->dropColumn('total_carbon');
        });

        Schema::table('trash_prices', function (Blueprint $table) {
            $table->dropColumn('carbon_factor');
        });
    }
};
