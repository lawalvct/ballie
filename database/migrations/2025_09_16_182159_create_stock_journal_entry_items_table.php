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
        Schema::create('stock_journal_entry_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_journal_entry_id')->constrained('stock_journal_entries')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

            // Item details
            $table->enum('movement_type', ['in', 'out']); // In for receipt/production, Out for consumption/issue
            $table->decimal('quantity', 15, 4); // Quantity with 4 decimal precision
            $table->decimal('rate', 15, 2)->default(0); // Rate per unit
            $table->decimal('amount', 15, 2)->default(0); // Total amount (quantity * rate)

            // Stock tracking
            $table->decimal('stock_before', 15, 4)->default(0); // Stock before this transaction
            $table->decimal('stock_after', 15, 4)->default(0); // Stock after this transaction

            // Additional details
            $table->string('batch_number')->nullable(); // Batch/Lot number
            $table->date('expiry_date')->nullable(); // Expiry date for batch
            $table->text('remarks')->nullable(); // Line item remarks

            $table->timestamps();

            // Indexes
            $table->index(['stock_journal_entry_id', 'product_id'], 'sj_items_entry_product_idx');
            $table->index('movement_type');
            $table->index('batch_number');
            $table->index('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_journal_entry_items');
    }
};
