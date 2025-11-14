<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ShiftSchedule;
use App\Models\EmployeeShiftAssignment;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShiftController extends Controller
{
    public function index(Request $request, Tenant $tenant)
    {
        $tenantId = $tenant->id;

        $shifts = ShiftSchedule::where('tenant_id', $tenantId)
            ->withCount('employeeAssignments')
            ->orderBy('name')
            ->get();

        return view('tenant.shifts.index', compact('shifts'));
    }

    public function create(Tenant $tenant)
    {
        return view('tenant.shifts.create');
    }

    public function store(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:shift_schedules,code',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'working_days' => 'required|array',
            'working_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'late_grace_minutes' => 'nullable|integer|min:0|max:60',
            'work_hours' => 'required|numeric|min:0',
            'shift_allowance' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:500',
        ]);

        $tenantId = $tenant->id;

        DB::beginTransaction();
        try {
            $data = $request->only([
                'name',
                'code',
                'start_time',
                'end_time',
                'late_grace_minutes',
                'work_hours',
                'shift_allowance',
                'description',
            ]);

            $data['tenant_id'] = $tenantId;
            $data['working_days'] = $request->working_days;
            $data['is_active'] = true;

            $shift = ShiftSchedule::create($data);

            DB::commit();

            return redirect()
                ->route('tenant.payroll.shifts.index', $tenant)
                ->with('success', 'Shift created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create shift: ' . $e->getMessage());
        }
    }

    public function show(Tenant $tenant, $id)
    {
        $tenantId = $tenant->id;

        $shift = ShiftSchedule::where('id', $id)
            ->where('tenant_id', $tenantId)
            ->withCount('employeeAssignments')
            ->firstOrFail();

        $assignments = EmployeeShiftAssignment::with(['employee.department', 'shift'])
            ->where('shift_id', $shift->id)
            ->orderBy('effective_from', 'desc')
            ->paginate(20);

        return view('tenant.shifts.show', compact('shift', 'assignments'));
    }

    public function edit(Tenant $tenant, $id)
    {
        $tenantId = $tenant->id;

        $shift = ShiftSchedule::where('id', $id)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        return view('tenant.shifts.edit', compact('shift'));
    }

    public function update(Request $request, Tenant $tenant, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:shift_schedules,code,' . $id,
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'working_days' => 'required|array',
            'working_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'late_grace_minutes' => 'nullable|integer|min:0|max:60',
            'work_hours' => 'required|numeric|min:0',
            'shift_allowance' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'is_active' => 'required|boolean',
        ]);

        $tenantId = $tenant->id;
        $shift = ShiftSchedule::where('id', $id)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $shift->fill($request->only([
                'name',
                'code',
                'start_time',
                'end_time',
                'late_grace_minutes',
                'work_hours',
                'shift_allowance',
                'description',
                'is_active',
            ]));

            $shift->working_days = $request->working_days;
            $shift->save();

            DB::commit();

            return redirect()
                ->route('tenant.payroll.shifts.show', [$tenant, $shift->id])
                ->with('success', 'Shift updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update shift: ' . $e->getMessage());
        }
    }

    public function destroy(Tenant $tenant, $id)
    {
        $tenantId = $tenant->id;
        $shift = ShiftSchedule::where('id', $id)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        // Check if shift has active assignments
        $activeAssignments = EmployeeShiftAssignment::where('shift_id', $shift->id)
            ->where(function($q) {
                $q->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', now()->toDateString());
            })
            ->count();

        if ($activeAssignments > 0) {
            return back()->with('error', 'Cannot delete shift with active employee assignments.');
        }

        DB::beginTransaction();
        try {
            $shift->delete();
            DB::commit();

            return redirect()
                ->route('tenant.payroll.shifts.index', $tenant)
                ->with('success', 'Shift deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete shift: ' . $e->getMessage());
        }
    }

    // Employee Shift Assignments
    public function assignments(Request $request, Tenant $tenant)
    {
        $tenantId = $tenant->id;

        $query = EmployeeShiftAssignment::with(['employee.department', 'shift'])
            ->whereHas('employee', function($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            });

        if ($request->filled('department_id')) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where(function($q) {
                    $q->whereNull('effective_to')
                        ->orWhere('effective_to', '>=', now()->toDateString());
                })->where('effective_from', '<=', now()->toDateString());
            } else {
                $query->where('effective_to', '<', now()->toDateString());
            }
        }

        $assignments = $query->orderBy('effective_from', 'desc')->paginate(20);

        $departments = Department::where('tenant_id', $tenantId)->get();
        $employees = Employee::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();
        $shifts = ShiftSchedule::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->get();

        return view('tenant.shifts.assignments', compact(
            'assignments',
            'departments',
            'employees',
            'shifts'
        ));
    }

    public function assignEmployees(Request $request, Tenant $tenant)
    {
        $tenantId = $tenant->id;

        $shifts = ShiftSchedule::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->get();

        $employees = Employee::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->with(['department', 'currentShiftAssignment.shift'])
            ->orderBy('first_name')
            ->get();

        return view('tenant.shifts.assign-employees', compact('shifts', 'employees'));
    }

    public function storeAssignment(Request $request, Tenant $tenant)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'shift_id' => 'required|exists:shift_schedules,id',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'is_permanent' => 'required|boolean',
        ]);

        $tenantId = $tenant->id;
        $employee = Employee::where('id', $request->employee_id)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        $shift = ShiftSchedule::where('id', $request->shift_id)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            // End current active assignment if exists
            $currentAssignment = EmployeeShiftAssignment::where('employee_id', $employee->id)
                ->whereNull('effective_to')
                ->first();

            if ($currentAssignment) {
                $currentAssignment->effective_to = \Carbon\Carbon::parse($request->effective_from)
                    ->subDay()
                    ->toDateString();
                $currentAssignment->save();
            }

            // Create new assignment (include tenant_id)
            EmployeeShiftAssignment::create([
                'tenant_id' => $tenantId,
                'employee_id' => $employee->id,
                'shift_id' => $shift->id,
                'effective_from' => $request->effective_from,
                'effective_to' => $request->is_permanent ? null : $request->effective_to,
                'is_permanent' => $request->is_permanent,
            ]);

            DB::commit();

            return redirect()
                ->route('tenant.payroll.shifts.assignments', $tenant)
                ->with('success', 'Employee assigned to shift successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to assign shift: ' . $e->getMessage());
        }
    }

    public function endAssignment(Request $request, Tenant $tenant, $id)
    {
        $request->validate([
            'effective_to' => 'required|date',
        ]);

        $assignment = EmployeeShiftAssignment::where('id', $id)
            ->whereHas('employee', function($q) use ($tenant) {
                $q->where('tenant_id', $tenant->id);
            })
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $assignment->end($request->effective_to);
            DB::commit();

            return back()->with('success', 'Shift assignment ended successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to end assignment: ' . $e->getMessage());
        }
    }

    public function bulkAssign(Request $request, Tenant $tenant)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
            'shift_id' => 'required|exists:shift_schedules,id',
            'effective_from' => 'required|date',
            'is_permanent' => 'required|boolean',
            'effective_to' => 'nullable|date|after:effective_from',
        ]);

        $tenantId = $tenant->id;
        $success = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($request->employee_ids as $employeeId) {
                $employee = Employee::where('id', $employeeId)
                    ->where('tenant_id', $tenantId)
                    ->first();

                if (!$employee) {
                    $errors[] = "Employee #$employeeId not found";
                    continue;
                }

                // End current assignment
                $currentAssignment = EmployeeShiftAssignment::where('employee_id', $employee->id)
                    ->whereNull('effective_to')
                    ->first();

                if ($currentAssignment) {
                    $currentAssignment->effective_to = \Carbon\Carbon::parse($request->effective_from)
                        ->subDay()
                        ->toDateString();
                    $currentAssignment->save();
                }

                // Create new assignment (include tenant_id)
                EmployeeShiftAssignment::create([
                    'tenant_id' => $tenantId,
                    'employee_id' => $employee->id,
                    'shift_id' => $request->shift_id,
                    'effective_from' => $request->effective_from,
                    'effective_to' => $request->is_permanent ? null : $request->effective_to,
                    'is_permanent' => $request->is_permanent,
                ]);

                $success++;
            }

            DB::commit();

            $message = "$success employee(s) assigned to shift successfully.";
            if (count($errors) > 0) {
                $message .= ' Errors: ' . implode(', ', $errors);
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to assign employees: ' . $e->getMessage());
        }
    }
}
