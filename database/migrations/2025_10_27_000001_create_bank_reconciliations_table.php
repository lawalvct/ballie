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
        Schema::create('bank_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('bank_id');

            // Reconciliation Period
            $table->date('reconciliation_date');
            $table->string('statement_number')->nullable();
            $table->date('statement_start_date');
            $table->date('statement_end_date');

            // Balances
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('closing_balance_per_bank', 15, 2);
            $table->decimal('closing_balance_per_books', 15, 2);
            $table->decimal('difference', 15, 2)->default(0);

            // Status
            $table->enum('status', ['draft', 'in_progress', 'completed', 'cancelled'])->default('draft');

            // Reconciliation Details
            $table->integer('total_transactions')->default(0);
            $table->integer('reconciled_transactions')->default(0);
            $table->integer('unreconciled_transactions')->default(0);

            // Adjustments
            $table->decimal('bank_charges', 15, 2)->default(0);
            $table->decimal('interest_earned', 15, 2)->default(0);
            $table->decimal('other_adjustments', 15, 2)->default(0);

            // Notes
            $table->text('notes')->nullable();
            $table->text('discrepancy_notes')->nullable();

            // Audit Fields
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('bank_id')->references('id')->on('banks')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('completed_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['tenant_id', 'bank_id'], 'idx_recon_tenant_bank');
            $table->index('reconciliation_date', 'idx_recon_date');
            $table->index('status', 'idx_recon_status');
            $table->index(['statement_start_date', 'statement_end_date'], 'idx_recon_period');
        });

        // Create pivot table for reconciliation items
        Schema::create('bank_reconciliation_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bank_reconciliation_id');
            $table->unsignedBigInteger('voucher_entry_id');

            // Item Details
            $table->date('transaction_date');
            $table->string('transaction_type'); // voucher, bank_charge, interest, adjustment
            $table->string('reference_number')->nullable();
            $table->text('description')->nullable();

            // Amounts
            $table->decimal('debit_amount', 15, 2)->default(0);
            $table->decimal('credit_amount', 15, 2)->default(0);

            // Reconciliation Status
            $table->enum('status', ['cleared', 'uncleared', 'excluded'])->default('uncleared');
            $table->date('cleared_date')->nullable();

            // Bank Statement Details
            $table->date('bank_statement_date')->nullable();
            $table->string('bank_reference')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            // Foreign Keys
            $table->foreign('bank_reconciliation_id', 'fk_recon_items_recon')
                  ->references('id')
                  ->on('bank_reconciliations')
                  ->onDelete('cascade');
            $table->foreign('voucher_entry_id')->references('id')->on('voucher_entries')->onDelete('cascade');

            // Indexes
            $table->index('status', 'idx_recon_item_status');
            $table->index('transaction_date', 'idx_recon_item_date');
            $table->index(['bank_reconciliation_id', 'status'], 'idx_recon_item_recon_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_reconciliation_items');
        Schema::dropIfExists('bank_reconciliations');
    }
};
