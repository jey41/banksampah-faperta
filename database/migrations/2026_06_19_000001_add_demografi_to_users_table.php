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
            $table->integer('umur')->nullable()->after('account_no');
            $table->enum('gender', ['L', 'P'])->nullable()->after('umur');
            $table->string('status_pekerjaan')->nullable()->after('gender');
            $table->string('universitas')->nullable()->after('status_pekerjaan');
            $table->string('fakultas')->nullable()->after('universitas');
            $table->string('pendidikan_terakhir')->nullable()->after('fakultas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['umur', 'gender', 'status_pekerjaan', 'universitas', 'fakultas', 'pendidikan_terakhir']);
        });
    }
};
