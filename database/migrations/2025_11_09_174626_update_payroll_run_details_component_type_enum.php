<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the enum column to include 'earning' and 'employer_contribution'
        DB::statement("ALTER TABLE `payroll_run_details` MODIFY COLUMN `component_type` ENUM('earning', 'deduction', 'employer_contribution') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE `payroll_run_details` MODIFY COLUMN `component_type` ENUM('allowance', 'deduction') NOT NULL");
    }
};
