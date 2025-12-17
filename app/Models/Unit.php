<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'symbol',
        'description',
        'is_base_unit',
        'base_unit_id',
        'conversion_factor',
        'is_active',
    ];

    protected $casts = [
        'is_base_unit' => 'boolean',
        'is_active' => 'boolean',
        'conversion_factor' => 'decimal:6',
    ];

    protected $attributes = [
        'is_base_unit' => false,
        'is_active' => true,
        'conversion_factor' => 1.0,
    ];

    /**
     * Get the tenant that owns the unit.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the base unit if this is a derived unit.
     */
    public function baseUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    /**
     * Get all derived units of this base unit.
     */
    public function derivedUnits(): HasMany
    {
        return $this->hasMany(Unit::class, 'base_unit_id');
    }

    /**
     * Get all products that use this unit as primary unit.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'primary_unit_id');
    }

    /**
     * Scope a query to only include units for a specific tenant.
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope a query to only include active units.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include base units.
     */
    public function scopeBaseUnits($query)
    {
        return $query->where('is_base_unit', true);
    }

    /**
     * Scope a query to only include derived units.
     */
    public function scopeDerivedUnits($query)
    {
        return $query->where('is_base_unit', false);
    }

    /**
     * Get the full unit display name with symbol.
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->name} ({$this->symbol})";
    }

    /**
     * Get the unit type as a string.
     */
    public function getTypeAttribute(): string
    {
        return $this->is_base_unit ? 'Base Unit' : 'Derived Unit';
    }

    /**
     * Get the status as a string.
     */
    public function getStatusAttribute(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    /**
     * Get the status color for UI display.
     */
    public function getStatusColorAttribute(): string
    {
        return $this->is_active ? 'green' : 'red';
    }

    /**
     * Get the type color for UI display.
     */
    public function getTypeColorAttribute(): string
    {
        return $this->is_base_unit ? 'blue' : 'purple';
    }

    /**
     * Convert a value from this unit to the base unit.
     */
    public function convertToBaseUnit(float $value): float
    {
        if ($this->is_base_unit) {
            return $value;
        }

        return $value * $this->conversion_factor;
    }

    /**
     * Convert a value from the base unit to this unit.
     */
    public function convertFromBaseUnit(float $value): float
    {
        if ($this->is_base_unit) {
            return $value;
        }

        return $value / $this->conversion_factor;
    }

    /**
     * Convert a value from this unit to another unit.
     */
    public function convertTo(Unit $targetUnit, float $value): float
    {
        if ($this->id === $targetUnit->id) {
            return $value;
        }

        // Both units must belong to the same base unit family
        $thisBaseUnit = $this->is_base_unit ? $this : $this->baseUnit;
        $targetBaseUnit = $targetUnit->is_base_unit ? $targetUnit : $targetUnit->baseUnit;

        if ($thisBaseUnit->id !== $targetBaseUnit->id) {
            throw new \InvalidArgumentException('Cannot convert between different unit families.');
        }

        // Convert to base unit first, then to target unit
        $baseValue = $this->convertToBaseUnit($value);
        return $targetUnit->convertFromBaseUnit($baseValue);
    }

    /**
     * Check if this unit can be converted to another unit.
     */
    public function canConvertTo(Unit $targetUnit): bool
    {
        if ($this->id === $targetUnit->id) {
            return true;
        }

        $thisBaseUnit = $this->is_base_unit ? $this : $this->baseUnit;
        $targetBaseUnit = $targetUnit->is_base_unit ? $targetUnit : $targetUnit->baseUnit;

        return $thisBaseUnit && $targetBaseUnit && $thisBaseUnit->id === $targetBaseUnit->id;
    }

    /**
     * Get all units in the same family (same base unit).
     */
    public function getUnitFamily()
    {
        $baseUnit = $this->is_base_unit ? $this : $this->baseUnit;

        if (!$baseUnit) {
            return collect([$this]);
        }

        return Unit::where('tenant_id', $this->tenant_id)
            ->where(function ($query) use ($baseUnit) {
                $query->where('id', $baseUnit->id)
                      ->orWhere('base_unit_id', $baseUnit->id);
            })
            ->get();
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($unit) {
            // Ensure base units have conversion_factor of 1
            if ($unit->is_base_unit) {
                $unit->base_unit_id = null;
                $unit->conversion_factor = 1.0;
            }
        });

        static::updating(function ($unit) {
            // Ensure base units have conversion_factor of 1
            if ($unit->is_base_unit) {
                $unit->base_unit_id = null;
                $unit->conversion_factor = 1.0;
            }
        });

        static::deleting(function ($unit) {
            // Prevent deletion if unit has derived units
            if ($unit->derivedUnits()->count() > 0) {
                throw new \Exception('Cannot delete unit with derived units.');
            }

            // Prevent deletion if unit is used by products
            if ($unit->products()->count() > 0) {
                throw new \Exception('Cannot delete unit that is used by products.');
            }
        });
    }
}
