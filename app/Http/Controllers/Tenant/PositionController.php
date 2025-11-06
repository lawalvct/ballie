<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\Department;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PositionController extends Controller
{
    /**
     * Display a listing of the positions.
     */
    public function index(Tenant $tenant)
    {
        $positions = Position::where('tenant_id', $tenant->id)
            ->with(['department', 'reportsTo', 'employees'])
            ->withCount('employees')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        $departments = Department::where('tenant_id', $tenant->id)
            ->active()
            ->orderBy('name')
            ->get();

        return view('tenant.payroll.positions.index', compact('tenant', 'positions', 'departments'));
    }

    /**
     * Show the form for creating a new position.
     */
    public function create(Tenant $tenant)
    {
        $departments = Department::where('tenant_id', $tenant->id)
            ->active()
            ->orderBy('name')
            ->get();

        $parentPositions = Position::where('tenant_id', $tenant->id)
            ->active()
            ->orderBy('name')
            ->get();

        return view('tenant.payroll.positions.create', compact('tenant', 'departments', 'parentPositions'));
    }

    /**
     * Store a newly created position in storage.
     */
    public function store(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:positions,code',
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'level' => 'required|integer|min:1|max:10',
            'reports_to_position_id' => 'nullable|exists:positions,id',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gte:min_salary',
            'requirements' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            // Return JSON for AJAX requests
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $data['tenant_id'] = $tenant->id;
        $data['is_active'] = $request->has('is_active') ? true : ($request->input('is_active') ?? true);

        $position = Position::create($data);

        // Return JSON for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Position created successfully.',
                'id' => $position->id,
                'position' => $position
            ]);
        }

        return redirect()->route('tenant.payroll.positions.index', $tenant)
            ->with('success', 'Position created successfully.');
    }

    /**
     * Display the specified position.
     */
    public function show(Tenant $tenant, Position $position)
    {
        $position->load(['department', 'reportsTo', 'subordinates', 'employees.department']);

        return view('tenant.payroll.positions.show', compact('tenant', 'position'));
    }

    /**
     * Show the form for editing the specified position.
     */
    public function edit(Tenant $tenant, Position $position)
    {
        $departments = Department::where('tenant_id', $tenant->id)
            ->active()
            ->orderBy('name')
            ->get();

        $parentPositions = Position::where('tenant_id', $tenant->id)
            ->where('id', '!=', $position->id)
            ->active()
            ->orderBy('name')
            ->get();

        return view('tenant.payroll.positions.edit', compact('tenant', 'position', 'departments', 'parentPositions'));
    }

    /**
     * Update the specified position in storage.
     */
    public function update(Request $request, Tenant $tenant, Position $position)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:positions,code,' . $position->id,
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'level' => 'required|integer|min:1|max:10',
            'reports_to_position_id' => 'nullable|exists:positions,id',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gte:min_salary',
            'requirements' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Prevent self-referencing
        if ($request->reports_to_position_id == $position->id) {
            return redirect()->back()
                ->withErrors(['reports_to_position_id' => 'A position cannot report to itself.'])
                ->withInput();
        }

        $data = $validator->validated();
        $data['is_active'] = $request->has('is_active');

        $position->update($data);

        return redirect()->route('tenant.payroll.positions.index', $tenant)
            ->with('success', 'Position updated successfully.');
    }

    /**
     * Remove the specified position from storage.
     */
    public function destroy(Tenant $tenant, Position $position)
    {
        if ($position->hasEmployees()) {
            return redirect()->back()
                ->with('error', 'Cannot delete position with assigned employees. Please reassign employees first.');
        }

        if ($position->subordinates()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete position with subordinate positions. Please reassign subordinates first.');
        }

        $position->delete();

        return redirect()->route('tenant.payroll.positions.index', $tenant)
            ->with('success', 'Position deleted successfully.');
    }

    /**
     * Get positions by department (AJAX).
     */
    public function byDepartment(Tenant $tenant, Request $request)
    {
        $departmentId = $request->input('department_id');

        $positions = Position::where('tenant_id', $tenant->id)
            ->where('department_id', $departmentId)
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'level']);

        return response()->json($positions);
    }

    /**
     * Toggle position status.
     */
    public function toggleStatus(Tenant $tenant, Position $position)
    {
        $position->update(['is_active' => !$position->is_active]);

        $status = $position->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Position {$status} successfully.");
    }
}
