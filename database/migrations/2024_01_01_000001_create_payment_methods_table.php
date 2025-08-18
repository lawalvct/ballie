<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Cash, Card, Mobile Money, etc.
            $table->string('code')->unique();
            $table->boolean('requires_reference')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->decimal('charge_percentage', 5, 2)->default(0);
            $table->decimal('charge_amount', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_methods');
    }
};
