<?php

namespace App\Http\Controllers\Api\Tenant\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\EmployeeSalaryComponent;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    /**
     * List employees.
     */
    public function index(Request $request, Tenant $tenant)
    {
        $query = Employee::where('tenant_id', $tenant->id)
            ->with(['department', 'position', 'currentSalary'])
            ->orderBy('first_name')
            ->orderBy('last_name');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('employee_number', 'like', "%{$search}%");
            });
        }

        $departmentId = $request->get('department_id', $request->get('department'));
        if (!empty($departmentId)) {
            $query->where('department_id', $departmentId);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('position_id')) {
            $query->where('position_id', $request->get('position_id'));
        }

        if ($request->filled('position')) {
            $positionSearch = $request->get('position');
            $query->where(function ($q) use ($positionSearch) {
                $q->where('job_title', 'like', "%{$positionSearch}%")
                    ->orWhereHas('position', function ($positionQuery) use ($positionSearch) {
                        $positionQuery->where('name', 'like', "%{$positionSearch}%")
                            ->orWhere('code', 'like', "%{$positionSearch}%");
                    });
            });
        }

        $perPage = (int) $request->get('per_page', 20);
        $employees = $query->paginate($perPage);

        $employees->getCollection()->transform(function (Employee $employee) {
            return $this->formatEmployee($employee);
        });

        return response()->json([
            'success' => true,
            'message' => 'Employees retrieved successfully',
            'data' => $employees,
        ]);
    }

    /**
     * Create an employee.
     */
    public function store(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), $this->rules($tenant->id));

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        return DB::transaction(function () use ($request, $tenant, $validator) {
            $data = $validator->validated();
            $avatarPath = $this->storeAvatar($request, null);

            $employee = new Employee();
            $employee->fill($data);
            $employee->tenant_id = $tenant->id;
            $employee->status = $data['status'] ?? 'active';
            $employee->avatar = $avatarPath;

            if ($request->has('attendance_deduction_exempt')) {
                $employee->attendance_deduction_exempt = $request->boolean('attendance_deduction_exempt');
            }

            if ($request->has('pension_exempt')) {
                $employee->pension_exempt = $request->boolean('pension_exempt');
            }

            $employee->save();

            $effectiveDate = $data['effective_date'] ?? $data['hire_date'] ?? now()->toDateString();

            $salary = EmployeeSalary::create([
                'employee_id' => $employee->id,
                'basic_salary' => $data['basic_salary'],
                'effective_date' => $effectiveDate,
                'is_current' => true,
                'created_by' => Auth::id(),
            ]);

            if (!empty($data['components'])) {
                $this->syncSalaryComponents($salary, $data['components']);
            }

            $employee->load(['department', 'position', 'currentSalary.salaryComponents.salaryComponent']);

            return response()->json([
                'success' => true,
                'message' => 'Employee created successfully',
                'data' => [
                    'employee' => $this->formatEmployee($employee, true),
                ],
            ], 201);
        });
    }

    /**
     * Show an employee.
     */
    public function show(Tenant $tenant, Employee $employee)
    {
        if ($employee->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
            ], 404);
        }

        $employee->load(['department', 'position', 'currentSalary.salaryComponents.salaryComponent']);

        return response()->json([
            'success' => true,
            'message' => 'Employee retrieved successfully',
            'data' => [
                'employee' => $this->formatEmployee($employee, true),
            ],
        ]);
    }

    /**
     * Update an employee.
     */
    public function update(Request $request, Tenant $tenant, Employee $employee)
    {
        if ($employee->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), $this->rules($tenant->id, $employee->id));

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        return DB::transaction(function () use ($request, $employee, $validator) {
            $data = $validator->validated();

            if ($request->boolean('remove_avatar')) {
                $this->removeAvatar($employee->avatar);
                $employee->avatar = null;
            }

            if ($request->hasFile('avatar')) {
                $employee->avatar = $this->storeAvatar($request, $employee->avatar);
            }

            if ($request->has('attendance_deduction_exempt')) {
                $employee->attendance_deduction_exempt = $request->boolean('attendance_deduction_exempt');
            }

            if ($request->has('pension_exempt')) {
                $employee->pension_exempt = $request->boolean('pension_exempt');
            }

            if (!empty($data['status'])) {
                $employee->status = $data['status'];
            }

            $employee->fill($data);
            $employee->save();

            $currentSalary = $employee->currentSalary;
            $salaryChanged = !$currentSalary || (float) $currentSalary->basic_salary !== (float) $data['basic_salary'];

            if ($salaryChanged) {
                if ($currentSalary) {
                    $currentSalary->update(['is_current' => false]);
                }

                $effectiveDate = $data['effective_date'] ?? now()->toDateString();

                $salary = EmployeeSalary::create([
                    'employee_id' => $employee->id,
                    'basic_salary' => $data['basic_salary'],
                    'effective_date' => $effectiveDate,
                    'is_current' => true,
                    'created_by' => Auth::id(),
                ]);

                if (!empty($data['components'])) {
                    $this->syncSalaryComponents($salary, $data['components']);
                }
            } elseif (!empty($data['components']) && $currentSalary) {
                $currentSalary->salaryComponents()->delete();
                $this->syncSalaryComponents($currentSalary, $data['components']);
            }

            $employee->load(['department', 'position', 'currentSalary.salaryComponents.salaryComponent']);

            return response()->json([
                'success' => true,
                'message' => 'Employee updated successfully',
                'data' => [
                    'employee' => $this->formatEmployee($employee, true),
                ],
            ]);
        });
    }

    /**
     * Delete an employee.
     */
    public function destroy(Tenant $tenant, Employee $employee)
    {
        if ($employee->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
            ], 404);
        }

        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Employee deleted successfully',
        ]);
    }

    private function rules(int $tenantId, ?int $employeeId = null): array
    {
        $emailRule = Rule::unique('employees', 'email')->where('tenant_id', $tenantId);
        if ($employeeId) {
            $emailRule = $emailRule->ignore($employeeId);
        }

        $employeeNumberRule = Rule::unique('employees', 'employee_number')->where('tenant_id', $tenantId);
        if ($employeeId) {
            $employeeNumberRule = $employeeNumberRule->ignore($employeeId);
        }

        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', $emailRule],
            'phone' => 'nullable|string|max:20',
            'employee_number' => ['nullable', 'string', 'max:50', $employeeNumberRule],
            'department_id' => ['required', Rule::exists('departments', 'id')->where('tenant_id', $tenantId)],
            'position_id' => ['nullable', Rule::exists('positions', 'id')->where('tenant_id', $tenantId)],
            'job_title' => 'required|string|max:255',
            'hire_date' => 'required|date',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'marital_status' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'state_of_origin' => 'nullable|string|max:255',
            'confirmation_date' => 'nullable|date',
            'employment_type' => 'required|in:full_time,part_time,contract,intern,casual',
            'pay_frequency' => 'required|in:monthly,weekly,bi_weekly,annual,contract',
            'status' => 'nullable|in:active,inactive,terminated',
            'attendance_deduction_exempt' => 'nullable|boolean',
            'attendance_exemption_reason' => 'nullable|string|max:500',
            'basic_salary' => 'required|numeric|min:0',
            'effective_date' => 'nullable|date',
            'bank_name' => 'nullable|string|max:255',
            'bank_code' => 'nullable|string|max:50',
            'account_number' => 'nullable|string|max:20',
            'account_name' => 'nullable|string|max:255',
            'tin' => 'nullable|string|max:20',
            'annual_relief' => 'nullable|numeric|min:0',
            'pension_pin' => 'nullable|string|max:20',
            'pfa_name' => 'nullable|string|max:255',
            'pfa_provider' => 'nullable|string|max:255',
            'rsa_pin' => 'nullable|string|max:255',
            'pension_exempt' => 'nullable|boolean',
            'components' => 'nullable|array',
            'components.*.id' => ['required', Rule::exists('salary_components', 'id')->where('tenant_id', $tenantId)],
            'components.*.amount' => 'nullable|numeric|min:0',
            'components.*.percentage' => 'nullable|numeric|min:0|max:100',
            'components.*.is_active' => 'nullable|boolean',
            'avatar' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'remove_avatar' => 'nullable|boolean',
        ];
    }

    private function formatEmployee(Employee $employee, bool $withDetails = false): array
    {
        $currentSalary = $employee->currentSalary;

        $data = [
            'id' => $employee->id,
            'first_name' => $employee->first_name,
            'last_name' => $employee->last_name,
            'full_name' => $employee->full_name,
            'email' => $employee->email,
            'phone' => $employee->phone,
            'employee_number' => $employee->employee_number,
            'avatar' => $employee->avatar,
            'avatar_url' => $employee->avatar ? asset($employee->avatar) : null,
            'department_id' => $employee->department_id,
            'department_name' => $employee->department?->name,
            'position_id' => $employee->position_id,
            'position_name' => $employee->position?->name,
            'position_code' => $employee->position?->code,
            'job_title' => $employee->job_title,
            'status' => $employee->status,
            'employment_type' => $employee->employment_type,
            'pay_frequency' => $employee->pay_frequency,
            'hire_date' => $employee->hire_date?->toDateString(),
            'basic_salary' => $currentSalary?->basic_salary,
            'gross_salary' => $currentSalary?->gross_salary,
            'total_allowances' => $currentSalary?->total_allowances,
            'total_deductions' => $currentSalary?->total_deductions,
            'created_at' => $employee->created_at?->toDateTimeString(),
            'updated_at' => $employee->updated_at?->toDateTimeString(),
        ];

        if ($withDetails) {
            $data['date_of_birth'] = $employee->date_of_birth?->toDateString();
            $data['gender'] = $employee->gender;
            $data['marital_status'] = $employee->marital_status;
            $data['address'] = $employee->address;
            $data['city'] = $employee->city ?? null;
            $data['state'] = $employee->state ?? null;
            $data['postal_code'] = $employee->postal_code ?? null;
            $data['country'] = $employee->country ?? null;
            $data['state_of_origin'] = $employee->state_of_origin;
            $data['confirmation_date'] = $employee->confirmation_date?->toDateString();
            $data['attendance_deduction_exempt'] = (bool) $employee->attendance_deduction_exempt;
            $data['attendance_exemption_reason'] = $employee->attendance_exemption_reason;
            $data['bank_name'] = $employee->bank_name;
            $data['bank_code'] = $employee->bank_code;
            $data['account_number'] = $employee->account_number;
            $data['account_name'] = $employee->account_name;
            $data['tin'] = $employee->tin;
            $data['annual_relief'] = $employee->annual_relief;
            $data['pension_pin'] = $employee->pension_pin;
            $data['pfa_name'] = $employee->pfa_name;
            $data['pfa_provider'] = $employee->pfa_provider;
            $data['rsa_pin'] = $employee->rsa_pin;
            $data['pension_exempt'] = (bool) $employee->pension_exempt;
            $data['portal_link'] = $employee->portal_link;
            $data['portal_token_expires_at'] = $employee->portal_token_expires_at?->toDateTimeString();

            $data['current_salary'] = $currentSalary ? [
                'id' => $currentSalary->id,
                'basic_salary' => $currentSalary->basic_salary,
                'effective_date' => $currentSalary->effective_date?->toDateString(),
                'gross_salary' => $currentSalary->gross_salary,
                'total_allowances' => $currentSalary->total_allowances,
                'total_deductions' => $currentSalary->total_deductions,
                'components' => $currentSalary->salaryComponents->map(function ($component) {
                    return [
                        'id' => $component->id,
                        'salary_component_id' => $component->salary_component_id,
                        'name' => $component->salaryComponent?->name,
                        'code' => $component->salaryComponent?->code,
                        'type' => $component->salaryComponent?->type,
                        'calculation_type' => $component->salaryComponent?->calculation_type,
                        'amount' => $component->amount,
                        'percentage' => $component->percentage,
                        'calculated_amount' => $component->calculated_amount,
                        'is_active' => (bool) $component->is_active,
                    ];
                })->values(),
            ] : null;
        }

        return $data;
    }

    private function storeAvatar(Request $request, ?string $currentPath): ?string
    {
        if (!$request->hasFile('avatar')) {
            return $currentPath;
        }

        if (!empty($currentPath)) {
            $this->removeAvatar($currentPath);
        }

        $avatar = $request->file('avatar');
        $filename = time() . '_' . uniqid() . '.' . $avatar->getClientOriginalExtension();
        $employeesPath = public_path('employees');

        if (!file_exists($employeesPath)) {
            mkdir($employeesPath, 0755, true);
        }

        $avatar->move($employeesPath, $filename);

        return 'employees/' . $filename;
    }

    private function removeAvatar(?string $path): void
    {
        if ($path && file_exists(public_path($path))) {
            unlink(public_path($path));
        }
    }

    private function syncSalaryComponents(EmployeeSalary $salary, array $components): void
    {
        foreach ($components as $component) {
            if (empty($component['amount']) && empty($component['percentage'])) {
                continue;
            }

            EmployeeSalaryComponent::create([
                'employee_salary_id' => $salary->id,
                'salary_component_id' => $component['id'],
                'amount' => $component['amount'] ?? null,
                'percentage' => $component['percentage'] ?? null,
                'is_active' => $component['is_active'] ?? true,
            ]);
        }
    }
}
