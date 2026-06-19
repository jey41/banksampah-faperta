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
        // Add category_type to trash_prices for Umum/Donasi split
        Schema::table('trash_prices', function (Blueprint $table) {
            $table->enum('category_type', ['umum', 'donasi'])->default('umum')->after('category');
        });

        // Add price snapshot columns to deposit_items for dynamic pricing support
        Schema::table('deposit_items', function (Blueprint $table) {
            $table->string('item_name')->nullable()->after('trash_price_id');
            $table->string('item_category')->nullable()->after('item_name');
            $table->enum('item_category_type', ['umum', 'donasi'])->nullable()->after('item_category');
        });

        // Add donation_category to deposits
        Schema::table('deposits', function (Blueprint $table) {
            $table->enum('donation_category', ['umum', 'donasi'])->default('umum')->after('is_donation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn('donation_category');
        });

        Schema::table('deposit_items', function (Blueprint $table) {
            $table->dropColumn(['item_name', 'item_category', 'item_category_type']);
        });

        Schema::table('trash_prices', function (Blueprint $table) {
            $table->dropColumn('category_type');
        });
    }
};
