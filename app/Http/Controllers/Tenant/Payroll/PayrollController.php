<?php

namespace App\Http\Controllers\Tenant\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Department;
use App\Models\Employee;
use App\Models\SalaryComponent;
use App\Models\EmployeeSalary;
use App\Models\EmployeeSalaryComponent;
use App\Models\PayrollPeriod;
use App\Models\PayrollRun;
use App\Models\EmployeeLoan;
use App\Models\TaxBracket;
use App\Services\PayrollAccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PayrollController extends Controller
{
    /**
     * Display the payroll dashboard
     */
    public function index(Request $request, Tenant $tenant)
    {
        $totalEmployees = Employee::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->count();

        $currentMonth = now()->format('Y-m');
        $currentPayroll = PayrollPeriod::where('tenant_id', $tenant->id)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        $monthlyPayrollCost = $currentPayroll ? $currentPayroll->total_gross : 0;

        $pendingPayrolls = PayrollPeriod::where('tenant_id', $tenant->id)
            ->whereIn('status', ['draft', 'processing'])
            ->count();

        $recentPayrolls = PayrollPeriod::where('tenant_id', $tenant->id)
            ->with(['createdBy', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('tenant.payroll.index', compact(
            'tenant',
            'totalEmployees',
            'monthlyPayrollCost',
            'pendingPayrolls',
            'recentPayrolls'
        ));
    }

    /**
     * Employee Management
     */
    public function employees(Request $request, Tenant $tenant)
    {
        $query = Employee::where('tenant_id', $tenant->id)
            ->with(['department', 'currentSalary']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $employees = $query->orderBy('first_name')->paginate(20);
        $departments = Department::where('tenant_id', $tenant->id)->active()->get();

        return view('tenant.payroll.employees.index', compact(
            'tenant', 'employees', 'departments'
        ));
    }

    public function createEmployee(Tenant $tenant)
    {
        $departments = Department::where('tenant_id', $tenant->id)->active()->get();
        $salaryComponents = SalaryComponent::where('tenant_id', $tenant->id)->active()->get();

        return view('tenant.payroll.employees.create', compact(
            'tenant', 'departments', 'salaryComponents'
        ));
    }

    public function storeEmployee(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'nullable|string|max:20',
            'department_id' => 'required|exists:departments,id',
            'job_title' => 'required|string|max:255',
            'hire_date' => 'required|date',
            'employment_type' => 'required|in:permanent,contract,casual',
            'pay_frequency' => 'required|in:monthly,weekly,contract',
            'basic_salary' => 'required|numeric|min:0',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:20',
            'account_name' => 'nullable|string|max:255',
            'tin' => 'nullable|string|max:20',
            'pension_pin' => 'nullable|string|max:20',
            'components' => 'nullable|array',
            'components.*.id' => 'exists:salary_components,id',
            'components.*.amount' => 'nullable|numeric|min:0',
            'components.*.percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        return DB::transaction(function () use ($validated, $tenant) {
            // Create employee
            $employee = Employee::create(array_merge($validated, [
                'tenant_id' => $tenant->id,
                'status' => 'active'
            ]));

            // Create salary structure
            $salary = EmployeeSalary::create([
                'employee_id' => $employee->id,
                'basic_salary' => $validated['basic_salary'],
                'effective_date' => $validated['hire_date'],
                'is_current' => true,
                'created_by' => Auth::id(),
            ]);

            // Add salary components
            if (!empty($validated['components'])) {
                foreach ($validated['components'] as $component) {
                    if (!empty($component['amount']) || !empty($component['percentage'])) {
                        EmployeeSalaryComponent::create([
                            'employee_salary_id' => $salary->id,
                            'salary_component_id' => $component['id'],
                            'amount' => $component['amount'] ?? null,
                            'percentage' => $component['percentage'] ?? null,
                            'is_active' => true,
                        ]);
                    }
                }
            }

            return redirect()
                ->route('tenant.payroll.employees.show', [$tenant, $employee])
                ->with('success', 'Employee created successfully.');
        });
    }

    public function showEmployee(Tenant $tenant, Employee $employee)
    {
        $employee->load([
            'department',
            'currentSalary.salaryComponents.salaryComponent',
            'payrollRuns' => function($q) {
                $q->with('payrollPeriod')->orderBy('created_at', 'desc')->limit(10);
            },
            'loans'
        ]);

        return view('tenant.payroll.employees.show', compact('tenant', 'employee'));
    }

    /**
     * Departments Management
     */
    public function departments(Request $request, Tenant $tenant)
    {
        $departments = Department::where('tenant_id', $tenant->id)
            ->withCount('employees')
            ->orderBy('name')
            ->get();

        return view('tenant.payroll.departments.index', compact('tenant', 'departments'));
    }

    public function storeDepartment(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:departments,code',
            'description' => 'nullable|string',
        ]);

        Department::create(array_merge($validated, [
            'tenant_id' => $tenant->id,
            'is_active' => true
        ]));

        return redirect()
            ->route('tenant.payroll.departments.index', $tenant)
            ->with('success', 'Department created successfully.');
    }

    /**
     * Salary Components Management
     */
    public function components(Request $request, Tenant $tenant)
    {
        $components = SalaryComponent::where('tenant_id', $tenant->id)
            ->orderBy('type')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('tenant.payroll.components.index', compact('tenant', 'components'));
    }

    public function storeComponent(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:salary_components,code',
            'type' => 'required|in:allowance,deduction',
            'calculation_type' => 'required|in:fixed,percentage',
            'is_taxable' => 'boolean',
            'is_pensionable' => 'boolean',
            'description' => 'nullable|string',
        ]);

        SalaryComponent::create(array_merge($validated, [
            'tenant_id' => $tenant->id,
            'is_active' => true,
            'sort_order' => SalaryComponent::where('tenant_id', $tenant->id)->max('sort_order') + 1
        ]));

        return redirect()
            ->route('tenant.payroll.components.index', $tenant)
            ->with('success', 'Salary component created successfully.');
    }

    /**
     * Payroll Processing
     */
    public function processing(Request $request, Tenant $tenant)
    {
        $payrollPeriods = PayrollPeriod::where('tenant_id', $tenant->id)
            ->with(['createdBy', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('tenant.payroll.processing.index', compact('tenant', 'payrollPeriods'));
    }

    public function createPayroll(Tenant $tenant)
    {
        // Suggest next month's payroll period
        $nextMonth = now()->addMonth();
        $startDate = $nextMonth->startOfMonth();
        $endDate = $nextMonth->endOfMonth();
        $payDate = $endDate->copy()->addDays(2); // Pay 2 days after month end

        return view('tenant.payroll.processing.create', compact(
            'tenant', 'startDate', 'endDate', 'payDate'
        ));
    }

    public function storePayroll(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'pay_date' => 'required|date|after_or_equal:end_date',
            'type' => 'required|in:monthly,weekly,contract',
        ]);

        $payrollPeriod = PayrollPeriod::create(array_merge($validated, [
            'tenant_id' => $tenant->id,
            'status' => 'draft',
            'created_by' => Auth::id(),
        ]));

        return redirect()
            ->route('tenant.payroll.processing.show', [$tenant, $payrollPeriod])
            ->with('success', 'Payroll period created successfully.');
    }

    public function showPayroll(Tenant $tenant, PayrollPeriod $period)
    {
        $period->load([
            'payrollRuns.employee.department',
            'createdBy',
            'approvedBy'
        ]);

        return view('tenant.payroll.processing.show', compact('tenant', 'period'));
    }

    public function generatePayroll(Request $request, Tenant $tenant, PayrollPeriod $period)
    {
        if (!$period->canBeProcessed()) {
            return redirect()->back()->with('error', 'Payroll cannot be processed in current status.');
        }

        try {
            DB::transaction(function () use ($period) {
                $period->generatePayrollForAllEmployees();
            });

            return redirect()
                ->route('tenant.payroll.processing.show', [$tenant, $period])
                ->with('success', 'Payroll generated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error generating payroll: ' . $e->getMessage());
        }
    }

    public function approvePayroll(Request $request, Tenant $tenant, PayrollPeriod $period)
    {
        if (!$period->canBeApproved()) {
            return redirect()->back()->with('error', 'Payroll cannot be approved in current status.');
        }

        try {
            DB::transaction(function () use ($period) {
                // Create accounting entries
                $period->createAccountingEntries();

                // Update status
                $period->update([
                    'status' => 'approved',
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                ]);
            });

            return redirect()
                ->route('tenant.payroll.processing.show', [$tenant, $period])
                ->with('success', 'Payroll approved and accounting entries created.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error approving payroll: ' . $e->getMessage());
        }
    }

    /**
     * Export bank payment file
     */
    public function exportBankFile(Tenant $tenant, PayrollPeriod $period)
    {
        if ($period->status !== 'approved') {
            return redirect()->back()->with('error', 'Payroll must be approved before exporting bank file.');
        }

        $payrollRuns = $period->payrollRuns()->with('employee')->get();

        $filename = "payroll_bank_file_{$period->name}_{now()->format('Y_m_d')}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($payrollRuns) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Employee Number', 'Employee Name', 'Account Number',
                'Bank Name', 'Amount', 'Narration'
            ]);

            foreach ($payrollRuns as $run) {
                fputcsv($file, [
                    $run->employee->employee_number,
                    $run->employee->full_name,
                    $run->employee->account_number,
                    $run->employee->bank_name,
                    number_format($run->net_salary, 2, '.', ''),
                    "Salary payment for {$run->payrollPeriod->name}"
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Tax report
     */
    public function taxReport(Request $request, Tenant $tenant)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month');

        $query = PayrollRun::whereHas('payrollPeriod', function($q) use ($tenant, $year, $month) {
            $q->where('tenant_id', $tenant->id)
              ->whereYear('pay_date', $year);

            if ($month) {
                $q->whereMonth('pay_date', $month);
            }
        })->with(['employee', 'payrollPeriod']);

        $taxData = $query->get()->groupBy('employee_id')->map(function($runs) {
            $employee = $runs->first()->employee;
            return [
                'employee' => $employee,
                'total_gross' => $runs->sum('gross_salary'),
                'total_tax' => $runs->sum('monthly_tax'),
                'runs' => $runs
            ];
        });

        return view('tenant.payroll.reports.tax-report', compact(
            'tenant', 'taxData', 'year', 'month'
        ));
    }

    /**
     * Export employees data to CSV
     */
    public function exportEmployees(Request $request, Tenant $tenant)
    {
        $employees = Employee::where('tenant_id', $tenant->id)
            ->with(['department', 'currentSalary.salaryComponents.component'])
            ->orderBy('first_name')
            ->get();

        $filename = 'employees_' . $tenant->slug . '_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($employees) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'Employee ID',
                'First Name',
                'Last Name',
                'Email',
                'Phone',
                'Department',
                'Position',
                'Employment Date',
                'Status',
                'Basic Salary',
                'Total Salary',
                'Bank Account',
                'Address'
            ]);

            // Add employee data
            foreach ($employees as $employee) {
                $basicSalary = $employee->currentSalary ?
                    $employee->currentSalary->salaryComponents
                        ->where('component.type', 'earning')
                        ->where('component.name', 'Basic Salary')
                        ->first()?->amount ?? 0 : 0;

                $totalSalary = $employee->currentSalary ?
                    $employee->currentSalary->salaryComponents
                        ->where('component.type', 'earning')
                        ->sum('amount') : 0;

                fputcsv($file, [
                    $employee->employee_id,
                    $employee->first_name,
                    $employee->last_name,
                    $employee->email,
                    $employee->phone,
                    $employee->department?->name ?? '',
                    $employee->position,
                    $employee->employment_date?->format('Y-m-d') ?? '',
                    ucfirst($employee->status),
                    number_format($basicSalary, 2),
                    number_format($totalSalary, 2),
                    $employee->bank_account_number ?? '',
                    $employee->address ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate payslip for an employee
     */
    public function generatePayslip(Request $request, Tenant $tenant, Employee $employee)
    {
        // Validate that the employee belongs to this tenant
        if ($employee->tenant_id !== $tenant->id) {
            abort(404);
        }

        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));

        // Get payroll runs for this employee for the specified period
        $payrollRuns = PayrollRun::whereHas('payrollPeriod', function($q) use ($tenant, $year, $month) {
            $q->where('tenant_id', $tenant->id)
              ->whereYear('pay_date', $year)
              ->whereMonth('pay_date', $month);
        })->where('employee_id', $employee->id)
          ->with(['payrollPeriod', 'employee.currentSalary.salaryComponents.component'])
          ->get();

        if ($payrollRuns->isEmpty()) {
            return redirect()->back()->with('error', 'No payslip data found for the selected period.');
        }

        $payrollRun = $payrollRuns->first();

        return view('tenant.payroll.employees.payslip', compact(
            'tenant', 'employee', 'payrollRun', 'year', 'month'
        ));
    }
}
