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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');

            // Basic Information
            $table->string('name');
            $table->string('symbol');
            $table->text('description')->nullable();

            // Unit Hierarchy
            $table->boolean('is_base_unit')->default(false);
            $table->foreignId('base_unit_id')->nullable()->constrained('units')->onDelete('cascade');
            $table->decimal('conversion_factor', 12, 6)->default(1.000000);

            // Status
            $table->boolean('is_active')->default(true);

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('tenant_id');
            $table->index('is_base_unit');
            $table->index('base_unit_id');
            $table->index('is_active');
            $table->unique(['tenant_id', 'symbol']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
