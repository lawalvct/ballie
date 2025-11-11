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
        Schema::create('employee_announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');

            // Announcement details
            $table->string('title');
            $table->text('message');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->enum('delivery_method', ['email', 'sms', 'both'])->default('email');

            // Targeting
            $table->enum('recipient_type', ['all', 'department', 'selected'])->default('all');
            $table->json('department_ids')->nullable(); // For department-based targeting
            $table->json('employee_ids')->nullable(); // For selected employees

            // Delivery status
            $table->enum('status', ['draft', 'scheduled', 'sending', 'sent', 'failed'])->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();

            // Delivery tracking
            $table->integer('total_recipients')->default(0);
            $table->integer('email_sent_count')->default(0);
            $table->integer('sms_sent_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->text('error_message')->nullable();

            // Additional options
            $table->boolean('requires_acknowledgment')->default(false);
            $table->date('expires_at')->nullable();
            $table->text('attachment_path')->nullable(); // For file attachments

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('tenant_id');
            $table->index('status');
            $table->index('scheduled_at');
            $table->index('created_by');
        });

        // Announcement recipients tracking (for acknowledgment)
        Schema::create('announcement_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained('employee_announcements')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');

            // Delivery status per recipient
            $table->boolean('email_sent')->default(false);
            $table->timestamp('email_sent_at')->nullable();
            $table->boolean('sms_sent')->default(false);
            $table->timestamp('sms_sent_at')->nullable();

            // Acknowledgment tracking
            $table->boolean('acknowledged')->default(false);
            $table->timestamp('acknowledged_at')->nullable();
            $table->text('acknowledgment_note')->nullable();

            // Read tracking
            $table->boolean('read')->default(false);
            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['announcement_id', 'employee_id']);
            $table->index('acknowledged');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcement_recipients');
        Schema::dropIfExists('employee_announcements');
    }
};
