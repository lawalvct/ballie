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
        Schema::table('overtime_records', function (Blueprint $table) {
            // Make overtime_type nullable since fixed amount doesn't require it
            DB::statement("ALTER TABLE overtime_records MODIFY overtime_type ENUM('weekday', 'weekend', 'holiday', 'emergency') NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('overtime_records', function (Blueprint $table) {
            // Revert to NOT NULL with default
            DB::statement("ALTER TABLE overtime_records MODIFY overtime_type ENUM('weekday', 'weekend', 'holiday', 'emergency') NOT NULL DEFAULT 'weekday'");
        });
    }
};
