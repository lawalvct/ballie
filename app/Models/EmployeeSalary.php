<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeSalary extends Model
{
    protected $fillable = [
        'employee_id',
        'basic_salary',
        'effective_date',
        'end_date',
        'is_current',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'effective_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function salaryComponents(): HasMany
    {
        return $this->hasMany(EmployeeSalaryComponent::class);
    }

    // Methods
    public function getTotalAllowancesAttribute(): float
    {
        return $this->salaryComponents()
            ->whereHas('salaryComponent', function($q) {
                $q->where('type', 'earning')->where('is_active', true);
            })
            ->get()
            ->sum(function($component) {
                if ($component->salaryComponent->calculation_type === 'percentage') {
                    return ($this->basic_salary * $component->percentage) / 100;
                } elseif ($component->salaryComponent->calculation_type === 'fixed') {
                    return $component->amount ?? 0;
                } else {
                    // For 'variable' or 'computed'
                    return $component->amount ?? 0;
                }
            });
    }

    public function getTotalDeductionsAttribute(): float
    {
        return $this->salaryComponents()
            ->whereHas('salaryComponent', function($q) {
                $q->where('type', 'deduction')->where('is_active', true);
            })
            ->get()
            ->sum(function($component) {
                if ($component->salaryComponent->calculation_type === 'percentage') {
                    return ($this->basic_salary * $component->percentage) / 100;
                } elseif ($component->salaryComponent->calculation_type === 'fixed') {
                    return $component->amount ?? 0;
                } else {
                    // For 'variable' or 'computed'
                    return $component->amount ?? 0;
                }
            });
    }

    public function getGrossSalaryAttribute(): float
    {
        return $this->basic_salary + $this->total_allowances;
    }
}
