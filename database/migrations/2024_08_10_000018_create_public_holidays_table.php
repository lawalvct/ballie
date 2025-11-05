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
        Schema::create('public_holidays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('name'); // New Year, Christmas, Independence Day
            $table->date('holiday_date');
            $table->text('description')->nullable();
            $table->boolean('is_recurring')->default(false); // Repeats every year
            $table->integer('recurring_day')->nullable(); // Day of month (1-31)
            $table->integer('recurring_month')->nullable(); // Month (1-12)
            $table->boolean('is_paid')->default(true); // Paid holiday
            $table->boolean('is_working_day')->default(false); // Some holidays might require work
            $table->decimal('overtime_multiplier', 4, 2)->default(2.0); // 2x pay if worked
            $table->boolean('is_active')->default(true);

            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'holiday_date']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public_holidays');
    }
};
