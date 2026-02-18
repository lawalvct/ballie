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
        Schema::table('plans', function (Blueprint $table) {
            $table->integer('quarterly_price')->after('monthly_price')->default(0); // Price in kobo (3 months)
            $table->integer('biannual_price')->after('quarterly_price')->default(0); // Price in kobo (6 months)

            // Add new boolean feature flags for better segmentation
            $table->boolean('has_ecommerce')->default(false)->after('has_advanced_reports');
            $table->boolean('has_audit_log')->default(false)->after('has_ecommerce');
            $table->boolean('has_multi_location')->default(false)->after('has_audit_log');
            $table->boolean('has_multi_currency')->default(false)->after('has_multi_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn([
                'quarterly_price',
                'biannual_price',
                'has_ecommerce',
                'has_audit_log',
                'has_multi_location',
                'has_multi_currency',
            ]);
        });
    }
};
