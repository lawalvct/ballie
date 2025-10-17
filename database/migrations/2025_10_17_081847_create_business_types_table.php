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
        Schema::create('business_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category'); // Retail, Professional, Food, etc.
            $table->string('icon')->nullable(); // Emoji or icon class
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('category');
            $table->index('is_active');
        });

        // Add business_type_id to tenants table (after business_structure column)
        Schema::table('tenants', function (Blueprint $table) {
            $table->foreignId('business_type_id')->nullable()->after('business_structure')->constrained('business_types')->nullOnDelete();
            $table->index('business_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['business_type_id']);
            $table->dropColumn('business_type_id');
        });

        Schema::dropIfExists('business_types');
    }
};
