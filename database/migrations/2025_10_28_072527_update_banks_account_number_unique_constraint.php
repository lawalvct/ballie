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
        Schema::table('banks', function (Blueprint $table) {
            // Drop the old unique constraint
            $table->dropUnique('banks_account_number_unique');

            // Add composite unique constraint: account_number unique per tenant
            $table->unique(['tenant_id', 'account_number'], 'banks_tenant_account_number_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banks', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('banks_tenant_account_number_unique');

            // Restore the old simple unique constraint
            $table->unique('account_number', 'banks_account_number_unique');
        });
    }
};
