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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');

            // Basic Information
            $table->enum('type', ['item', 'service'])->default('item');
            $table->string('name');
            $table->string('sku')->nullable();
            $table->text('description')->nullable();

            // Category relationship
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->onDelete('set null');

            $table->string('brand')->nullable();
            $table->string('hsn_code')->nullable();

            // Pricing
            $table->decimal('purchase_rate', 15, 2)->default(0);
            $table->decimal('sales_rate', 15, 2)->default(0);
            $table->decimal('mrp', 15, 2)->nullable();

            // Units - relationship with units table
            $table->foreignId('primary_unit_id')->nullable()->constrained('units')->onDelete('set null');
            $table->decimal('unit_conversion_factor', 12, 6)->default(1.000000);

            // Stock Management
            $table->decimal('opening_stock', 15, 2)->default(0);
            $table->decimal('current_stock', 15, 2)->default(0);
            $table->decimal('reorder_level', 15, 2)->nullable();

            // Ledger Integration (Basic)
            $table->foreignId('stock_asset_account_id')->nullable()->constrained('ledger_accounts')->onDelete('set null');
            $table->foreignId('sales_account_id')->nullable()->constrained('ledger_accounts')->onDelete('set null');
            $table->foreignId('purchase_account_id')->nullable()->constrained('ledger_accounts')->onDelete('set null');

            // Stock Valuation
            $table->decimal('opening_stock_value', 15, 2)->default(0);
            $table->decimal('current_stock_value', 15, 2)->default(0);

            // Taxation
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->boolean('tax_inclusive')->default(false);

            // Physical Properties
            $table->string('barcode')->nullable();
            $table->string('image_path')->nullable();

            // Options
            $table->boolean('maintain_stock')->default(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_saleable')->default(true);
            $table->boolean('is_purchasable')->default(true);

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('tenant_id');
            $table->index('category_id');
            $table->index('is_active');
            $table->index('sku');
            $table->index('barcode');
            $table->unique(['tenant_id', 'sku']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
