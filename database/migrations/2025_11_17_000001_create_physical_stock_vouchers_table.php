<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('physical_stock_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('voucher_number')->unique();
            $table->date('voucher_date');
            $table->string('reference_number')->nullable();
            $table->enum('adjustment_type', ['shortage', 'excess', 'mixed'])->default('mixed');
            $table->integer('total_items')->default(0);
            $table->decimal('total_adjustments', 15, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->enum('status', ['draft', 'pending', 'approved', 'cancelled'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['tenant_id', 'voucher_date']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'voucher_number']);
            $table->index(['voucher_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('physical_stock_vouchers');
    }
};
