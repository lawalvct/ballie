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
        Schema::create('affiliate_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained()->onDelete('cascade');
            $table->foreignId('referred_tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('referral_source')->default('direct'); // direct, social, email, etc.
            $table->string('conversion_type')->default('registration'); // registration, first_payment, etc.
            $table->decimal('conversion_value', 15, 2)->default(0); // Value of first payment
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->timestamp('conversion_date')->nullable(); // When they made first payment
            $table->json('tracking_data')->nullable(); // UTM parameters, etc.
            $table->timestamps();

            $table->index(['affiliate_id']);
            $table->index(['referred_tenant_id']);
            $table->index(['status']);
            $table->index(['conversion_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_referrals');
    }
};
