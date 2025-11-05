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
        Schema::create('employee_shift_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('shift_id')->constrained('shift_schedules')->onDelete('cascade');
            $table->date('effective_from');
            $table->date('effective_to')->nullable(); // Null means ongoing
            $table->boolean('is_permanent')->default(false); // Permanent or temporary assignment
            $table->text('remarks')->nullable();
            $table->boolean('is_active')->default(true);

            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index(['tenant_id', 'is_active'], 'emp_shift_assign_tenant_active');
            $table->index(['employee_id', 'effective_from', 'effective_to'], 'emp_shift_assign_dates');
            $table->index('shift_id', 'emp_shift_assign_shift');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_shift_assignments');
    }
};
