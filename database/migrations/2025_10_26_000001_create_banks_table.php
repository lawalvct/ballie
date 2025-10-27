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
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('ledger_account_id')->nullable()->constrained('ledger_accounts')->onDelete('set null');

            // Bank Information
            $table->string('bank_name'); // e.g., First Bank, GTBank, Access Bank
            $table->string('account_name'); // Account holder name
            $table->string('account_number')->unique(); // Bank account number
            $table->string('account_type')->nullable(); // savings, current, fixed deposit, etc.
            $table->string('branch_name')->nullable();
            $table->string('branch_code')->nullable();
            $table->string('swift_code')->nullable(); // For international transfers
            $table->string('iban')->nullable(); // International Bank Account Number
            $table->string('routing_number')->nullable(); // For US banks
            $table->string('sort_code')->nullable(); // For UK banks

            // Contact Information
            $table->string('branch_address')->nullable();
            $table->string('branch_city')->nullable();
            $table->string('branch_state')->nullable();
            $table->string('branch_phone')->nullable();
            $table->string('branch_email')->nullable();
            $table->string('relationship_manager')->nullable(); // Account manager name
            $table->string('manager_phone')->nullable();
            $table->string('manager_email')->nullable();

            // Account Details
            $table->string('currency', 3)->default('NGN');
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->decimal('minimum_balance', 15, 2)->default(0); // Minimum balance requirement
            $table->decimal('overdraft_limit', 15, 2)->default(0); // Overdraft facility limit
            $table->date('account_opening_date')->nullable();
            $table->date('last_reconciliation_date')->nullable();
            $table->decimal('last_reconciled_balance', 15, 2)->nullable();

            // Online Banking
            $table->string('online_banking_url')->nullable();
            $table->string('online_banking_username')->nullable();
            $table->text('online_banking_notes')->nullable(); // Encrypted login details notes

            // Bank Charges & Limits
            $table->decimal('monthly_maintenance_fee', 15, 2)->default(0);
            $table->decimal('transaction_limit_daily', 15, 2)->nullable();
            $table->decimal('transaction_limit_monthly', 15, 2)->nullable();
            $table->integer('free_transactions_per_month')->default(0);
            $table->decimal('excess_transaction_fee', 15, 2)->default(0);

            // Additional Information
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->json('custom_fields')->nullable(); // For any additional custom data

            // Status & Flags
            $table->enum('status', ['active', 'inactive', 'closed', 'suspended'])->default('active');
            $table->boolean('is_primary')->default(false); // Mark as primary bank account
            $table->boolean('is_payroll_account')->default(false); // Used for payroll
            $table->boolean('enable_reconciliation')->default(true);
            $table->boolean('enable_auto_import')->default(false); // For bank feed integration

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('tenant_id');
            $table->index('ledger_account_id');
            $table->index('bank_name');
            $table->index('account_number');
            $table->index('status');
            $table->index('is_primary');
            $table->index('account_opening_date');
            $table->index('last_reconciliation_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banks');
    }
};
