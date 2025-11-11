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
        // Get all foreign keys on the table
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'cash_register_sessions'
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ");

        // Drop all existing foreign keys
        foreach ($foreignKeys as $fk) {
            DB::statement("ALTER TABLE cash_register_sessions DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
        }

        // Fix column types to ensure they're bigint unsigned
        DB::statement('ALTER TABLE cash_register_sessions
            MODIFY COLUMN user_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE cash_register_sessions
            MODIFY COLUMN cash_register_id BIGINT UNSIGNED NOT NULL');

        // Add tenant_id column if it doesn't exist
        if (!Schema::hasColumn('cash_register_sessions', 'tenant_id')) {
            Schema::table('cash_register_sessions', function (Blueprint $table) {
                $table->unsignedBigInteger('tenant_id')->after('id')->nullable();
            });
        } else {
            // Ensure tenant_id is also bigint unsigned
            DB::statement('ALTER TABLE cash_register_sessions
                MODIFY COLUMN tenant_id BIGINT UNSIGNED NULL');
        }

        // Delete any orphaned records that don't have valid references
        DB::statement('DELETE FROM cash_register_sessions WHERE user_id NOT IN (SELECT id FROM users)');
        DB::statement('DELETE FROM cash_register_sessions WHERE cash_register_id NOT IN (SELECT id FROM cash_registers)');

        // Set tenant_id based on the user's tenant
        DB::statement('UPDATE cash_register_sessions crs
            INNER JOIN users u ON crs.user_id = u.id
            SET crs.tenant_id = u.tenant_id
            WHERE crs.tenant_id IS NULL');

        // Make tenant_id not nullable now
        DB::statement('ALTER TABLE cash_register_sessions
            MODIFY COLUMN tenant_id BIGINT UNSIGNED NOT NULL');

        // Delete any remaining invalid records
        DB::statement('DELETE FROM cash_register_sessions WHERE tenant_id NOT IN (SELECT id FROM tenants)');

        // Add correct foreign keys with explicit names to avoid conflicts
        DB::statement('ALTER TABLE cash_register_sessions
            ADD CONSTRAINT cash_register_sessions_tenant_id_fk
            FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE');

        DB::statement('ALTER TABLE cash_register_sessions
            ADD CONSTRAINT cash_register_sessions_cash_register_id_fk
            FOREIGN KEY (cash_register_id) REFERENCES cash_registers(id) ON DELETE CASCADE');

        DB::statement('ALTER TABLE cash_register_sessions
            ADD CONSTRAINT cash_register_sessions_user_id_fk
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE cash_register_sessions DROP FOREIGN KEY cash_register_sessions_tenant_id_fk');
        DB::statement('ALTER TABLE cash_register_sessions DROP FOREIGN KEY cash_register_sessions_cash_register_id_fk');
        DB::statement('ALTER TABLE cash_register_sessions DROP FOREIGN KEY cash_register_sessions_user_id_fk');
    }
};
