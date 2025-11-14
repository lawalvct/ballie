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
        Schema::table('overtime_records', function (Blueprint $table) {
            // Add calculation_method enum field
            $table->enum('calculation_method', ['hourly', 'fixed'])->default('hourly')->after('overtime_date');

            // Make time-related fields nullable for fixed amount overtime
            $table->time('start_time')->nullable()->change();
            $table->time('end_time')->nullable()->change();
            $table->integer('total_hours')->nullable()->change();
            $table->decimal('hourly_rate', 10, 2)->nullable()->change();
            $table->decimal('multiplier', 4, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('overtime_records', function (Blueprint $table) {
            $table->dropColumn('calculation_method');

            // Revert to NOT NULL (careful: this will fail if there are NULL values)
            $table->time('start_time')->nullable(false)->change();
            $table->time('end_time')->nullable(false)->change();
            $table->integer('total_hours')->nullable(false)->change();
            $table->decimal('hourly_rate', 10, 2)->nullable(false)->change();
            $table->decimal('multiplier', 4, 2)->nullable(false)->change();
        });
    }
};
