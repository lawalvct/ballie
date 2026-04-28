<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyncTombstone extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'table_name',
        'record_sync_uuid',
        'server_id',
        'deleted_by',
        'reason',
        'deleted_at',
        'created_at',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'server_id' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public static function record(
        int $tenantId,
        string $tableName,
        string $syncUuid,
        ?int $serverId = null,
        ?int $deletedBy = null,
        ?string $reason = null,
    ): self {
        return static::updateOrCreate(
            [
                'tenant_id' => $tenantId,
                'table_name' => $tableName,
                'record_sync_uuid' => $syncUuid,
            ],
            [
                'server_id' => $serverId,
                'deleted_by' => $deletedBy,
                'reason' => $reason,
                'deleted_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
