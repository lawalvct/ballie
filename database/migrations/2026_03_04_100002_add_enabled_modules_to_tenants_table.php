<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds enabled_modules JSON column to tenants table.
     * NULL means "use defaults from business category" (backward compatible).
     * Existing tenants are NOT affected — they keep NULL which resolves to 'hybrid' (all modules).
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (!Schema::hasColumn('tenants', 'enabled_modules')) {
                $table->json('enabled_modules')
                    ->nullable()
                    ->after('settings')
                    ->comment('JSON array of enabled module keys. NULL = use category defaults.');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (Schema::hasColumn('tenants', 'enabled_modules')) {
                $table->dropColumn('enabled_modules');
            }
        });
    }
};
