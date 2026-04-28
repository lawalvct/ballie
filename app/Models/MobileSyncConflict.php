<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MobileSyncConflict extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'mobile_device_id',
        'mobile_mutation_id',
        'client_mutation_id',
        'table_name',
        'record_sync_uuid',
        'conflict_type',
        'client_payload',
        'server_payload',
        'diff',
        'resolution',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'client_payload' => 'array',
        'server_payload' => 'array',
        'diff' => 'array',
        'resolved_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function mutation(): BelongsTo
    {
        return $this->belongsTo(MobileMutation::class, 'mobile_mutation_id');
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(MobileDevice::class, 'mobile_device_id');
    }
}
