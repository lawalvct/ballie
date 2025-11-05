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
        // Add missing columns to existing business_types table
        Schema::table('business_types', function (Blueprint $table) {
            if (!Schema::hasColumn('business_types', 'slug')) {
                $table->string('slug')->unique()->after('name');
            }
            if (!Schema::hasColumn('business_types', 'category')) {
                $table->string('category')->after('slug'); // Retail, Professional, Food, etc.
            }
            if (!Schema::hasColumn('business_types', 'icon')) {
                $table->string('icon')->nullable()->after('category'); // Emoji or icon class
            }
            if (!Schema::hasColumn('business_types', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('description');
            }

            if (!Schema::hasIndex('business_types', 'business_types_category_index')) {
                $table->index('category');
            }
        });

        // Add business_type_id to tenants table (after business_type column)
        if (!Schema::hasColumn('tenants', 'business_type_id')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->foreignId('business_type_id')->nullable()->after('business_type')->constrained('business_types')->nullOnDelete();
                $table->index('business_type_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['business_type_id']);
            $table->dropIndex(['business_type_id']);
            $table->dropColumn('business_type_id');
        });

        Schema::table('business_types', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropColumn(['slug', 'category', 'icon', 'sort_order']);
        });
    }
};
