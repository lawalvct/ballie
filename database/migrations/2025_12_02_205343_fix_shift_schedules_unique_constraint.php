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
        Schema::table('shift_schedules', function (Blueprint $table) {
            // Check if the old unique constraint exists and drop it
            $indexes = DB::select("SHOW INDEX FROM shift_schedules WHERE Key_name = 'shift_schedules_code_unique'");
            if (!empty($indexes)) {
                $table->dropUnique('shift_schedules_code_unique');
            }

            // Check if the new constraint already exists
            $newIndexes = DB::select("SHOW INDEX FROM shift_schedules WHERE Key_name = 'shift_schedules_tenant_code_unique'");
            if (empty($newIndexes)) {
                // Add composite unique constraint on tenant_id and code
                $table->unique(['tenant_id', 'code'], 'shift_schedules_tenant_code_unique');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shift_schedules', function (Blueprint $table) {
            // Check if composite constraint exists and drop it
            $indexes = DB::select("SHOW INDEX FROM shift_schedules WHERE Key_name = 'shift_schedules_tenant_code_unique'");
            if (!empty($indexes)) {
                $table->dropUnique('shift_schedules_tenant_code_unique');
            }

            // Check if old constraint exists
            $oldIndexes = DB::select("SHOW INDEX FROM shift_schedules WHERE Key_name = 'shift_schedules_code_unique'");
            if (empty($oldIndexes)) {
                // Restore the original unique constraint on code
                $table->unique('code', 'shift_schedules_code_unique');
            }
        });
    }
};
