<?php

namespace App\Http\Controllers\Tenant\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockLocation;
use App\Models\StockMovement;
use App\Models\Tenant;
use App\Services\ModuleRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StockLocationController extends Controller
{
    private function ensureModuleEnabled(Tenant $tenant): void
    {
        if (!ModuleRegistry::isModuleEnabled($tenant, 'stock_locations')) {
            abort(403, 'Stock Locations module is not enabled for this tenant.');
        }
    }

    public function index(Request $request, Tenant $tenant)
    {
        $this->ensureModuleEnabled($tenant);

        // Make sure the tenant has at least the default Store
        StockLocation::ensureMainForTenant($tenant->id);

        $locations = StockLocation::where('tenant_id', $tenant->id)
            ->orderByDesc('is_main')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('tenant.inventory.stock-locations.index', compact('tenant', 'locations'));
    }

    public function create(Request $request, Tenant $tenant)
    {
        $this->ensureModuleEnabled($tenant);

        $types = StockLocation::TYPES;
        return view('tenant.inventory.stock-locations.create', compact('tenant', 'types'));
    }

    public function store(Request $request, Tenant $tenant)
    {
        $this->ensureModuleEnabled($tenant);

        $validated = $this->validateLocation($request, $tenant);

        $validated['tenant_id'] = $tenant->id;
        $validated['created_by'] = Auth::id();
        $validated['is_active'] = (bool) ($validated['is_active'] ?? true);
        $validated['is_main'] = (bool) ($validated['is_main'] ?? false);
        $validated['is_wip'] = (bool) ($validated['is_wip'] ?? false);

        StockLocation::create($validated);

        return redirect()
            ->route('tenant.inventory.stock-locations.index', ['tenant' => $tenant->slug])
            ->with('success', 'Stock location created successfully.');
    }

    public function edit(Request $request, Tenant $tenant, StockLocation $stockLocation)
    {
        $this->ensureModuleEnabled($tenant);
        $this->ensureBelongsToTenant($tenant, $stockLocation);

        $types = StockLocation::TYPES;
        return view('tenant.inventory.stock-locations.edit', compact('tenant', 'stockLocation', 'types'));
    }

    public function update(Request $request, Tenant $tenant, StockLocation $stockLocation)
    {
        $this->ensureModuleEnabled($tenant);
        $this->ensureBelongsToTenant($tenant, $stockLocation);

        $validated = $this->validateLocation($request, $tenant, $stockLocation->id);
        $validated['updated_by'] = Auth::id();
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);
        $validated['is_main'] = (bool) ($validated['is_main'] ?? $stockLocation->is_main);
        $validated['is_wip'] = (bool) ($validated['is_wip'] ?? false);

        // Block deactivating the only main location
        if ($stockLocation->is_main && !$validated['is_active']) {
            return back()->withInput()->with('error', 'The main location cannot be deactivated.');
        }

        $stockLocation->update($validated);

        return redirect()
            ->route('tenant.inventory.stock-locations.index', ['tenant' => $tenant->slug])
            ->with('success', 'Stock location updated successfully.');
    }

    public function destroy(Request $request, Tenant $tenant, StockLocation $stockLocation)
    {
        $this->ensureModuleEnabled($tenant);
        $this->ensureBelongsToTenant($tenant, $stockLocation);

        if ($stockLocation->is_main) {
            return back()->with('error', 'The main location cannot be deleted.');
        }

        $hasMovements = StockMovement::where('tenant_id', $tenant->id)
            ->where(function ($q) use ($stockLocation) {
                $q->where('stock_location_id', $stockLocation->id)
                  ->orWhere('from_stock_location_id', $stockLocation->id)
                  ->orWhere('to_stock_location_id', $stockLocation->id);
            })
            ->exists();

        if ($hasMovements) {
            return back()->with('error', 'This location has stock movements and cannot be deleted. Deactivate it instead.');
        }

        $stockLocation->delete();

        return redirect()
            ->route('tenant.inventory.stock-locations.index', ['tenant' => $tenant->slug])
            ->with('success', 'Stock location deleted successfully.');
    }

    public function setMain(Request $request, Tenant $tenant, StockLocation $stockLocation)
    {
        $this->ensureModuleEnabled($tenant);
        $this->ensureBelongsToTenant($tenant, $stockLocation);

        $stockLocation->update([
            'is_main' => true,
            'is_active' => true,
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', "{$stockLocation->name} is now the main location.");
    }

    private function ensureBelongsToTenant(Tenant $tenant, StockLocation $location): void
    {
        if ($location->tenant_id !== $tenant->id) {
            abort(404);
        }
    }

    private function validateLocation(Request $request, Tenant $tenant, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => [
                'required', 'string', 'max:150',
                Rule::unique('stock_locations', 'name')
                    ->where(fn ($q) => $q->where('tenant_id', $tenant->id))
                    ->ignore($ignoreId),
            ],
            'code' => [
                'required', 'string', 'max:50',
                Rule::unique('stock_locations', 'code')
                    ->where(fn ($q) => $q->where('tenant_id', $tenant->id))
                    ->ignore($ignoreId),
            ],
            'type' => ['required', Rule::in(array_keys(StockLocation::TYPES))],
            'description' => ['nullable', 'string', 'max:500'],
            'is_main' => ['nullable', 'boolean'],
            'is_wip' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);
    }
}
