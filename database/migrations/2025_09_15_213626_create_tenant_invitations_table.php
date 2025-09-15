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
        Schema::create('tenant_invitations', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();

            // Company information
            $table->string('company_name');
            $table->string('company_email');
            $table->string('phone')->nullable();
            $table->string('business_type');

            // Owner information
            $table->string('owner_name');
            $table->string('owner_email');

            // Subscription details
            $table->unsignedBigInteger('plan_id');
            $table->enum('billing_cycle', ['monthly', 'yearly']);

            // Invitation details
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'accepted', 'expired', 'cancelled'])->default('pending');
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();

            // Tracking
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('tenant_id')->nullable(); // Set when invitation is accepted

            $table->timestamps();

            // Foreign keys
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('super_admins')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('set null');

            // Indexes
            $table->index(['token']);
            $table->index(['status']);
            $table->index(['expires_at']);
            $table->index(['owner_email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_invitations');
    }
};
