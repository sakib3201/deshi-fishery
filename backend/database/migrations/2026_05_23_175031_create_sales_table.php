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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pond_id')->constrained()->cascadeOnDelete();
            $table->string('sale_code')->unique();
            $table->enum('sale_type', ['wholesale', 'retail']);
            $table->date('date');
            $table->string('fish_type');
            $table->decimal('avg_fish_weight_g', 8, 2);
            $table->decimal('quantity_kg', 10, 2);
            $table->decimal('rate_per_kg', 12, 2);
            $table->decimal('total_amount', 12, 2);
            $table->string('customer_name')->nullable();
            $table->json('custom_tags')->nullable();
            $table->enum('payment_status', ['pending', 'partial', 'paid'])->default('pending');
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->decimal('amount_due', 12, 2);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index('farm_id');
            $table->index('pond_id');
            $table->index('date');
            $table->index('payment_status');
            $table->index('sale_type');
            $table->index(['farm_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
