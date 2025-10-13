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
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('affiliate_code')->unique();
            $table->string('company_name');
            $table->string('phone')->nullable();
            $table->text('bio')->nullable();
            $table->decimal('custom_commission_rate', 5, 2)->nullable(); // Override global rate
            $table->enum('status', ['pending', 'active', 'suspended', 'rejected'])->default('pending');
            $table->integer('total_referrals')->default(0);
            $table->decimal('total_commissions', 15, 2)->default(0);
            $table->decimal('total_paid', 15, 2)->default(0);
            $table->json('payment_details')->nullable(); // Bank details, PayPal, etc.
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('last_payout_at')->nullable();
            $table->timestamps();

            $table->index(['affiliate_code']);
            $table->index(['status']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliates');
    }
};
