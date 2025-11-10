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
        Schema::table('employees', function (Blueprint $table) {
            // Add field to exempt employee from attendance-based deductions
            $table->boolean('attendance_deduction_exempt')->default(false)->after('status');

            // Optional: Add reason/notes for the exemption
            $table->text('attendance_exemption_reason')->nullable()->after('attendance_deduction_exempt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['attendance_deduction_exempt', 'attendance_exemption_reason']);
        });
    }
};
