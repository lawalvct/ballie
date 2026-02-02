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
        Schema::create('payout_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');

            // Request details
            $table->string('request_number')->unique();
            $table->decimal('requested_amount', 15, 2);
            $table->decimal('deduction_amount', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2); // Amount after deductions
            $table->string('deduction_type')->nullable(); // 'percentage' or 'fixed'
            $table->decimal('deduction_rate', 8, 2)->nullable(); // Rate applied

            // Available balance at time of request
            $table->decimal('available_balance', 15, 2);

            // Bank account details
            $table->string('bank_name');
            $table->string('account_name');
            $table->string('account_number');
            $table->string('bank_code')->nullable(); // For automated transfers

            // Status tracking
            $table->enum('status', ['pending', 'approved', 'processing', 'completed', 'rejected', 'cancelled'])
                  ->default('pending');

            // Processing details
            $table->foreignId('processed_by')->nullable()->constrained('super_admins')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->string('payment_reference')->nullable(); // Transfer reference
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();

            // Request notes from tenant
            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['tenant_id', 'status']);
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payout_requests');
    }
};
