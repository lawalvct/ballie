<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeLeaveBalance extends Model
{
    protected $fillable = [
        'tenant_id', 'employee_id', 'leave_type_id', 'year',
        'opening_balance', 'allocated_days', 'accrued_days', 'used_days',
        'pending_days', 'available_days', 'carried_forward', 'expired_days',
        'last_accrual_date'
    ];

    protected $casts = [
        'year' => 'integer',
        'opening_balance' => 'decimal:2',
        'allocated_days' => 'decimal:2',
        'accrued_days' => 'decimal:2',
        'used_days' => 'decimal:2',
        'pending_days' => 'decimal:2',
        'available_days' => 'decimal:2',
        'carried_forward' => 'decimal:2',
        'expired_days' => 'decimal:2',
        'last_accrual_date' => 'date',
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

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    // Methods
    public function recalculateBalance(): void
    {
        $this->available_days = $this->opening_balance
            + $this->allocated_days
            + $this->accrued_days
            - $this->used_days
            - $this->pending_days;

        $this->save();
    }

    public function hasAvailableDays(float $requestedDays): bool
    {
        return $this->available_days >= $requestedDays;
    }

    public function deductDays(float $days): void
    {
        $this->used_days += $days;
        $this->recalculateBalance();
    }

    public function addPendingDays(float $days): void
    {
        $this->pending_days += $days;
        $this->recalculateBalance();
    }

    public function removePendingDays(float $days): void
    {
        $this->pending_days -= $days;
        $this->recalculateBalance();
    }

    public function accrueMonthly(): void
    {
        if ($this->leaveType->accrual_type !== 'monthly') {
            return;
        }

        $lastAccrualDate = $this->last_accrual_date ?? now()->startOfYear();
        $monthsSinceLastAccrual = now()->diffInMonths($lastAccrualDate);

        if ($monthsSinceLastAccrual > 0) {
            $accrualAmount = $this->leaveType->accrual_rate * $monthsSinceLastAccrual;
            $this->accrued_days += $accrualAmount;
            $this->last_accrual_date = now();
            $this->recalculateBalance();
        }
    }

    public static function initializeBalance(Employee $employee, LeaveType $leaveType, int $year): self
    {
        return static::create([
            'tenant_id' => $employee->tenant_id,
            'employee_id' => $employee->id,
            'leave_type_id' => $leaveType->id,
            'year' => $year,
            'allocated_days' => $leaveType->default_days_per_year,
            'available_days' => $leaveType->default_days_per_year,
        ]);
    }

    public function carryForwardToNextYear(): ?self
    {
        if (!$this->leaveType->carry_forward) {
            return null;
        }

        $carryForward = min(
            $this->available_days,
            $this->leaveType->max_carry_forward_days
        );

        $expired = $this->available_days - $carryForward;

        if ($expired > 0) {
            $this->expired_days = $expired;
            $this->save();
        }

        if ($carryForward > 0) {
            return static::create([
                'tenant_id' => $this->tenant_id,
                'employee_id' => $this->employee_id,
                'leave_type_id' => $this->leave_type_id,
                'year' => $this->year + 1,
                'opening_balance' => $carryForward,
                'carried_forward' => $carryForward,
                'allocated_days' => $this->leaveType->default_days_per_year,
                'available_days' => $carryForward + $this->leaveType->default_days_per_year,
            ]);
        }

        return null;
    }
}
