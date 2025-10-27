<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable(); // Nullable for social login
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();
            $table->enum('role', ['owner', 'admin', 'manager', 'accountant', 'sales', 'employee'])->default('employee');
            $table->json('permissions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();

            // Social login fields
            $table->string('social_provider')->nullable();
            $table->string('social_provider_id')->nullable();
            $table->string('social_avatar')->nullable();

            $table->rememberToken();
            $table->timestamps();

            // Composite unique constraint for email within tenant
            $table->unique(['tenant_id', 'email']);
            $table->index(['social_provider', 'social_provider_id']);
            $table->index('role');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
