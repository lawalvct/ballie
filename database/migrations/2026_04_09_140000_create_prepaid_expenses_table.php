<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prepaid_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('voucher_id')->constrained('vouchers')->onDelete('cascade');
            $table->foreignId('voucher_entry_id')->constrained('voucher_entries')->onDelete('cascade');
            $table->foreignId('prepaid_account_id')->constrained('ledger_accounts')->onDelete('restrict');
            $table->foreignId('expense_account_id')->constrained('ledger_accounts')->onDelete('restrict');
            $table->decimal('total_amount', 15, 2);
            $table->decimal('installment_amount', 15, 2);
            $table->unsignedSmallInteger('installments_count');
            $table->unsignedSmallInteger('installments_posted')->default(0);
            $table->string('frequency', 20)->default('monthly'); // monthly, quarterly, yearly
            $table->date('start_date');
            $table->date('next_posting_date')->nullable();
            $table->date('end_date');
            $table->string('description', 500)->nullable();
            $table->string('status', 20)->default('active'); // active, completed, cancelled, paused
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('meta_data')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status', 'next_posting_date'], 'prepaid_tenant_status_next');
            $table->index('voucher_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prepaid_expenses');
    }
};
