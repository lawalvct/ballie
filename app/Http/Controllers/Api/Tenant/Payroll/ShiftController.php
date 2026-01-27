<?php

namespace App\Http\Controllers\Api\Tenant\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeShiftAssignment;
use App\Models\ShiftSchedule;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ShiftController extends Controller
{
    /**
     * List shifts.
     */
    public function index(Request $request, Tenant $tenant)
    {
        $query = ShiftSchedule::where('tenant_id', $tenant->id)
            ->withCount('employeeAssignments')
            ->orderBy('name');

        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->get('per_page', 20);
        $shifts = $query->paginate($perPage);

        $shifts->getCollection()->transform(function (ShiftSchedule $shift) {
            return $this->formatShift($shift);
        });

        return response()->json([
            'success' => true,
            'message' => 'Shifts retrieved successfully',
            'data' => $shifts,
        ]);
    }

    /**
     * Create a shift.
     */
    public function store(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), $this->shiftRules($tenant->id));

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['tenant_id'] = $tenant->id;
        $data['is_active'] = $request->has('is_active')
            ? (bool) $request->get('is_active')
            : true;

        $shift = ShiftSchedule::create($data);
        $shift->loadCount('employeeAssignments');

        return response()->json([
            'success' => true,
            'message' => 'Shift created successfully',
            'data' => [
                'shift' => $this->formatShift($shift),
            ],
        ], 201);
    }

    /**
     * Show a shift (with assignments).
     */
    public function show(Request $request, Tenant $tenant, ShiftSchedule $shift)
    {
        if ($shift->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Shift not found',
            ], 404);
        }

        $shift->loadCount('employeeAssignments');

        $assignmentsQuery = EmployeeShiftAssignment::with(['employee.department', 'shift'])
            ->where('shift_id', $shift->id)
            ->orderBy('effective_from', 'desc');

        $perPage = (int) $request->get('per_page', 20);
        $assignments = $assignmentsQuery->paginate($perPage);

        $assignments->getCollection()->transform(function (EmployeeShiftAssignment $assignment) {
            return $this->formatAssignment($assignment);
        });

        return response()->json([
            'success' => true,
            'message' => 'Shift retrieved successfully',
            'data' => [
                'shift' => $this->formatShift($shift),
                'assignments' => $assignments,
            ],
        ]);
    }

    /**
     * Update a shift.
     */
    public function update(Request $request, Tenant $tenant, ShiftSchedule $shift)
    {
        if ($shift->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Shift not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), $this->shiftRules($tenant->id, $shift->id));

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['is_active'] = $request->has('is_active')
            ? (bool) $request->get('is_active')
            : $shift->is_active;

        $shift->update($data);
        $shift->loadCount('employeeAssignments');

        return response()->json([
            'success' => true,
            'message' => 'Shift updated successfully',
            'data' => [
                'shift' => $this->formatShift($shift),
            ],
        ]);
    }

    /**
     * Delete a shift.
     */
    public function destroy(Tenant $tenant, ShiftSchedule $shift)
    {
        if ($shift->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Shift not found',
            ], 404);
        }

        $activeAssignments = EmployeeShiftAssignment::where('shift_id', $shift->id)
            ->where(function ($q) {
                $q->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', now()->toDateString());
            })
            ->count();

        if ($activeAssignments > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete shift with active employee assignments.',
            ], 409);
        }

        $shift->delete();

        return response()->json([
            'success' => true,
            'message' => 'Shift deleted successfully',
        ]);
    }

    /**
     * List shift assignments.
     */
    public function assignments(Request $request, Tenant $tenant)
    {
        $query = EmployeeShiftAssignment::with(['employee.department', 'shift'])
            ->whereHas('employee', function ($q) use ($tenant) {
                $q->where('tenant_id', $tenant->id);
            });

        if ($request->filled('department_id')) {
            $departmentId = $request->get('department_id');
            $query->whereHas('employee', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->get('employee_id'));
        }

        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->get('shift_id'));
        }

        if ($request->filled('status')) {
            if ($request->get('status') === 'active') {
                $query->where(function ($q) {
                    $q->whereNull('effective_to')
                        ->orWhere('effective_to', '>=', now()->toDateString());
                })->where('effective_from', '<=', now()->toDateString());
            } elseif ($request->get('status') === 'ended') {
                $query->where('effective_to', '<', now()->toDateString());
            }
        }

        $perPage = (int) $request->get('per_page', 20);
        $assignments = $query->orderBy('effective_from', 'desc')->paginate($perPage);

        $assignments->getCollection()->transform(function (EmployeeShiftAssignment $assignment) {
            return $this->formatAssignment($assignment);
        });

        return response()->json([
            'success' => true,
            'message' => 'Shift assignments retrieved successfully',
            'data' => $assignments,
        ]);
    }

    /**
     * Assign a single employee to a shift.
     */
    public function storeAssignment(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), $this->assignmentRules());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $employee = Employee::where('id', $data['employee_id'])
            ->where('tenant_id', $tenant->id)
            ->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
            ], 404);
        }

        $shift = ShiftSchedule::where('id', $data['shift_id'])
            ->where('tenant_id', $tenant->id)
            ->first();

        if (!$shift) {
            return response()->json([
                'success' => false,
                'message' => 'Shift not found',
            ], 404);
        }

        return DB::transaction(function () use ($data, $employee, $shift, $tenant) {
            $currentAssignment = EmployeeShiftAssignment::where('employee_id', $employee->id)
                ->whereNull('effective_to')
                ->first();

            if ($currentAssignment) {
                $currentAssignment->effective_to = Carbon::parse($data['effective_from'])->subDay()->toDateString();
                $currentAssignment->is_active = false;
                $currentAssignment->save();
            }

            $assignment = EmployeeShiftAssignment::create([
                'tenant_id' => $tenant->id,
                'employee_id' => $employee->id,
                'shift_id' => $shift->id,
                'effective_from' => $data['effective_from'],
                'effective_to' => $data['is_permanent'] ? null : ($data['effective_to'] ?? null),
                'is_permanent' => $data['is_permanent'],
                'is_active' => true,
            ]);

            $assignment->load(['employee.department', 'shift']);

            return response()->json([
                'success' => true,
                'message' => 'Employee assigned to shift successfully',
                'data' => [
                    'assignment' => $this->formatAssignment($assignment),
                ],
            ], 201);
        });
    }

    /**
     * Bulk assign employees to a shift.
     */
    public function bulkAssign(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), $this->bulkAssignmentRules());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $shift = ShiftSchedule::where('id', $data['shift_id'])
            ->where('tenant_id', $tenant->id)
            ->first();

        if (!$shift) {
            return response()->json([
                'success' => false,
                'message' => 'Shift not found',
            ], 404);
        }

        $success = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($data['employee_ids'] as $employeeId) {
                $employee = Employee::where('id', $employeeId)
                    ->where('tenant_id', $tenant->id)
                    ->first();

                if (!$employee) {
                    $errors[] = "Employee #{$employeeId} not found";
                    continue;
                }

                $currentAssignment = EmployeeShiftAssignment::where('employee_id', $employee->id)
                    ->whereNull('effective_to')
                    ->first();

                if ($currentAssignment) {
                    $currentAssignment->effective_to = Carbon::parse($data['effective_from'])->subDay()->toDateString();
                    $currentAssignment->is_active = false;
                    $currentAssignment->save();
                }

                EmployeeShiftAssignment::create([
                    'tenant_id' => $tenant->id,
                    'employee_id' => $employee->id,
                    'shift_id' => $shift->id,
                    'effective_from' => $data['effective_from'],
                    'effective_to' => $data['is_permanent'] ? null : ($data['effective_to'] ?? null),
                    'is_permanent' => $data['is_permanent'],
                    'is_active' => true,
                ]);

                $success++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk assign employees: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Bulk assignment completed',
            'data' => [
                'success_count' => $success,
                'errors' => $errors,
            ],
        ]);
    }

    /**
     * End a shift assignment.
     */
    public function endAssignment(Request $request, Tenant $tenant, EmployeeShiftAssignment $assignment)
    {
        $validator = Validator::make($request->all(), [
            'effective_to' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($assignment->employee->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Assignment not found',
            ], 404);
        }

        $endDate = Carbon::parse($request->get('effective_to'));
        if ($endDate->lt($assignment->effective_from)) {
            return response()->json([
                'success' => false,
                'message' => 'End date cannot be earlier than effective from date.',
            ], 422);
        }

        $assignment->end($endDate);
        $assignment->load(['employee.department', 'shift']);

        return response()->json([
            'success' => true,
            'message' => 'Shift assignment ended successfully',
            'data' => [
                'assignment' => $this->formatAssignment($assignment),
            ],
        ]);
    }

    private function shiftRules(int $tenantId, ?int $shiftId = null): array
    {
        $codeRule = 'required|string|max:20|unique:shift_schedules,code';
        if ($shiftId) {
            $codeRule .= ',' . $shiftId . ',id';
        }

        return [
            'name' => 'required|string|max:100',
            'code' => $codeRule,
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'working_days' => 'required|array',
            'working_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'late_grace_minutes' => 'nullable|integer|min:0|max:60',
            'work_hours' => 'required|numeric|min:0',
            'shift_allowance' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ];
    }

    private function assignmentRules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'shift_id' => 'required|exists:shift_schedules,id',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'is_permanent' => 'required|boolean',
        ];
    }

    private function bulkAssignmentRules(): array
    {
        return [
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
            'shift_id' => 'required|exists:shift_schedules,id',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'is_permanent' => 'required|boolean',
        ];
    }

    private function formatShift(ShiftSchedule $shift): array
    {
        return [
            'id' => $shift->id,
            'name' => $shift->name,
            'code' => $shift->code,
            'description' => $shift->description,
            'start_time' => $shift->start_time?->format('H:i'),
            'end_time' => $shift->end_time?->format('H:i'),
            'time_range' => $shift->start_time && $shift->end_time
                ? $shift->start_time->format('g:i A') . ' - ' . $shift->end_time->format('g:i A')
                : null,
            'working_days' => $shift->working_days ?? [],
            'late_grace_minutes' => $shift->late_grace_minutes,
            'work_hours' => $shift->work_hours,
            'shift_allowance' => $shift->shift_allowance,
            'is_active' => (bool) $shift->is_active,
            'employees_count' => $shift->employee_assignments_count ?? 0,
            'created_at' => $shift->created_at?->toDateTimeString(),
            'updated_at' => $shift->updated_at?->toDateTimeString(),
        ];
    }

    private function formatAssignment(EmployeeShiftAssignment $assignment): array
    {
        return [
            'id' => $assignment->id,
            'employee_id' => $assignment->employee_id,
            'employee_name' => $assignment->employee?->full_name,
            'employee_number' => $assignment->employee?->employee_number,
            'department_id' => $assignment->employee?->department_id,
            'department_name' => $assignment->employee?->department?->name,
            'shift_id' => $assignment->shift_id,
            'shift_name' => $assignment->shift?->name,
            'shift_time' => $assignment->shift?->getFormattedTimeRange(),
            'effective_from' => $assignment->effective_from?->toDateString(),
            'effective_to' => $assignment->effective_to?->toDateString(),
            'is_permanent' => (bool) $assignment->is_permanent,
            'is_active' => (bool) $assignment->is_active,
            'status' => $assignment->isCurrentlyActive() ? 'active' : 'ended',
            'created_at' => $assignment->created_at?->toDateTimeString(),
            'updated_at' => $assignment->updated_at?->toDateTimeString(),
        ];
    }
}
