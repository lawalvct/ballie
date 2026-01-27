<?php

namespace App\Http\Controllers\Api\Tenant\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    /**
     * List departments.
     */
    public function index(Request $request, Tenant $tenant)
    {
        $query = Department::where('tenant_id', $tenant->id)
            ->withCount('employees')
            ->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->get('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        $perPage = (int) $request->get('per_page', 50);
        $departments = $query->paginate($perPage);

        $departments->getCollection()->transform(function (Department $department) {
            return $this->formatDepartment($department);
        });

        return response()->json([
            'success' => true,
            'message' => 'Departments retrieved successfully',
            'data' => $departments,
        ]);
    }

    /**
     * Create a department.
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

        $department = Department::create([
            'tenant_id' => $tenant->id,
            'name' => $request->get('name'),
            'code' => $request->get('code'),
            'description' => $request->get('description'),
            'is_active' => $request->has('is_active')
                ? filter_var($request->get('is_active'), FILTER_VALIDATE_BOOLEAN)
                : true,
        ]);

        $department->loadCount('employees');

        return response()->json([
            'success' => true,
            'message' => 'Department created successfully',
            'data' => [
                'department' => $this->formatDepartment($department),
            ],
        ], 201);
    }

    /**
     * Show a department.
     */
    public function show(Tenant $tenant, Department $department)
    {
        if ($department->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found',
            ], 404);
        }

        $department->loadCount('employees');

        return response()->json([
            'success' => true,
            'message' => 'Department retrieved successfully',
            'data' => [
                'department' => $this->formatDepartment($department),
            ],
        ]);
    }

    /**
     * Update a department.
     */
    public function update(Request $request, Tenant $tenant, Department $department)
    {
        if ($department->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), $this->rules($tenant->id, $department->id));

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $department->update([
            'name' => $request->get('name'),
            'code' => $request->get('code'),
            'description' => $request->get('description'),
            'is_active' => $request->has('is_active')
                ? filter_var($request->get('is_active'), FILTER_VALIDATE_BOOLEAN)
                : $department->is_active,
        ]);

        $department->loadCount('employees');

        return response()->json([
            'success' => true,
            'message' => 'Department updated successfully',
            'data' => [
                'department' => $this->formatDepartment($department),
            ],
        ]);
    }

    /**
     * Delete a department.
     */
    public function destroy(Tenant $tenant, Department $department)
    {
        if ($department->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found',
            ], 404);
        }

        if ($department->employees()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Department has employees and cannot be deleted.',
            ], 409);
        }

        $department->delete();

        return response()->json([
            'success' => true,
            'message' => 'Department deleted successfully',
        ]);
    }

    private function rules(int $tenantId, ?int $departmentId = null): array
    {
        $codeRule = 'required|string|max:10|unique:departments,code';
        if ($departmentId) {
            $codeRule .= ',' . $departmentId . ',id,tenant_id,' . $tenantId;
        }

        return [
            'name' => 'required|string|max:255',
            'code' => $codeRule,
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }

    private function formatDepartment(Department $department): array
    {
        return [
            'id' => $department->id,
            'name' => $department->name,
            'code' => $department->code,
            'description' => $department->description,
            'is_active' => (bool) $department->is_active,
            'employees_count' => $department->employees_count ?? $department->employees()->count(),
            'created_at' => $department->created_at?->toDateTimeString(),
            'updated_at' => $department->updated_at?->toDateTimeString(),
        ];
    }
}
