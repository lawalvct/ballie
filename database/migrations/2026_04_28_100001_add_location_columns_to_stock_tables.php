<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->unsignedBigInteger('stock_location_id')->nullable()->after('product_id');
            $table->unsignedBigInteger('from_stock_location_id')->nullable()->after('stock_location_id');
            $table->unsignedBigInteger('to_stock_location_id')->nullable()->after('from_stock_location_id');

            $table->foreign('stock_location_id')->references('id')->on('stock_locations')->nullOnDelete();
            $table->foreign('from_stock_location_id')->references('id')->on('stock_locations')->nullOnDelete();
            $table->foreign('to_stock_location_id')->references('id')->on('stock_locations')->nullOnDelete();

            $table->index(['tenant_id', 'stock_location_id', 'product_id'], 'sm_tenant_loc_prod_idx');
            $table->index(['tenant_id', 'product_id', 'stock_location_id'], 'sm_tenant_prod_loc_idx');
        });

        Schema::table('stock_journal_entries', function (Blueprint $table) {
            $table->unsignedBigInteger('from_stock_location_id')->nullable()->after('entry_type');
            $table->unsignedBigInteger('to_stock_location_id')->nullable()->after('from_stock_location_id');

            $table->foreign('from_stock_location_id')->references('id')->on('stock_locations')->nullOnDelete();
            $table->foreign('to_stock_location_id')->references('id')->on('stock_locations')->nullOnDelete();
        });

        Schema::table('stock_journal_entry_items', function (Blueprint $table) {
            $table->unsignedBigInteger('stock_location_id')->nullable()->after('product_id');

            $table->foreign('stock_location_id')->references('id')->on('stock_locations')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stock_journal_entry_items', function (Blueprint $table) {
            $table->dropForeign(['stock_location_id']);
            $table->dropColumn('stock_location_id');
        });

        Schema::table('stock_journal_entries', function (Blueprint $table) {
            $table->dropForeign(['from_stock_location_id']);
            $table->dropForeign(['to_stock_location_id']);
            $table->dropColumn(['from_stock_location_id', 'to_stock_location_id']);
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['stock_location_id']);
            $table->dropForeign(['from_stock_location_id']);
            $table->dropForeign(['to_stock_location_id']);
            $table->dropIndex('sm_tenant_loc_prod_idx');
            $table->dropIndex('sm_tenant_prod_loc_idx');
            $table->dropColumn(['stock_location_id', 'from_stock_location_id', 'to_stock_location_id']);
        });
    }
};
