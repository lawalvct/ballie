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
        Schema::table('products', function (Blueprint $table) {
            // Add slug for URL-friendly product links
            if (!Schema::hasColumn('products', 'slug')) {
                $table->string('slug')->nullable()->after('name');
                $table->index('slug');
            }

            // Add short and long descriptions for e-commerce
            if (!Schema::hasColumn('products', 'short_description')) {
                $table->text('short_description')->nullable()->after('description');
            }

            if (!Schema::hasColumn('products', 'long_description')) {
                $table->text('long_description')->nullable()->after('short_description');
            }

            // Add e-commerce visibility flags
            if (!Schema::hasColumn('products', 'is_visible_online')) {
                $table->boolean('is_visible_online')->default(true)->after('is_purchasable');
            }

            if (!Schema::hasColumn('products', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('is_visible_online');
            }

            // Add view counter for popularity tracking
            if (!Schema::hasColumn('products', 'view_count')) {
                $table->unsignedInteger('view_count')->default(0)->after('is_featured');
            }
        });

        // Generate slugs for existing products
        DB::statement("UPDATE products SET slug = LOWER(REPLACE(REPLACE(REPLACE(name, ' ', '-'), '/', '-'), '&', 'and')) WHERE slug IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'slug',
                'short_description',
                'long_description',
                'is_visible_online',
                'is_featured',
                'view_count',
            ]);
        });
    }
};
