<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->json('features'); // Array of features
            $table->integer('monthly_price'); // Price in kobo
            $table->integer('yearly_price'); // Price in kobo
            $table->integer('max_users')->nullable();
            $table->integer('max_customers')->nullable();
            $table->boolean('has_pos')->default(false);
            $table->boolean('has_payroll')->default(false);
            $table->boolean('has_api_access')->default(false);
            $table->boolean('has_advanced_reports')->default(false);
            $table->string('support_level')->default('email');
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
