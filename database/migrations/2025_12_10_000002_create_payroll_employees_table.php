<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');

            // Employee Identification
            $table->string('employee_number')->unique();
            $table->string('unique_link_token')->unique(); // For self-service portal

            // Personal Information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->default('single');
            $table->text('address')->nullable();
            $table->string('state_of_origin')->nullable();

            // Employment Information
            $table->string('job_title');
            $table->date('hire_date');
            $table->date('confirmation_date')->nullable();
            $table->enum('employment_type', ['permanent', 'contract', 'casual'])->default('permanent');
            $table->enum('pay_frequency', ['monthly', 'weekly', 'contract'])->default('monthly');
            $table->enum('status', ['active', 'suspended', 'terminated'])->default('active');

            // Bank Information
            $table->string('bank_name')->nullable();
            $table->string('bank_code')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();

            // Tax Information
            $table->string('tin')->nullable(); // Tax Identification Number
            $table->decimal('annual_relief', 15, 2)->default(200000); // Current ₦200,000 relief

            // Pension Information (for NSITF)
            $table->string('pension_pin')->nullable(); // Pension PIN
            $table->string('pfa_name')->nullable(); // Pension Fund Administrator

            // Avatar
            $table->string('avatar')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'department_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
