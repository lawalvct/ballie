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
        Schema::table('subscriptions', function (Blueprint $table) {
            // Add plan_id field to reference plans table
            $table->foreignId('plan_id')->nullable()->after('tenant_id')->constrained('plans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Remove plan_id foreign key and column
            $table->dropForeign(['plan_id']);
            $table->dropColumn('plan_id');
        });
    }
};
