<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('ledger_account_id')->nullable()->constrained('ledger_accounts')->onDelete('set null');

            $table->enum('vendor_type', ['individual', 'business'])->default('business');

            // Individual fields
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();

            // Business fields
            $table->string('company_name')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('registration_number')->nullable();

            // Contact information
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('website')->nullable();

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
            $table->decimal('total_purchases', 15, 2)->default(0);
            $table->decimal('outstanding_balance', 15, 2)->default(0);
            $table->timestamp('last_purchase_date')->nullable();
            $table->string('last_purchase_number')->nullable();

            // Banking information
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_name')->nullable();

            // Additional information
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('tenant_id');
            $table->index('ledger_account_id');
            $table->index('email');
            $table->index('vendor_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
