<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class EmployeeLeave extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'employee_id', 'leave_type_id', 'leave_number',
        'start_date', 'end_date', 'total_days', 'working_days', 'reason',
        'employee_remarks', 'document_path', 'contact_during_leave', 'reliever_id',
        'status', 'approved_by', 'approved_at', 'approval_remarks',
        'rejected_by', 'rejected_at', 'rejection_reason',
        'cancelled_by', 'cancelled_at', 'cancellation_reason',
        'actual_return_date', 'is_emergency', 'is_half_day', 'half_day_period',
        'created_by', 'updated_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_days' => 'decimal:2',
        'working_days' => 'decimal:2',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'actual_return_date' => 'date',
        'is_emergency' => 'boolean',
        'is_half_day' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($leave) {
            if (empty($leave->leave_number)) {
                $leave->leave_number = static::generateLeaveNumber($leave->tenant_id);
            }
        });
    }

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

    public function reliever(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reliever_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
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
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'approved')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'approved')
            ->where('start_date', '>', now());
    }

    // Methods
    public static function generateLeaveNumber($tenantId): string
    {
        $prefix = 'LV-' . date('Y') . '-';
        $lastLeave = static::where('tenant_id', $tenantId)
            ->where('leave_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastLeave) {
            $lastNumber = intval(substr($lastLeave->leave_number, strlen($prefix)));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function calculateWorkingDays(): float
    {
        if ($this->is_half_day) {
            return 0.5;
        }

        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);
        $workingDays = 0;
        $totalDays = 0;

        while ($start->lte($end)) {
            $totalDays++;

            // Check if it's a weekend
            $isWeekend = $start->isWeekend();

            // Check if it's a public holiday
            $isHoliday = PublicHoliday::where('tenant_id', $this->tenant_id)
                ->where('holiday_date', $start->toDateString())
                ->where('is_active', true)
                ->exists();

            // Count as working day if:
            // - Not weekend (unless leave type includes weekends)
            // - Not holiday (unless leave type includes holidays)
            if ((!$isWeekend || $this->leaveType->weekends_included) &&
                (!$isHoliday || $this->leaveType->holidays_included)) {
                $workingDays++;
            }

            $start->addDay();
        }

        $this->total_days = $totalDays;
        $this->working_days = $workingDays;

        return $workingDays;
    }

    public function approve(int $userId, ?string $remarks = null): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        // Check if employee has sufficient balance
        $balance = EmployeeLeaveBalance::where('employee_id', $this->employee_id)
            ->where('leave_type_id', $this->leave_type_id)
            ->where('year', Carbon::parse($this->start_date)->year)
            ->first();

        if (!$balance || !$balance->hasAvailableDays($this->working_days)) {
            return false;
        }

        // Update leave status
        $this->status = 'approved';
        $this->approved_by = $userId;
        $this->approved_at = now();
        $this->approval_remarks = $remarks;
        $this->save();

        // Update leave balance
        $balance->removePendingDays($this->working_days);
        $balance->deductDays($this->working_days);

        // Create attendance records for leave days
        $this->createAttendanceRecords();

        return true;
    }

    public function reject(int $userId, string $reason): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->status = 'rejected';
        $this->rejected_by = $userId;
        $this->rejected_at = now();
        $this->rejection_reason = $reason;
        $this->save();

        // Remove pending days from balance
        $balance = EmployeeLeaveBalance::where('employee_id', $this->employee_id)
            ->where('leave_type_id', $this->leave_type_id)
            ->where('year', Carbon::parse($this->start_date)->year)
            ->first();

        if ($balance) {
            $balance->removePendingDays($this->working_days);
        }

        return true;
    }

    public function cancel(int $userId, string $reason): bool
    {
        if (!in_array($this->status, ['pending', 'approved'])) {
            return false;
        }

        $this->status = 'cancelled';
        $this->cancelled_by = $userId;
        $this->cancelled_at = now();
        $this->cancellation_reason = $reason;
        $this->save();

        // Restore leave balance if already approved
        if ($this->status === 'approved') {
            $balance = EmployeeLeaveBalance::where('employee_id', $this->employee_id)
                ->where('leave_type_id', $this->leave_type_id)
                ->where('year', Carbon::parse($this->start_date)->year)
                ->first();

            if ($balance) {
                $balance->used_days -= $this->working_days;
                $balance->recalculateBalance();
            }

            // Delete attendance records
            AttendanceRecord::where('employee_id', $this->employee_id)
                ->whereBetween('attendance_date', [$this->start_date, $this->end_date])
                ->where('status', 'on_leave')
                ->delete();
        } else {
            // Remove pending days
            $balance = EmployeeLeaveBalance::where('employee_id', $this->employee_id)
                ->where('leave_type_id', $this->leave_type_id)
                ->where('year', Carbon::parse($this->start_date)->year)
                ->first();

            if ($balance) {
                $balance->removePendingDays($this->working_days);
            }
        }

        return true;
    }

    protected function createAttendanceRecords(): void
    {
        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);

        while ($start->lte($end)) {
            // Only create attendance for working days
            if (!$start->isWeekend()) {
                AttendanceRecord::updateOrCreate(
                    [
                        'employee_id' => $this->employee_id,
                        'attendance_date' => $start->toDateString(),
                    ],
                    [
                        'tenant_id' => $this->tenant_id,
                        'status' => 'on_leave',
                        'remarks' => "On {$this->leaveType->name} - {$this->leave_number}",
                        'is_approved' => true,
                    ]
                );
            }

            $start->addDay();
        }
    }

    public function isActive(): bool
    {
        return $this->status === 'approved' &&
               Carbon::now()->between($this->start_date, $this->end_date);
    }

    public function isUpcoming(): bool
    {
        return $this->status === 'approved' &&
               Carbon::now()->lt($this->start_date);
    }

    public function isPast(): bool
    {
        return $this->status === 'approved' &&
               Carbon::now()->gt($this->end_date);
    }
}
