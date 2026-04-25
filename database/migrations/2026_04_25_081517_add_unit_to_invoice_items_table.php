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
        Schema::table('invoice_items', function (Blueprint $table) {
            if (!Schema::hasColumn('invoice_items', 'unit')) {
                $table->string('unit', 50)->nullable()->after('description');
            }
        });

        // Backfill from each item's product primary unit symbol where missing.
        if (Schema::hasColumn('invoice_items', 'unit')) {
            \Illuminate\Support\Facades\DB::statement("
                UPDATE invoice_items ii
                INNER JOIN products p ON p.id = ii.product_id
                LEFT JOIN units u ON u.id = p.primary_unit_id
                SET ii.unit = u.symbol
                WHERE (ii.unit IS NULL OR ii.unit = '') AND u.symbol IS NOT NULL
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            if (Schema::hasColumn('invoice_items', 'unit')) {
                $table->dropColumn('unit');
            }
        });
    }
};
