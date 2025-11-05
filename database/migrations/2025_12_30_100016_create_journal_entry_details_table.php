<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('journal_entry_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('journal_entry_id');
            $table->unsignedBigInteger('ledger_account_id');
            $table->text('description')->nullable();
            $table->decimal('debit_amount', 15, 2)->default(0);
            $table->decimal('credit_amount', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('cascade');
            $table->foreign('ledger_account_id')->references('id')->on('ledger_accounts');
            $table->index(['journal_entry_id', 'ledger_account_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('journal_entry_details');
    }
};
