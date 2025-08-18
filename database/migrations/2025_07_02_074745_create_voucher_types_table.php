<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('voucher_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('name'); // Journal, Payment, Receipt, Sales, Purchase, Contra
            $table->string('code', 30); // JV, PV, RV, SV, PUR, CV
            $table->string('abbreviation', 5); // J, P, R, S, PU, C
            $table->text('description')->nullable();
            $table->string('numbering_method')->default('auto'); // auto, manual
            $table->string('prefix')->nullable(); // JV-, PV-, etc.
            $table->integer('starting_number')->default(1);
            $table->integer('current_number')->default(0);
            $table->boolean('has_reference')->default(false); // Does this voucher type require reference number?
            $table->boolean('affects_inventory')->default(false); // Does this affect stock?
            $table->enum('inventory_effect', ['increase', 'decrease', 'none'])->default('none'); // Inventory effect
            $table->boolean('affects_cashbank')->default(false); // Does this affect cash/bank accounts?
            $table->boolean('is_system_defined')->default(true); // System defined or user created
            $table->boolean('is_active')->default(true);
            $table->json('default_accounts')->nullable(); // Default account mappings
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants');
            $table->unique(['tenant_id', 'code']);
            $table->index(['tenant_id', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('voucher_types');
    }
};
