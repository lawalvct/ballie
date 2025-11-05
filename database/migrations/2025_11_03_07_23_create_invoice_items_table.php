<?php
namespace Database\Migrations;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
Schema::create('invoice_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('voucher_id')->constrained()->onDelete('cascade');
    $table->foreignId('product_id')->constrained();
    $table->string('product_name');
    $table->text('description')->nullable();
   
    $table->decimal('quantity', 10, 2);
    $table->decimal('rate', 15, 2);
    $table->decimal('purchase_rate', 15, 2)->default(0);
    $table->decimal('discount', 15, 2)->default(0);
    $table->decimal('tax', 15, 2)->default(0);
    $table->boolean('is_tax_inclusive')->default(false);
    $table->decimal('amount', 15, 2); // Before tax/discount
    $table->decimal('total', 15, 2)->nullable(); // After tax/discount
    $table->timestamps();
    $table->softDeletes(); // Optional
});
    }

    public function down()
    {
        Schema::dropIfExists('invoice_items');
    }
};
