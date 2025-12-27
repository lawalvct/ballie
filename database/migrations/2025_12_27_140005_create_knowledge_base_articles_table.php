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
        Schema::create('knowledge_base_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('support_categories')->onDelete('restrict');
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->text('excerpt')->nullable();
            $table->string('featured_image')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->integer('view_count')->default(0);
            $table->integer('helpful_count')->default(0);
            $table->integer('not_helpful_count')->default(0);
            $table->integer('sort_order')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->foreignId('author_id')->constrained('super_admins')->onDelete('restrict');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('category_id');
            $table->index('slug');
            $table->index(['is_published', 'is_featured']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_base_articles');
    }
};
