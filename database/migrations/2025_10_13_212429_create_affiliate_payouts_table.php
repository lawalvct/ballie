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
        Schema::create('affiliate_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained()->onDelete('cascade');
            $table->decimal('total_amount', 15, 2); // Total payout amount
            $table->decimal('fee_amount', 15, 2)->default(0); // Platform fee deducted
            $table->decimal('net_amount', 15, 2); // Amount actually paid
            $table->string('payout_method'); // bank_transfer, paypal, stripe, etc.
            $table->json('payout_details')->nullable(); // Transaction details, reference numbers
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('requested_at');
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['affiliate_id']);
            $table->index(['status']);
            $table->index(['requested_at']);
            $table->index(['processed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_payouts');
    }
};
