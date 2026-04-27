<?php

namespace App\Http\Controllers\Tenant\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreUnitRequest;
use App\Http\Requests\Tenant\UpdateUnitRequest;
use App\Models\Unit;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnitController extends Controller
{
    /**
     * Display a listing of the units.
     */
    public function index(Request $request, Tenant $tenant)
    {
        $query = Unit::forTenant($tenant->id)
            ->with(['baseUnit', 'derivedUnits'])
            ->withCount('products');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('symbol', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            if ($request->get('type') === 'base') {
                $query->baseUnits();
            } elseif ($request->get('type') === 'derived') {
                $query->where('is_base_unit', false);
            }
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->get('status') === 'active') {
                $query->active();
            } elseif ($request->get('status') === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Sort
        $sortBy = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');

        if (in_array($sortBy, ['name', 'symbol', 'is_base_unit', 'is_active', 'created_at'])) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $units = $query->paginate(30)->withQueryString();

        // Statistics
        $totalUnits = Unit::forTenant($tenant->id)->count();
        $activeUnits = Unit::forTenant($tenant->id)->active()->count();
        $baseUnits = Unit::forTenant($tenant->id)->baseUnits()->count();
        $derivedUnits = Unit::forTenant($tenant->id)->where('is_base_unit', false)->count();

        return view('tenant.inventory.units.index', compact(
            'units',
            'tenant',
            'totalUnits',
            'activeUnits',
            'baseUnits',
            'derivedUnits'
        ));
    }

    /**
     * Show the form for creating a new unit.
     */
    public function create(Tenant $tenant)
    {
        $baseUnits = Unit::forTenant($tenant->id)->baseUnits()->active()->get();

        return view('tenant.inventory.units.create', compact('tenant', 'baseUnits'));
    }

    /**
     * Store a newly created unit in storage.
     */
    public function store(StoreUnitRequest $request, Tenant $tenant)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();
            $validated['tenant_id'] = $tenant->id;

            // If it's a base unit, ensure base_unit_id is null and conversion_factor is 1
            if ($validated['is_base_unit']) {
                $validated['base_unit_id'] = null;
                $validated['conversion_factor'] = 1.0;
            }

            $unit = Unit::create($validated);

            DB::commit();

            return redirect()
                ->route('tenant.inventory.units.index', ['tenant' => $tenant->slug])
                ->with('success', 'Unit created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create unit. Please try again.');
        }
    }

    /**
     * Display the specified unit.
     */
    public function show(Tenant $tenant, Unit $unit)
    {
        $this->authorize('view', $unit);

        $unit->load(['baseUnit', 'derivedUnits', 'products']);

        return view('tenant.inventory.units.show', compact('tenant', 'unit'));
    }

    /**
     * Show the form for editing the specified unit.
     */
    public function edit(Tenant $tenant, Unit $unit)
    {
        $this->authorize('update', $unit);

        $baseUnits = Unit::forTenant($tenant->id)
            ->baseUnits()
            ->active()
            ->where('id', '!=', $unit->id)
            ->get();

        return view('tenant.inventory.units.edit', compact('tenant', 'unit', 'baseUnits'));
    }

    /**
     * Update the specified unit in storage.
     */
    public function update(UpdateUnitRequest $request, Tenant $tenant, Unit $unit)
    {
        $this->authorize('update', $unit);

        try {
            DB::beginTransaction();

            $validated = $request->validated();

            // If it's a base unit, ensure base_unit_id is null and conversion_factor is 1
            if ($validated['is_base_unit']) {
                $validated['base_unit_id'] = null;
                $validated['conversion_factor'] = 1.0;
            }

            $unit->update($validated);

            DB::commit();

            return redirect()
                ->route('tenant.inventory.units.index', ['tenant' => $tenant->slug])
                ->with('success', 'Unit updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update unit. Please try again.');
        }
    }

    /**
     * Remove the specified unit from storage.
     */
    public function destroy(Request $request, Tenant $tenant, Unit $unit)
    {
        $this->authorize('delete', $unit);

        try {
            DB::beginTransaction();

            // Check if unit is being used by products
            if ($unit->products()->count() > 0) {
                DB::rollBack();

                $message = 'Cannot delete unit. It is being used by products.';

                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return redirect()
                    ->back()
                    ->with('error', $message);
            }

            // Check if unit has derived units
            if ($unit->derivedUnits()->count() > 0) {
                DB::rollBack();

                $message = 'Cannot delete unit. It has derived units.';

                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return redirect()
                    ->back()
                    ->with('error', $message);
            }

            $unit->delete();

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Unit deleted successfully.',
                ]);
            }

            return redirect()
                ->route('tenant.inventory.units.index', ['tenant' => $tenant->slug])
                ->with('success', 'Unit deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Failed to delete unit. Please try again.');
        }
    }

    /**
     * Toggle the status of the specified unit.
     */
    public function toggleStatus(Request $request, Tenant $tenant, Unit $unit)
    {
        $this->authorize('update', $unit);

        try {
            $unit->update(['is_active' => !$unit->is_active]);

            $status = $unit->is_active ? 'activated' : 'deactivated';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'is_active' => $unit->is_active,
                    'status' => $unit->status,
                    'status_color' => $unit->status_color,
                    'message' => "Unit {$status} successfully.",
                ]);
            }

            return redirect()
                ->back()
                ->with('success', "Unit {$status} successfully.");

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to update unit status. Please try again.');
        }
    }
}
