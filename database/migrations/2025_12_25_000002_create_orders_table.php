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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('order_number')->unique();

            // Customer Information
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->string('customer_email');
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();

            // Status
            $table->enum('status', ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid', 'partially_paid', 'refunded'])->default('unpaid');

            // Payment Information
            $table->enum('payment_method', ['cash_on_delivery', 'nomba', 'paystack', 'stripe', 'flutterwave', 'bank_transfer'])->default('cash_on_delivery');
            $table->string('payment_gateway_reference')->nullable();

            // Amounts
            $table->decimal('subtotal', 15, 2);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('shipping_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);

            // Coupon
            $table->string('coupon_code')->nullable();

            // Addresses
            $table->unsignedBigInteger('shipping_address_id')->nullable();
            $table->boolean('billing_same_as_shipping')->default(true);
            $table->unsignedBigInteger('billing_address_id')->nullable();

            // Notes
            $table->text('notes')->nullable(); // Customer notes
            $table->text('admin_notes')->nullable(); // Internal notes

            // Tracking
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            // Accounting Integration
            $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->onDelete('set null');

            // Timestamps
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('tenant_id');
            $table->index('customer_id');
            $table->index('order_number');
            $table->index('status');
            $table->index('payment_status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
