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
        Schema::table('stock_movements', function (Blueprint $table) {
            // Add transaction tracking fields
            $table->enum('transaction_type', [
                'opening_stock', 'purchase', 'sales', 'stock_journal',
                'physical_adjustment', 'transfer_in', 'transfer_out',
                'invoice', 'purchase_return', 'sales_return', 'manufacturing'
            ])->after('type')->default('stock_journal');

            $table->date('transaction_date')->after('created_at')->default(now());
            $table->string('transaction_reference')->nullable()->after('transaction_type');

            // Polymorphic relationship to source transactions
            $table->string('source_transaction_type')->nullable()->after('transaction_reference');
            $table->unsignedBigInteger('source_transaction_id')->nullable()->after('source_transaction_type');

            // Additional tracking fields
            $table->string('batch_number')->nullable()->after('source_transaction_id');
            $table->date('expiry_date')->nullable()->after('batch_number');
            $table->json('additional_data')->nullable()->after('expiry_date');

            // Add indexes for performance
            $table->index(['product_id', 'transaction_date'], 'idx_product_transaction_date');
            $table->index(['tenant_id', 'transaction_date'], 'idx_tenant_transaction_date');
            $table->index(['transaction_type', 'transaction_date'], 'idx_transaction_type_date');
            $table->index(['source_transaction_type', 'source_transaction_id'], 'idx_source_transaction');
            $table->index(['tenant_id', 'product_id', 'transaction_date'], 'idx_tenant_product_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_product_transaction_date');
            $table->dropIndex('idx_tenant_transaction_date');
            $table->dropIndex('idx_transaction_type_date');
            $table->dropIndex('idx_source_transaction');
            $table->dropIndex('idx_tenant_product_date');

            // Drop columns
            $table->dropColumn([
                'transaction_type',
                'transaction_date',
                'transaction_reference',
                'source_transaction_type',
                'source_transaction_id',
                'batch_number',
                'expiry_date',
                'additional_data'
            ]);
        });
    }
};
