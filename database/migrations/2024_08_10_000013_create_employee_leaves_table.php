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
        Schema::create('employee_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('leave_type_id')->constrained('leave_types')->onDelete('restrict');
            $table->string('leave_number')->unique(); // LV-2024-001
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_days', 8, 2); // Total leave days (excluding weekends/holidays if configured)
            $table->decimal('working_days', 8, 2); // Actual working days affected
            $table->text('reason');
            $table->text('employee_remarks')->nullable();
            $table->string('document_path')->nullable(); // Medical certificate, etc.
            $table->string('contact_during_leave')->nullable(); // Phone number or address
            $table->foreignId('reliever_id')->nullable()->constrained('employees'); // Who covers during leave

            // Approval workflow
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_remarks')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users');
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users');
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();

            // Tracking
            $table->date('actual_return_date')->nullable(); // If different from end_date
            $table->boolean('is_emergency')->default(false);
            $table->boolean('is_half_day')->default(false);
            $table->enum('half_day_period', ['morning', 'afternoon'])->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['employee_id', 'start_date', 'end_date']);
            $table->index('leave_type_id');
            $table->index('leave_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_leaves');
    }
};
