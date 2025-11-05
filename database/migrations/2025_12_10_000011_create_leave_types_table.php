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
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Annual, Sick, Casual, Maternity, Paternity, etc.
            $table->string('code')->unique(); // ANN, SICK, CAS, MAT, PAT
            $table->text('description')->nullable();
            $table->integer('default_days_per_year')->default(0); // Default allocation per year
            $table->boolean('is_paid')->default(true); // Paid or unpaid leave
            $table->boolean('requires_document')->default(false); // Medical certificate, etc.
            $table->boolean('carry_forward')->default(false); // Can unused days be carried to next year
            $table->integer('max_carry_forward_days')->default(0);
            $table->integer('max_consecutive_days')->nullable(); // Maximum days that can be taken at once
            $table->integer('min_notice_days')->default(0); // Notice period required before taking leave
            $table->boolean('weekends_included')->default(false); // Count weekends in leave calculation
            $table->boolean('holidays_included')->default(false); // Count public holidays in leave calculation
            $table->enum('accrual_type', ['yearly', 'monthly', 'none'])->default('yearly'); // How leave accrues
            $table->decimal('accrual_rate', 8, 2)->nullable(); // Days accrued per month if monthly
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system_defined')->default(false);
            $table->integer('sort_order')->default(0);
            $table->json('applicable_to')->nullable(); // Gender restrictions, department restrictions
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'is_active']);
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};
