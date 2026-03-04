<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');

            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Billable milestone amount (Naira)
            $table->decimal('amount', 15, 2)->nullable();
            $table->boolean('is_billable')->default(true);

            // Link to invoice (vouchers table stores invoices)
            $table->foreignId('invoice_id')->nullable()->constrained('vouchers')->onDelete('set null');

            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Indexes
            $table->index(['project_id', 'is_billable']);
            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_milestones');
    }
};
