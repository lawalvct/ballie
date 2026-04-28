<?php

namespace App\Services\MobileSync;

use App\Models\MobileDevice;
use App\Models\SyncTombstone;
use App\Models\Tenant;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class PullService
{
    public function __construct(private SyncRegistry $registry)
    {
    }

    /**
     * Run an incremental pull for the requested tables.
     *
     * @param  array<int, string>|null  $requestedTables  null = all allowed
     * @return array{server_time: string, tables: array<string, array{records: array, deletions: array, has_more: bool, next_cursor: string|null}>}
     */
    public function pull(
        Tenant $tenant,
        User $user,
        MobileDevice $device,
        ?string $lastPulledAt,
        ?array $requestedTables = null,
        ?int $limit = null,
    ): array {
        $allowed = $this->registry->pullableTablesFor($user);

        if ($requestedTables) {
            $allowed = array_values(array_intersect($allowed, $requestedTables));
        }

        $maxTables = (int) config('mobile_sync.pull.max_tables_per_request', 30);
        $allowed = array_slice($allowed, 0, $maxTables);

        $defaultLimit = (int) config('mobile_sync.pull.default_limit', 200);
        $maxLimit = (int) config('mobile_sync.pull.max_limit', 1000);
        $limit = max(1, min($limit ?: $defaultLimit, $maxLimit));

        $serverTime = CarbonImmutable::now()->toIso8601String();
        $tables = [];

        foreach ($allowed as $tableName) {
            $tables[$tableName] = $this->pullTable($tenant, $tableName, $lastPulledAt, $limit);
        }

        $device->forceFill([
            'last_synced_at' => now(),
            'last_seen_at' => now(),
        ])->save();

        return [
            'server_time' => $serverTime,
            'tables' => $tables,
        ];
    }

    /**
     * @return array{records: array, deletions: array, has_more: bool, next_cursor: string|null}
     */
    private function pullTable(Tenant $tenant, string $table, ?string $since, int $limit): array
    {
        $def = $this->registry->get($table);
        if (!$def) {
            return ['records' => [], 'deletions' => [], 'has_more' => false, 'next_cursor' => null];
        }

        $modelClass = Arr::get($def, 'model');
        if (!class_exists($modelClass)) {
            return ['records' => [], 'deletions' => [], 'has_more' => false, 'next_cursor' => null];
        }

        /** @var Model $modelClass */
        $query = $modelClass::query();

        if (Arr::get($def, 'tenant_scoped')) {
            $query->where('tenant_id', $tenant->id);
        } elseif ($parentVia = Arr::get($def, 'parent_via')) {
            $relation = $parentVia['relation'] ?? null;
            if ($relation) {
                $query->whereHas($relation, function (Builder $q) use ($tenant) {
                    $q->where('tenant_id', $tenant->id);
                });
            }
        }

        if ($since) {
            $query->where(function (Builder $q) use ($since) {
                $q->where('updated_at', '>', $since)
                    ->orWhere('created_at', '>', $since);
            });
        }

        // include soft-deleted rows so the client can drop them locally
        if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive($modelClass), true)) {
            $query->withTrashed();
        }

        $records = $query->orderBy('updated_at')
            ->orderBy('id')
            ->limit($limit + 1)
            ->get();

        $hasMore = $records->count() > $limit;
        if ($hasMore) {
            $records = $records->slice(0, $limit)->values();
        }

        $publicAttrs = $this->registry->publicAttributes($table);
        $records = $records->map(function (Model $row) use ($publicAttrs) {
            $array = $row->toArray();
            if ($publicAttrs) {
                $array = Arr::only($array, $publicAttrs);
            }
            return $array;
        })->values();

        $deletions = SyncTombstone::query()
            ->where('tenant_id', $tenant->id)
            ->where('table_name', $table)
            ->when($since, fn ($q) => $q->where('deleted_at', '>', $since))
            ->orderBy('deleted_at')
            ->limit($limit)
            ->get(['record_sync_uuid', 'server_id', 'deleted_at'])
            ->map(fn ($t) => [
                'sync_uuid' => $t->record_sync_uuid,
                'server_id' => $t->server_id,
                'deleted_at' => optional($t->deleted_at)->toIso8601String(),
            ])->values();

        $nextCursor = null;
        if ($hasMore) {
            $last = $records->last();
            $nextCursor = $last['updated_at'] ?? null;
        }

        return [
            'records' => $records->all(),
            'deletions' => $deletions->all(),
            'has_more' => $hasMore,
            'next_cursor' => $nextCursor,
        ];
    }
}
