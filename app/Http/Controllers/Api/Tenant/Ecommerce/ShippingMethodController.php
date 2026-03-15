<?php

namespace App\Http\Controllers\Api\Tenant\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class ShippingMethodController extends Controller
{
    /**
     * List all shipping methods
     */
    public function index(Request $request, Tenant $tenant)
    {
        try {
            $methods = ShippingMethod::where('tenant_id', $tenant->id)
                ->orderBy('name')
                ->get()
                ->map(fn($method) => [
                    'id' => $method->id,
                    'name' => $method->name,
                    'description' => $method->description,
                    'cost' => (float) $method->cost,
                    'estimated_days' => $method->estimated_days,
                    'is_active' => (bool) $method->is_active,
                    'created_at' => $method->created_at->toIso8601String(),
                ]);

            return response()->json([
                'success' => true,
                'data' => $methods,
            ]);
        } catch (Exception $e) {
            Log::error('Shipping methods list API error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to load shipping methods.',
            ], 500);
        }
    }

    /**
     * Store new shipping method
     */
    public function store(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
            'estimated_days' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            $validated['tenant_id'] = $tenant->id;
            $validated['is_active'] = $request->boolean('is_active', true);

            $method = ShippingMethod::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Shipping method created successfully.',
                'data' => [
                    'id' => $method->id,
                    'name' => $method->name,
                    'description' => $method->description,
                    'cost' => (float) $method->cost,
                    'estimated_days' => $method->estimated_days,
                    'is_active' => (bool) $method->is_active,
                    'created_at' => $method->created_at->toIso8601String(),
                ],
            ], 201);
        } catch (Exception $e) {
            Log::error('Shipping method store API error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create shipping method.',
            ], 500);
        }
    }

    /**
     * Show shipping method
     */
    public function show(Request $request, Tenant $tenant, $methodId)
    {
        try {
            $method = ShippingMethod::where('tenant_id', $tenant->id)->findOrFail($methodId);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $method->id,
                    'name' => $method->name,
                    'description' => $method->description,
                    'cost' => (float) $method->cost,
                    'estimated_days' => $method->estimated_days,
                    'is_active' => (bool) $method->is_active,
                    'created_at' => $method->created_at->toIso8601String(),
                    'updated_at' => $method->updated_at->toIso8601String(),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Shipping method not found.',
            ], 404);
        }
    }

    /**
     * Update shipping method
     */
    public function update(Request $request, Tenant $tenant, $methodId)
    {
        $method = ShippingMethod::where('tenant_id', $tenant->id)->findOrFail($methodId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
            'estimated_days' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            $validated['is_active'] = $request->boolean('is_active', $method->is_active);
            $method->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Shipping method updated successfully.',
                'data' => [
                    'id' => $method->id,
                    'name' => $method->name,
                    'description' => $method->description,
                    'cost' => (float) $method->cost,
                    'estimated_days' => $method->estimated_days,
                    'is_active' => (bool) $method->is_active,
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Shipping method update API error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update shipping method.',
            ], 500);
        }
    }

    /**
     * Delete shipping method
     */
    public function destroy(Request $request, Tenant $tenant, $methodId)
    {
        $method = ShippingMethod::where('tenant_id', $tenant->id)->findOrFail($methodId);

        try {
            $method->delete();

            return response()->json([
                'success' => true,
                'message' => 'Shipping method deleted successfully.',
            ]);
        } catch (Exception $e) {
            Log::error('Shipping method delete API error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete shipping method.',
            ], 500);
        }
    }

    /**
     * Toggle shipping method active status
     */
    public function toggle(Request $request, Tenant $tenant, $methodId)
    {
        $method = ShippingMethod::where('tenant_id', $tenant->id)->findOrFail($methodId);

        try {
            $method->update(['is_active' => !$method->is_active]);

            return response()->json([
                'success' => true,
                'message' => 'Shipping method status updated.',
                'data' => [
                    'is_active' => (bool) $method->is_active,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle status.',
            ], 500);
        }
    }
}
