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
        Schema::create('stock_journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');

            // Journal Entry Details
            $table->string('journal_number')->unique(); // Auto-generated journal number
            $table->date('journal_date'); // Entry date
            $table->string('reference_number')->nullable(); // Manual reference
            $table->text('narration')->nullable(); // Description/narration

            // Entry Type (consumption, production, adjustment, transfer)
            $table->enum('entry_type', ['consumption', 'production', 'adjustment', 'transfer']);

            // Status tracking
            $table->enum('status', ['draft', 'posted', 'cancelled'])->default('draft');
            $table->timestamp('posted_at')->nullable();
            $table->foreignId('posted_by')->nullable()->constrained('users');

            // Audit fields
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();

            // Indexes
            $table->index('tenant_id');
            $table->index('journal_date');
            $table->index('entry_type');
            $table->index('status');
            $table->index(['tenant_id', 'journal_date']);
            $table->index(['tenant_id', 'entry_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_journal_entries');
    }
};
