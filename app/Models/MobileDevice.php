<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MobileDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'device_uuid',
        'device_name',
        'platform',
        'app_version',
        'os_version',
        'push_token',
        'last_seen_at',
        'last_synced_at',
        'last_pull_cursor',
        'revoked_at',
        'revoked_reason',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
        'last_synced_at' => 'datetime',
        'revoked_at' => 'datetime',
        'last_pull_cursor' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mutations(): HasMany
    {
        return $this->hasMany(MobileMutation::class);
    }

    public function isActive(): bool
    {
        return $this->revoked_at === null;
    }

    public function markSeen(): void
    {
        $this->forceFill(['last_seen_at' => now()])->save();
    }
}
