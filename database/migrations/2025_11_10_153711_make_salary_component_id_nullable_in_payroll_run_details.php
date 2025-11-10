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
        Schema::table('payroll_run_details', function (Blueprint $table) {
            // Make the column nullable using raw SQL to avoid foreign key issues
            DB::statement('ALTER TABLE payroll_run_details MODIFY salary_component_id BIGINT UNSIGNED NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_run_details', function (Blueprint $table) {
            // Make the column NOT nullable again
            DB::statement('ALTER TABLE payroll_run_details MODIFY salary_component_id BIGINT UNSIGNED NOT NULL');
        });
    }
};
