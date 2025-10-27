<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('plan'); // starter, professional, enterprise
            $table->string('billing_cycle'); // monthly, yearly
            $table->integer('amount'); // Amount in kobo (Nigerian currency)
            $table->string('currency', 3)->default('NGN');
            $table->string('status'); // active, cancelled, expired, suspended
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->timestamp('cancelled_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
        });

        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->integer('amount'); // Amount in kobo
            $table->string('currency', 3)->default('NGN');
            $table->string('status'); // pending, successful, failed, cancelled
            $table->string('payment_method');
            $table->string('payment_reference')->unique();
            $table->string('gateway_reference')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('gateway_response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
        Schema::dropIfExists('subscriptions');
    }
};
