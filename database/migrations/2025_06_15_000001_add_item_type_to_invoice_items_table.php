<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            // Add item_type column: 'product' for inventory items, 'service' for service items
            $table->enum('item_type', ['product', 'service'])->default('product')->after('voucher_id');

            // Make product_id nullable for service items (no product reference needed)
            $table->foreignId('product_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('item_type');
            // Revert product_id to non-nullable
            $table->foreignId('product_id')->nullable(false)->change();
        });
    }
};
