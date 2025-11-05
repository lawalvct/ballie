<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'name', 'code', 'description', 'default_days_per_year',
        'is_paid', 'requires_document', 'carry_forward', 'max_carry_forward_days',
        'max_consecutive_days', 'min_notice_days', 'weekends_included', 'holidays_included',
        'accrual_type', 'accrual_rate', 'is_active', 'is_system_defined', 'sort_order',
        'applicable_to'
    ];

    protected $casts = [
        'default_days_per_year' => 'integer',
        'is_paid' => 'boolean',
        'requires_document' => 'boolean',
        'carry_forward' => 'boolean',
        'max_carry_forward_days' => 'integer',
        'max_consecutive_days' => 'integer',
        'min_notice_days' => 'integer',
        'weekends_included' => 'boolean',
        'holidays_included' => 'boolean',
        'accrual_rate' => 'decimal:2',
        'is_active' => 'boolean',
        'is_system_defined' => 'boolean',
        'sort_order' => 'integer',
        'applicable_to' => 'array',
    ];

    // Relationships
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function employeeLeaves(): HasMany
    {
        return $this->hasMany(EmployeeLeave::class);
    }

    public function leaveBalances(): HasMany
    {
        return $this->hasMany(EmployeeLeaveBalance::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    // Methods
    public static function createDefaultTypes($tenantId): void
    {
        $defaultTypes = [
            [
                'name' => 'Annual Leave',
                'code' => 'ANN',
                'description' => 'Paid annual vacation leave',
                'default_days_per_year' => 21,
                'is_paid' => true,
                'carry_forward' => true,
                'max_carry_forward_days' => 5,
                'accrual_type' => 'monthly',
                'accrual_rate' => 1.75, // 21/12 days per month
                'is_system_defined' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Sick Leave',
                'code' => 'SICK',
                'description' => 'Paid sick leave with medical certificate',
                'default_days_per_year' => 10,
                'is_paid' => true,
                'requires_document' => true,
                'accrual_type' => 'yearly',
                'is_system_defined' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Casual Leave',
                'code' => 'CAS',
                'description' => 'Short casual leave for personal matters',
                'default_days_per_year' => 5,
                'is_paid' => true,
                'max_consecutive_days' => 2,
                'accrual_type' => 'yearly',
                'is_system_defined' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Maternity Leave',
                'code' => 'MAT',
                'description' => 'Maternity leave for female employees',
                'default_days_per_year' => 90,
                'is_paid' => true,
                'requires_document' => true,
                'max_consecutive_days' => 90,
                'min_notice_days' => 30,
                'applicable_to' => ['gender' => 'female'],
                'accrual_type' => 'none',
                'is_system_defined' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Paternity Leave',
                'code' => 'PAT',
                'description' => 'Paternity leave for male employees',
                'default_days_per_year' => 7,
                'is_paid' => true,
                'max_consecutive_days' => 7,
                'applicable_to' => ['gender' => 'male'],
                'accrual_type' => 'none',
                'is_system_defined' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Unpaid Leave',
                'code' => 'UNP',
                'description' => 'Leave without pay',
                'default_days_per_year' => 0,
                'is_paid' => false,
                'accrual_type' => 'none',
                'is_system_defined' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($defaultTypes as $type) {
            static::create(array_merge(['tenant_id' => $tenantId], $type));
        }
    }

    public function calculateAccruedDays($monthsWorked): float
    {
        if ($this->accrual_type === 'monthly' && $this->accrual_rate) {
            return $this->accrual_rate * $monthsWorked;
        }

        if ($this->accrual_type === 'yearly') {
            return $this->default_days_per_year;
        }

        return 0;
    }
}
