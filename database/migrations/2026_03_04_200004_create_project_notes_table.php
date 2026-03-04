<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->text('content');
            $table->boolean('is_internal')->default(true);

            $table->timestamps();

            // Indexes
            $table->index('project_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_notes');
    }
};
