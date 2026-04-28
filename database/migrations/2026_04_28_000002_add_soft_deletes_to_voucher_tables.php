<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 1 mobile offline sync — additive only.
 *
 * Adds soft deletes to vouchers and voucher_entries so the mobile app
 * can detect deletions without losing audit history. Existing rows are
 * untouched (deleted_at remains NULL).
 *
 * Idempotent: guarded by hasColumn so re-runs are no-ops.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('vouchers') && !Schema::hasColumn('vouchers', 'deleted_at')) {
            Schema::table('vouchers', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
                $table->index(['tenant_id', 'deleted_at'], 'vouchers_tenant_deleted_at_index');
            });
        }

        if (Schema::hasTable('voucher_entries') && !Schema::hasColumn('voucher_entries', 'deleted_at')) {
            Schema::table('voucher_entries', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
                $table->index('deleted_at', 'voucher_entries_deleted_at_index');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('vouchers') && Schema::hasColumn('vouchers', 'deleted_at')) {
            Schema::table('vouchers', function (Blueprint $table) {
                try {
                    $table->dropIndex('vouchers_tenant_deleted_at_index');
                } catch (\Throwable $e) {
                }
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasTable('voucher_entries') && Schema::hasColumn('voucher_entries', 'deleted_at')) {
            Schema::table('voucher_entries', function (Blueprint $table) {
                try {
                    $table->dropIndex('voucher_entries_deleted_at_index');
                } catch (\Throwable $e) {
                }
                $table->dropSoftDeletes();
            });
        }
    }
};
