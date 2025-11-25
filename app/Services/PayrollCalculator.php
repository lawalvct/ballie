<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Models\TaxBracket;
use App\Models\AttendanceRecord;
use Carbon\Carbon;

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

        // Step 2: Calculate attendance-based adjustments (overtime & absent deductions)
        $this->calculateAttendanceAdjustments();

        // Step 3: Calculate PAYE tax (only if enabled for this period)
        if ($this->period->apply_paye_tax) {
            $this->calculatePAYE();
        } else {
            // Set tax values to zero if not applied
            $this->calculations['annual_gross'] = $this->calculations['gross_salary'] * 12;
            $this->calculations['consolidated_relief'] = 0;
            $this->calculations['taxable_income'] = 0;
            $this->calculations['annual_tax'] = 0;
            $this->calculations['monthly_tax'] = 0;
        }

        // Step 4: Calculate NSITF (only if enabled for this period)
        if ($this->period->apply_nsitf) {
            $this->calculateNSITF();
        } else {
            $this->calculations['nsitf_contribution'] = 0;
        }

        // Step 4.5: Calculate Pension (8% employee, 10% employer)
        $this->calculatePension();

        // Step 5: Calculate other deductions
        $this->calculateOtherDeductions();

        // Step 6: Calculate net salary
        $this->calculateNetSalary();

        return $this->preparePayrollData();
    }

    private function calculateGrossSalary(): void
    {
        $salary = $this->employee->currentSalary;

        // Check if employee has a current salary
        if (!$salary) {
            throw new \Exception("Employee {$this->employee->full_name} ({$this->employee->employee_number}) does not have a current salary set. Please set up their salary before generating payroll.");
        }

        $this->calculations['basic_salary'] = $salary->basic_salary;
        $this->calculations['earnings'] = []; // Store individual earnings for details

        $allowances = 0;
        foreach ($salary->salaryComponents as $component) {
            // Use 'earning' instead of 'allowance' as per database schema
            if ($component->salaryComponent->type === 'earning' && $component->is_active) {
                if ($component->salaryComponent->calculation_type === 'percentage') {
                    $amount = ($this->calculations['basic_salary'] * $component->percentage) / 100;
                } elseif ($component->salaryComponent->calculation_type === 'fixed') {
                    $amount = $component->amount;
                } else {
                    // For 'variable' or 'computed', use the amount as-is
                    $amount = $component->amount ?? 0;
                }

                $allowances += $amount;

                // Store for PayrollRunDetail
                $this->calculations['earnings'][] = [
                    'salary_component_id' => $component->salary_component_id,
                    'component_name' => $component->salaryComponent->name,
                    'component_type' => 'earning',
                    'amount' => $amount,
                    'is_taxable' => $component->salaryComponent->is_taxable,
                ];
            }
        }

        $this->calculations['total_allowances'] = $allowances;
        $this->calculations['gross_salary'] = $this->calculations['basic_salary'] + $allowances;
    }

    private function calculateAttendanceAdjustments(): void
    {
        // Check if employee is exempt from attendance-based deductions
        if ($this->employee->attendance_deduction_exempt) {
            // Still track attendance summary but don't apply deductions/overtime
            $startDate = Carbon::parse($this->period->start_date);
            $endDate = Carbon::parse($this->period->end_date);

            $attendanceRecords = AttendanceRecord::where('employee_id', $this->employee->id)
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->get();

            // Calculate working days
            $workingDays = 0;
            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                if (!$currentDate->isWeekend()) {
                    $workingDays++;
                }
                $currentDate->addDay();
            }

            // Store attendance summary only (no financial impact)
            $this->calculations['attendance_summary'] = [
                'working_days' => $workingDays,
                'total_days' => $attendanceRecords->count(),
                'present_days' => $attendanceRecords->whereIn('status', ['present', 'late'])->count(),
                'absent_days' => $attendanceRecords->whereIn('status', ['absent'])->count(),
                'late_days' => $attendanceRecords->where('status', 'late')->count(),
                'half_days' => $attendanceRecords->where('status', 'half_day')->count(),
                'overtime_hours' => round($attendanceRecords->sum('overtime_minutes') / 60, 2),
                'exempt_from_deductions' => true,
                'exemption_reason' => $this->employee->attendance_exemption_reason,
            ];

            return; // Exit early - no financial adjustments
        }

        // Get attendance records for this payroll period
        $startDate = Carbon::parse($this->period->start_date);
        $endDate = Carbon::parse($this->period->end_date);

        $attendanceRecords = AttendanceRecord::where('employee_id', $this->employee->id)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->get();

        // Calculate working days in the period (excluding weekends)
        $workingDays = 0;
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            if (!$currentDate->isWeekend()) {
                $workingDays++;
            }
            $currentDate->addDay();
        }

        // Count absent days
        $absentDays = $attendanceRecords->whereIn('status', ['absent'])->count();

        // Calculate daily salary rate
        $dailySalaryRate = $workingDays > 0 ? $this->calculations['basic_salary'] / $workingDays : 0;

        // Calculate absent deduction
        $absentDeduction = $absentDays * $dailySalaryRate;

        // Store absent deduction
        if ($absentDeduction > 0) {
            $this->calculations['deductions'][] = [
                'salary_component_id' => null,
                'component_name' => 'Absent Days Deduction',
                'component_type' => 'deduction',
                'amount' => round($absentDeduction, 2),
                'is_taxable' => false,
                'metadata' => [
                    'absent_days' => $absentDays,
                    'daily_rate' => round($dailySalaryRate, 2),
                ],
            ];
        }

        // Calculate overtime pay
        $totalOvertimeMinutes = $attendanceRecords->sum('overtime_minutes');
        $totalOvertimeHours = $totalOvertimeMinutes / 60;

        if ($totalOvertimeHours > 0) {
            // Calculate hourly rate (assuming 8 hours per day)
            $hourlyRate = $dailySalaryRate / 8;

            // Overtime multiplier (1.5x for regular overtime)
            $overtimeMultiplier = 1.5;

            $overtimePay = $totalOvertimeHours * $hourlyRate * $overtimeMultiplier;

            // Store overtime as earning
            $this->calculations['earnings'][] = [
                'salary_component_id' => null,
                'component_name' => 'Overtime Pay',
                'component_type' => 'earning',
                'amount' => round($overtimePay, 2),
                'is_taxable' => true,
                'metadata' => [
                    'overtime_hours' => round($totalOvertimeHours, 2),
                    'hourly_rate' => round($hourlyRate, 2),
                    'multiplier' => $overtimeMultiplier,
                ],
            ];

            // Add overtime to gross salary
            $this->calculations['gross_salary'] += $overtimePay;
        }

        // Store attendance summary
        $this->calculations['attendance_summary'] = [
            'working_days' => $workingDays,
            'total_days' => $attendanceRecords->count(),
            'present_days' => $attendanceRecords->whereIn('status', ['present', 'late'])->count(),
            'absent_days' => $absentDays,
            'late_days' => $attendanceRecords->where('status', 'late')->count(),
            'half_days' => $attendanceRecords->where('status', 'half_day')->count(),
            'leave_days' => $attendanceRecords->where('status', 'on_leave')->count(),
            'total_hours' => round($attendanceRecords->sum('work_hours_minutes') / 60, 2),
            'overtime_hours' => round($totalOvertimeHours, 2),
            'absent_deduction' => round($absentDeduction, 2),
            'overtime_pay' => round($overtimePay ?? 0, 2),
        ];
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

    private function calculatePension(): void
    {
        // Skip pension if employee is exempt
        if ($this->employee->pension_exempt) {
            $this->calculations['pension_employee'] = 0;
            $this->calculations['pension_employer'] = 0;
            $this->calculations['pension_total'] = 0;
            return;
        }

        $basicSalary = $this->calculations['basic_salary'];
        
        // Employee contribution: 8% of basic salary
        $employeeContribution = $basicSalary * 0.08;
        
        // Employer contribution: 10% of basic salary
        $employerContribution = $basicSalary * 0.10;
        
        $this->calculations['pension_employee'] = $employeeContribution;
        $this->calculations['pension_employer'] = $employerContribution;
        $this->calculations['pension_total'] = $employeeContribution + $employerContribution;
        
        // Add employee pension as deduction
        if (!isset($this->calculations['deductions'])) {
            $this->calculations['deductions'] = [];
        }
        
        $this->calculations['deductions'][] = [
            'salary_component_id' => null,
            'component_name' => 'Pension (Employee 8%)',
            'component_type' => 'deduction',
            'amount' => $employeeContribution,
            'is_taxable' => false,
        ];
    }

    private function calculateOtherDeductions(): void
    {
        $salary = $this->employee->currentSalary;
        $otherDeductions = 0;

        // Initialize deductions array if not already set (from attendance)
        if (!isset($this->calculations['deductions'])) {
            $this->calculations['deductions'] = [];
        }

        // Regular deductions (union dues, pension, etc.)
        foreach ($salary->salaryComponents as $component) {
            if ($component->salaryComponent->type === 'deduction' && $component->is_active) {
                if ($component->salaryComponent->calculation_type === 'percentage') {
                    $amount = ($this->calculations['basic_salary'] * $component->percentage) / 100;
                } elseif ($component->salaryComponent->calculation_type === 'fixed') {
                    $amount = $component->amount;
                } else {
                    // For 'variable' or 'computed', use the amount as-is
                    $amount = $component->amount ?? 0;
                }

                $otherDeductions += $amount;

                // Store for PayrollRunDetail
                $this->calculations['deductions'][] = [
                    'salary_component_id' => $component->salary_component_id,
                    'component_name' => $component->salaryComponent->name,
                    'component_type' => 'deduction',
                    'amount' => $amount,
                    'is_taxable' => false, // Deductions are not taxable
                ];
            }
        }

        // Loan deductions
        $loanDeductions = $this->employee->activeLoans->sum('monthly_deduction');
        $otherDeductions += $loanDeductions;

        // Add absent deduction if exists
        $absentDeduction = collect($this->calculations['deductions'])
            ->where('component_name', 'Absent Days Deduction')
            ->sum('amount');
        $otherDeductions += $absentDeduction;

        $this->calculations['other_deductions'] = $otherDeductions;

        // Only add tax to total deductions if it's being applied
        $taxAmount = $this->period->apply_paye_tax ? $this->calculations['monthly_tax'] : 0;
        $this->calculations['total_deductions'] = $taxAmount + $otherDeductions;
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
            'pension_employee' => $this->calculations['pension_employee'] ?? 0,
            'pension_employer' => $this->calculations['pension_employer'] ?? 0,
            'other_deductions' => $this->calculations['other_deductions'],
            'total_deductions' => $this->calculations['total_deductions'],
            'net_salary' => $this->calculations['net_salary'],
            'payment_status' => 'pending',
        ];
    }

    /**
     * Get detailed breakdown of earnings and deductions
     */
    public function getComponentDetails(): array
    {
        return [
            'earnings' => $this->calculations['earnings'] ?? [],
            'deductions' => $this->calculations['deductions'] ?? [],
        ];
    }
}
