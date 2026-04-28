<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Deferred catch-up for the three Jan-15 quotation migrations whose original
 * filenames are dated BEFORE their dependency tables (vouchers, products,
 * customers, vendors, ledger_accounts, users) are created on a fresh DB.
 *
 * On production: the original Jan-15 migrations already ran successfully when
 * the underlying dependency tables existed, so the schema is fully present
 * here and every guard below short-circuits — this migration is a no-op.
 *
 * On a fresh `migrate:fresh` / `RefreshDatabase` test run: the original
 * Jan-15 migrations no-op (deferred via their own dependency guards) and
 * this migration creates the three pieces in the correct dependency order.
 *
 * Safe to re-run; every block is guarded by hasTable / hasColumn.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. quotations
        if (
            !Schema::hasTable('quotations')
            && Schema::hasTable('tenants')
            && Schema::hasTable('customers')
            && Schema::hasTable('vendors')
            && Schema::hasTable('ledger_accounts')
            && Schema::hasTable('users')
            && Schema::hasTable('vouchers')
        ) {
            Schema::create('quotations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');

                $table->string('quotation_number');
                $table->date('quotation_date');
                $table->date('expiry_date')->nullable();

                $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
                $table->foreignId('vendor_id')->nullable()->constrained('vendors')->onDelete('set null');
                $table->foreignId('customer_ledger_id')->constrained('ledger_accounts')->onDelete('restrict');

                $table->string('reference_number')->nullable();
                $table->string('subject')->nullable();
                $table->text('terms_and_conditions')->nullable();
                $table->text('notes')->nullable();

                $table->decimal('subtotal', 15, 2)->default(0);
                $table->decimal('discount_amount', 15, 2)->default(0);
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->decimal('total_amount', 15, 2)->default(0);

                $table->enum('status', ['draft', 'sent', 'accepted', 'rejected', 'expired', 'converted'])->default('draft');

                $table->foreignId('converted_to_invoice_id')->nullable()->constrained('vouchers')->onDelete('set null');
                $table->timestamp('converted_at')->nullable();

                $table->timestamp('sent_at')->nullable();
                $table->timestamp('accepted_at')->nullable();
                $table->timestamp('rejected_at')->nullable();
                $table->text('rejection_reason')->nullable();

                $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

                $table->timestamps();
                $table->softDeletes();

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

        // 2. quotation_items
        if (
            !Schema::hasTable('quotation_items')
            && Schema::hasTable('quotations')
            && Schema::hasTable('products')
        ) {
            Schema::create('quotation_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('quotation_id')->constrained('quotations')->onDelete('cascade');
                $table->foreignId('product_id')->constrained('products')->onDelete('restrict');

                $table->string('product_name');
                $table->text('description')->nullable();
                $table->decimal('quantity', 15, 2);
                $table->string('unit')->nullable();
                $table->decimal('rate', 15, 2);

                $table->decimal('discount', 15, 2)->default(0);
                $table->decimal('tax', 15, 2)->default(0);
                $table->boolean('is_tax_inclusive')->default(false);
                $table->decimal('amount', 15, 2);
                $table->decimal('total', 15, 2)->nullable();

                $table->integer('sort_order')->default(0);

                $table->timestamps();

                $table->index('quotation_id');
                $table->index('product_id');
                $table->index('sort_order');
            });
        }

        // 3. vouchers.quotation_id column
        if (
            Schema::hasTable('vouchers')
            && Schema::hasTable('quotations')
            && !Schema::hasColumn('vouchers', 'quotation_id')
        ) {
            Schema::table('vouchers', function (Blueprint $table) {
                $table->foreignId('quotation_id')->nullable()->after('voucher_type_id')
                    ->constrained('quotations')->onDelete('set null');
                $table->index('quotation_id');
            });
        }
    }

    public function down(): void
    {
        // Intentionally empty. The original Jan-15 migrations own the
        // schema lifecycle for these objects; rolling them back is their
        // responsibility.
    }
};
