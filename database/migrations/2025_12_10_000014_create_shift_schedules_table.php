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
        Schema::create('shift_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Morning Shift, Night Shift, etc.
            $table->string('code'); // MS, NS, ES
            $table->text('description')->nullable();

            // Shift times
            $table->time('start_time'); // 08:00
            $table->time('end_time'); // 17:00
            $table->integer('work_hours')->default(8); // Expected work hours
            $table->integer('break_minutes')->default(60); // Break duration

            // Grace periods
            $table->integer('late_grace_minutes')->default(15); // Grace period for late coming
            $table->integer('early_out_grace_minutes')->default(15); // Grace period for early leaving

            // Shift allowance
            $table->decimal('shift_allowance', 10, 2)->default(0); // Additional pay for this shift
            $table->boolean('is_night_shift')->default(false);

            // Working days
            $table->json('working_days')->nullable(); // ['monday', 'tuesday', ...] or null for all days

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0);

            // Color for calendar view
            $table->string('color')->default('#3b82f6'); // Hex color code

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'is_active']);
            $table->index('code');
            $table->unique(['tenant_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_schedules');
    }
};
