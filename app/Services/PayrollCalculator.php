<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Models\TaxBracket;

class PayrollCalculator
{
    private Employee $employee;
    private PayrollPeriod $period;
    private array $calculations = [];

    public function __construct(Employee $employee, PayrollPeriod $period)
    {
        $this->employee = $employee;
        $this->period = $period;
    }

    public function calculate(): array
    {
        // Step 1: Calculate basic salary and allowances
        $this->calculateGrossSalary();

        // Step 2: Calculate PAYE tax
        $this->calculatePAYE();

        // Step 3: Calculate NSITF
        $this->calculateNSITF();

        // Step 4: Calculate other deductions
        $this->calculateOtherDeductions();

        // Step 5: Calculate net salary
        $this->calculateNetSalary();

        return $this->preparePayrollData();
    }

    private function calculateGrossSalary(): void
    {
        $salary = $this->employee->currentSalary;
        $this->calculations['basic_salary'] = $salary->basic_salary;

        $allowances = 0;
        foreach ($salary->salaryComponents as $component) {
            if ($component->salaryComponent->type === 'allowance' && $component->is_active) {
                if ($component->salaryComponent->calculation_type === 'percentage') {
                    $amount = ($this->calculations['basic_salary'] * $component->percentage) / 100;
                } else {
                    $amount = $component->amount;
                }
                $allowances += $amount;
            }
        }

        $this->calculations['total_allowances'] = $allowances;
        $this->calculations['gross_salary'] = $this->calculations['basic_salary'] + $allowances;
    }

    private function calculatePAYE(): void
    {
        $annualGross = $this->calculations['gross_salary'] * 12;
        $consolidatedRelief = max($annualGross * 0.01, $this->employee->annual_relief); // 1% or ₦200k minimum
        $taxableIncome = $annualGross - $consolidatedRelief;

        $annualTax = $this->calculateTaxFromBrackets($taxableIncome);
        $monthlyTax = $annualTax / 12;

        $this->calculations['annual_gross'] = $annualGross;
        $this->calculations['consolidated_relief'] = $consolidatedRelief;
        $this->calculations['taxable_income'] = $taxableIncome;
        $this->calculations['annual_tax'] = $annualTax;
        $this->calculations['monthly_tax'] = $monthlyTax;
    }

    private function calculateTaxFromBrackets(float $taxableIncome): float
    {
        $currentYear = date('Y');
        $brackets = TaxBracket::where('tenant_id', $this->employee->tenant_id)
            ->where('year', $currentYear)
            ->where('is_active', true)
            ->orderBy('min_amount')
            ->get();

        if ($brackets->isEmpty()) {
            // Use default Nigerian PAYE rates for 2024
            $brackets = collect([
                (object)['min_amount' => 0, 'max_amount' => 300000, 'rate' => 7],
                (object)['min_amount' => 300000, 'max_amount' => 600000, 'rate' => 11],
                (object)['min_amount' => 600000, 'max_amount' => 1100000, 'rate' => 15],
                (object)['min_amount' => 1100000, 'max_amount' => 1600000, 'rate' => 19],
                (object)['min_amount' => 1600000, 'max_amount' => 3200000, 'rate' => 21],
                (object)['min_amount' => 3200000, 'max_amount' => null, 'rate' => 24],
            ]);
        }

        $totalTax = 0;
        $remainingIncome = $taxableIncome;

        foreach ($brackets as $bracket) {
            if ($remainingIncome <= 0) break;

            $bracketMin = $bracket->min_amount;
            $bracketMax = $bracket->max_amount ?? PHP_FLOAT_MAX;
            $bracketRate = $bracket->rate / 100;

            if ($taxableIncome > $bracketMin) {
                $taxableInBracket = min($remainingIncome, $bracketMax - $bracketMin);
                $taxInBracket = $taxableInBracket * $bracketRate;
                $totalTax += $taxInBracket;
                $remainingIncome -= $taxableInBracket;
            }
        }

        return $totalTax;
    }

    private function calculateNSITF(): void
    {
        // NSITF is 1% of annual gross (employer contribution)
        $nsitf = ($this->calculations['annual_gross'] * 0.01) / 12;
        $this->calculations['nsitf_contribution'] = $nsitf;
    }

    private function calculateOtherDeductions(): void
    {
        $salary = $this->employee->currentSalary;
        $otherDeductions = 0;

        // Regular deductions (union dues, etc.)
        foreach ($salary->salaryComponents as $component) {
            if ($component->salaryComponent->type === 'deduction' && $component->is_active) {
                if ($component->salaryComponent->calculation_type === 'percentage') {
                    $amount = ($this->calculations['basic_salary'] * $component->percentage) / 100;
                } else {
                    $amount = $component->amount;
                }
                $otherDeductions += $amount;
            }
        }

        // Loan deductions
        $loanDeductions = $this->employee->activeLoans->sum('monthly_deduction');
        $otherDeductions += $loanDeductions;

        $this->calculations['other_deductions'] = $otherDeductions;
        $this->calculations['total_deductions'] = $this->calculations['monthly_tax'] + $otherDeductions;
    }

    private function calculateNetSalary(): void
    {
        $this->calculations['net_salary'] = $this->calculations['gross_salary'] - $this->calculations['total_deductions'];
    }

    private function preparePayrollData(): array
    {
        return [
            'payroll_period_id' => $this->period->id,
            'employee_id' => $this->employee->id,
            'basic_salary' => $this->calculations['basic_salary'],
            'total_allowances' => $this->calculations['total_allowances'],
            'gross_salary' => $this->calculations['gross_salary'],
            'annual_gross' => $this->calculations['annual_gross'],
            'consolidated_relief' => $this->calculations['consolidated_relief'],
            'taxable_income' => $this->calculations['taxable_income'],
            'annual_tax' => $this->calculations['annual_tax'],
            'monthly_tax' => $this->calculations['monthly_tax'],
            'nsitf_contribution' => $this->calculations['nsitf_contribution'],
            'other_deductions' => $this->calculations['other_deductions'],
            'total_deductions' => $this->calculations['total_deductions'],
            'net_salary' => $this->calculations['net_salary'],
            'payment_status' => 'pending',
        ];
    }
}
