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
        Schema::table('ledger_accounts', function (Blueprint $table) {
            // Add flag to identify the Opening Balance Equity account
            $table->boolean('is_opening_balance_account')->default(false)->after('is_system_defined');

            // Add reference to the voucher that was created for this account's opening balance
            $table->unsignedBigInteger('opening_balance_voucher_id')->nullable()->after('opening_balance');

            // Add foreign key constraint
            $table->foreign('opening_balance_voucher_id')
                  ->references('id')
                  ->on('vouchers')
                  ->onDelete('set null');

            // Add index for quick lookup of opening balance account
            $table->index('is_opening_balance_account');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ledger_accounts', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['opening_balance_voucher_id']);

            // Drop index
            $table->dropIndex(['is_opening_balance_account']);

            // Drop columns
            $table->dropColumn(['is_opening_balance_account', 'opening_balance_voucher_id']);
        });
    }
};
