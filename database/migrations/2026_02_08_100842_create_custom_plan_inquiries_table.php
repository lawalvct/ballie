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
        Schema::create('custom_plan_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('business_name');
            $table->string('contact_name');
            $table->string('email');
            $table->string('phone');
            $table->integer('num_companies')->nullable();
            $table->string('interest')->nullable(); // lifetime, custom_app, both, other
            $table->text('requirements')->nullable();
            $table->string('status')->default('pending'); // pending, contacted, converted, closed
            $table->text('admin_notes')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_plan_inquiries');
    }
};
