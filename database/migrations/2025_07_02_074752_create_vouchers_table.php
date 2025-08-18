<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('voucher_type_id'); // Reference to voucher_types table
            $table->string('voucher_number');
            $table->date('voucher_date');
            $table->string('reference_number')->nullable();
            $table->text('narration')->nullable();
            $table->decimal('total_amount', 15, 2);
            $table->enum('status', ['draft', 'posted', 'cancelled'])->default('draft');
            $table->unsignedBigInteger('created_by');
            $table->timestamp('posted_at')->nullable();
            $table->unsignedBigInteger('posted_by')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants');
            $table->foreign('voucher_type_id')->references('id')->on('voucher_types');
            $table->unique(['tenant_id', 'voucher_type_id', 'voucher_number']);
            $table->index(['tenant_id', 'voucher_date']);
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('vouchers');
    }
};
