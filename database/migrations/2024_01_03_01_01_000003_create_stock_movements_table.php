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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

            $table->string('type'); // opening_stock, purchase, sale, adjustment, return, transfer_in, transfer_out, damage
            $table->decimal('quantity', 15, 2); // Can be negative for outward movements
            $table->decimal('old_stock', 15, 2);
            $table->decimal('new_stock', 15, 2);
            $table->decimal('rate', 15, 2)->default(0);
            $table->string('reference')->nullable(); // Reference number (invoice, purchase order, etc.)
            $table->text('remarks')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();

            // Indexes
            $table->index('tenant_id');
            $table->index('product_id');
            $table->index('type');
            $table->index('created_at');
            $table->index(['tenant_id', 'product_id']);
            $table->index(['tenant_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};