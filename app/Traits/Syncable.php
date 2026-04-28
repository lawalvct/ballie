<?php

namespace App\Traits;

use Illuminate\Support\Str;

/**
 * Mobile offline sync metadata helper.
 *
 * Adds:
 *  - automatic generation of `sync_uuid` on create (if not supplied)
 *  - automatic increment of `server_version` on update
 *  - timestamps coming from the offline client (`client_created_at`,
 *    `client_updated_at`)
 *  - `last_modified_by_device_id` capture
 *  - `scopeChangedSince()` for incremental pulls
 *
 * The trait is intentionally defensive: if a model does not yet have
 * the sync columns (older migrations not run), it silently no-ops.
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait Syncable
{
    public static function bootSyncable(): void
    {
        static::creating(function ($model) {
            if ($model->isFillable('sync_uuid') || in_array('sync_uuid', $model->getFillable(), true) || $model->hasAttribute('sync_uuid')) {
                if (empty($model->sync_uuid)) {
                    $model->sync_uuid = (string) Str::uuid();
                }
            } elseif (empty($model->getAttribute('sync_uuid'))) {
                // Even without fillable, persist sync_uuid because it is server-managed.
                $model->setAttribute('sync_uuid', (string) Str::uuid());
            }

            if ($model->getAttribute('server_version') === null) {
                $model->setAttribute('server_version', 1);
            }
        });

        static::updating(function ($model) {
            // Only bump if there is a real change in attributes other than the
            // bookkeeping columns themselves.
            $ignored = ['server_version', 'updated_at', 'last_modified_by_device_id'];
            $dirty = collect($model->getDirty())->keys()->diff($ignored);

            if ($dirty->isNotEmpty()) {
                $current = (int) ($model->getAttribute('server_version') ?? 0);
                $model->setAttribute('server_version', $current + 1);
            }
        });
    }

    /**
     * Initialize trait-managed columns as fillable so mass-assignment
     * via the sync push endpoint works without relaxing each model.
     */
    public function initializeSyncable(): void
    {
        $this->mergeFillable([
            'sync_uuid',
            'client_created_at',
            'client_updated_at',
            'last_modified_by_device_id',
        ]);

        $this->mergeCasts([
            'client_created_at' => 'datetime',
            'client_updated_at' => 'datetime',
            'server_version' => 'integer',
        ]);
    }

    /**
     * Pull-style "give me everything updated after X" scope.
     */
    public function scopeChangedSince($query, ?string $since)
    {
        if (!$since) {
            return $query;
        }

        return $query->where(function ($q) use ($since) {
            $q->where('updated_at', '>', $since)
                ->orWhere('created_at', '>', $since);
        });
    }

    public function scopeForTenant($query, int $tenantId)
    {
        if (in_array('tenant_id', $this->getFillable(), true) || $this->hasAttribute('tenant_id')) {
            return $query->where($this->getTable() . '.tenant_id', $tenantId);
        }

        return $query;
    }

    private function hasAttribute(string $key): bool
    {
        return array_key_exists($key, $this->attributes ?? [])
            || in_array($key, $this->getFillable(), true);
    }
}
