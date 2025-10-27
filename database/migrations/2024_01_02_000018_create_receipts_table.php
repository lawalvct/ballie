<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->string('receipt_number')->unique();
            $table->enum('type', ['original', 'duplicate', 'refund']);
            $table->json('receipt_data'); // Store complete receipt data
            $table->boolean('is_printed')->default(false);
            $table->boolean('is_emailed')->default(false);
            $table->timestamp('printed_at')->nullable();
            $table->timestamp('emailed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('receipts');
    }
};
