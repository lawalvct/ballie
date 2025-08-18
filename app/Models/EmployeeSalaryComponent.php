<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSalaryComponent extends Model
{
    protected $fillable = [
        'employee_salary_id',
        'salary_component_id',
        'amount',
        'percentage',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function employeeSalary(): BelongsTo
    {
        return $this->belongsTo(EmployeeSalary::class);
    }

    public function salaryComponent(): BelongsTo
    {
        return $this->belongsTo(SalaryComponent::class);
    }

    // Methods
    public function getCalculatedAmountAttribute(): float
    {
        if ($this->salaryComponent->calculation_type === 'percentage') {
            return ($this->employeeSalary->basic_salary * $this->percentage) / 100;
        }
        return $this->amount ?? 0;
    }
}
