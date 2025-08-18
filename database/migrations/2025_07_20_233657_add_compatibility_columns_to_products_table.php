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
        Schema::table('products', function (Blueprint $table) {
            // Add compatibility columns if they don't exist
            if (!Schema::hasColumn('products', 'quantity')) {
                $table->decimal('quantity', 15, 2)->nullable()->after('current_stock');
            }

            if (!Schema::hasColumn('products', 'selling_price')) {
                $table->decimal('selling_price', 15, 2)->nullable()->after('sales_rate');
            }

            if (!Schema::hasColumn('products', 'minimum_stock_level')) {
                $table->decimal('minimum_stock_level', 15, 2)->nullable()->after('reorder_level');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'selling_price', 'minimum_stock_level']);
        });
    }
};
