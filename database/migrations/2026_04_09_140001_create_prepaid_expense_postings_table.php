<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prepaid_expense_postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prepaid_expense_id')->constrained('prepaid_expenses')->onDelete('cascade');
            $table->foreignId('voucher_id')->constrained('vouchers')->onDelete('cascade');
            $table->unsignedSmallInteger('installment_number');
            $table->decimal('amount', 15, 2);
            $table->date('posting_date');
            $table->string('status', 20)->default('posted'); // posted, failed, reversed
            $table->timestamps();

            $table->index('prepaid_expense_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prepaid_expense_postings');
    }
};
