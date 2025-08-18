<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('super_admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();
            $table->enum('role', ['super_admin', 'admin', 'support'])->default('admin');
            $table->json('permissions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();

            // Social login fields
            $table->string('social_provider')->nullable();
            $table->string('social_provider_id')->nullable();
            $table->string('social_avatar')->nullable();

            $table->rememberToken();
            $table->timestamps();

            $table->index(['social_provider', 'social_provider_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('super_admins');
    }
};
