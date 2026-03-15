<?php

namespace App\Http\Controllers\Api\Tenant\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class CouponController extends Controller
{
    /**
     * List coupons with filters and pagination
     */
    public function index(Request $request, Tenant $tenant)
    {
        try {
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

            $perPage = min($request->integer('per_page', 20), 50);
            $coupons = $query->latest()->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $coupons->map(fn($coupon) => [
                    'id' => $coupon->id,
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => (float) $coupon->value,
                    'min_order_amount' => $coupon->min_order_amount ? (float) $coupon->min_order_amount : null,
                    'max_discount_amount' => $coupon->max_discount_amount ? (float) $coupon->max_discount_amount : null,
                    'usage_limit' => $coupon->usage_limit,
                    'usage_count' => $coupon->usage_count,
                    'usages_count' => $coupon->usages_count,
                    'per_customer_limit' => $coupon->per_customer_limit,
                    'valid_from' => $coupon->valid_from?->toIso8601String(),
                    'valid_to' => $coupon->valid_to?->toIso8601String(),
                    'is_active' => (bool) $coupon->is_active,
                    'is_expired' => $coupon->valid_to && $coupon->valid_to->isPast(),
                    'created_at' => $coupon->created_at->toIso8601String(),
                ]),
                'pagination' => [
                    'current_page' => $coupons->currentPage(),
                    'last_page' => $coupons->lastPage(),
                    'per_page' => $coupons->perPage(),
                    'total' => $coupons->total(),
                    'from' => $coupons->firstItem(),
                    'to' => $coupons->lastItem(),
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Coupons list API error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to load coupons.',
            ], 500);
        }
    }

    /**
     * Store new coupon
     */
    public function store(Request $request, Tenant $tenant)
    {
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

        try {
            $validated['tenant_id'] = $tenant->id;
            $validated['code'] = strtoupper($validated['code']);
            $validated['is_active'] = $request->boolean('is_active', true);
            $validated['usage_count'] = 0;

            $coupon = Coupon::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Coupon created successfully.',
                'data' => [
                    'id' => $coupon->id,
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => (float) $coupon->value,
                    'is_active' => (bool) $coupon->is_active,
                    'valid_from' => $coupon->valid_from?->toIso8601String(),
                    'valid_to' => $coupon->valid_to?->toIso8601String(),
                    'created_at' => $coupon->created_at->toIso8601String(),
                ],
            ], 201);
        } catch (Exception $e) {
            Log::error('Coupon store API error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create coupon.',
            ], 500);
        }
    }

    /**
     * Show coupon details
     */
    public function show(Request $request, Tenant $tenant, $couponId)
    {
        try {
            $coupon = Coupon::where('tenant_id', $tenant->id)
                ->withCount('usages')
                ->findOrFail($couponId);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $coupon->id,
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => (float) $coupon->value,
                    'min_order_amount' => $coupon->min_order_amount ? (float) $coupon->min_order_amount : null,
                    'max_discount_amount' => $coupon->max_discount_amount ? (float) $coupon->max_discount_amount : null,
                    'usage_limit' => $coupon->usage_limit,
                    'usage_count' => $coupon->usage_count,
                    'usages_count' => $coupon->usages_count,
                    'per_customer_limit' => $coupon->per_customer_limit,
                    'valid_from' => $coupon->valid_from?->toIso8601String(),
                    'valid_to' => $coupon->valid_to?->toIso8601String(),
                    'is_active' => (bool) $coupon->is_active,
                    'is_expired' => $coupon->valid_to && $coupon->valid_to->isPast(),
                    'created_at' => $coupon->created_at->toIso8601String(),
                    'updated_at' => $coupon->updated_at->toIso8601String(),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon not found.',
            ], 404);
        }
    }

    /**
     * Update coupon
     */
    public function update(Request $request, Tenant $tenant, $couponId)
    {
        $coupon = Coupon::where('tenant_id', $tenant->id)->findOrFail($couponId);

        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:coupons,code,' . $coupon->id . ',id,tenant_id,' . $tenant->id,
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

        try {
            $validated['code'] = strtoupper($validated['code']);
            $validated['is_active'] = $request->boolean('is_active', $coupon->is_active);

            $coupon->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Coupon updated successfully.',
                'data' => [
                    'id' => $coupon->id,
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => (float) $coupon->value,
                    'is_active' => (bool) $coupon->is_active,
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Coupon update API error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update coupon.',
            ], 500);
        }
    }

    /**
     * Delete coupon (only if unused)
     */
    public function destroy(Request $request, Tenant $tenant, $couponId)
    {
        $coupon = Coupon::where('tenant_id', $tenant->id)->findOrFail($couponId);

        try {
            if ($coupon->usage_count > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete a coupon that has been used.',
                ], 422);
            }

            $coupon->delete();

            return response()->json([
                'success' => true,
                'message' => 'Coupon deleted successfully.',
            ]);
        } catch (Exception $e) {
            Log::error('Coupon delete API error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete coupon.',
            ], 500);
        }
    }

    /**
     * Toggle coupon active status
     */
    public function toggle(Request $request, Tenant $tenant, $couponId)
    {
        $coupon = Coupon::where('tenant_id', $tenant->id)->findOrFail($couponId);

        try {
            $coupon->update(['is_active' => !$coupon->is_active]);

            return response()->json([
                'success' => true,
                'message' => 'Coupon status updated.',
                'data' => [
                    'is_active' => (bool) $coupon->is_active,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle coupon status.',
            ], 500);
        }
    }
}
