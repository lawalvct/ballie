<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 1 mobile offline sync — new tables only.
 *
 * - mobile_devices       : trusted device registry per tenant/user.
 * - mobile_mutations     : idempotent log of every offline mutation
 *                          pushed by a device.
 * - sync_tombstones      : deletion log for tables that are hard-deleted
 *                          or otherwise need a propagated delete event.
 * - mobile_sync_conflicts: server-detected conflicts that require user
 *                          or admin review on the mobile app.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('mobile_devices')) {
            Schema::create('mobile_devices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('device_uuid', 64);
                $table->string('device_name')->nullable();
                $table->string('platform', 32)->nullable(); // ios | android | web
                $table->string('app_version', 32)->nullable();
                $table->string('os_version', 64)->nullable();
                $table->string('push_token')->nullable();
                $table->timestamp('last_seen_at')->nullable();
                $table->timestamp('last_synced_at')->nullable();
                $table->unsignedBigInteger('last_pull_cursor')->nullable();
                $table->timestamp('revoked_at')->nullable();
                $table->string('revoked_reason')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'device_uuid'], 'mobile_devices_tenant_device_unique');
                $table->index(['tenant_id', 'user_id'], 'mobile_devices_tenant_user_index');
                $table->index('last_seen_at', 'mobile_devices_last_seen_index');
            });
        }

        if (!Schema::hasTable('mobile_mutations')) {
            Schema::create('mobile_mutations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('mobile_device_id')->nullable()->constrained('mobile_devices')->nullOnDelete();
                $table->string('device_uuid', 64);
                $table->string('client_mutation_id', 64);
                $table->string('table_name', 64);
                $table->uuid('record_sync_uuid')->nullable();
                $table->string('action', 16); // create | update | delete
                $table->unsignedBigInteger('base_server_version')->nullable();
                $table->string('payload_hash', 64)->nullable();
                $table->json('payload')->nullable();
                $table->json('server_response')->nullable();
                $table->string('status', 16)->default('pending'); // pending|applied|conflict|failed|skipped
                $table->string('error_code', 64)->nullable();
                $table->text('error_message')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->timestamps();

                $table->unique(
                    ['tenant_id', 'device_uuid', 'client_mutation_id'],
                    'mobile_mutations_idempotency_unique'
                );
                $table->index(['tenant_id', 'status'], 'mobile_mutations_tenant_status_index');
                $table->index(['tenant_id', 'table_name', 'record_sync_uuid'], 'mobile_mutations_record_index');
                $table->index('processed_at', 'mobile_mutations_processed_at_index');
            });
        }

        if (!Schema::hasTable('sync_tombstones')) {
            Schema::create('sync_tombstones', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
                $table->string('table_name', 64);
                $table->uuid('record_sync_uuid');
                $table->unsignedBigInteger('server_id')->nullable();
                $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('reason')->nullable();
                $table->timestamp('deleted_at')->useCurrent();
                $table->timestamp('created_at')->useCurrent();

                $table->unique(
                    ['tenant_id', 'table_name', 'record_sync_uuid'],
                    'sync_tombstones_tenant_record_unique'
                );
                $table->index(['tenant_id', 'table_name', 'deleted_at'], 'sync_tombstones_pull_index');
            });
        }

        if (!Schema::hasTable('mobile_sync_conflicts')) {
            Schema::create('mobile_sync_conflicts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('mobile_device_id')->nullable()->constrained('mobile_devices')->nullOnDelete();
                $table->foreignId('mobile_mutation_id')->nullable()->constrained('mobile_mutations')->nullOnDelete();
                $table->string('client_mutation_id', 64)->nullable();
                $table->string('table_name', 64);
                $table->uuid('record_sync_uuid')->nullable();
                $table->string('conflict_type', 32); // version_mismatch | duplicate | validation | permission | stock | numbering
                $table->json('client_payload')->nullable();
                $table->json('server_payload')->nullable();
                $table->json('diff')->nullable();
                $table->string('resolution', 32)->nullable(); // server_wins | client_wins | merged | manual
                $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('resolved_at')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'table_name', 'record_sync_uuid'], 'mobile_sync_conflicts_record_index');
                $table->index(['tenant_id', 'resolved_at'], 'mobile_sync_conflicts_pending_index');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mobile_sync_conflicts');
        Schema::dropIfExists('sync_tombstones');
        Schema::dropIfExists('mobile_mutations');
        Schema::dropIfExists('mobile_devices');
    }
};
