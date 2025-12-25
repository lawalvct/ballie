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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');

            // Coupon Details
            $table->string('code'); // Coupon code
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('value', 15, 2); // Percentage or fixed amount

            // Constraints
            $table->decimal('min_order_amount', 15, 2)->nullable(); // Minimum order amount required
            $table->decimal('max_discount_amount', 15, 2)->nullable(); // Maximum discount for percentage types

            // Usage Limits
            $table->integer('usage_limit')->nullable(); // Total times it can be used (null = unlimited)
            $table->integer('usage_count')->default(0); // Current usage count
            $table->integer('per_customer_limit')->nullable(); // Times per customer (null = unlimited)

            // Validity Period
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Indexes
            $table->index('tenant_id');
            $table->index('code');
            $table->index('is_active');
            $table->index('valid_from');
            $table->index('valid_to');

            // Unique coupon code per tenant
            $table->unique(['tenant_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
