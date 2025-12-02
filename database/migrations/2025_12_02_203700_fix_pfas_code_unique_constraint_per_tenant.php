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
        Schema::table('pfas', function (Blueprint $table) {
            // Drop the old unique constraint on code alone
            $table->dropUnique('pfas_code_unique');

            // Add a composite unique constraint on tenant_id and code
            $table->unique(['tenant_id', 'code'], 'pfas_tenant_code_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pfas', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('pfas_tenant_code_unique');

            // Restore the old unique constraint on code alone
            $table->unique('code', 'pfas_code_unique');
        });
    }
};
