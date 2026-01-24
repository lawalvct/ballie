<?php

namespace App\Http\Controllers\Api\Tenant\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UnitController extends Controller
{
    /**
     * List units with filters and pagination.
     */
    public function index(Request $request, Tenant $tenant)
    {
        $query = Unit::forTenant($tenant->id)
            ->with(['baseUnit'])
            ->withCount(['products', 'derivedUnits']);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('symbol', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            if ($request->get('type') === 'base') {
                $query->baseUnits();
            } elseif ($request->get('type') === 'derived') {
                $query->where('is_base_unit', false);
            }
        }

        if ($request->filled('status')) {
            if ($request->get('status') === 'active') {
                $query->active();
            } elseif ($request->get('status') === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $sortBy = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        $allowedSorts = ['name', 'symbol', 'is_base_unit', 'is_active', 'created_at'];

        if (in_array($sortBy, $allowedSorts, true)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $perPage = (int) $request->get('per_page', 15);
        $units = $query->paginate($perPage);

        $units->getCollection()->transform(function (Unit $unit) {
            return $this->formatUnit($unit);
        });

        $statistics = [
            'total_units' => Unit::forTenant($tenant->id)->count(),
            'active_units' => Unit::forTenant($tenant->id)->active()->count(),
            'base_units' => Unit::forTenant($tenant->id)->baseUnits()->count(),
            'derived_units' => Unit::forTenant($tenant->id)->where('is_base_unit', false)->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Units retrieved successfully',
            'data' => $units,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Get create form data.
     */
    public function create(Request $request, Tenant $tenant)
    {
        $baseUnits = Unit::forTenant($tenant->id)
            ->baseUnits()
            ->active()
            ->orderBy('name')
            ->get()
            ->map(function (Unit $unit) {
                return [
                    'id' => $unit->id,
                    'name' => $unit->name,
                    'symbol' => $unit->symbol,
                    'display_name' => $unit->display_name,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Unit form data retrieved successfully',
            'data' => [
                'base_units' => $baseUnits,
            ],
        ]);
    }

    /**
     * Store a new unit.
     */
    public function store(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('units')->where(function ($query) use ($tenant) {
                    return $query->where('tenant_id', $tenant->id);
                }),
            ],
            'symbol' => [
                'required',
                'string',
                'max:10',
                Rule::unique('units')->where(function ($query) use ($tenant) {
                    return $query->where('tenant_id', $tenant->id);
                }),
            ],
            'description' => 'nullable|string|max:1000',
            'is_base_unit' => 'required|boolean',
            'base_unit_id' => [
                'nullable',
                'required_if:is_base_unit,false',
                'exists:units,id',
            ],
            'conversion_factor' => [
                'nullable',
                'required_if:is_base_unit,false',
                'numeric',
                'min:0.000001',
                'max:999999.999999',
            ],
            'is_active' => 'boolean',
        ]);

        $validator->after(function ($validator) use ($request, $tenant) {
            if (!$request->boolean('is_base_unit') && $request->filled('base_unit_id')) {
                $exists = Unit::where('id', $request->get('base_unit_id'))
                    ->where('tenant_id', $tenant->id)
                    ->where('is_base_unit', true)
                    ->exists();

                if (!$exists) {
                    $validator->errors()->add('base_unit_id', 'The selected base unit is invalid.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $validated['tenant_id'] = $tenant->id;
        $validated['is_base_unit'] = $request->boolean('is_base_unit');
        $validated['is_active'] = $request->boolean('is_active', true);

        if ($validated['is_base_unit']) {
            $validated['base_unit_id'] = null;
            $validated['conversion_factor'] = 1.0;
        }

        $unit = Unit::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Unit created successfully',
            'data' => [
                'unit' => $this->formatUnit($unit->fresh(['baseUnit'])->loadCount(['products', 'derivedUnits'])),
            ],
        ], 201);
    }

    /**
     * Show a unit.
     */
    public function show(Request $request, Tenant $tenant, Unit $unit)
    {
        if ($unit->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unit not found',
            ], 404);
        }

        $unit->load(['baseUnit', 'derivedUnits', 'products']);
        $unit->loadCount(['products', 'derivedUnits']);

        $products = $unit->products->take(10)->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'sales_rate' => (float) ($product->sales_rate ?? 0),
                'selling_price' => (float) ($product->selling_price ?? 0),
            ];
        })->values();

        $derivedUnits = $unit->derivedUnits->map(function (Unit $derived) {
            return [
                'id' => $derived->id,
                'name' => $derived->name,
                'symbol' => $derived->symbol,
                'conversion_factor' => (float) ($derived->conversion_factor ?? 0),
                'status' => $derived->status,
                'status_color' => $derived->status_color,
                'type_color' => $derived->type_color,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'Unit retrieved successfully',
            'data' => [
                'unit' => $this->formatUnit($unit),
                'derived_units' => $derivedUnits,
                'products' => $products,
                'products_count' => $unit->products_count,
            ],
        ]);
    }

    /**
     * Update a unit.
     */
    public function update(Request $request, Tenant $tenant, Unit $unit)
    {
        if ($unit->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unit not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('units')->where(function ($query) use ($tenant) {
                    return $query->where('tenant_id', $tenant->id);
                })->ignore($unit->id),
            ],
            'symbol' => [
                'required',
                'string',
                'max:10',
                Rule::unique('units')->where(function ($query) use ($tenant) {
                    return $query->where('tenant_id', $tenant->id);
                })->ignore($unit->id),
            ],
            'description' => 'nullable|string|max:1000',
            'is_base_unit' => 'required|boolean',
            'base_unit_id' => [
                'nullable',
                'required_if:is_base_unit,false',
                'exists:units,id',
                'different:' . $unit->id,
            ],
            'conversion_factor' => [
                'nullable',
                'required_if:is_base_unit,false',
                'numeric',
                'min:0.000001',
                'max:999999.999999',
            ],
            'is_active' => 'boolean',
        ]);

        $validator->after(function ($validator) use ($request, $tenant, $unit) {
            if (!$request->boolean('is_base_unit') && $request->filled('base_unit_id')) {
                $exists = Unit::where('id', $request->get('base_unit_id'))
                    ->where('tenant_id', $tenant->id)
                    ->where('is_base_unit', true)
                    ->exists();

                if (!$exists) {
                    $validator->errors()->add('base_unit_id', 'The selected base unit is invalid.');
                }
            }

            if ($request->filled('base_unit_id') && $request->get('base_unit_id') == $unit->id) {
                $validator->errors()->add('base_unit_id', 'A unit cannot be its own base unit.');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $validated['is_base_unit'] = $request->boolean('is_base_unit');
        $validated['is_active'] = $request->boolean('is_active', true);

        if ($validated['is_base_unit']) {
            $validated['base_unit_id'] = null;
            $validated['conversion_factor'] = 1.0;
        }

        $unit->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Unit updated successfully',
            'data' => [
                'unit' => $this->formatUnit($unit->fresh(['baseUnit'])->loadCount(['products', 'derivedUnits'])),
            ],
        ]);
    }

    /**
     * Toggle unit status.
     */
    public function toggleStatus(Request $request, Tenant $tenant, Unit $unit)
    {
        if ($unit->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unit not found',
            ], 404);
        }

        $unit->update(['is_active' => !$unit->is_active]);

        return response()->json([
            'success' => true,
            'message' => $unit->is_active ? 'Unit activated successfully' : 'Unit deactivated successfully',
            'data' => [
                'unit' => $this->formatUnit($unit->fresh(['baseUnit'])->loadCount(['products', 'derivedUnits'])),
            ],
        ]);
    }

    /**
     * Delete a unit.
     */
    public function destroy(Request $request, Tenant $tenant, Unit $unit)
    {
        if ($unit->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unit not found',
            ], 404);
        }

        try {
            $unit->delete();

            return response()->json([
                'success' => true,
                'message' => 'Unit deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    private function formatUnit(Unit $unit): array
    {
        return [
            'id' => $unit->id,
            'name' => $unit->name,
            'symbol' => $unit->symbol,
            'display_name' => $unit->display_name,
            'description' => $unit->description,
            'is_base_unit' => (bool) $unit->is_base_unit,
            'base_unit_id' => $unit->base_unit_id,
            'base_unit' => $unit->baseUnit ? [
                'id' => $unit->baseUnit->id,
                'name' => $unit->baseUnit->name,
                'symbol' => $unit->baseUnit->symbol,
            ] : null,
            'conversion_factor' => (float) ($unit->conversion_factor ?? 0),
            'is_active' => (bool) $unit->is_active,
            'status' => $unit->status,
            'status_color' => $unit->status_color,
            'type' => $unit->type,
            'type_color' => $unit->type_color,
            'products_count' => (int) ($unit->products_count ?? $unit->products()->count()),
            'derived_units_count' => (int) ($unit->derived_units_count ?? $unit->derivedUnits()->count()),
            'can_delete' => (($unit->products_count ?? $unit->products()->count()) == 0) && (($unit->derived_units_count ?? $unit->derivedUnits()->count()) == 0),
            'created_at' => $unit->created_at?->toDateTimeString(),
            'updated_at' => $unit->updated_at?->toDateTimeString(),
        ];
    }
}
