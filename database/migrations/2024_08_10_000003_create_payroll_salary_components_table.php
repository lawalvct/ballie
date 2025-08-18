<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('salary_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('type', ['earning', 'deduction', 'employer_contribution']);
            $table->enum('calculation_type', ['fixed', 'percentage', 'variable', 'computed'])->default('fixed');
            $table->boolean('is_taxable')->default(true);
            $table->boolean('is_pensionable')->default(false); // For NSITF calculation
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('salary_components');
    }
};
