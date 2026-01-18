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
        Schema::table('voucher_types', function (Blueprint $table) {
            $table->enum('category', ['accounting', 'inventory', 'pos', 'payroll', 'ecommerce'])
                  ->default('accounting')
                  ->after('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voucher_types', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
