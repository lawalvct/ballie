<?php

namespace App\Http\Controllers\Api\Tenant\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Position;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PositionController extends Controller
{
    /**
     * List positions.
     */
    public function index(Request $request, Tenant $tenant)
    {
        $query = Position::where('tenant_id', $tenant->id)
            ->with(['department', 'reportsTo'])
            ->withCount('employees')
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->get('department_id'));
        }

        if ($request->filled('level')) {
            $query->where('level', $request->get('level'));
        }

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
        $positions = $query->paginate($perPage);

        $positions->getCollection()->transform(function (Position $position) {
            return $this->formatPosition($position);
        });

        return response()->json([
            'success' => true,
            'message' => 'Positions retrieved successfully',
            'data' => $positions,
        ]);
    }

    /**
     * Create a position.
     */
    public function store(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), $this->rules($tenant->id));

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['tenant_id'] = $tenant->id;
        $data['is_active'] = $request->has('is_active')
            ? (bool) $request->get('is_active')
            : ($request->input('is_active') ?? true);

        if (!empty($data['reports_to_position_id']) && $data['reports_to_position_id'] == $data['id'] ?? null) {
            return response()->json([
                'success' => false,
                'message' => 'A position cannot report to itself.',
            ], 422);
        }

        $position = Position::create($data);
        $position->load(['department', 'reportsTo']);
        $position->loadCount('employees');

        return response()->json([
            'success' => true,
            'message' => 'Position created successfully',
            'data' => [
                'position' => $this->formatPosition($position),
            ],
        ], 201);
    }

    /**
     * Show a position.
     */
    public function show(Tenant $tenant, Position $position)
    {
        if ($position->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Position not found',
            ], 404);
        }

        $position->load(['department', 'reportsTo', 'subordinates', 'employees']);
        $position->loadCount('employees');

        return response()->json([
            'success' => true,
            'message' => 'Position retrieved successfully',
            'data' => [
                'position' => $this->formatPosition($position, true),
            ],
        ]);
    }

    /**
     * Update a position.
     */
    public function update(Request $request, Tenant $tenant, Position $position)
    {
        if ($position->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Position not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), $this->rules($tenant->id, $position->id));

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($request->get('reports_to_position_id') == $position->id) {
            return response()->json([
                'success' => false,
                'message' => 'A position cannot report to itself.',
            ], 422);
        }

        $data = $validator->validated();
        $data['is_active'] = $request->has('is_active')
            ? (bool) $request->get('is_active')
            : $position->is_active;

        $position->update($data);
        $position->load(['department', 'reportsTo']);
        $position->loadCount('employees');

        return response()->json([
            'success' => true,
            'message' => 'Position updated successfully',
            'data' => [
                'position' => $this->formatPosition($position),
            ],
        ]);
    }

    /**
     * Delete a position.
     */
    public function destroy(Tenant $tenant, Position $position)
    {
        if ($position->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Position not found',
            ], 404);
        }

        if ($position->hasEmployees()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete position with assigned employees. Please reassign employees first.',
            ], 409);
        }

        if ($position->subordinates()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete position with subordinate positions. Please reassign subordinates first.',
            ], 409);
        }

        $position->delete();

        return response()->json([
            'success' => true,
            'message' => 'Position deleted successfully',
        ]);
    }

    /**
     * Positions by department.
     */
    public function byDepartment(Request $request, Tenant $tenant)
    {
        $departmentId = $request->get('department_id');

        $positions = Position::where('tenant_id', $tenant->id)
            ->when($departmentId, function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'level']);

        return response()->json([
            'success' => true,
            'message' => 'Positions retrieved successfully',
            'data' => $positions,
        ]);
    }

    private function rules(int $tenantId, ?int $positionId = null): array
    {
        $codeRule = 'required|string|max:50|unique:positions,code';
        if ($positionId) {
            $codeRule .= ',' . $positionId . ',id';
        }

        return [
            'name' => 'required|string|max:255',
            'code' => $codeRule,
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'level' => 'required|integer|min:1|max:10',
            'reports_to_position_id' => 'nullable|exists:positions,id',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gte:min_salary',
            'requirements' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    private function formatPosition(Position $position, bool $withRelations = false): array
    {
        $data = [
            'id' => $position->id,
            'name' => $position->name,
            'code' => $position->code,
            'description' => $position->description,
            'department_id' => $position->department_id,
            'department_name' => $position->department?->name,
            'level' => $position->level,
            'level_name' => $position->level_name ?? 'Level ' . $position->level,
            'reports_to_position_id' => $position->reports_to_position_id,
            'reports_to_name' => $position->reportsTo?->name,
            'min_salary' => (float) ($position->min_salary ?? 0),
            'max_salary' => (float) ($position->max_salary ?? 0),
            'salary_range' => $position->salary_range ?? null,
            'requirements' => $position->requirements,
            'responsibilities' => $position->responsibilities,
            'is_active' => (bool) $position->is_active,
            'sort_order' => $position->sort_order,
            'employees_count' => $position->employees_count ?? $position->employees()->count(),
            'created_at' => $position->created_at?->toDateTimeString(),
            'updated_at' => $position->updated_at?->toDateTimeString(),
        ];

        if ($withRelations) {
            $data['subordinates'] = $position->subordinates->map(function (Position $subordinate) {
                return [
                    'id' => $subordinate->id,
                    'name' => $subordinate->name,
                    'code' => $subordinate->code,
                    'level' => $subordinate->level,
                ];
            })->values();
        }

        return $data;
    }
}
