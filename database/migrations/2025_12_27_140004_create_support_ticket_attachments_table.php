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
        Schema::create('support_ticket_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('support_tickets')->onDelete('cascade');
            $table->foreignId('reply_id')->nullable()->constrained('support_ticket_replies')->onDelete('cascade');
            $table->string('filename');
            $table->string('original_name');
            $table->string('file_path');
            $table->bigInteger('file_size');
            $table->string('mime_type');
            $table->string('uploaded_by_type');
            $table->unsignedBigInteger('uploaded_by_id');
            $table->timestamps();

            $table->index('ticket_id');
            $table->index('reply_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_ticket_attachments');
    }
};
