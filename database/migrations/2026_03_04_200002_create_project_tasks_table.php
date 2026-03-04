<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');

            $table->string('title');
            $table->text('description')->nullable();

            $table->enum('status', ['todo', 'in_progress', 'review', 'done'])
                  ->default('todo');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])
                  ->default('medium');

            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->integer('sort_order')->default(0);
            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->decimal('actual_hours', 8, 2)->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['project_id', 'status']);
            $table->index(['tenant_id', 'assigned_to']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_tasks');
    }
};
