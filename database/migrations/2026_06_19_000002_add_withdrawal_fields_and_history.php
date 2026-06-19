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
        // Add withdrawal_method to withdrawals table
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->enum('withdrawal_method', ['tunai', 'transfer_bank'])->default('transfer_bank')->after('amount');
            $table->bigInteger('admin_fee')->default(0)->after('withdrawal_method');
            $table->string('bank_type')->nullable()->after('bank_name'); // 'btn' or 'other' or null for cash
        });

        // Create withdrawal_history table for tracking withdrawal lifecycle
        Schema::create('withdrawal_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('withdrawal_id')->constrained('withdrawals')->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'rejected', 'processed', 'completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawal_history');
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropColumn(['withdrawal_method', 'admin_fee', 'bank_type']);
        });
    }
};
