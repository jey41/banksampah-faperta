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
        Schema::table('pickup_requests', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('pickup_phone');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->decimal('estimated_distance', 8, 2)->nullable()->after('longitude')->comment('Distance in km');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pickup_requests', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'estimated_distance']);
        });
    }
};
