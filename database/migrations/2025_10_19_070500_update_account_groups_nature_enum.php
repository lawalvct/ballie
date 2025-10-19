<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL, we need to use raw SQL to modify the enum
        DB::statement("ALTER TABLE account_groups MODIFY COLUMN nature ENUM('assets', 'liabilities', 'equity', 'income', 'expenses') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum (but this might fail if 'equity' values exist)
        DB::statement("ALTER TABLE account_groups MODIFY COLUMN nature ENUM('assets', 'liabilities', 'income', 'expenses') NOT NULL");
    }
};
