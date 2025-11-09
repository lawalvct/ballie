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
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->date('attendance_date');

            // Clock In/Out Information
            $table->datetime('clock_in')->nullable();
            $table->datetime('clock_out')->nullable();
            $table->string('clock_in_ip')->nullable();
            $table->string('clock_out_ip')->nullable();
            $table->string('clock_in_location')->nullable();
            $table->string('clock_out_location')->nullable();
            $table->text('clock_in_notes')->nullable();
            $table->text('clock_out_notes')->nullable();

            // Scheduled Times
            $table->datetime('scheduled_in')->nullable();
            $table->datetime('scheduled_out')->nullable();

            // Time Calculations (in minutes)
            $table->integer('late_minutes')->default(0);
            $table->integer('early_out_minutes')->default(0);
            $table->integer('work_hours_minutes')->default(0); // Actual work hours
            $table->integer('break_minutes')->default(0);
            $table->integer('overtime_minutes')->default(0);

            // Status
            $table->enum('status', ['present', 'absent', 'late', 'half_day', 'on_leave', 'weekend', 'holiday'])->default('absent');
            $table->string('absence_reason')->nullable();

            // Approval
            $table->boolean('is_approved')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();

            // Shift Reference
            $table->foreignId('shift_id')->nullable()->constrained('shift_schedules');

            // Admin Notes
            $table->text('remarks')->nullable();
            $table->text('admin_notes')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->unique(['employee_id', 'attendance_date']);
            $table->index(['tenant_id', 'attendance_date']);
            $table->index(['status', 'is_approved']);
            $table->index('shift_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
