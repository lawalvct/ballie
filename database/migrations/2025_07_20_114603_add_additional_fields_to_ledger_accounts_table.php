<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ledger_accounts', function (Blueprint $table) {
            // Add fields that might be missing for advanced features
            $table->text('description')->nullable()->after('email');
            $table->unsignedBigInteger('parent_id')->nullable()->after('account_group_id');
            $table->enum('account_type', ['asset', 'liability', 'equity', 'income', 'expense'])->after('balance_type');
            $table->decimal('current_balance', 15, 2)->default(0)->after('opening_balance');
            $table->timestamp('last_transaction_date')->nullable()->after('current_balance');
            $table->boolean('is_system_defined')->default(false)->after('is_active');
            $table->json('tags')->nullable()->after('is_system_defined');

            // Add foreign key for parent relationship
            $table->foreign('parent_id')->references('id')->on('ledger_accounts');

            // Add indexes for better performance
            $table->index(['tenant_id', 'account_type']);
            $table->index(['tenant_id', 'parent_id']);
            $table->index(['tenant_id', 'is_active', 'account_type']);
        });
    }

    public function down()
    {
        Schema::table('ledger_accounts', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['tenant_id', 'account_type']);
            $table->dropIndex(['tenant_id', 'parent_id']);
            $table->dropIndex(['tenant_id', 'is_active', 'account_type']);

            $table->dropColumn([
                'description',
                'parent_id',
                'account_type',
                'current_balance',
                'last_transaction_date',
                'is_system_defined',
                'tags'
            ]);
        });
    }
};
