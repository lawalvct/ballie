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
        Schema::create('support_response_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subject')->nullable();
            $table->longText('content');
            $table->foreignId('category_id')->nullable()->constrained('support_categories')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->integer('usage_count')->default(0);
            $table->foreignId('created_by')->constrained('super_admins')->onDelete('restrict');
            $table->timestamps();

            $table->index('category_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_response_templates');
    }
};
