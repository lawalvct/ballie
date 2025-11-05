<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payroll_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_period_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');

            // Salary breakdown
            $table->decimal('basic_salary', 15, 2);
            $table->decimal('total_allowances', 15, 2)->default(0);
            $table->decimal('gross_salary', 15, 2);

            // Tax calculations
            $table->decimal('annual_gross', 15, 2); // For PAYE calculation
            $table->decimal('consolidated_relief', 15, 2); // 1% of annual gross or ₦200k minimum
            $table->decimal('taxable_income', 15, 2);
            $table->decimal('annual_tax', 15, 2);
            $table->decimal('monthly_tax', 15, 2);

            // NSITF (1% of annual gross for employers)
            $table->decimal('nsitf_contribution', 15, 2)->default(0);

            // Other deductions
            $table->decimal('other_deductions', 15, 2)->default(0);
            $table->decimal('total_deductions', 15, 2);
            $table->decimal('net_salary', 15, 2);

            // Payment tracking
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_reference')->nullable();

            // Accounting integration
            $table->foreignId('salary_expense_voucher_id')->nullable()->constrained('vouchers')->onDelete('set null');
            $table->foreignId('tax_payable_voucher_id')->nullable()->constrained('vouchers')->onDelete('set null');

            $table->timestamps();

            // Ensure one record per employee per period
            $table->unique(['payroll_period_id', 'employee_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('payroll_runs');
    }
};
