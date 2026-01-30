<?php

namespace App\Http\Controllers\Api\Tenant\Reports;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Models\PayrollRun;
use App\Models\Tenant;
use Illuminate\Http\Request;

class PayrollReportsController extends Controller
{
    public function summary(Request $request, Tenant $tenant)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month');
        $status = $request->get('status');

        $query = PayrollPeriod::where('tenant_id', $tenant->id)
            ->with(['payrollRuns.employee.department', 'createdBy', 'approvedBy']);

        $query->whereYear('pay_date', $year);

        if ($month) {
            $query->whereMonth('pay_date', $month);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $payrollPeriods = $query->orderBy('pay_date', 'desc')->get();

        $totalPeriods = $payrollPeriods->count();
        $totalEmployees = 0;
        $totalGross = 0;
        $totalDeductions = 0;
        $totalNet = 0;
        $totalTax = 0;

        foreach ($payrollPeriods as $period) {
            $runs = $period->payrollRuns;
            $totalEmployees += $runs->count();
            $totalGross += $runs->sum('gross_salary');
            $totalDeductions += $runs->sum('total_deductions');
            $totalNet += $runs->sum('net_salary');
            $totalTax += $runs->sum('monthly_tax');
        }

        $monthlyData = $payrollPeriods->groupBy(function ($period) {
            return $period->pay_date->format('Y-m');
        })->map(function ($periods, $monthKey) {
            $runs = $periods->flatMap->payrollRuns;
            return [
                'month' => $monthKey,
                'periods' => $periods->count(),
                'employees' => $runs->count(),
                'gross' => (float) $runs->sum('gross_salary'),
                'deductions' => (float) $runs->sum('total_deductions'),
                'net' => (float) $runs->sum('net_salary'),
                'tax' => (float) $runs->sum('monthly_tax'),
            ];
        })->sortByDesc('month')->values();

        $departmentData = collect();
        foreach ($payrollPeriods as $period) {
            foreach ($period->payrollRuns as $run) {
                $deptName = $run->employee->department->name ?? 'No Department';
                if (!$departmentData->has($deptName)) {
                    $departmentData->put($deptName, [
                        'employees' => 0,
                        'gross' => 0,
                        'deductions' => 0,
                        'net' => 0,
                    ]);
                }
                $dept = $departmentData->get($deptName);
                $dept['employees']++;
                $dept['gross'] += $run->gross_salary;
                $dept['deductions'] += $run->total_deductions;
                $dept['net'] += $run->net_salary;
                $departmentData->put($deptName, $dept);
            }
        }

        $summary = [
            'total_periods' => $totalPeriods,
            'total_employees' => $totalEmployees,
            'total_gross' => (float) $totalGross,
            'total_deductions' => (float) $totalDeductions,
            'total_net' => (float) $totalNet,
            'total_tax' => (float) $totalTax,
            'average_per_employee' => $totalEmployees > 0 ? (float) ($totalNet / $totalEmployees) : 0,
        ];

        $periods = $payrollPeriods->map(function ($period) {
            return [
                'id' => $period->id,
                'name' => $period->name,
                'start_date' => $period->start_date?->toDateString(),
                'end_date' => $period->end_date?->toDateString(),
                'pay_date' => $period->pay_date?->toDateString(),
                'status' => $period->status,
                'employees' => $period->payrollRuns->count(),
                'gross' => (float) ($period->payrollRuns->sum('gross_salary')),
                'net' => (float) ($period->payrollRuns->sum('net_salary')),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'Payroll summary retrieved successfully',
            'data' => [
                'filters' => [
                    'year' => (int) $year,
                    'month' => $month,
                    'status' => $status,
                ],
                'summary' => $summary,
                'monthly_breakdown' => $monthlyData,
                'department_breakdown' => $departmentData,
                'periods' => $periods,
            ],
        ]);
    }

    public function taxReport(Request $request, Tenant $tenant)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month');

        $query = PayrollRun::whereHas('payrollPeriod', function ($q) use ($tenant, $year, $month) {
            $q->where('tenant_id', $tenant->id)
                ->whereYear('pay_date', $year);

            if ($month) {
                $q->whereMonth('pay_date', $month);
            }
        })->with(['employee.department', 'payrollPeriod']);

        $taxData = $query->get()->groupBy('employee_id')->map(function ($runs) {
            $employee = $runs->first()->employee;
            $totalGross = $runs->sum('gross_salary');
            $totalTax = $runs->sum('monthly_tax');
            return [
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->full_name ?? trim($employee->first_name . ' ' . $employee->last_name),
                    'employee_number' => $employee->employee_number,
                    'department' => $employee->department?->name,
                    'email' => $employee->email,
                ],
                'total_gross' => (float) $totalGross,
                'total_tax' => (float) $totalTax,
                'tax_rate' => $totalGross > 0 ? (float) (($totalTax / $totalGross) * 100) : 0,
                'run_count' => $runs->count(),
            ];
        })->values();

        $totalGross = $taxData->sum('total_gross');
        $totalTax = $taxData->sum('total_tax');

        return response()->json([
            'success' => true,
            'message' => 'Tax report retrieved successfully',
            'data' => [
                'filters' => [
                    'year' => (int) $year,
                    'month' => $month,
                ],
                'summary' => [
                    'total_employees' => $taxData->count(),
                    'total_gross' => (float) $totalGross,
                    'total_tax' => (float) $totalTax,
                    'average_tax_rate' => $totalGross > 0 ? (float) (($totalTax / $totalGross) * 100) : 0,
                ],
                'records' => $taxData,
            ],
        ]);
    }

    public function taxSummary(Request $request, Tenant $tenant)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month');

        $query = PayrollRun::whereHas('payrollPeriod', function ($q) use ($tenant, $year, $month) {
            $q->where('tenant_id', $tenant->id)
                ->whereYear('pay_date', $year);

            if ($month) {
                $q->whereMonth('pay_date', $month);
            }
        })->with(['employee.department', 'payrollPeriod']);

        $payrollRuns = $query->get();

        $totalTax = $payrollRuns->sum('monthly_tax');
        $totalGross = $payrollRuns->sum('gross_salary');
        $totalEmployees = $payrollRuns->groupBy('employee_id')->count();
        $averageTaxRate = $totalGross > 0 ? ($totalTax / $totalGross) * 100 : 0;

        $monthlyData = $payrollRuns->groupBy(function ($run) {
            return $run->payrollPeriod->pay_date->format('Y-m');
        })->map(function ($runs, $monthKey) {
            return [
                'month' => $monthKey,
                'employees' => $runs->groupBy('employee_id')->count(),
                'gross' => (float) $runs->sum('gross_salary'),
                'tax' => (float) $runs->sum('monthly_tax'),
                'net' => (float) $runs->sum('net_salary'),
            ];
        })->sortByDesc('month')->values();

        $departmentData = collect();
        foreach ($payrollRuns as $run) {
            $deptName = $run->employee->department->name ?? 'No Department';
            if (!$departmentData->has($deptName)) {
                $departmentData->put($deptName, [
                    'employees' => collect(),
                    'gross' => 0,
                    'tax' => 0,
                ]);
            }
            $dept = $departmentData->get($deptName);
            $dept['employees']->push($run->employee_id);
            $dept['gross'] += $run->gross_salary;
            $dept['tax'] += $run->monthly_tax;
            $departmentData->put($deptName, $dept);
        }

        $departmentData = $departmentData->map(function ($data) {
            $data['employees'] = $data['employees']->unique()->count();
            $data['gross'] = (float) $data['gross'];
            $data['tax'] = (float) $data['tax'];
            return $data;
        });

        return response()->json([
            'success' => true,
            'message' => 'Tax summary retrieved successfully',
            'data' => [
                'filters' => [
                    'year' => (int) $year,
                    'month' => $month,
                ],
                'summary' => [
                    'total_tax' => (float) $totalTax,
                    'total_gross' => (float) $totalGross,
                    'total_employees' => (int) $totalEmployees,
                    'average_tax_rate' => (float) $averageTaxRate,
                ],
                'monthly_breakdown' => $monthlyData,
                'department_breakdown' => $departmentData,
            ],
        ]);
    }

    public function employeeSummary(Request $request, Tenant $tenant)
    {
        $year = $request->get('year', now()->year);
        $departmentId = $request->get('department_id');

        $query = Employee::where('tenant_id', $tenant->id)
            ->with(['department', 'currentSalary']);

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        $employees = $query->orderBy('first_name')->get();

        $departments = Department::where('tenant_id', $tenant->id)
            ->orderBy('name')
            ->get();

        $employeeData = $employees->map(function ($employee) use ($year) {
            $runs = PayrollRun::where('employee_id', $employee->id)
                ->whereHas('payrollPeriod', function ($q) use ($year) {
                    $q->whereYear('pay_date', $year);
                })
                ->get();

            return [
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->full_name ?? trim($employee->first_name . ' ' . $employee->last_name),
                    'employee_number' => $employee->employee_number,
                    'department' => $employee->department?->name,
                ],
                'payroll_count' => $runs->count(),
                'total_gross' => (float) $runs->sum('gross_salary'),
                'total_deductions' => (float) $runs->sum('total_deductions'),
                'total_tax' => (float) $runs->sum('monthly_tax'),
                'total_net' => (float) $runs->sum('net_salary'),
                'average_gross' => $runs->count() > 0 ? (float) $runs->avg('gross_salary') : 0,
                'average_net' => $runs->count() > 0 ? (float) $runs->avg('net_salary') : 0,
            ];
        });

        $summary = [
            'total_employees' => $employeeData->count(),
            'total_gross' => (float) $employeeData->sum('total_gross'),
            'total_deductions' => (float) $employeeData->sum('total_deductions'),
            'total_tax' => (float) $employeeData->sum('total_tax'),
            'total_net' => (float) $employeeData->sum('total_net'),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Employee summary retrieved successfully',
            'data' => [
                'filters' => [
                    'year' => (int) $year,
                    'department_id' => $departmentId,
                ],
                'summary' => $summary,
                'departments' => $departments,
                'records' => $employeeData,
            ],
        ]);
    }

    public function bankSchedule(Request $request, Tenant $tenant)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month');
        $status = $request->get('status', 'approved');

        $query = PayrollPeriod::where('tenant_id', $tenant->id)
            ->with(['payrollRuns.employee.department', 'createdBy', 'approvedBy']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($year && $month) {
            $query->whereYear('pay_date', $year)
                ->whereMonth('pay_date', $month);
        } elseif ($year && !$month) {
            $query->whereYear('pay_date', $year);
        }

        $payrollPeriods = $query->orderBy('pay_date', 'desc')->get();

        $totalEmployees = 0;
        $totalGross = 0;
        $totalDeductions = 0;
        $totalNet = 0;

        foreach ($payrollPeriods as $period) {
            $totalEmployees += $period->payrollRuns->count();
            $totalGross += $period->total_gross ?? 0;
            $totalDeductions += $period->total_deductions ?? 0;
            $totalNet += $period->total_net ?? 0;
        }

        $summary = [
            'total_periods' => $payrollPeriods->count(),
            'total_employees' => $totalEmployees,
            'total_gross' => (float) $totalGross,
            'total_deductions' => (float) $totalDeductions,
            'total_net' => (float) $totalNet,
        ];

        $periods = $payrollPeriods->map(function ($period) {
            return [
                'id' => $period->id,
                'name' => $period->name,
                'start_date' => $period->start_date?->toDateString(),
                'end_date' => $period->end_date?->toDateString(),
                'pay_date' => $period->pay_date?->toDateString(),
                'employees' => $period->payrollRuns->count(),
                'gross' => (float) ($period->total_gross ?? 0),
                'deductions' => (float) ($period->total_deductions ?? 0),
                'net' => (float) ($period->total_net ?? 0),
                'status' => $period->status,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'Bank schedule retrieved successfully',
            'data' => [
                'filters' => [
                    'year' => (int) $year,
                    'month' => $month,
                    'status' => $status,
                ],
                'summary' => $summary,
                'periods' => $periods,
            ],
        ]);
    }

    public function detailedReport(Request $request, Tenant $tenant)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month');
        $departmentId = $request->get('department_id');

        $query = PayrollRun::whereHas('payrollPeriod', function ($q) use ($tenant, $year, $month) {
            $q->where('tenant_id', $tenant->id)
                ->whereYear('pay_date', $year);

            if ($month) {
                $q->whereMonth('pay_date', $month);
            }
        })->with(['employee.department', 'payrollPeriod']);

        if ($departmentId) {
            $query->whereHas('employee', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        $payrollRuns = $query->orderBy('created_at', 'desc')->get();

        $departments = Department::where('tenant_id', $tenant->id)
            ->orderBy('name')
            ->get();

        $totals = [
            'gross' => (float) $payrollRuns->sum('gross_salary'),
            'deductions' => (float) $payrollRuns->sum('total_deductions'),
            'tax' => (float) $payrollRuns->sum('monthly_tax'),
            'net' => (float) $payrollRuns->sum('net_salary'),
        ];

        $records = $payrollRuns->map(function ($run) {
            return [
                'id' => $run->id,
                'period' => [
                    'id' => $run->payrollPeriod?->id,
                    'name' => $run->payrollPeriod?->name,
                    'pay_date' => $run->payrollPeriod?->pay_date?->toDateString(),
                ],
                'employee' => [
                    'id' => $run->employee?->id,
                    'name' => $run->employee?->full_name ?? trim($run->employee?->first_name . ' ' . $run->employee?->last_name),
                    'employee_number' => $run->employee?->employee_number,
                    'department' => $run->employee?->department?->name,
                ],
                'gross' => (float) $run->gross_salary,
                'deductions' => (float) $run->total_deductions,
                'tax' => (float) $run->monthly_tax,
                'net' => (float) $run->net_salary,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Detailed payroll report retrieved successfully',
            'data' => [
                'filters' => [
                    'year' => (int) $year,
                    'month' => $month,
                    'department_id' => $departmentId,
                ],
                'totals' => $totals,
                'departments' => $departments,
                'records' => $records,
            ],
        ]);
    }
}
