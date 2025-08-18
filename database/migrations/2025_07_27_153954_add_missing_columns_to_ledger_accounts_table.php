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
            // Add missing columns expected by the model
            $table->string('account_type')->default('asset')->after('account_group_id');
            $table->unsignedBigInteger('parent_id')->nullable()->after('account_type');
            $table->decimal('current_balance', 15, 2)->default(0)->after('opening_balance');
            $table->date('last_transaction_date')->nullable()->after('current_balance');
            $table->text('description')->nullable()->after('email');
            $table->boolean('is_system_account')->default(false)->after('is_active');
            $table->json('tags')->nullable()->after('is_system_account');

            // Add foreign key for parent_id
            $table->foreign('parent_id')->references('id')->on('ledger_accounts')->onDelete('set null');

            // Add index for better performance
            $table->index(['tenant_id', 'account_type']);
            $table->index(['tenant_id', 'parent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ledger_accounts', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['parent_id']);

            // Drop indexes
            $table->dropIndex(['tenant_id', 'account_type']);
            $table->dropIndex(['tenant_id', 'parent_id']);

            // Drop columns
            $table->dropColumn([
                'account_type',
                'parent_id',
                'current_balance',
                'last_transaction_date',
                'description',
                'is_system_account',
                'tags'
            ]);
        });
    }
};
