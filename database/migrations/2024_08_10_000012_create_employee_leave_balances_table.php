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
        Schema::create('employee_leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('leave_type_id')->constrained('leave_types')->onDelete('cascade');
            $table->integer('year'); // Leave balance year
            $table->decimal('opening_balance', 8, 2)->default(0); // Balance brought forward
            $table->decimal('allocated_days', 8, 2)->default(0); // Days allocated for this year
            $table->decimal('accrued_days', 8, 2)->default(0); // Days accrued so far (for monthly accrual)
            $table->decimal('used_days', 8, 2)->default(0); // Days already used
            $table->decimal('pending_days', 8, 2)->default(0); // Days in pending leave requests
            $table->decimal('available_days', 8, 2)->default(0); // Available = opening + allocated + accrued - used - pending
            $table->decimal('carried_forward', 8, 2)->default(0); // Days carried from previous year
            $table->decimal('expired_days', 8, 2)->default(0); // Days that expired (couldn't be carried forward)
            $table->date('last_accrual_date')->nullable(); // Last date accrual was calculated
            $table->timestamps();

            $table->unique(['employee_id', 'leave_type_id', 'year'], 'emp_leave_year_unique');
            $table->index(['tenant_id', 'year']);
            $table->index('employee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_leave_balances');
    }
};
