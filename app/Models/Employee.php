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
        'tenant_id', 'department_id', 'employee_number', 'unique_link_token',
        'first_name', 'last_name', 'email', 'phone', 'date_of_birth', 'gender',
        'marital_status', 'address', 'state_of_origin', 'job_title', 'hire_date',
        'confirmation_date', 'employment_type', 'pay_frequency', 'status',
        'bank_name', 'bank_code', 'account_number', 'account_name',
        'tin', 'annual_relief', 'pension_pin', 'pfa_name', 'avatar'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'confirmation_date' => 'date',
        'annual_relief' => 'decimal:2',
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
}
