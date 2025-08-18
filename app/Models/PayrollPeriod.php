<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollPeriod extends Model
{
    protected $fillable = [
        'tenant_id', 'name', 'start_date', 'end_date', 'pay_date', 'type',
        'status', 'total_gross', 'total_deductions', 'total_net', 'total_tax',
        'total_nsitf', 'created_by', 'approved_by', 'approved_at'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'pay_date' => 'date',
        'approved_at' => 'datetime',
        'total_gross' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_net' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total_nsitf' => 'decimal:2',
    ];

    // Relationships
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function payrollRuns(): HasMany
    {
        return $this->hasMany(PayrollRun::class);
    }

    // Methods
    public function canBeProcessed(): bool
    {
        return $this->status === 'draft';
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'processing';
    }

    public function canBePaid(): bool
    {
        return $this->status === 'approved';
    }

    public function generatePayrollForAllEmployees(): void
    {
        $employees = Employee::where('tenant_id', $this->tenant_id)
            ->where('status', 'active')
            ->with(['currentSalary.salaryComponents.salaryComponent', 'activeLoans'])
            ->get();

        foreach ($employees as $employee) {
            $this->generatePayrollForEmployee($employee);
        }

        $this->updateTotals();
        $this->update(['status' => 'processing']);
    }

    private function generatePayrollForEmployee(Employee $employee): void
    {
        $calculator = new \App\Services\PayrollCalculator($employee, $this);
        $payrollData = $calculator->calculate();

        PayrollRun::updateOrCreate(
            [
                'payroll_period_id' => $this->id,
                'employee_id' => $employee->id
            ],
            $payrollData
        );
    }

    public function updateTotals(): void
    {
        $totals = $this->payrollRuns()
            ->selectRaw('
                SUM(gross_salary) as total_gross,
                SUM(total_deductions) as total_deductions,
                SUM(net_salary) as total_net,
                SUM(monthly_tax) as total_tax,
                SUM(nsitf_contribution) as total_nsitf
            ')
            ->first();

        $this->update([
            'total_gross' => $totals->total_gross ?? 0,
            'total_deductions' => $totals->total_deductions ?? 0,
            'total_net' => $totals->total_net ?? 0,
            'total_tax' => $totals->total_tax ?? 0,
            'total_nsitf' => $totals->total_nsitf ?? 0,
        ]);
    }

    public function createAccountingEntries(): void
    {
        $accountingService = new \App\Services\PayrollAccountingService($this);
        $accountingService->createJournalEntries();
    }
}
