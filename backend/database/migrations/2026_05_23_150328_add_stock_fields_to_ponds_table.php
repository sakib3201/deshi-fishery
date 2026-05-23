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
        Schema::table('ponds', function (Blueprint $table) {
            $table->unsignedInteger('current_stock_quantity')->default(0)->after('size');
            $table->decimal('current_stock_weight_kg', 10, 2)->default(0.00)->after('current_stock_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ponds', function (Blueprint $table) {
            $table->dropColumn(['current_stock_quantity', 'current_stock_weight_kg']);
        });
    }
};
