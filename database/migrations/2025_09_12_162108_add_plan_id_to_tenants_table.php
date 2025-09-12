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
        Schema::table('tenants', function (Blueprint $table) {
            // Add plan_id field to reference plans table
            $table->foreignId('plan_id')->nullable()->after('website')->constrained('plans')->onDelete('set null');

            // Remove old subscription_plan field if it exists
            if (Schema::hasColumn('tenants', 'subscription_plan')) {
                $table->dropColumn('subscription_plan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // Remove plan_id foreign key and column
            $table->dropForeign(['plan_id']);
            $table->dropColumn('plan_id');

            // Re-add subscription_plan if needed
            $table->string('subscription_plan')->nullable()->after('website');
        });
    }
};
