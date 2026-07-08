<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change existing 'admin' to 'super_admin'
        DB::table('users')->where('role', 'admin')->update(['role' => 'super_admin']);

        // Alter enum to only have 3 roles (MySQL only)
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'petugas', 'nasabah') DEFAULT 'nasabah'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'admin', 'petugas', 'nasabah') DEFAULT 'nasabah'");
        }
    }
};
