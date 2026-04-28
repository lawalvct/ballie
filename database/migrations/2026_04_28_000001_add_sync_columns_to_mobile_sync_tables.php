<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 1 mobile offline sync — additive only.
 *
 * Adds sync metadata (sync_uuid, server_version, client_created_at,
 * client_updated_at, last_modified_by_device_id) to tables that the
 * mobile app needs to pull and/or push.
 *
 * No data is rewritten. Existing rows keep `sync_uuid = NULL` until the
 * `ballie:sync:backfill-uuids` Artisan command is run during a low
 * traffic window.
 *
 * SAFE FOR PRODUCTION: every column is nullable and every index is
 * created behind hasColumn / hasIndex guards so re-running the
 * migration on an already-patched DB is idempotent.
 */
return new class extends Migration
{
    /**
     * Tables that get tenant-scoped sync metadata.
     * Each has a `tenant_id` column already.
     */
    private array $tenantScopedTables = [
        'customers',
        'vendors',
        'product_categories',
        'units',
        'products',
        'ledger_accounts',
        'account_groups',
        'voucher_types',
        'stock_locations',
        'vouchers',
        'quotations',
    ];

    /**
     * Tables that hang off a parent and don't carry tenant_id directly.
     * They still get sync columns but are scoped through the parent.
     */
    private array $childTables = [
        'voucher_entries',   // belongs to vouchers (tenant via voucher)
        'quotation_items',   // belongs to quotations (tenant via quotation)
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

        // Indexes — added separately so we can guard against re-runs.
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
