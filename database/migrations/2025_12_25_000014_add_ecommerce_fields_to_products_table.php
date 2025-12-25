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
        Schema::table('products', function (Blueprint $table) {
            // E-commerce specific fields
            $table->string('slug')->nullable()->after('sku');
            $table->text('short_description')->nullable()->after('description');
            $table->text('long_description')->nullable()->after('short_description');
            $table->boolean('is_visible_online')->default(false)->after('is_active');
            $table->boolean('is_featured')->default(false)->after('is_visible_online');

            // Shipping dimensions
            $table->decimal('weight', 10, 2)->nullable()->after('is_featured'); // in kg
            $table->decimal('length', 10, 2)->nullable()->after('weight'); // in cm
            $table->decimal('width', 10, 2)->nullable()->after('length'); // in cm
            $table->decimal('height', 10, 2)->nullable()->after('width'); // in cm

            // SEO fields
            $table->string('meta_title')->nullable()->after('height');
            $table->text('meta_description')->nullable()->after('meta_title');

            // Analytics
            $table->integer('view_count')->default(0)->after('meta_description');
            $table->integer('online_stock_alert_level')->nullable()->after('view_count');

            // Index for slug (unique per tenant)
            $table->index(['tenant_id', 'slug']);
            $table->index('is_visible_online');
            $table->index('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'slug']);
            $table->dropIndex(['is_visible_online']);
            $table->dropIndex(['is_featured']);

            $table->dropColumn([
                'slug',
                'short_description',
                'long_description',
                'is_visible_online',
                'is_featured',
                'weight',
                'length',
                'width',
                'height',
                'meta_title',
                'meta_description',
                'view_count',
                'online_stock_alert_level'
            ]);
        });
    }
};
