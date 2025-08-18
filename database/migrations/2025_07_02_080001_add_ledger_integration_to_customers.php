<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('ledger_account_id')->nullable()->after('tenant_id')->constrained('ledger_accounts')->onDelete('set null');
            $table->decimal('outstanding_balance', 15, 2)->default(0)->after('total_spent');

            // Add index
            $table->index('ledger_account_id');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['ledger_account_id']);
            $table->dropColumn(['ledger_account_id', 'outstanding_balance']);
        });
    }
};