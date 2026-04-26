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
        Schema::table('stock_journal_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_journal_entries', 'operator_id')) {
                $table->foreignId('operator_id')->nullable()->after('entry_type')->constrained('employees')->nullOnDelete();
            }
            if (!Schema::hasColumn('stock_journal_entries', 'assistant_operator_id')) {
                $table->foreignId('assistant_operator_id')->nullable()->after('operator_id')->constrained('employees')->nullOnDelete();
            }
            if (!Schema::hasColumn('stock_journal_entries', 'production_batch_number')) {
                $table->string('production_batch_number')->nullable()->after('assistant_operator_id');
            }
            if (!Schema::hasColumn('stock_journal_entries', 'work_order_number')) {
                $table->string('work_order_number')->nullable()->after('production_batch_number');
            }
            if (!Schema::hasColumn('stock_journal_entries', 'production_shift')) {
                $table->string('production_shift', 50)->nullable()->after('work_order_number');
            }
            if (!Schema::hasColumn('stock_journal_entries', 'machine_name')) {
                $table->string('machine_name')->nullable()->after('production_shift');
            }
            if (!Schema::hasColumn('stock_journal_entries', 'production_started_at')) {
                $table->time('production_started_at')->nullable()->after('machine_name');
            }
            if (!Schema::hasColumn('stock_journal_entries', 'production_ended_at')) {
                $table->time('production_ended_at')->nullable()->after('production_started_at');
            }
            if (!Schema::hasColumn('stock_journal_entries', 'production_notes')) {
                $table->text('production_notes')->nullable()->after('production_ended_at');
            }

            $table->index(['tenant_id', 'entry_type', 'journal_date'], 'sj_entries_tenant_type_date_idx');
            $table->index(['tenant_id', 'operator_id'], 'sj_entries_tenant_operator_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_journal_entries', function (Blueprint $table) {
            $table->dropIndex('sj_entries_tenant_type_date_idx');
            $table->dropIndex('sj_entries_tenant_operator_idx');

            if (Schema::hasColumn('stock_journal_entries', 'assistant_operator_id')) {
                $table->dropConstrainedForeignId('assistant_operator_id');
            }
            if (Schema::hasColumn('stock_journal_entries', 'operator_id')) {
                $table->dropConstrainedForeignId('operator_id');
            }
            foreach ([
                'production_batch_number',
                'work_order_number',
                'production_shift',
                'machine_name',
                'production_started_at',
                'production_ended_at',
                'production_notes',
            ] as $column) {
                if (Schema::hasColumn('stock_journal_entries', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
