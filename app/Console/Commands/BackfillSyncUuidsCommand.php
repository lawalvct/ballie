<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Generates `sync_uuid` values for legacy rows so the mobile sync
 * layer can reference them.
 *
 * Safe for production:
 *  - Idempotent: only touches rows where `sync_uuid IS NULL`.
 *  - Chunked: processes 500 rows at a time (configurable).
 *  - Dry-run: `--dry-run` prints counts without writing.
 *  - Per-table: `--table=customers` to backfill a single table.
 *  - Sleeps briefly between chunks to keep DB load light.
 */
class BackfillSyncUuidsCommand extends Command
{
    protected $signature = 'ballie:sync:backfill-uuids
                            {--table= : Restrict to a single table from the registry}
                            {--chunk= : Override chunk size}
                            {--dry-run : Report counts without writing}';

    protected $description = 'Backfill sync_uuid for existing rows on tables registered for mobile sync.';

    public function handle(): int
    {
        $registry = config('mobile_sync.registry', []);
        if (!$registry) {
            $this->error('mobile_sync.registry is empty. Aborting.');
            return self::FAILURE;
        }

        $only = $this->option('table');
        $chunk = (int) ($this->option('chunk') ?: config('mobile_sync.backfill.chunk_size', 500));
        $sleepMs = (int) config('mobile_sync.backfill.sleep_ms_between_chunks', 50);
        $dryRun = (bool) $this->option('dry-run');

        $tables = $only ? [$only] : array_keys($registry);

        foreach ($tables as $table) {
            if (!Arr::has($registry, $table)) {
                $this->warn("Table {$table} is not in the sync registry. Skipping.");
                continue;
            }

            $this->backfillTable($table, $chunk, $sleepMs, $dryRun);
        }

        $this->info('Backfill complete.');
        return self::SUCCESS;
    }

    private function backfillTable(string $table, int $chunk, int $sleepMs, bool $dryRun): void
    {
        if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'sync_uuid')) {
            $this->warn("Skipping {$table}: table or sync_uuid column missing.");
            return;
        }

        $pending = DB::table($table)->whereNull('sync_uuid')->count();

        if ($pending === 0) {
            $this->line("✓ {$table}: nothing to do");
            return;
        }

        $this->info("→ {$table}: {$pending} row(s) need sync_uuid" . ($dryRun ? ' (dry run)' : ''));

        if ($dryRun) {
            return;
        }

        $bar = $this->output->createProgressBar($pending);
        $bar->start();

        $touched = 0;
        do {
            $ids = DB::table($table)
                ->whereNull('sync_uuid')
                ->orderBy('id')
                ->limit($chunk)
                ->pluck('id')
                ->all();

            if (empty($ids)) {
                break;
            }

            DB::transaction(function () use ($table, $ids) {
                foreach ($ids as $id) {
                    DB::table($table)
                        ->where('id', $id)
                        ->whereNull('sync_uuid')
                        ->update(['sync_uuid' => (string) Str::uuid()]);
                }
            });

            $touched += count($ids);
            $bar->advance(count($ids));

            if ($sleepMs > 0) {
                usleep($sleepMs * 1000);
            }
        } while (count($ids) === $chunk);

        $bar->finish();
        $this->newLine();
        $this->info("  ✓ {$table}: backfilled {$touched} row(s)");
    }
}
