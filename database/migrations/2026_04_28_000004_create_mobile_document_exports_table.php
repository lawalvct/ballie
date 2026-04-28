<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 1 mobile offline sync — new table only.
 *
 * Tracks server-generated PDFs/files that the mobile app should
 * download, cache locally and re-show offline (e.g. official invoice
 * PDFs, customer ledger statements).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('mobile_document_exports')) {
            return;
        }

        Schema::create('mobile_document_exports', function (Blueprint $table) {
            $table->id();
            $table->uuid('export_uuid')->unique();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('mobile_device_id')->nullable()->constrained('mobile_devices')->nullOnDelete();

            $table->string('document_type', 32); // invoice_pdf | customer_statement
            $table->string('source_table', 64)->nullable();
            $table->uuid('source_sync_uuid')->nullable();
            $table->unsignedBigInteger('source_server_id')->nullable();
            $table->uuid('customer_sync_uuid')->nullable();

            $table->string('disk', 32)->default('local');
            $table->string('storage_path');
            $table->string('mime_type', 100)->nullable();
            $table->string('file_name');
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('checksum', 64)->nullable();

            $table->date('period_from')->nullable();
            $table->date('period_to')->nullable();

            $table->string('status', 16)->default('pending'); // pending | ready | failed | expired
            $table->text('error_message')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('downloaded_at')->nullable();

            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'document_type'], 'mobile_doc_exports_type_index');
            $table->index(['tenant_id', 'source_table', 'source_sync_uuid'], 'mobile_doc_exports_source_index');
            $table->index(['tenant_id', 'customer_sync_uuid'], 'mobile_doc_exports_customer_index');
            $table->index(['tenant_id', 'status'], 'mobile_doc_exports_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobile_document_exports');
    }
};
