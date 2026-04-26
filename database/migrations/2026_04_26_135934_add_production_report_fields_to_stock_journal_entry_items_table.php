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
        Schema::table('stock_journal_entry_items', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_journal_entry_items', 'unit_snapshot')) {
                $table->string('unit_snapshot', 50)->nullable()->after('quantity');
            }
            if (!Schema::hasColumn('stock_journal_entry_items', 'rejected_quantity')) {
                $table->decimal('rejected_quantity', 15, 4)->default(0)->after('unit_snapshot');
            }
            if (!Schema::hasColumn('stock_journal_entry_items', 'waste_quantity')) {
                $table->decimal('waste_quantity', 15, 4)->default(0)->after('rejected_quantity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_journal_entry_items', function (Blueprint $table) {
            foreach (['waste_quantity', 'rejected_quantity', 'unit_snapshot'] as $column) {
                if (Schema::hasColumn('stock_journal_entry_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
