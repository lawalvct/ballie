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
        Schema::create('affiliate_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained()->onDelete('cascade');
            $table->foreignId('referred_tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('affiliate_referral_id')->constrained()->onDelete('cascade');
            $table->string('payment_reference')->nullable(); // Reference to payment system
            $table->decimal('payment_amount', 15, 2); // Total payment amount
            $table->decimal('commission_rate', 5, 2); // Rate used for this commission
            $table->decimal('commission_amount', 15, 2); // Actual commission earned
            $table->string('commission_type')->default('recurring'); // first_payment, recurring, bonus
            $table->enum('status', ['pending', 'approved', 'paid', 'cancelled'])->default('pending');
            $table->text('description')->nullable();
            $table->timestamp('payment_date'); // When customer made payment
            $table->timestamp('due_date')->nullable(); // When commission is due to be paid
            $table->timestamp('paid_date')->nullable(); // When commission was paid to affiliate
            $table->timestamps();

            $table->index(['affiliate_id']);
            $table->index(['referred_tenant_id']);
            $table->index(['status']);
            $table->index(['payment_date']);
            $table->index(['due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_commissions');
    }
};
