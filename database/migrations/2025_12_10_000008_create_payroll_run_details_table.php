<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payroll_run_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_run_id')->constrained()->onDelete('cascade');
            $table->foreignId('salary_component_id')->constrained()->onDelete('cascade');
            $table->string('component_name'); // Snapshot of name at time of calculation
            $table->enum('component_type', ['earning', 'deduction', 'employer_contribution']);
            $table->decimal('amount', 15, 2);
            $table->boolean('is_taxable');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payroll_run_details');
    }
};
