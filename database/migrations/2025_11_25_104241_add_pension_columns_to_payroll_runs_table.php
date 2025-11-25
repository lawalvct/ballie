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
        Schema::table('payroll_runs', function (Blueprint $table) {
            $table->decimal('pension_employee', 15, 2)->default(0)->after('nsitf_contribution');
            $table->decimal('pension_employer', 15, 2)->default(0)->after('pension_employee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_runs', function (Blueprint $table) {
            $table->dropColumn(['pension_employee', 'pension_employer']);
        });
    }
};
