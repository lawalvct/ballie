<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 4 mobile offline sync — additive only.
 *
 * Adds sync metadata (sync_uuid, server_version, client_created_at,
 * client_updated_at, last_modified_by_device_id) to the stock journal
 * tables so mobile can push draft transfers / production / consumption
 * / adjustment entries created offline.
 *
 * Posting (status='posted') remains online-only; this migration only
 * unlocks the draft create path.
 *
 * SAFE FOR PRODUCTION: every column is nullable, every index is
 * created behind hasColumn / hasIndex guards, and re-runs are
 * idempotent. Existing rows keep `sync_uuid = NULL` until backfilled.
 */
return new class extends Migration
{
    /** Tenant-scoped (has tenant_id directly). */
    private array $tenantScopedTables = [
        'stock_journal_entries',
    ];

    /** Child rows scoped via the parent stock journal entry. */
    private array $childTables = [
        'stock_journal_entry_items',
    ];

    public function up(): void
    {
        foreach ($this->tenantScopedTables as $table) {
            $this->addSyncColumns($table, tenantScoped: true);
        }

        foreach ($this->childTables as $table) {
            $this->addSyncColumns($table, tenantScoped: false);
        }
    }

    public function down(): void
    {
        foreach (array_merge($this->tenantScopedTables, $this->childTables) as $table) {
            $this->dropSyncColumns($table);
        }
    }

    private function addSyncColumns(string $table, bool $tenantScoped): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($table) {
            if (!Schema::hasColumn($table, 'sync_uuid')) {
                $blueprint->uuid('sync_uuid')->nullable()->after('id');
            }
            if (!Schema::hasColumn($table, 'server_version')) {
                $blueprint->unsignedBigInteger('server_version')->default(1)->after('sync_uuid');
            }
            if (!Schema::hasColumn($table, 'client_created_at')) {
                $blueprint->timestamp('client_created_at')->nullable()->after('server_version');
            }
            if (!Schema::hasColumn($table, 'client_updated_at')) {
                $blueprint->timestamp('client_updated_at')->nullable()->after('client_created_at');
            }
            if (!Schema::hasColumn($table, 'last_modified_by_device_id')) {
                $blueprint->string('last_modified_by_device_id', 64)
                    ->nullable()
                    ->after('client_updated_at');
            }
        });

        Schema::table($table, function (Blueprint $blueprint) use ($table, $tenantScoped) {
            $existingIndexes = $this->existingIndexNames($table);

            $uniqueName = "{$table}_sync_uuid_unique";
            if (Schema::hasColumn($table, 'sync_uuid') && !in_array($uniqueName, $existingIndexes, true)) {
                $blueprint->unique('sync_uuid', $uniqueName);
            }

            $updatedAtIdx = "{$table}_sync_updated_at_index";
            if (
                Schema::hasColumn($table, 'updated_at')
                && !in_array($updatedAtIdx, $existingIndexes, true)
            ) {
                if ($tenantScoped && Schema::hasColumn($table, 'tenant_id')) {
                    $blueprint->index(['tenant_id', 'updated_at'], $updatedAtIdx);
                } else {
                    $blueprint->index('updated_at', $updatedAtIdx);
                }
            }
        });
    }

    private function dropSyncColumns(string $table): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        $existingIndexes = $this->existingIndexNames($table);

        Schema::table($table, function (Blueprint $blueprint) use ($table, $existingIndexes) {
            $uniqueName = "{$table}_sync_uuid_unique";
            $updatedAtIdx = "{$table}_sync_updated_at_index";

            if (in_array($uniqueName, $existingIndexes, true)) {
                $blueprint->dropUnique($uniqueName);
            }
            if (in_array($updatedAtIdx, $existingIndexes, true)) {
                $blueprint->dropIndex($updatedAtIdx);
            }

            foreach ([
                'sync_uuid',
                'server_version',
                'client_created_at',
                'client_updated_at',
                'last_modified_by_device_id',
            ] as $column) {
                if (Schema::hasColumn($table, $column)) {
                    $blueprint->dropColumn($column);
                }
            }
        });
    }

    private function existingIndexNames(string $table): array
    {
        try {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            return array_keys($sm->listTableIndexes($table));
        } catch (\Throwable $e) {
            return [];
        }
    }
};
