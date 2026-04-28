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
        // Defensive guard. See note in 2025_01_15_000001_create_quotations_table.
        // Deferred to 2025_09_03_000001 when running a fresh database.
        if (!Schema::hasTable('vouchers') || !Schema::hasTable('quotations')) {
            return;
        }
        if (Schema::hasColumn('vouchers', 'quotation_id')) {
            return;
        }

        Schema::table('vouchers', function (Blueprint $table) {
            // Add quotation reference to track which quotation was converted to this invoice
            $table->foreignId('quotation_id')->nullable()->after('voucher_type_id')
                ->constrained('quotations')->onDelete('set null');

            // Add index for better query performance
            $table->index('quotation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropForeign(['quotation_id']);
            $table->dropIndex(['quotation_id']);
            $table->dropColumn('quotation_id');
        });
    }
};
