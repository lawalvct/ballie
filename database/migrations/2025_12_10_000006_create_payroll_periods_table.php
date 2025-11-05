<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "January 2024", "Week 1 - Jan 2024"
            $table->date('start_date');
            $table->date('end_date');
            $table->date('pay_date');
            $table->enum('type', ['monthly', 'weekly', 'contract'])->default('monthly');
            $table->enum('status', ['draft', 'processing', 'approved', 'paid', 'closed'])->default('draft');
            $table->decimal('total_gross', 15, 2)->default(0);
            $table->decimal('total_deductions', 15, 2)->default(0);
            $table->decimal('total_net', 15, 2)->default(0);
            $table->decimal('total_tax', 15, 2)->default(0);
            $table->decimal('total_nsitf', 15, 2)->default(0);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payroll_periods');
    }
};
