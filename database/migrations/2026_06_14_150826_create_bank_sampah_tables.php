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
        Schema::create('trash_prices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', ['plastik', 'kertas', 'logam', 'kaca', 'minyak_jelantah', 'lainnya']);
            $table->bigInteger('price_buy'); // Harga beli dari nasabah (per kg/L)
            $table->bigInteger('price_sell'); // Harga jual ke pabrik (per kg/L)
            $table->string('unit')->default('kg');
            $table->timestamps();
        });

        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->bigInteger('total_price')->default(0);
            $table->decimal('weight_total', 10, 2)->default(0.00);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('deposit_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deposit_id')->constrained('deposits')->onDelete('cascade');
            $table->foreignId('trash_price_id')->constrained('trash_prices')->onDelete('restrict');
            $table->decimal('weight', 10, 2);
            $table->bigInteger('price_per_unit');
            $table->bigInteger('total_price');
            $table->timestamps();
        });

        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->bigInteger('amount');
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('account_name');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->text('image_url')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
        Schema::dropIfExists('withdrawals');
        Schema::dropIfExists('deposit_items');
        Schema::dropIfExists('deposits');
        Schema::dropIfExists('trash_prices');
    }
};
