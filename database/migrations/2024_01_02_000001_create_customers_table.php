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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->enum('customer_type', ['individual', 'business'])->default('individual');

            // Individual fields
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();

            // Business fields
            $table->string('company_name')->nullable();
            $table->string('tax_id')->nullable();

            // Contact information
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();

            // Address information
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();

            // Financial information
            $table->string('currency', 3)->default('NGN');
            $table->string('payment_terms')->nullable();
            $table->decimal('total_spent', 15, 2)->default(0);
            $table->timestamp('last_invoice_date')->nullable();
            $table->string('last_invoice_number')->nullable();

            // Additional information
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('tenant_id');
            $table->index('email');
            $table->index('customer_type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
