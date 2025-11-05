<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Currency rates table (shared across all tenants)
        Schema::create('currency_rates', function (Blueprint $table) {
            $table->id();
            $table->string('from_currency', 3);
            $table->string('to_currency', 3);
            $table->decimal('rate', 15, 6);
            $table->date('date');
            $table->timestamps();

            $table->unique(['from_currency', 'to_currency', 'date']);
        });

        // Tax rates table (shared across all tenants)
        // Schema::create('tax_rates', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('country', 2);
        //     $table->string('state')->nullable();
        //     $table->string('tax_type'); // VAT, Sales Tax, etc.
        //     $table->decimal('rate', 5, 2);
        //     $table->date('effective_from');
        //     $table->date('effective_to')->nullable();
        //     $table->boolean('is_active')->default(true);
        //     $table->timestamps();
        // });

        // Nigerian states table (shared resource)
        Schema::create('nigerian_states', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 2);
            $table->string('capital');
            $table->timestamps();
        });

        // Business types table (shared resource)
        Schema::create('business_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_types');
        Schema::dropIfExists('nigerian_states');
        Schema::dropIfExists('tax_rates');
        Schema::dropIfExists('currency_rates');
    }
};
