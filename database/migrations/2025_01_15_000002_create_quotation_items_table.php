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
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('quotations')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');

            // Item Details
            $table->string('product_name'); // Snapshot at time of quotation
            $table->text('description')->nullable();
            $table->decimal('quantity', 15, 2);
            $table->string('unit')->nullable(); // Unit of measurement
            $table->decimal('rate', 15, 2); // Unit price

            // Pricing Details
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->boolean('is_tax_inclusive')->default(false);
            $table->decimal('amount', 15, 2); // quantity * rate (before tax/discount)
            $table->decimal('total', 15, 2)->nullable(); // Final amount after tax/discount

            // Additional Information
            $table->integer('sort_order')->default(0); // For ordering items

            $table->timestamps();

            // Indexes
            $table->index('quotation_id');
            $table->index('product_id');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
    }
};
