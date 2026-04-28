<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Syncable;

class StockLocation extends Model
{
    use HasFactory, Syncable;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'type',
        'description',
        'is_main',
        'is_wip',
        'is_active',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_main' => 'boolean',
        'is_wip' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public const TYPES = [
        'store' => 'Store',
        'warehouse' => 'Warehouse',
        'department' => 'Department',
        'production' => 'Production',
        'wip' => 'Work In Progress',
        'branch' => 'Branch',
        'other' => 'Other',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $location) {
            if ($location->is_main) {
                static::where('tenant_id', $location->tenant_id)
                    ->where('id', '!=', $location->id)
                    ->where('is_main', true)
                    ->update(['is_main' => false]);
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'stock_location_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Get the main (default) Store location for a tenant. Auto-creates if missing.
     */
    public static function getMainForTenant($tenantId): self
    {
        $main = static::where('tenant_id', $tenantId)
            ->where('is_main', true)
            ->where('is_active', true)
            ->first();

        if ($main) {
            return $main;
        }

        return static::ensureMainForTenant($tenantId);
    }

    /**
     * Ensure a tenant has a default Store location. Idempotent.
     */
    public static function ensureMainForTenant($tenantId): self
    {
        $existing = static::where('tenant_id', $tenantId)
            ->where(function ($q) {
                $q->where('is_main', true)
                  ->orWhere('code', 'STORE');
            })
            ->first();

        if ($existing) {
            if (!$existing->is_main || !$existing->is_active) {
                $existing->update(['is_main' => true, 'is_active' => true]);
            }
            return $existing;
        }

        return static::create([
            'tenant_id' => $tenantId,
            'name' => 'Store',
            'code' => 'STORE',
            'type' => 'store',
            'description' => 'Default main store location',
            'is_main' => true,
            'is_active' => true,
            'sort_order' => 0,
        ]);
    }

    public function getTypeLabelAttribute(): string
    {
        return static::TYPES[$this->type] ?? ucfirst($this->type);
    }
}
