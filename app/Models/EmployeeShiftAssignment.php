<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class EmployeeShiftAssignment extends Model
{
    protected $fillable = [
        'tenant_id', 'employee_id', 'shift_id',
        'effective_from', 'effective_to', 'is_permanent',
        'remarks', 'is_active', 'created_by', 'updated_by'
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'is_permanent' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(ShiftSchedule::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrent($query)
    {
        $today = Carbon::today();
        return $query->where('effective_from', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('effective_to')
                  ->orWhere('effective_to', '>=', $today);
            });
    }

    // Methods
    public function isCurrentlyActive(): bool
    {
        $today = Carbon::today();

        return $this->is_active &&
               $this->effective_from <= $today &&
               ($this->effective_to === null || $this->effective_to >= $today);
    }

    public function end(Carbon $endDate): void
    {
        $this->effective_to = $endDate;
        $this->is_active = false;
        $this->save();
    }
}
