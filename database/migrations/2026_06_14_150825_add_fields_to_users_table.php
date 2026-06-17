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
            $table->enum('role', ['admin', 'petugas', 'nasabah'])->default('nasabah')->after('password');
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending')->after('role');
            $table->string('phone')->nullable()->after('status');
            $table->text('address')->nullable()->after('phone');
            $table->bigInteger('saldo')->default(0)->after('address');
            $table->string('account_no')->unique()->nullable()->after('saldo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'status', 'phone', 'address', 'saldo', 'account_no']);
        });
    }
};
