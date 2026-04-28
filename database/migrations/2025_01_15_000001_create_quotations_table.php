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
        // Defensive guard: this migration is dated before tables it depends on
        // (vouchers, customers, vendors, ledger_accounts, users) when running
        // a fresh `migrate` / `migrate:fresh`. On production this migration
        // already ran successfully, so the table exists and we no-op. On a
        // fresh DB the dependencies are not yet present and a later migration
        // (2025_09_03_000001_ensure_quotation_tables_and_columns) creates
        // everything once the deps exist.
        if (Schema::hasTable('quotations')) {
            return;
        }
        foreach (['tenants', 'customers', 'vendors', 'ledger_accounts', 'users', 'vouchers'] as $dep) {
            if (!Schema::hasTable($dep)) {
                return; // Deferred to 2025_09_03_000001.
            }
        }

        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');

            // Quotation Details
            $table->string('quotation_number'); // QT-2025-0001
            $table->date('quotation_date');
            $table->date('expiry_date')->nullable(); // Quote validity period

            // Customer/Vendor Information
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->onDelete('set null');
            $table->foreignId('customer_ledger_id')->constrained('ledger_accounts')->onDelete('restrict');

            // Reference and Description
            $table->string('reference_number')->nullable();
            $table->string('subject')->nullable(); // Quote title/subject
            $table->text('terms_and_conditions')->nullable();
            $table->text('notes')->nullable();

            // Financial Details
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);

            // Status Management
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected', 'expired', 'converted'])->default('draft');

            // Conversion Tracking
            $table->foreignId('converted_to_invoice_id')->nullable()->constrained('vouchers')->onDelete('set null');
            $table->timestamp('converted_at')->nullable();

            // Status Timestamps
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();

            // Audit Fields
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'quotation_date']);
            $table->index(['tenant_id', 'quotation_number']);
            $table->index('customer_id');
            $table->index('vendor_id');
            $table->index('customer_ledger_id');
            $table->index('converted_to_invoice_id');
            $table->index('expiry_date');
            $table->unique(['tenant_id', 'quotation_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
