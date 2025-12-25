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
        Schema::create('ecommerce_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');

            // Store Status
            $table->boolean('is_store_enabled')->default(false);
            $table->string('store_name')->nullable();
            $table->text('store_description')->nullable();
            $table->string('store_logo')->nullable();
            $table->string('store_banner')->nullable();

            // Customer Registration Options
            $table->boolean('allow_guest_checkout')->default(true);
            $table->boolean('allow_email_registration')->default(true);
            $table->boolean('allow_google_login')->default(true);
            $table->boolean('require_phone_number')->default(false);

            // Currency & Tax
            $table->string('default_currency', 3)->default('NGN');
            $table->boolean('tax_enabled')->default(false);
            $table->decimal('tax_percentage', 5, 2)->nullable();

            // Shipping
            $table->boolean('shipping_enabled')->default(true);

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            // Social Media
            $table->string('social_facebook')->nullable();
            $table->string('social_instagram')->nullable();
            $table->string('social_twitter')->nullable();

            // Theme
            $table->string('theme_primary_color')->default('#2c5aa0');
            $table->string('theme_secondary_color')->default('#27ae60');

            // Payment Gateway Settings (stored as JSON for flexibility)
            $table->json('payment_gateway_settings')->nullable();

            $table->timestamps();

            // Indexes
            $table->unique('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ecommerce_settings');
    }
};
