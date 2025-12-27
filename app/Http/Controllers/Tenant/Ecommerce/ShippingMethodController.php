<?php

namespace App\Http\Controllers\Tenant\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;

class ShippingMethodController extends Controller
{
    public function index(Request $request)
    {
        $tenant = tenant();

        $shippingMethods = ShippingMethod::where('tenant_id', $tenant->id)
            ->orderBy('name')
            ->get();

        return view('tenant.ecommerce.shipping-methods.index', compact('tenant', 'shippingMethods'));
    }

    public function create(Request $request)
    {
        $tenant = tenant();

        return view('tenant.ecommerce.shipping-methods.create', compact('tenant'));
    }

    public function store(Request $request)
    {
        $tenant = tenant();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
            'estimated_days' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['tenant_id'] = $tenant->id;
        $validated['is_active'] = $request->has('is_active');

        ShippingMethod::create($validated);

        return redirect()->route('tenant.ecommerce.shipping-methods.index', ['tenant' => $tenant->slug])
            ->with('success', 'Shipping method created successfully!');
    }

    public function edit(Request $request, $id)
    {
        $tenant = tenant();

        $shippingMethod = ShippingMethod::where('tenant_id', $tenant->id)
            ->findOrFail($id);

        return view('tenant.ecommerce.shipping-methods.edit', compact('tenant', 'shippingMethod'));
    }

    public function update(Request $request, $id)
    {
        $tenant = tenant();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
            'estimated_days' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $shippingMethod = ShippingMethod::where('tenant_id', $tenant->id)
            ->findOrFail($id);

        $shippingMethod->update($validated);

        return redirect()->route('tenant.ecommerce.shipping-methods.index', ['tenant' => $tenant->slug])
            ->with('success', 'Shipping method updated successfully!');
    }

    public function destroy(Request $request, $id)
    {
        $tenant = tenant();

        $shippingMethod = ShippingMethod::where('tenant_id', $tenant->id)
            ->findOrFail($id);

        $shippingMethod->delete();

        return redirect()->route('tenant.ecommerce.shipping-methods.index', ['tenant' => $tenant->slug])
            ->with('success', 'Shipping method deleted successfully!');
    }

    public function toggle(Request $request, $id)
    {
        $tenant = tenant();

        $shippingMethod = ShippingMethod::where('tenant_id', $tenant->id)
            ->findOrFail($id);

        $shippingMethod->update(['is_active' => !$shippingMethod->is_active]);

        return redirect()->back()->with('success', 'Shipping method status updated!');
    }
}
