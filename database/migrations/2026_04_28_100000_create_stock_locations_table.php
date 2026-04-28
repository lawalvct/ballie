<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('name');
            $table->string('code', 50);
            $table->string('type', 30)->default('store'); // store, warehouse, department, production, wip, branch, other
            $table->text('description')->nullable();
            $table->boolean('is_main')->default(false);
            $table->boolean('is_wip')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['tenant_id', 'code']);
            $table->unique(['tenant_id', 'name']);
            $table->index(['tenant_id', 'is_active']);
            $table->index(['tenant_id', 'is_main']);
            $table->index(['tenant_id', 'is_wip']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_locations');
    }
};
