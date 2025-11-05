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
        Schema::create('overtime_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('overtime_number')->unique(); // OT-2024-001
            $table->date('overtime_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('total_hours'); // Total overtime hours
            $table->decimal('hourly_rate', 10, 2); // Rate per hour
            $table->decimal('multiplier', 4, 2)->default(1.5); // 1.5x, 2x for weekends/holidays
            $table->decimal('total_amount', 10, 2); // Calculated overtime pay
            $table->text('reason');
            $table->text('work_description')->nullable(); // What work was done

            // Type
            $table->enum('overtime_type', ['weekday', 'weekend', 'holiday', 'emergency'])->default('weekday');

            // Approval workflow
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_remarks')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users');
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();

            // Payment tracking
            $table->foreignId('payroll_run_id')->nullable()->constrained('payroll_runs');
            $table->boolean('is_paid')->default(false);
            $table->date('paid_date')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['employee_id', 'overtime_date']);
            $table->index('overtime_number');
            $table->index(['is_paid', 'payroll_run_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overtime_records');
    }
};
