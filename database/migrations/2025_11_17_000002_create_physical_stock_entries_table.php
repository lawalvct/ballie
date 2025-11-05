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
        Schema::create('physical_stock_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('physical_stock_voucher_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->decimal('book_quantity', 15, 4)->default(0)->comment('Quantity as per books/system');
            $table->decimal('physical_quantity', 15, 4)->default(0)->comment('Actual physical quantity counted');
            $table->decimal('difference_quantity', 15, 4)->default(0)->comment('Physical - Book quantity');
            $table->decimal('current_rate', 15, 2)->default(0)->comment('Rate used for valuation');
            $table->decimal('difference_value', 15, 2)->default(0)->comment('Difference quantity * rate');
            $table->string('batch_number')->nullable()->comment('Batch/Lot number if applicable');
            $table->date('expiry_date')->nullable()->comment('Expiry date for batch items');
            $table->string('location')->nullable()->comment('Storage location/warehouse');
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes for performance
            $table->index(['physical_stock_voucher_id', 'product_id'], 'idx_pse_voucher_product');
            $table->index(['product_id', 'batch_number'], 'idx_pse_product_batch');
            $table->index(['difference_quantity'], 'idx_pse_difference'); // For filtering non-zero differences

            // Ensure one entry per product per voucher (unless using batches)
            $table->unique(['physical_stock_voucher_id', 'product_id', 'batch_number'], 'unq_pse_voucher_product_batch');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('physical_stock_entries');
    }
};
