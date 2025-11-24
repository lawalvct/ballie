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

/**
 * PayrollController
 *
 * Handles all payroll operations including employee management, salary components,
 * payroll processing, and payslip generation.
 *
 * SALARY COMPONENT SYSTEM:
 * -----------------------
 * The payroll system uses a flexible salary component architecture:
 *
 * 1. Component Types:
 *    - 'earning': Income components (e.g., Housing Allowance, Transport Allowance, Bonus)
 *    - 'deduction': Deduction components (e.g., Pension, Union Dues, Loan Repayment)
 *    - 'employer_contribution': Employer-paid items (e.g., NSITF, Pension Employer Share)
 *
 * 2. Calculation Types:
 *    - 'fixed': Fixed amount per period (e.g., ₦50,000 housing allowance)
 *    - 'percentage': Percentage of basic salary (e.g., 10% transport = 10% of basic)
 *    - 'variable': Amount can change each period (manually entered)
 *    - 'computed': Calculated by system logic (e.g., overtime, bonuses)
 *
 * 3. Component Properties:
 *    - is_taxable: Whether the component affects PAYE tax calculation
 *    - is_pensionable: Whether the component is included in pension calculation
 *    - is_active: Whether the component is currently in use
 *
 * 4. Payroll Calculation Flow:
 *    a) Basic Salary + Earnings (earning components) = Gross Salary
 *    b) Calculate PAYE Tax on taxable income
 *    c) Subtract Deductions (deduction components)
 *    d) Gross Salary - Total Deductions = Net Salary
 *
 * 5. Database Structure:
 *    - salary_components: Master list of all component types
 *    - employee_salary_components: Employee-specific component assignments
 *    - payroll_run_details: Snapshot of component amounts per payroll period
 *
 * 6. Usage Example:
 *    - Create component: "Housing Allowance", type='earning', calculation='percentage', 20%
 *    - Assign to employee: Employee gets 20% of their basic salary as housing
 *    - On payroll run: If basic=₦100,000, housing=₦20,000, gross=₦120,000
 */
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
            ->get()
            ->map(function($period) {
                // Calculate employee count and totals for each payroll period
                $payrollRuns = $period->payrollRuns()->get();

                return (object)[
                    'id' => $period->id,
                    'name' => $period->name,
                    'type' => $period->type,
                    'status' => $period->status,
                    'start_date' => $period->start_date,
                    'end_date' => $period->end_date,
                    'pay_date' => $period->pay_date,
                    'total_gross' => $payrollRuns->sum('gross_salary'),
                    'total_deductions' => $payrollRuns->sum(function($run) {
                        return $run->monthly_tax + $run->other_deductions;
                    }),
                    'total_net' => $payrollRuns->sum('net_salary'),
                    'employee_count' => $payrollRuns->count(),
                    'created_at' => $period->created_at,
                    'created_by' => $period->createdBy,
                    'approved_by' => $period->approvedBy,
                    'approved_at' => $period->approved_at,
                ];
            });

        // Get department summary
        $departmentSummary = Department::where('tenant_id', $tenant->id)
            ->with(['employees' => function($query) {
                $query->where('status', 'active');
            }, 'employees.currentSalary'])
            ->get()
            ->map(function($department) {
                $employees = $department->employees;
                $monthlyCost = $employees->sum(function($employee) {
                    return $employee->currentSalary ? $employee->currentSalary->basic_salary : 0;
                });

                return (object)[
                    'name' => $department->name,
                    'employee_count' => $employees->count(),
                    'monthly_cost' => $monthlyCost,
                ];
            });

        // Get monthly payroll data for the last 12 months
        $monthlyPayrollData = [];
        $startDate = now()->subMonths(11)->startOfMonth();

        for ($i = 0; $i < 12; $i++) {
            $month = $startDate->copy()->addMonths($i);

            // Query payroll periods for this month using start_date, end_date, or pay_date
            $periodData = PayrollPeriod::where('tenant_id', $tenant->id)
                ->where(function($query) use ($month) {
                    $query->where(function($q) use ($month) {
                        // Check if start_date is in this month
                        $q->whereYear('start_date', $month->year)
                          ->whereMonth('start_date', $month->month);
                    })
                    ->orWhere(function($q) use ($month) {
                        // Or if end_date is in this month
                        $q->whereYear('end_date', $month->year)
                          ->whereMonth('end_date', $month->month);
                    })
                    ->orWhere(function($q) use ($month) {
                        // Or if pay_date is in this month
                        $q->whereYear('pay_date', $month->year)
                          ->whereMonth('pay_date', $month->month);
                    });
                })
                ->get();

            $grossTotal = 0;
            $netTotal = 0;
            $employeeCount = 0;

            foreach ($periodData as $period) {
                $runs = $period->payrollRuns()->get();
                $grossTotal += $runs->sum('gross_salary');
                $netTotal += $runs->sum('net_salary');
                $employeeCount += $runs->count();
            }

            $monthlyPayrollData[] = [
                'month' => $month->format('M Y'),
                'month_short' => $month->format('M'),
                'gross' => $grossTotal,
                'net' => $netTotal,
                'employees' => $employeeCount,
            ];
        }        return view('tenant.payroll.index', compact(
            'tenant',
            'totalEmployees',
            'monthlyPayrollCost',
            'pendingPayrolls',
            'recentPayrolls',
            'departmentSummary',
            'monthlyPayrollData'
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
        $positions = \App\Models\Position::where('tenant_id', $tenant->id)->active()->orderBy('name')->get();
        $salaryComponents = SalaryComponent::where('tenant_id', $tenant->id)->active()->get();

        return view('tenant.payroll.employees.create', compact(
            'tenant', 'departments', 'positions', 'salaryComponents'
        ));
    }

    public function storeEmployee(Request $request, Tenant $tenant)
    {
        return DB::transaction(function () use ($request, $tenant) {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:employees,email,NULL,id,tenant_id,' . $tenant->id,
                'phone' => 'nullable|string|max:20',
                'department_id' => 'required|exists:departments,id',
                'position_id' => 'nullable|exists:positions,id',
                'job_title' => 'required|string|max:255',
                'hire_date' => 'required|date',

                 'employment_type' => 'required|in:full_time,contract,casual,intern,part_time',
                'pay_frequency' => 'required|in:monthly,weekly,contract',
                'attendance_deduction_exempt' => 'nullable|boolean',
                'attendance_exemption_reason' => 'nullable|string|max:500',
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
                'avatar' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            ]);

            // Handle avatar upload
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');
                $filename = time() . '_' . uniqid() . '.' . $avatar->getClientOriginalExtension();

                // Create employees directory if it doesn't exist
                $employeesPath = public_path('employees');
                if (!file_exists($employeesPath)) {
                    mkdir($employeesPath, 0755, true);
                }

                // Move file to public/employees
                $avatar->move($employeesPath, $filename);
                $avatarPath = 'employees/' . $filename;
            }

            // Create employee
            $employee = Employee::create(array_merge($validated, [
                'tenant_id' => $tenant->id,
                'status' => 'active',
                'avatar' => $avatarPath,
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
            'loans',
            'documents.uploader'
        ]);

        return view('tenant.payroll.employees.show', compact('tenant', 'employee'));
    }

    public function uploadDocument(Request $request, Tenant $tenant, Employee $employee)
    {
        if ($employee->tenant_id !== $tenant->id) {
            abort(404);
        }

        $validated = $request->validate([
            'document_type' => 'required|string|max:255',
            'document_name' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        $file = $request->file('file');
        $fileSize = $file->getSize();
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = 'employee_documents/' . $employee->id;

        $fullPath = public_path($path);
        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0755, true);
        }

        $file->move($fullPath, $filename);

        \App\Models\EmployeeDocument::create([
            'tenant_id' => $tenant->id,
            'employee_id' => $employee->id,
            'document_type' => $validated['document_type'],
            'document_name' => $validated['document_name'],
            'file_path' => $path . '/' . $filename,
            'file_size' => $fileSize,
            'uploaded_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Document uploaded successfully.');
    }

    public function downloadDocument(Tenant $tenant, Employee $employee, $documentId)
    {
        if ($employee->tenant_id !== $tenant->id) {
            abort(404);
        }

        $document = \App\Models\EmployeeDocument::where('employee_id', $employee->id)
            ->where('id', $documentId)
            ->firstOrFail();

        $filePath = public_path($document->file_path);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
        $downloadName = $document->document_name . '.' . $extension;

        return response()->download($filePath, $downloadName);
    }

    public function resetPortalToken(Tenant $tenant, Employee $employee)
    {
        if ($employee->tenant_id !== $tenant->id) {
            abort(404);
        }

        try {
            $employee->regeneratePortalToken();

            return response()->json([
                'success' => true,
                'message' => 'Portal token reset successfully',
                'portal_link' => $employee->portal_link,
                'expires_at' => $employee->portal_token_expires_at->format('M d, Y')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset portal token: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteDocument(Tenant $tenant, Employee $employee, $documentId)
    {
        if ($employee->tenant_id !== $tenant->id) {
            abort(404);
        }

        $document = \App\Models\EmployeeDocument::where('employee_id', $employee->id)
            ->where('id', $documentId)
            ->firstOrFail();

        $filePath = public_path($document->file_path);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $document->delete();

        return redirect()->back()->with('success', 'Document deleted successfully.');
    }

    public function editEmployee(Tenant $tenant, Employee $employee)
    {
        // Validate that the employee belongs to this tenant
        if ($employee->tenant_id !== $tenant->id) {
            abort(404);
        }

        $employee->load([
            'department',
            'position',
            'currentSalary.salaryComponents.salaryComponent'
        ]);

        $departments = Department::where('tenant_id', $tenant->id)->active()->get();
        $positions = \App\Models\Position::where('tenant_id', $tenant->id)->active()->orderBy('name')->get();
        $salaryComponents = SalaryComponent::where('tenant_id', $tenant->id)->active()->get();

        return view('tenant.payroll.employees.edit', compact(
            'tenant', 'employee', 'departments', 'positions', 'salaryComponents'
        ));
    }

    public function updateEmployee(Request $request, Tenant $tenant, Employee $employee)
    {
        // Validate that the employee belongs to this tenant
        if ($employee->tenant_id !== $tenant->id) {
            abort(404);
        }

        return DB::transaction(function () use ($request, $tenant, $employee) {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:employees,email,' . $employee->id,
                'phone' => 'nullable|string|max:20',
                'department_id' => 'required|exists:departments,id',
                'position_id' => 'nullable|exists:positions,id',
                'job_title' => 'required|string|max:255',
                'hire_date' => 'required|date',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|in:male,female,other',
                'address' => 'nullable|string',
                'city' => 'nullable|string|max:255',
                'state' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:20',
                'country' => 'nullable|string|max:255',
                'employment_type' => 'required|in:full_time,contract,casual,intern,part_time',
                'pay_frequency' => 'required|in:monthly,weekly,contract',
                'attendance_deduction_exempt' => 'nullable|boolean',
                'attendance_exemption_reason' => 'nullable|string|max:500',
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
                'avatar' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
                'remove_avatar' => 'nullable|boolean',
            ]);

            // Handle avatar removal
            if ($request->has('remove_avatar') && $request->remove_avatar) {
                if ($employee->avatar && file_exists(public_path($employee->avatar))) {
                    unlink(public_path($employee->avatar));
                }
                $validated['avatar'] = null;
            }

            // Handle new avatar upload
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($employee->avatar && file_exists(public_path($employee->avatar))) {
                    unlink(public_path($employee->avatar));
                }

                $avatar = $request->file('avatar');
                $filename = time() . '_' . uniqid() . '.' . $avatar->getClientOriginalExtension();

                // Create employees directory if it doesn't exist
                $employeesPath = public_path('employees');
                if (!file_exists($employeesPath)) {
                    mkdir($employeesPath, 0755, true);
                }

                // Move file to public/employees
                $avatar->move($employeesPath, $filename);
                $validated['avatar'] = 'employees/' . $filename;
            }

            // Update employee
            $employee->update($validated);

            // Check if salary has changed
            $currentSalary = $employee->currentSalary;
            if (!$currentSalary || $currentSalary->basic_salary != $validated['basic_salary']) {
                // Mark old salary as not current
                if ($currentSalary) {
                    $currentSalary->update(['is_current' => false]);
                }

                // Create new salary structure
                $salary = EmployeeSalary::create([
                    'employee_id' => $employee->id,
                    'basic_salary' => $validated['basic_salary'],
                    'effective_date' => now(),
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
            } else {
                // Update existing salary components only
                if ($currentSalary && !empty($validated['components'])) {
                    // Delete old components
                    $currentSalary->salaryComponents()->delete();

                    // Add new components
                    foreach ($validated['components'] as $component) {
                        if (!empty($component['amount']) || !empty($component['percentage'])) {
                            EmployeeSalaryComponent::create([
                                'employee_salary_id' => $currentSalary->id,
                                'salary_component_id' => $component['id'],
                                'amount' => $component['amount'] ?? null,
                                'percentage' => $component['percentage'] ?? null,
                                'is_active' => true,
                            ]);
                        }
                    }
                }
            }

            return redirect()
                ->route('tenant.payroll.employees.show', [$tenant, $employee])
                ->with('success', 'Employee updated successfully.');
        });
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

        $department = Department::create(array_merge($validated, [
            'tenant_id' => $tenant->id,
            'is_active' => true
        ]));

        // Return JSON for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Department created successfully.',
                'id' => $department->id,
                'department' => $department
            ]);
        }

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
            'type' => 'required|in:earning,deduction,employer_contribution',
            'calculation_type' => 'required|in:fixed,percentage,variable,computed',
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

    /**
     * Export payroll processing summary
     */
    public function exportProcessingSummary(Request $request, Tenant $tenant)
    {
        $payrollPeriods = PayrollPeriod::where('tenant_id', $tenant->id)
            ->with(['createdBy', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'payroll_processing_summary_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($payrollPeriods) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'Period Name',
                'Type',
                'Start Date',
                'End Date',
                'Pay Date',
                'Status',
                'Total Employees',
                'Gross Pay',
                'Total Deductions',
                'Net Pay',
                'Created By',
                'Created At',
                'Approved By',
                'Approved At',
            ]);

            // Add data rows
            foreach ($payrollPeriods as $period) {
                fputcsv($file, [
                    $period->name,
                    ucfirst($period->type),
                    $period->start_date,
                    $period->end_date,
                    $period->pay_date,
                    ucfirst($period->status),
                    $period->total_employees ?? 0,
                    number_format($period->total_gross_pay ?? 0, 2),
                    number_format($period->total_deductions ?? 0, 2),
                    number_format($period->total_net_pay ?? 0, 2),
                    $period->createdBy->name ?? '',
                    $period->created_at->format('Y-m-d H:i:s'),
                    $period->approvedBy->name ?? '',
                    $period->approved_at ? $period->approved_at->format('Y-m-d H:i:s') : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function createPayroll(Tenant $tenant)
    {
        // Suggest next month's payroll period
        $nextMonth = now()->addMonth();
        $startDate = $nextMonth->startOfMonth();
        $endDate = $nextMonth->endOfMonth();
        $payDate = $endDate->copy()->addDays(2); // Pay 2 days after month end

        // Get active employees count
        $activeEmployees = Employee::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->count();

        return view('tenant.payroll.processing.create', compact(
            'tenant', 'startDate', 'endDate', 'payDate', 'activeEmployees'
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

    /**
     * Process payroll (alias for storePayroll)
     */
    public function processPayroll(Request $request, Tenant $tenant)
    {
        return $this->storePayroll($request, $tenant);
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

    /**
     * Show payroll period (alias for showPayroll)
     */
    public function showPayrollPeriod(Tenant $tenant, PayrollPeriod $period)
    {
        return $this->showPayroll($tenant, $period);
    }

    public function generatePayroll(Request $request, Tenant $tenant, PayrollPeriod $period)
    {
        if (!$period->canBeProcessed()) {
            return redirect()->back()->with('error', 'Payroll cannot be processed in current status.');
        }

        // Validate the options
        $validated = $request->validate([
            'apply_paye_tax' => 'required|boolean',
            'apply_nsitf' => 'required|boolean',
            'paye_tax_rate' => 'nullable|numeric|min:0|max:100',
            'nsitf_rate' => 'nullable|numeric|min:0|max:100',
            'tax_exemption_reason' => 'nullable|string|max:500',
        ]);

        try {
            DB::transaction(function () use ($period, $validated) {
                // Update period with tax and NSITF preferences
                $period->update([
                    'apply_paye_tax' => $validated['apply_paye_tax'],
                    'apply_nsitf' => $validated['apply_nsitf'],
                    'paye_tax_rate' => $validated['paye_tax_rate'] ?? null,
                    'nsitf_rate' => $validated['nsitf_rate'] ?? null,
                    'tax_exemption_reason' => $validated['tax_exemption_reason'] ?? null,
                ]);

                $period->generatePayrollForAllEmployees();
            });

            $message = 'Payroll generated successfully.';
            if (!$validated['apply_paye_tax']) {
                $message .= ' PAYE tax was not applied as requested.';
            }
            if (!$validated['apply_nsitf']) {
                $message .= ' NSITF contribution was not applied as requested.';
            }

            return redirect()
                ->route('tenant.payroll.processing.show', [$tenant, $period])
                ->with('success', $message);
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
     * Payroll summary report - overview of all payroll periods
     */
    public function payrollSummary(Request $request, Tenant $tenant)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month');
        $status = $request->get('status');

        // Get payroll periods based on filters
        $query = PayrollPeriod::where('tenant_id', $tenant->id)
            ->with(['payrollRuns.employee.department', 'createdBy', 'approvedBy']);

        // Filter by year
        $query->whereYear('pay_date', $year);

        // Filter by month if provided
        if ($month) {
            $query->whereMonth('pay_date', $month);
        }

        // Filter by status if provided
        if ($status) {
            $query->where('status', $status);
        }

        $payrollPeriods = $query->orderBy('pay_date', 'desc')->get();

        // Calculate summary statistics
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

        // Calculate monthly breakdown
        $monthlyData = $payrollPeriods->groupBy(function($period) {
            return $period->pay_date->format('Y-m');
        })->map(function($periods, $monthKey) {
            $runs = $periods->flatMap->payrollRuns;
            return [
                'month' => $monthKey,
                'periods' => $periods->count(),
                'employees' => $runs->count(),
                'gross' => $runs->sum('gross_salary'),
                'deductions' => $runs->sum('total_deductions'),
                'net' => $runs->sum('net_salary'),
                'tax' => $runs->sum('monthly_tax'),
            ];
        })->sortByDesc('month');

        // Department breakdown
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
                $deptData = $departmentData->get($deptName);
                $deptData['employees']++;
                $deptData['gross'] += $run->gross_salary;
                $deptData['deductions'] += $run->total_deductions;
                $deptData['net'] += $run->net_salary;
                $departmentData->put($deptName, $deptData);
            }
        }

        $summary = [
            'total_periods' => $totalPeriods,
            'total_employees' => $totalEmployees,
            'total_gross' => $totalGross,
            'total_deductions' => $totalDeductions,
            'total_net' => $totalNet,
            'total_tax' => $totalTax,
            'average_per_employee' => $totalEmployees > 0 ? $totalNet / $totalEmployees : 0,
        ];

        return view('tenant.payroll.reports.summary', compact(
            'tenant',
            'payrollPeriods',
            'summary',
            'monthlyData',
            'departmentData',
            'year',
            'month',
            'status'
        ));
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
     * Tax summary report - aggregated tax information
     */
    public function taxSummary(Request $request, Tenant $tenant)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month');

        $query = PayrollRun::whereHas('payrollPeriod', function($q) use ($tenant, $year, $month) {
            $q->where('tenant_id', $tenant->id)
              ->whereYear('pay_date', $year);

            if ($month) {
                $q->whereMonth('pay_date', $month);
            }
        })->with(['employee.department', 'payrollPeriod']);

        $payrollRuns = $query->get();

        // Calculate summary statistics
        $totalTax = $payrollRuns->sum('monthly_tax');
        $totalGross = $payrollRuns->sum('gross_salary');
        $totalEmployees = $payrollRuns->groupBy('employee_id')->count();
        $averageTaxRate = $totalGross > 0 ? ($totalTax / $totalGross) * 100 : 0;

        // Monthly breakdown
        $monthlyData = $payrollRuns->groupBy(function($run) {
            return $run->payrollPeriod->pay_date->format('Y-m');
        })->map(function($runs, $monthKey) {
            return [
                'month' => $monthKey,
                'employees' => $runs->groupBy('employee_id')->count(),
                'gross' => $runs->sum('gross_salary'),
                'tax' => $runs->sum('monthly_tax'),
                'net' => $runs->sum('net_salary'),
            ];
        })->sortByDesc('month');

        // Department breakdown
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
            $deptData = $departmentData->get($deptName);
            $deptData['employees']->push($run->employee_id);
            $deptData['gross'] += $run->gross_salary;
            $deptData['tax'] += $run->monthly_tax;
            $departmentData->put($deptName, $deptData);
        }

        // Calculate unique employees per department
        $departmentData = $departmentData->map(function($data) {
            $data['employees'] = $data['employees']->unique()->count();
            return $data;
        });

        $summary = [
            'total_tax' => $totalTax,
            'total_gross' => $totalGross,
            'total_employees' => $totalEmployees,
            'average_tax_rate' => $averageTaxRate,
        ];

        return view('tenant.payroll.reports.tax-summary', compact(
            'tenant',
            'summary',
            'monthlyData',
            'departmentData',
            'year',
            'month'
        ));
    }

    /**
     * Employee summary report - detailed employee payroll statistics
     */
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

        // Get departments for filter
        $departments = \App\Models\Department::where('tenant_id', $tenant->id)
            ->orderBy('name')
            ->get();

        // Calculate payroll statistics for each employee
        $employeeData = $employees->map(function($employee) use ($year) {
            $runs = PayrollRun::where('employee_id', $employee->id)
                ->whereHas('payrollPeriod', function($q) use ($year) {
                    $q->whereYear('pay_date', $year);
                })
                ->get();

            return [
                'employee' => $employee,
                'payroll_count' => $runs->count(),
                'total_gross' => $runs->sum('gross_salary'),
                'total_deductions' => $runs->sum('total_deductions'),
                'total_tax' => $runs->sum('monthly_tax'),
                'total_net' => $runs->sum('net_salary'),
                'average_gross' => $runs->count() > 0 ? $runs->avg('gross_salary') : 0,
                'average_net' => $runs->count() > 0 ? $runs->avg('net_salary') : 0,
            ];
        });

        // Summary statistics
        $summary = [
            'total_employees' => $employeeData->count(),
            'total_gross' => $employeeData->sum('total_gross'),
            'total_deductions' => $employeeData->sum('total_deductions'),
            'total_tax' => $employeeData->sum('total_tax'),
            'total_net' => $employeeData->sum('total_net'),
        ];

        return view('tenant.payroll.reports.employee-summary', compact(
            'tenant',
            'employeeData',
            'summary',
            'departments',
            'year',
            'departmentId'
        ));
    }

    /**
     * Detailed payroll report - comprehensive breakdown
     */
    public function detailedReport(Request $request, Tenant $tenant)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month');
        $departmentId = $request->get('department_id');

        $query = PayrollRun::whereHas('payrollPeriod', function($q) use ($tenant, $year, $month) {
            $q->where('tenant_id', $tenant->id)
              ->whereYear('pay_date', $year);

            if ($month) {
                $q->whereMonth('pay_date', $month);
            }
        })->with(['employee.department', 'payrollPeriod']);

        if ($departmentId) {
            $query->whereHas('employee', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        $payrollRuns = $query->orderBy('created_at', 'desc')->get();

        // Get departments for filter
        $departments = \App\Models\Department::where('tenant_id', $tenant->id)
            ->orderBy('name')
            ->get();

        // Calculate totals
        $totals = [
            'employees' => $payrollRuns->groupBy('employee_id')->count(),
            'gross' => $payrollRuns->sum('gross_salary'),
            'basic' => $payrollRuns->sum('basic_salary'),
            'allowances' => $payrollRuns->sum('total_allowances'),
            'deductions' => $payrollRuns->sum('total_deductions'),
            'tax' => $payrollRuns->sum('monthly_tax'),
            'net' => $payrollRuns->sum('net_salary'),
        ];

        return view('tenant.payroll.reports.detailed', compact(
            'tenant',
            'payrollRuns',
            'totals',
            'departments',
            'year',
            'month',
            'departmentId'
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
     * Download employee import template
     */
    public function downloadEmployeeTemplate(Tenant $tenant)
    {
        $filename = 'employees_template_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'First Name *',
                'Last Name *',
                'Email *',
                'Phone',
                'Department',
                'Position',
                'Job Title',
                'Employment Type',
                'Pay Frequency',
                'Hire Date (YYYY-MM-DD)',
                'Confirmation Date (YYYY-MM-DD)',
                'Gender',
                'Marital Status',
                'Date of Birth (YYYY-MM-DD)',
                'Address',
                'State of Origin',
                'Basic Salary',
                'Bank Name',
                'Bank Code',
                'Account Number',
                'Account Name',
                'TIN',
                'Pension PIN',
                'PFA Name',
            ]);

            // Add example row
            fputcsv($file, [
                'John',
                'Doe',
                'john.doe@example.com',
                '08012345678',
                'IT',
                'Senior Developer',
                'Senior Developer',
                'permanent',
                'monthly',
                '2024-01-15',
                '2024-03-15',
                'male',
                'married',
                '1990-05-20',
                '123 Main Street',
                'Lagos',
                '500000',
                'First Bank',
                '011',
                '0123456789',
                'JOHN DOE',
                '12345-6789',
                'A123456789',
                'AIICO Pension Managers',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import employees from CSV file
     */
    public function importEmployees(Request $request, Tenant $tenant)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:csv,txt,xlsx,xls|mimetypes:text/csv,text/plain,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet|max:2048',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        }

        $file = $request->file('file');
        $imported = 0;
        $errors = 0;
        $errorDetails = [];

        try {
            $handle = fopen($file->getRealPath(), 'r');
            $headers = fgetcsv($handle); // Skip header row

            $rowNumber = 2;
            while (($row = fgetcsv($handle)) !== false) {
                try {
                    // Map CSV columns to array
                    $data = [
                        'first_name' => $row[0] ?? null,
                        'last_name' => $row[1] ?? null,
                        'email' => $row[2] ?? null,
                        'phone' => $row[3] ?? null,
                        'department_name' => $row[4] ?? null,
                        'position_name' => $row[5] ?? null,
                        'job_title' => $row[6] ?? null,
                        'employment_type' => $row[7] ?? 'permanent',
                        'pay_frequency' => $row[8] ?? 'monthly',
                        'hire_date' => $row[9] ?? null,
                        'confirmation_date' => $row[10] ?? null,
                        'gender' => $row[11] ?? null,
                        'marital_status' => $row[12] ?? null,
                        'date_of_birth' => $row[13] ?? null,
                        'address' => $row[14] ?? null,
                        'state_of_origin' => $row[15] ?? null,
                        'basic_salary' => $row[16] ?? 0,
                        'bank_name' => $row[17] ?? null,
                        'bank_code' => $row[18] ?? null,
                        'account_number' => $row[19] ?? null,
                        'account_name' => $row[20] ?? null,
                        'tin' => $row[21] ?? null,
                        'pension_pin' => $row[22] ?? null,
                        'pfa_name' => $row[23] ?? null,
                    ];

                    // Validate required fields
                    if (!$data['first_name'] || !$data['last_name'] || !$data['email']) {
                        $errors++;
                        $errorDetails[] = "Row {$rowNumber}: Missing required fields (First Name, Last Name, or Email)";
                        $rowNumber++;
                        continue;
                    }

                    // Check if employee exists
                    $existingEmployee = Employee::where('email', $data['email'])
                        ->where('tenant_id', $tenant->id)
                        ->first();

                    if ($existingEmployee) {
                        $errors++;
                        $errorDetails[] = "Row {$rowNumber}: Employee with email {$data['email']} already exists";
                        $rowNumber++;
                        continue;
                    }

                    // Handle department
                    $department = null;
                    if ($data['department_name']) {
                        $department = Department::where('tenant_id', $tenant->id)
                            ->where('name', $data['department_name'])
                            ->first();

                        if (!$department) {
                            // Create department if it doesn't exist
                            $department = Department::create([
                                'tenant_id' => $tenant->id,
                                'name' => $data['department_name'],
                                'code' => strtoupper(substr($data['department_name'], 0, 3)),
                                'is_active' => true,
                            ]);
                        }
                    }

                    // Handle position
                    $position = null;
                    if ($data['position_name'] && $department) {
                        $position = \App\Models\Position::where('tenant_id', $tenant->id)
                            ->where('name', $data['position_name'])
                            ->where('department_id', $department->id)
                            ->first();
                    }

                    // Create employee
                    $employee = Employee::create([
                        'tenant_id' => $tenant->id,
                        'department_id' => $department?->id,
                        'position_id' => $position?->id,
                        'first_name' => $data['first_name'],
                        'last_name' => $data['last_name'],
                        'email' => $data['email'],
                        'phone' => $data['phone'],
                        'job_title' => $data['job_title'] ?? $data['position_name'],
                        'employment_type' => $data['employment_type'],
                        'pay_frequency' => $data['pay_frequency'],
                        'hire_date' => $data['hire_date'] ? Carbon::createFromFormat('Y-m-d', $data['hire_date']) : now(),
                        'confirmation_date' => $data['confirmation_date'] ? Carbon::createFromFormat('Y-m-d', $data['confirmation_date']) : null,
                        'gender' => $data['gender'],
                        'marital_status' => $data['marital_status'] ?? 'single',
                        'date_of_birth' => $data['date_of_birth'] ? Carbon::createFromFormat('Y-m-d', $data['date_of_birth']) : null,
                        'address' => $data['address'],
                        'state_of_origin' => $data['state_of_origin'],
                        'bank_name' => $data['bank_name'],
                        'bank_code' => $data['bank_code'],
                        'account_number' => $data['account_number'],
                        'account_name' => $data['account_name'],
                        'tin' => $data['tin'],
                        'pension_pin' => $data['pension_pin'],
                        'pfa_name' => $data['pfa_name'],
                        'status' => 'active',
                    ]);

                    // Create salary record if basic salary provided
                    if ($data['basic_salary']) {
                        EmployeeSalary::create([
                            'employee_id' => $employee->id,
                            'basic_salary' => $data['basic_salary'],
                            'effective_date' => now(),
                            'is_current' => true,
                            'created_by' => auth()->id() ?? 1, // Fallback to user ID 1 if auth not available
                        ]);
                    }

                    $imported++;
                } catch (\Exception $e) {
                    $errors++;
                    $errorDetails[] = "Row {$rowNumber}: " . $e->getMessage();
                }

                $rowNumber++;
            }

            fclose($handle);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'imported' => $imported,
                    'errors' => $errors,
                    'message' => "Import completed: {$imported} employees imported, {$errors} errors",
                    'error_details' => array_slice($errorDetails, 0, 10),
                ], $errors > 0 && $imported === 0 ? 422 : 200);
            }

            return redirect()->route('tenant.payroll.employees.index', $tenant)
                ->with('success', "Import completed: {$imported} employees imported, {$errors} errors");

        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Error processing file: ' . $e->getMessage(),
                ], 422);
            }

            return redirect()->back()
                ->with('error', 'Error processing file: ' . $e->getMessage());
        }
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

    /**
     * Show salary update form
     */
    public function editSalary(Tenant $tenant, Employee $employee)
    {
        // Validate that the employee belongs to this tenant
        if ($employee->tenant_id !== $tenant->id) {
            abort(404);
        }

        $employee->load([
            'department',
            'position',
            'currentSalary.salaryComponents.salaryComponent'
        ]);

        $salaryComponents = SalaryComponent::where('tenant_id', $tenant->id)
            ->active()
            ->orderBy('sort_order')
            ->get();

        return view('tenant.payroll.employees.edit-salary', compact(
            'tenant', 'employee', 'salaryComponents'
        ));
    }

    /**
     * Update employee salary
     */
    public function updateSalary(Request $request, Tenant $tenant, Employee $employee)
    {
        // Validate that the employee belongs to this tenant
        if ($employee->tenant_id !== $tenant->id) {
            abort(404);
        }

        return DB::transaction(function () use ($request, $tenant, $employee) {
            $validated = $request->validate([
                'basic_salary' => 'required|numeric|min:0',
                'effective_date' => 'required|date',
                'notes' => 'nullable|string|max:1000',
                'components' => 'nullable|array',
                'components.*.enabled' => 'nullable|boolean',
                'components.*.amount' => 'nullable|numeric|min:0',
                'components.*.percentage' => 'nullable|numeric|min:0|max:100',
            ]);

            // Mark current salary as not current and set end date
            if ($employee->currentSalary) {
                $employee->currentSalary->update([
                    'is_current' => false,
                    'end_date' => now()->subDay()->toDateString(),
                ]);
            }

            // Create new salary record
            $newSalary = EmployeeSalary::create([
                'employee_id' => $employee->id,
                'basic_salary' => $validated['basic_salary'],
                'effective_date' => $validated['effective_date'],
                'is_current' => true,
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            // Handle salary components
            if (isset($validated['components'])) {
                foreach ($validated['components'] as $componentId => $componentData) {
                    if (isset($componentData['enabled']) && $componentData['enabled']) {
                        $salaryComponent = SalaryComponent::find($componentId);

                        if ($salaryComponent && $salaryComponent->tenant_id === $tenant->id) {
                            $componentRecord = [
                                'employee_salary_id' => $newSalary->id,
                                'salary_component_id' => $componentId,
                            ];

                            if ($salaryComponent->calculation_type === 'percentage') {
                                $componentRecord['percentage'] = $componentData['percentage'] ?? 0;
                                $componentRecord['amount'] = null;
                            } elseif ($salaryComponent->calculation_type === 'fixed') {
                                $componentRecord['amount'] = $componentData['amount'] ?? 0;
                                $componentRecord['percentage'] = null;
                            }

                            \App\Models\EmployeeSalaryComponent::create($componentRecord);
                        }
                    }
                }
            }

            return redirect()
                ->route('tenant.payroll.employees.show', ['tenant' => $tenant->slug, 'employee' => $employee->id])
                ->with('success', 'Employee salary updated successfully.');
        });
    }

    /**
     * Toggle employee status
     */
    public function toggleStatus(Tenant $tenant, Employee $employee)
    {
        // Validate that the employee belongs to this tenant
        if ($employee->tenant_id !== $tenant->id) {
            abort(404);
        }

        // Toggle status between active and inactive
        $newStatus = $employee->status === 'active' ? 'inactive' : 'active';
        $employee->update(['status' => $newStatus]);

        $statusText = $newStatus === 'active' ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Employee {$statusText} successfully.");
    }

    /**
     * Reset employee portal link
     */
    public function resetPortalLink(Tenant $tenant, Employee $employee)
    {
        // Validate that the employee belongs to this tenant
        if ($employee->tenant_id !== $tenant->id) {
            abort(404);
        }

        // Generate new portal token
        $employee->portal_token = Str::random(64);
        $employee->save();

        // You could send an email here with the new link
        // Mail::to($employee->email)->send(new PortalAccessMail($employee));

        return redirect()->back()
            ->with('success', 'Portal link has been reset successfully. New link has been generated.');
    }

    /**
     * View employee payslip
     */
    public function viewPayslip(Tenant $tenant, $payrollRunId)
    {
        $payrollRun = PayrollRun::whereHas('payrollPeriod', function($query) use ($tenant) {
                $query->where('tenant_id', $tenant->id);
            })
            ->with(['employee', 'payrollPeriod'])
            ->findOrFail($payrollRunId);

        return view('tenant.payroll.payslips.view', compact('tenant', 'payrollRun'));
    }

    /**
     * Download employee payslip as PDF
     */
    public function downloadPayslip(Tenant $tenant, $payrollRunId)
    {
        $payrollRun = PayrollRun::whereHas('payrollPeriod', function($query) use ($tenant) {
                $query->where('tenant_id', $tenant->id);
            })
            ->with(['employee', 'payrollPeriod', 'details'])
            ->findOrFail($payrollRunId);

        // Generate PDF
        $pdf = \PDF::loadView('tenant.payroll.payslips.pdf', compact('tenant', 'payrollRun'));

        $fileName = 'payslip_' . $payrollRun->employee->employee_number . '_' .
                    $payrollRun->payrollPeriod->start_date->format('Y-m') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Email payslip to employee
     */
    public function emailPayslip(Tenant $tenant, $payrollRunId)
    {
        $payrollRun = PayrollRun::whereHas('payrollPeriod', function($query) use ($tenant) {
                $query->where('tenant_id', $tenant->id);
            })
            ->with(['employee', 'payrollPeriod'])
            ->findOrFail($payrollRunId);

        // TODO: Implement email sending
        // Mail::to($payrollRun->employee->email)->send(new PayslipMail($payrollRun));

        return response()->json([
            'success' => true,
            'message' => 'Payslip sent successfully to ' . $payrollRun->employee->email
        ]);
    }

    /**
     * Bank Schedule Report - Shows approved payrolls ready for bank payment
     */
    public function bankSchedule(Request $request, Tenant $tenant)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month'); // Don't default to current month
        $status = $request->get('status', 'approved'); // approved, paid

        // Get payroll periods based on filters
        $query = PayrollPeriod::where('tenant_id', $tenant->id)
            ->with(['payrollRuns.employee.department', 'createdBy', 'approvedBy']);

        // Filter by status
        if ($status) {
            $query->where('status', $status);
        }

        // Filter by year/month - only if month is explicitly provided
        if ($year && $month) {
            $query->whereYear('pay_date', $year)
                  ->whereMonth('pay_date', $month);
        } elseif ($year && !$month) {
            // If only year is provided, show all months for that year
            $query->whereYear('pay_date', $year);
        }

        $payrollPeriods = $query->orderBy('pay_date', 'desc')->get();

        // Calculate totals
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
            'total_gross' => $totalGross,
            'total_deductions' => $totalDeductions,
            'total_net' => $totalNet,
        ];

        return view('tenant.payroll.reports.bank-schedule', compact(
            'tenant',
            'payrollPeriods',
            'summary',
            'year',
            'month',
            'status'
        ));
    }

    /**
     * Mark payroll period as paid (updates all payroll runs and period status)
     */
    public function markPayrollAsPaid(Request $request, Tenant $tenant, PayrollPeriod $period)
    {
        // Validate that the period is approved
        if ($period->status !== 'approved') {
            return redirect()->back()->with('error', 'Only approved payrolls can be marked as paid.');
        }

        $validated = $request->validate([
            'payment_reference' => 'nullable|string|max:255',
            'payment_date' => 'nullable|date',
        ]);

        try {
            DB::transaction(function () use ($period, $validated) {
                $paymentDate = $validated['payment_date'] ?? now();
                $reference = $validated['payment_reference'] ?? 'BANK_TRANSFER_' . $period->id . '_' . now()->format('YmdHis');

                // Mark all payroll runs as paid
                foreach ($period->payrollRuns as $run) {
                    $run->markAsPaid($reference);
                }

                // Update period status to paid
                $period->update([
                    'status' => 'paid',
                    'paid_at' => $paymentDate,
                ]);
            });

            return redirect()->back()->with('success', 'Payroll marked as paid successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error marking payroll as paid: ' . $e->getMessage());
        }
    }

    /**
     * Mark individual payroll run as paid
     */
    public function markPayslipAsPaid(Request $request, Tenant $tenant, $payrollRunId)
    {
        $payrollRun = PayrollRun::whereHas('payrollPeriod', function($query) use ($tenant) {
                $query->where('tenant_id', $tenant->id);
            })
            ->findOrFail($payrollRunId);

        $validated = $request->validate([
            'payment_reference' => 'nullable|string|max:255',
        ]);

        try {
            $reference = $validated['payment_reference'] ?? 'PAYMENT_' . $payrollRunId . '_' . now()->format('YmdHis');
            $payrollRun->markAsPaid($reference);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payslip marked as paid successfully.'
                ]);
            }

            return redirect()->back()->with('success', 'Payslip marked as paid successfully.');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error marking payslip as paid: ' . $e->getMessage());
        }
    }

    /**
     * Display all employee loans and advances
     */
    public function loans(Request $request, Tenant $tenant)
    {
        $query = EmployeeLoan::with(['employee.department', 'approvedBy'])
            ->whereHas('employee', function($q) use ($tenant) {
                $q->where('tenant_id', $tenant->id);
            });

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Search by loan number
        if ($request->filled('search')) {
            $query->where('loan_number', 'like', '%' . $request->search . '%');
        }

        $loans = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get all employees for filter
        $employees = Employee::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();

        // Calculate summary stats
        $stats = [
            'total_loans' => EmployeeLoan::whereHas('employee', function($q) use ($tenant) {
                $q->where('tenant_id', $tenant->id);
            })->count(),
            'active_loans' => EmployeeLoan::whereHas('employee', function($q) use ($tenant) {
                $q->where('tenant_id', $tenant->id);
            })->where('status', 'active')->count(),
            'total_amount' => EmployeeLoan::whereHas('employee', function($q) use ($tenant) {
                $q->where('tenant_id', $tenant->id);
            })->sum('loan_amount'),
            'total_outstanding' => EmployeeLoan::whereHas('employee', function($q) use ($tenant) {
                $q->where('tenant_id', $tenant->id);
            })->where('status', 'active')->sum('balance'),
        ];

        return view('tenant.payroll.loans.index', compact('tenant', 'loans', 'employees', 'stats'));
    }

    /**
     * Show form to create salary advance voucher
     */
    public function createSalaryAdvance(Request $request, Tenant $tenant)
    {
        // Get all active employees
        $employees = Employee::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->with(['department', 'currentSalary'])
            ->orderBy('first_name')
            ->get();

        // Get or create Salary Advance voucher type
        $salaryAdvanceVoucherType = \App\Models\VoucherType::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'code' => 'SA'
            ],
            [
                'name' => 'Salary Advance',
                'abbreviation' => 'SA',
                'description' => 'Salary advance (IOU) payments to employees',
                'numbering_method' => 'auto',
                'prefix' => 'SA-',
                'starting_number' => 1,
                'current_number' => 0,
                'has_reference' => true,
                'affects_inventory' => false,
                'affects_cashbank' => true,
                'is_system_defined' => true,
                'is_active' => true,
            ]
        );

        // Get common ledger accounts for salary advance
        $cashAccount = \App\Models\LedgerAccount::where('tenant_id', $tenant->id)
            ->where('name', 'Cash in Hand')
            ->first();

        $advanceAccount = \App\Models\LedgerAccount::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'code' => '1130'
            ],
            [
                'name' => 'Employee Advances',
                'account_type' => 'asset',
                'account_group_id' => \App\Models\AccountGroup::where('tenant_id', $tenant->id)
                    ->where('code', 'CA')
                    ->first()->id ?? null,
                'is_active' => true,
                'description' => 'Salary advances given to employees'
            ]
        );

        return view('tenant.payroll.salary-advance.create', compact(
            'tenant',
            'employees',
            'salaryAdvanceVoucherType',
            'cashAccount',
            'advanceAccount'
        ));
    }

    /**
     * Store salary advance voucher and create employee loan
     */
    public function storeSalaryAdvance(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:1',
            'duration_months' => 'required|integer|min:1|max:12',
            'purpose' => 'nullable|string|max:500',
            'voucher_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank',
            'reference' => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($request, $tenant, $validated) {
                $employee = Employee::findOrFail($validated['employee_id']);

                // Calculate monthly deduction
                $monthlyDeduction = $validated['amount'] / $validated['duration_months'];

                // Create Employee Loan record
                $loan = EmployeeLoan::create([
                    'employee_id' => $employee->id,
                    'loan_amount' => $validated['amount'],
                    'monthly_deduction' => $monthlyDeduction,
                    'duration_months' => $validated['duration_months'],
                    'start_date' => now(),
                    'purpose' => $validated['purpose'] ?? 'Salary Advance',
                    'status' => 'active',
                    'approved_by' => Auth::id(),
                ]);

                // Get or create necessary accounts
                $advanceAccount = \App\Models\LedgerAccount::firstOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'code' => '1130'
                    ],
                    [
                        'name' => 'Employee Advances',
                        'account_type' => 'asset',
                        'account_group_id' => \App\Models\AccountGroup::where('tenant_id', $tenant->id)
                            ->where('code', 'CA')
                            ->first()->id ?? null,
                        'is_active' => true,
                    ]
                );

                $cashOrBankAccount = null;
                if ($validated['payment_method'] === 'cash') {
                    $cashOrBankAccount = \App\Models\LedgerAccount::where('tenant_id', $tenant->id)
                        ->where('name', 'Cash in Hand')
                        ->first();
                } else {
                    // Get primary bank account
                    $cashOrBankAccount = \App\Models\LedgerAccount::where('tenant_id', $tenant->id)
                        ->where('account_type', 'asset')
                        ->where('name', 'like', '%Bank%')
                        ->first();
                }

                // Get or create Salary Advance voucher type
                $voucherType = \App\Models\VoucherType::where('tenant_id', $tenant->id)
                    ->where('code', 'SA')
                    ->first();

                // Create Voucher
                $voucher = \App\Models\Voucher::create([
                    'tenant_id' => $tenant->id,
                    'voucher_type_id' => $voucherType->id,
                    'voucher_number' => $voucherType->getNextVoucherNumber(),
                    'voucher_date' => $validated['voucher_date'],
                    'reference_number' => $validated['reference'] ?? $loan->loan_number,
                    'total_amount' => $validated['amount'],
                    'narration' => "Salary advance issued to {$employee->full_name} - {$loan->loan_number}",
                    'status' => 'posted', // Post immediately
                    'created_by' => Auth::id(),
                    'posted_by' => Auth::id(),
                    'posted_at' => now(),
                ]);

                // Create voucher entries (Debit: Employee Advances, Credit: Cash/Bank)
                \App\Models\VoucherEntry::create([
                    'voucher_id' => $voucher->id,
                    'ledger_account_id' => $advanceAccount->id,
                    'debit_amount' => $validated['amount'],
                    'credit_amount' => 0,
                    'particulars' => "Salary advance to {$employee->full_name} ({$employee->employee_number})",
                ]);

                \App\Models\VoucherEntry::create([
                    'voucher_id' => $voucher->id,
                    'ledger_account_id' => $cashOrBankAccount->id,
                    'debit_amount' => 0,
                    'credit_amount' => $validated['amount'],
                    'particulars' => "Payment for salary advance - {$loan->loan_number}",
                ]);

                // Update voucher type current number
                $voucherType->increment('current_number');

                return $voucher;
            });

            return redirect()
                ->route('tenant.payroll.loans.index', $tenant)
                ->with('success', 'Salary advance voucher created successfully. The loan will be deducted from employee payroll automatically.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error creating salary advance: ' . $e->getMessage());
        }
    }

    /**
     * Delete a payroll period and all associated data
     * This allows admins to roll back payroll for corrections
     */
    public function deletePayrollPeriod(Tenant $tenant, PayrollPeriod $period)
    {
        // Validate that the period belongs to this tenant
        if ($period->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized access.');
        }

        // Only allow deletion if not yet paid
        if ($period->status === 'paid') {
            return redirect()
                ->back()
                ->with('error', 'Cannot delete a paid payroll period. Please contact support for assistance.');
        }

        // If approved, warn about accounting entries
        if ($period->status === 'approved') {
            return redirect()
                ->back()
                ->with('error', 'This payroll has been approved and may have accounting entries. Please use the "Reset Generation" option or contact support.');
        }

        try {
            DB::beginTransaction();

            // Delete all payroll runs and their details
            foreach ($period->payrollRuns as $run) {
                // Delete payroll run details
                $run->details()->delete();

                // Delete the payroll run
                $run->delete();
            }

            // Delete the payroll period
            $period->delete();

            DB::commit();

            return redirect()
                ->route('tenant.payroll.processing.index', $tenant)
                ->with('success', 'Payroll period deleted successfully. You can now make corrections and recreate it.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to delete payroll period: ' . $e->getMessage());
        }
    }

    /**
     * Reset payroll generation (delete all payroll runs but keep the period)
     * Useful when you need to regenerate payroll with corrected data
     */
    public function resetPayrollGeneration(Request $request, Tenant $tenant, PayrollPeriod $period)
    {
        // Validate that the period belongs to this tenant
        if ($period->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized access.');
        }

        // Only allow reset if not yet approved or paid
        if (in_array($period->status, ['approved', 'paid'])) {
            return redirect()
                ->back()
                ->with('error', 'Cannot reset an approved or paid payroll. Please contact support for assistance.');
        }

        try {
            DB::beginTransaction();

            // Delete all payroll runs and their details
            foreach ($period->payrollRuns as $run) {
                // Delete payroll run details
                $run->details()->delete();

                // Delete the payroll run
                $run->delete();
            }

            // Reset period status and totals
            $period->update([
                'status' => 'draft',
                'total_gross' => 0,
                'total_deductions' => 0,
                'total_net' => 0,
                'total_tax' => 0,
                'total_nsitf' => 0,
            ]);

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Payroll generation reset successfully. You can now regenerate payroll with updated attendance or salary data.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to reset payroll generation: ' . $e->getMessage());
        }
    }

    /**
     * Show payroll settings page
     */
    public function settings(Tenant $tenant)
    {
        return view('tenant.payroll.settings', compact('tenant'));
    }

    /**
     * Update payroll settings
     */
    public function updateSettings(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'employee_number_format' => 'required|string|max:50',
        ]);

        $tenant->update($validated);

        return redirect()->back()->with('success', 'Payroll settings updated successfully.');
    }
}
