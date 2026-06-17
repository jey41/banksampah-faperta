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
        // 1. Create mutations table (double-entry ledger)
        Schema::create('mutations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['debit', 'kredit']); // debit: penarikan, kredit: setoran
            $table->bigInteger('amount');
            $table->nullableMorphs('sourceable'); // polymorph to Deposit / Withdrawal
            $table->bigInteger('balance_before');
            $table->bigInteger('balance_after');
            $table->timestamps();
        });

        // 2. Create activity_logs table (audit trail)
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // admin/petugas yang melakukan aksi
            $table->string('action');
            $table->text('description');
            $table->timestamps();
        });

        // 3. Create savings_targets table
        Schema::create('savings_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->bigInteger('target_amount');
            $table->boolean('is_achieved')->default(false);
            $table->timestamps();
        });

        // 4. Add is_donation to deposits
        Schema::table('deposits', function (Blueprint $table) {
            $table->boolean('is_donation')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn('is_donation');
        });

        Schema::dropIfExists('savings_targets');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('mutations');
    }
};
