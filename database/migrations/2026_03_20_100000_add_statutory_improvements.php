<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add VAT configuration columns to tenants
        Schema::table('tenants', function (Blueprint $table) {
            $table->decimal('vat_rate', 5, 2)->default(7.50)->after('tax_identification_number');
            $table->string('vat_registration_number')->nullable()->after('vat_rate');
        });

        // Tax filing history / compliance tracking
        Schema::create('tax_filings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // vat, paye, pension, nsitf, wht, cit
            $table->string('reference_number')->nullable();
            $table->string('period_label'); // "January 2026", "Q1 2026"
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('amount', 15, 2)->default(0);
            $table->enum('status', ['draft', 'filed', 'paid', 'overdue'])->default('draft');
            $table->date('due_date')->nullable();
            $table->date('filed_date')->nullable();
            $table->date('paid_date')->nullable();
            $table->string('payment_reference')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('filed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['tenant_id', 'type', 'status']);
            $table->index(['tenant_id', 'period_start', 'period_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_filings');

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['vat_rate', 'vat_registration_number']);
        });
    }
};
