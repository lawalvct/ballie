<?php

namespace App\Http\Controllers\Api\Tenant\Payroll;

use App\Http\Controllers\Controller;
use App\Models\SalaryComponent;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SalaryComponentController extends Controller
{
    /**
     * List salary components.
     */
    public function index(Request $request, Tenant $tenant)
    {
        $query = SalaryComponent::where('tenant_id', $tenant->id)
            ->orderBy('type')
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->get('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->get('per_page', 100);
        $components = $query->paginate($perPage);

        $components->getCollection()->transform(function (SalaryComponent $component) {
            return $this->formatComponent($component);
        });

        return response()->json([
            'success' => true,
            'message' => 'Salary components retrieved successfully',
            'data' => $components,
        ]);
    }

    /**
     * Create a salary component.
     */
    public function store(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), $this->rules());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $component = SalaryComponent::create([
            'tenant_id' => $tenant->id,
            'name' => $request->get('name'),
            'code' => $request->get('code'),
            'type' => $request->get('type'),
            'calculation_type' => $request->get('calculation_type'),
            'is_taxable' => (bool) $request->get('is_taxable', false),
            'is_pensionable' => (bool) $request->get('is_pensionable', false),
            'description' => $request->get('description'),
            'is_active' => $request->has('is_active')
                ? filter_var($request->get('is_active'), FILTER_VALIDATE_BOOLEAN)
                : true,
            'sort_order' => SalaryComponent::where('tenant_id', $tenant->id)->max('sort_order') + 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Salary component created successfully',
            'data' => [
                'component' => $this->formatComponent($component),
            ],
        ], 201);
    }

    /**
     * Show a salary component.
     */
    public function show(Tenant $tenant, SalaryComponent $component)
    {
        if ($component->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Salary component not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Salary component retrieved successfully',
            'data' => [
                'component' => $this->formatComponent($component),
            ],
        ]);
    }

    /**
     * Update a salary component.
     */
    public function update(Request $request, Tenant $tenant, SalaryComponent $component)
    {
        if ($component->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Salary component not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), $this->rules($component->id));

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $component->update([
            'name' => $request->get('name'),
            'code' => $request->get('code'),
            'type' => $request->get('type'),
            'calculation_type' => $request->get('calculation_type'),
            'is_taxable' => (bool) $request->get('is_taxable', $component->is_taxable),
            'is_pensionable' => (bool) $request->get('is_pensionable', $component->is_pensionable),
            'description' => $request->get('description'),
            'is_active' => $request->has('is_active')
                ? filter_var($request->get('is_active'), FILTER_VALIDATE_BOOLEAN)
                : $component->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Salary component updated successfully',
            'data' => [
                'component' => $this->formatComponent($component),
            ],
        ]);
    }

    /**
     * Delete a salary component.
     */
    public function destroy(Tenant $tenant, SalaryComponent $component)
    {
        if ($component->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Salary component not found',
            ], 404);
        }

        $assignedCount = $component->employeeSalaryComponents()->count();
        if ($assignedCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Salary component is assigned to employees and cannot be deleted.',
            ], 409);
        }

        $component->delete();

        return response()->json([
            'success' => true,
            'message' => 'Salary component deleted successfully',
        ]);
    }

    private function rules(?int $componentId = null): array
    {
        $codeRule = 'required|string|max:10|unique:salary_components,code';
        if ($componentId) {
            $codeRule .= ',' . $componentId . ',id';
        }

        return [
            'name' => 'required|string|max:255',
            'code' => $codeRule,
            'type' => 'required|in:earning,deduction,employer_contribution',
            'calculation_type' => 'required|in:fixed,percentage,variable,computed',
            'is_taxable' => 'nullable|boolean',
            'is_pensionable' => 'nullable|boolean',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }

    private function formatComponent(SalaryComponent $component): array
    {
        return [
            'id' => $component->id,
            'name' => $component->name,
            'code' => $component->code,
            'type' => $component->type,
            'calculation_type' => $component->calculation_type,
            'is_taxable' => (bool) $component->is_taxable,
            'is_pensionable' => (bool) $component->is_pensionable,
            'description' => $component->description,
            'is_active' => (bool) $component->is_active,
            'sort_order' => $component->sort_order,
            'created_at' => $component->created_at?->toDateTimeString(),
            'updated_at' => $component->updated_at?->toDateTimeString(),
        ];
    }
}
