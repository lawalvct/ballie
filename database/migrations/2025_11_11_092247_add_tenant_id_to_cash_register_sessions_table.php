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
        Schema::table('cash_register_sessions', function (Blueprint $table) {
            // Check if tenant_id column exists, if not add it
            // If it exists but doesn't have proper foreign key, this will fix it
            if (!Schema::hasColumn('cash_register_sessions', 'tenant_id')) {
                $table->foreignId('tenant_id')->after('id')->constrained('tenants')->onDelete('cascade');
            }
        });

        // Fix the foreign key if column already exists
        if (Schema::hasColumn('cash_register_sessions', 'tenant_id')) {
            // Drop incorrect foreign key if exists
            try {
                Schema::table('cash_register_sessions', function (Blueprint $table) {
                    $table->dropForeign(['tenant_id']);
                });
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }

            // Add correct foreign key
            Schema::table('cash_register_sessions', function (Blueprint $table) {
                $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_register_sessions', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
        });
    }
};
