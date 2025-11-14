<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'department_id', 'position_id', 'employee_number', 'unique_link_token',
        'first_name', 'last_name', 'email', 'phone', 'date_of_birth', 'gender',
        'marital_status', 'address', 'state_of_origin', 'job_title', 'hire_date',
        'confirmation_date', 'employment_type', 'pay_frequency', 'status',
        'attendance_deduction_exempt', 'attendance_exemption_reason',
        'bank_name', 'bank_code', 'account_number', 'account_name',
        'tin', 'annual_relief', 'pension_pin', 'pfa_name', 'avatar'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'confirmation_date' => 'date',
        'annual_relief' => 'decimal:2',
        'attendance_deduction_exempt' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employee) {
            if (empty($employee->employee_number)) {
                $employee->employee_number = static::generateEmployeeNumber($employee->tenant_id);
            }
            if (empty($employee->unique_link_token)) {
                $employee->unique_link_token = Str::random(32);
            }
        });
    }

    // Relationships
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function currentSalary(): HasOne
    {
        return $this->hasOne(EmployeeSalary::class)->where('is_current', true);
    }

    public function salaryHistory(): HasMany
    {
        return $this->hasMany(EmployeeSalary::class)->orderBy('effective_date', 'desc');
    }

    public function payrollRuns(): HasMany
    {
        return $this->hasMany(PayrollRun::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(EmployeeLoan::class);
    }

    public function activeLoans(): HasMany
    {
        return $this->hasMany(EmployeeLoan::class)->where('status', 'active');
    }

    // Attendance & Leave Relationships
    public function leaves(): HasMany
    {
        return $this->hasMany(EmployeeLeave::class)->orderBy('created_at', 'desc');
    }

    public function leaveBalances(): HasMany
    {
        return $this->hasMany(EmployeeLeaveBalance::class);
    }

    public function currentYearLeaveBalances(): HasMany
    {
        return $this->hasMany(EmployeeLeaveBalance::class)
            ->where('year', date('Y'));
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class)->orderBy('attendance_date', 'desc');
    }

    public function overtimeRecords(): HasMany
    {
        return $this->hasMany(OvertimeRecord::class)->orderBy('overtime_date', 'desc');
    }

    public function shiftAssignments(): HasMany
    {
        return $this->hasMany(EmployeeShiftAssignment::class)->orderBy('effective_from', 'desc');
    }

    public function currentShiftAssignment(): HasOne
    {
        return $this->hasOne(EmployeeShiftAssignment::class)
            ->where('effective_from', '<=', now())
            ->where(function($q) {
                $q->whereNull('effective_to')
                  ->orWhere('effective_to', '>=', now());
            })
            ->orderBy('effective_from', 'desc');
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getPortalLinkAttribute(): string
    {
        return route('employee.portal', ['token' => $this->unique_link_token]);
    }

    public function getCurrentBasicSalaryAttribute(): float
    {
        return $this->currentSalary?->basic_salary ?? 0;
    }

    // Methods
    public static function generateEmployeeNumber($tenantId): string
    {
        $prefix = 'EMP-' . date('Y') . '-';
        $lastEmployee = static::where('tenant_id', $tenantId)
            ->where('employee_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastEmployee) {
            $lastNumber = intval(substr($lastEmployee->employee_number, strlen($prefix)));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function calculateMonthlyGross(): float
    {
        $basic = $this->current_basic_salary;
        $allowances = $this->currentSalary?->salaryComponents()
            ->whereHas('salaryComponent', function($q) {
                $q->where('type', 'allowance')->where('is_active', true);
            })
            ->get()
            ->sum(function($component) {
                if ($component->salaryComponent->calculation_type === 'percentage') {
                    return ($this->current_basic_salary * $component->percentage) / 100;
                }
                return $component->amount ?? 0;
            });

        return $basic + $allowances;
    }

    public function calculateAnnualGross(): float
    {
        return $this->calculateMonthlyGross() * 12;
    }

    // Attendance Helper Methods
    public function getLeaveBalance($leaveTypeId, $year = null): ?EmployeeLeaveBalance
    {
        $year = $year ?? date('Y');
        return $this->leaveBalances()
            ->where('leave_type_id', $leaveTypeId)
            ->where('year', $year)
            ->first();
    }

    public function hasAvailableLeave($leaveTypeId, $days, $year = null): bool
    {
        $balance = $this->getLeaveBalance($leaveTypeId, $year);
        return $balance && $balance->hasAvailableDays($days);
    }

    public function getTodayAttendance()
    {
        return $this->attendanceRecords()
            ->where('attendance_date', now()->toDateString())
            ->first();
    }

    public function hasClockedInToday(): bool
    {
        $attendance = $this->getTodayAttendance();
        return $attendance && $attendance->clock_in_time !== null;
    }

    public function hasClockedOutToday(): bool
    {
        $attendance = $this->getTodayAttendance();
        return $attendance && $attendance->clock_out_time !== null;
    }

    public function getCurrentShift(): ?ShiftSchedule
    {
        $assignment = $this->currentShiftAssignment;
        return $assignment?->shiftSchedule;
    }

    public function getMonthlyAttendanceSummary($month = null, $year = null): array
    {
        $month = $month ?? date('m');
        $year = $year ?? date('Y');

        $records = $this->attendanceRecords()
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month)
            ->get();

        return [
            'total_days' => $records->count(),
            'present_days' => $records->where('status', 'present')->count(),
            'absent_days' => $records->where('status', 'absent')->count(),
            'half_day_count' => $records->where('status', 'half_day')->count(),
            'late_count' => $records->where('is_late', true)->count(),
            'total_late_minutes' => $records->sum('late_minutes'),
            'total_work_hours' => $records->sum('work_hours'),
            'total_overtime_hours' => $records->sum('overtime_hours'),
        ];
    }

    public function getMonthlyLeaveSummary($month = null, $year = null): array
    {
        $month = $month ?? date('m');
        $year = $year ?? date('Y');

        $leaves = $this->leaves()
            ->whereYear('start_date', $year)
            ->whereMonth('start_date', $month)
            ->where('status', 'approved')
            ->get();

        return [
            'total_leaves' => $leaves->count(),
            'total_days' => $leaves->sum('working_days'),
            'leaves_by_type' => $leaves->groupBy('leave_type_id')->map(function($items) {
                return [
                    'count' => $items->count(),
                    'days' => $items->sum('working_days'),
                ];
            }),
        ];
    }

    public function initializeLeaveBalances($year = null): void
    {
        $year = $year ?? date('Y');
        $leaveTypes = LeaveType::where('tenant_id', $this->tenant_id)
            ->where('is_active', true)
            ->get();

        foreach ($leaveTypes as $leaveType) {
            // Check if already exists
            $exists = $this->leaveBalances()
                ->where('leave_type_id', $leaveType->id)
                ->where('year', $year)
                ->exists();

            if (!$exists) {
                EmployeeLeaveBalance::create([
                    'tenant_id' => $this->tenant_id,
                    'employee_id' => $this->id,
                    'leave_type_id' => $leaveType->id,
                    'year' => $year,
                    'opening_balance' => 0,
                    'allocated_days' => $leaveType->max_days_per_year,
                    'accrued_days' => 0,
                    'used_days' => 0,
                    'pending_days' => 0,
                ]);
            }
        }
    }
}
