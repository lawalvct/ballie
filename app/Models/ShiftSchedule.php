<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShiftSchedule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'name', 'code', 'description',
        'start_time', 'end_time', 'work_hours', 'break_minutes',
        'late_grace_minutes', 'early_out_grace_minutes',
        'shift_allowance', 'is_night_shift', 'working_days',
        'is_active', 'is_default', 'sort_order', 'color'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'work_hours' => 'integer',
        'break_minutes' => 'integer',
        'late_grace_minutes' => 'integer',
        'early_out_grace_minutes' => 'integer',
        'shift_allowance' => 'decimal:2',
        'is_night_shift' => 'boolean',
        'working_days' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'shift_id');
    }

    public function employeeAssignments(): HasMany
    {
        return $this->hasMany(EmployeeShiftAssignment::class, 'shift_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // Methods
    /**
     * @deprecated Use DefaultShiftsSeeder::seedForTenant() instead
     * This method is kept for backward compatibility but seeding should be done via the seeder
     */
    public static function createDefaultShifts($tenantId): void
    {
        \Database\Seeders\DefaultShiftsSeeder::seedForTenant($tenantId);
    }

    public function isWorkingDay(string $dayName): bool
    {
        if (empty($this->working_days)) {
            return true; // All days if not specified
        }

        return in_array(strtolower($dayName), array_map('strtolower', $this->working_days));
    }

    public function getFormattedTimeRange(): string
    {
        return $this->start_time->format('h:i A') . ' - ' . $this->end_time->format('h:i A');
    }
}
