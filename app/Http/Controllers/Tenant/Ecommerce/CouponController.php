<?php

namespace App\Http\Controllers\Tenant\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $tenant = tenant();

        $query = Coupon::where('tenant_id', $tenant->id)
            ->withCount('usages');

        // Filters
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'expired') {
                $query->where('valid_to', '<', now());
            }
        }

        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        $coupons = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('tenant.ecommerce.coupons.index', compact('tenant', 'coupons'));
    }

    public function create(Request $request)
    {
        $tenant = tenant();

        return view('tenant.ecommerce.coupons.create', compact('tenant'));
    }

    public function store(Request $request)
    {
        $tenant = tenant();

        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:coupons,code,NULL,id,tenant_id,' . $tenant->id,
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'per_customer_limit' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['tenant_id'] = $tenant->id;
        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->has('is_active');
        $validated['usage_count'] = 0;

        Coupon::create($validated);

        return redirect()->route('tenant.ecommerce.coupons.index', ['tenant' => $tenant->slug])
            ->with('success', 'Coupon created successfully!');
    }

    public function edit(Request $request, $id)
    {
        $tenant = tenant();

        $coupon = Coupon::where('tenant_id', $tenant->id)->findOrFail($id);

        return view('tenant.ecommerce.coupons.edit', compact('tenant', 'coupon'));
    }

    public function update(Request $request, $id)
    {
        $tenant = tenant();

        $coupon = Coupon::where('tenant_id', $tenant->id)->findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:coupons,code,' . $id . ',id,tenant_id,' . $tenant->id,
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'per_customer_limit' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->has('is_active');

        $coupon->update($validated);

        return redirect()->route('tenant.ecommerce.coupons.index', ['tenant' => $tenant->slug])
            ->with('success', 'Coupon updated successfully!');
    }

    public function destroy(Request $request, $id)
    {
        $tenant = tenant();

        $coupon = Coupon::where('tenant_id', $tenant->id)->findOrFail($id);

        // Check if coupon has been used
        if ($coupon->usage_count > 0) {
            return redirect()->back()->with('error', 'Cannot delete coupon that has been used!');
        }

        $coupon->delete();

        return redirect()->route('tenant.ecommerce.coupons.index', ['tenant' => $tenant->slug])
            ->with('success', 'Coupon deleted successfully!');
    }

    public function toggle(Request $request, $id)
    {
        $tenant = tenant();

        $coupon = Coupon::where('tenant_id', $tenant->id)->findOrFail($id);

        $coupon->update(['is_active' => !$coupon->is_active]);

        return redirect()->back()->with('success', 'Coupon status updated!');
    }
}
