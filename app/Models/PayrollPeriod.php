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
        'total_nsitf', 'created_by', 'approved_by', 'approved_at',
        'apply_paye_tax', 'apply_nsitf', 'paye_tax_rate', 'nsitf_rate', 'tax_exemption_reason'
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
        'apply_paye_tax' => 'boolean',
        'apply_nsitf' => 'boolean',
        'paye_tax_rate' => 'decimal:2',
        'nsitf_rate' => 'decimal:2',
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

        $processedCount = 0;
        $skippedEmployees = [];

        foreach ($employees as $employee) {
            try {
                $this->generatePayrollForEmployee($employee);
                $processedCount++;
            } catch (\Exception $e) {
                // Skip employees without salary and collect their info
                $skippedEmployees[] = [
                    'name' => $employee->full_name,
                    'number' => $employee->employee_number,
                    'reason' => $e->getMessage()
                ];
            }
        }

        $this->updateTotals();
        $this->update(['status' => 'processing']);

        // If any employees were skipped, throw a warning exception
        if (count($skippedEmployees) > 0) {
            $message = "Payroll generated for {$processedCount} employees. ";
            $message .= count($skippedEmployees) . " employee(s) skipped:\n";
            foreach ($skippedEmployees as $skipped) {
                $message .= "- {$skipped['name']} ({$skipped['number']}): No salary record\n";
            }
            // Store this as a flash message instead of throwing
            session()->flash('warning', $message);
        }
    }

    private function generatePayrollForEmployee(Employee $employee): void
    {
        $calculator = new \App\Services\PayrollCalculator($employee, $this);
        $payrollData = $calculator->calculate();

        $payrollRun = PayrollRun::updateOrCreate(
            [
                'payroll_period_id' => $this->id,
                'employee_id' => $employee->id
            ],
            $payrollData
        );

        // Save detailed component breakdown
        $componentDetails = $calculator->getComponentDetails();

        // Delete existing details to avoid duplicates
        $payrollRun->details()->delete();

        // Save earnings details
        foreach ($componentDetails['earnings'] as $earning) {
            $payrollRun->details()->create($earning);
        }

        // Save deductions details
        foreach ($componentDetails['deductions'] as $deduction) {
            $payrollRun->details()->create($deduction);
        }

        // Add PAYE tax deduction if applied and amount > 0
        if ($this->apply_paye_tax && $payrollData['monthly_tax'] > 0) {
            $payrollRun->details()->create([
                'salary_component_id' => null,
                'component_name' => 'PAYE Tax',
                'component_type' => 'deduction',
                'amount' => $payrollData['monthly_tax'],
                'is_taxable' => false,
                'metadata' => [
                    'annual_gross' => $payrollData['annual_gross'],
                    'taxable_income' => $payrollData['taxable_income'],
                    'consolidated_relief' => $payrollData['consolidated_relief'],
                ],
            ]);
        }
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
