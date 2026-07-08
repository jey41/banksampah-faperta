<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add indexes for frequently filtered columns
        Schema::table('deposits', function (Blueprint $table) {
            $table->index('status');
            $table->index('created_at');
        });

        Schema::table('withdrawals', function (Blueprint $table) {
            $table->index('status');
            $table->index('created_at');
        });

        Schema::table('pickup_requests', function (Blueprint $table) {
            $table->index('status');
            $table->index('created_at');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index('action');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('pickup_requests', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex(['action']);
            $table->dropIndex(['created_at']);
        });
    }
};
