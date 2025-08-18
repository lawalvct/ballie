<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollRun extends Model
{
    protected $fillable = [
        'payroll_period_id', 'employee_id', 'basic_salary', 'total_allowances',
        'gross_salary', 'annual_gross', 'consolidated_relief', 'taxable_income',
        'annual_tax', 'monthly_tax', 'nsitf_contribution', 'other_deductions',
        'total_deductions', 'net_salary', 'payment_status', 'paid_at',
        'payment_reference', 'salary_expense_voucher_id', 'tax_payable_voucher_id'
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'total_allowances' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'annual_gross' => 'decimal:2',
        'consolidated_relief' => 'decimal:2',
        'taxable_income' => 'decimal:2',
        'annual_tax' => 'decimal:2',
        'monthly_tax' => 'decimal:2',
        'nsitf_contribution' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // Relationships
    public function payrollPeriod(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(PayrollRunDetail::class);
    }

    public function salaryExpenseVoucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class, 'salary_expense_voucher_id');
    }

    public function taxPayableVoucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class, 'tax_payable_voucher_id');
    }

    // Methods
    public function markAsPaid(string $reference = null): void
    {
        $this->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
            'payment_reference' => $reference,
        ]);
    }

    public function markAsFailed(): void
    {
        $this->update([
            'payment_status' => 'failed',
        ]);
    }

    public function getFormattedNetSalaryAttribute(): string
    {
        return '₦' . number_format($this->net_salary, 2);
    }

    public function getFormattedGrossSalaryAttribute(): string
    {
        return '₦' . number_format($this->gross_salary, 2);
    }
}
