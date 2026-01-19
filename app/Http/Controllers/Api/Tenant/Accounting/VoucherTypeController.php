<?php

namespace App\Http\Controllers\Api\Tenant\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\VoucherType;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VoucherTypeController extends Controller
{
    /**
     * Display a listing of voucher types.
     */
    public function index(Request $request)
    {
        $tenant = $request->tenant;

        $query = VoucherType::where('tenant_id', $tenant->id);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('abbreviation', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->get('status') === 'active');
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('is_system_defined', $request->get('type') === 'system');
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        // Sort
        $sortBy = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');

        $allowedSorts = ['name', 'code', 'created_at', 'is_active'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $voucherTypes = $query->paginate($perPage);

        // Get voucher counts for each type
        $voucherCounts = Voucher::where('tenant_id', $tenant->id)
            ->select('voucher_type_id', DB::raw('count(*) as count'))
            ->groupBy('voucher_type_id')
            ->pluck('count', 'voucher_type_id');

        // Add voucher count to each voucher type
        $voucherTypes->getCollection()->transform(function ($voucherType) use ($voucherCounts) {
            $voucherType->voucher_count = $voucherCounts->get($voucherType->id, 0);
            return $voucherType;
        });

        return response()->json([
            'success' => true,
            'message' => 'Voucher types retrieved successfully',
            'data' => $voucherTypes,
            'statistics' => [
                'total' => VoucherType::where('tenant_id', $tenant->id)->count(),
                'active' => VoucherType::where('tenant_id', $tenant->id)->where('is_active', true)->count(),
                'system_defined' => VoucherType::where('tenant_id', $tenant->id)->where('is_system_defined', true)->count(),
                'custom' => VoucherType::where('tenant_id', $tenant->id)->where('is_system_defined', false)->count(),
            ]
        ]);
    }

    /**
     * Get data for creating a new voucher type.
     */
    public function create(Request $request)
    {
        $tenant = $request->tenant;

        // Fetch all system-defined voucher types for reference
        $primaryVoucherTypes = VoucherType::where('tenant_id', $tenant->id)
            ->where('is_system_defined', true)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Create form data retrieved successfully',
            'data' => [
                'primary_voucher_types' => $primaryVoucherTypes,
                'categories' => [
                    'accounting' => 'Accounting',
                    'inventory' => 'Inventory',
                    'POS' => 'POS',
                    'payroll' => 'Payroll',
                    'ecommerce' => 'Ecommerce'
                ],
                'numbering_methods' => [
                    'auto' => 'Automatic',
                    'manual' => 'Manual'
                ]
            ]
        ]);
    }

    /**
     * Store a newly created voucher type.
     */
    public function store(Request $request)
    {
        $tenant = $request->tenant;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:30',
                'regex:/^[A-Z0-9_-]+$/',
                Rule::unique('voucher_types')->where('tenant_id', $tenant->id)
            ],
            'abbreviation' => ['required', 'string', 'max:5', 'regex:/^[A-Z]+$/'],
            'description' => ['nullable', 'string'],
            'category' => ['required', 'in:accounting,inventory,POS,payroll,ecommerce'],
            'numbering_method' => ['required', 'in:auto,manual'],
            'prefix' => ['nullable', 'string', 'max:10'],
            'starting_number' => ['required', 'integer', 'min:1'],
            'has_reference' => ['boolean'],
            'affects_inventory' => ['boolean'],
            'affects_cashbank' => ['boolean'],
            'is_active' => ['boolean'],
        ], [
            'code.regex' => 'Code can only contain uppercase letters, numbers, hyphens, and underscores.',
            'abbreviation.regex' => 'Abbreviation can only contain uppercase letters.',
        ]);

        $voucherType = VoucherType::create([
            'tenant_id' => $tenant->id,
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
            'abbreviation' => strtoupper($validated['abbreviation']),
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'],
            'numbering_method' => $validated['numbering_method'],
            'prefix' => $validated['prefix'] ?? null,
            'starting_number' => $validated['starting_number'],
            'current_number' => $validated['starting_number'] - 1,
            'has_reference' => $request->boolean('has_reference'),
            'affects_inventory' => $request->boolean('affects_inventory'),
            'affects_cashbank' => $request->boolean('affects_cashbank'),
            'is_system_defined' => false,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Voucher type created successfully',
            'data' => $voucherType
        ], 201);
    }

    /**
     * Display the specified voucher type.
     */
    public function show(Request $request, VoucherType $voucherType)
    {
        $tenant = $request->tenant;

        // Get voucher count
        $voucherCount = Voucher::where('tenant_id', $tenant->id)
            ->where('voucher_type_id', $voucherType->id)
            ->count();

        // Get recent vouchers
        $recentVouchers = Voucher::where('tenant_id', $tenant->id)
            ->where('voucher_type_id', $voucherType->id)
            ->latest()
            ->take(5)
            ->get();

        $voucherType->voucher_count = $voucherCount;
        $voucherType->recent_vouchers = $recentVouchers;

        return response()->json([
            'success' => true,
            'message' => 'Voucher type retrieved successfully',
            'data' => $voucherType
        ]);
    }

    /**
     * Update the specified voucher type.
     */
    public function update(Request $request, VoucherType $voucherType)
    {
        $tenant = $request->tenant;

        $rules = [
            'abbreviation' => ['required', 'string', 'max:5', 'regex:/^[A-Z]+$/'],
            'description' => ['nullable', 'string'],
            'category' => ['required', 'in:accounting,inventory,POS,payroll,ecommerce'],
            'numbering_method' => ['required', 'in:auto,manual'],
            'prefix' => ['nullable', 'string', 'max:10'],
            'starting_number' => ['required', 'integer', 'min:1'],
            'has_reference' => ['boolean'],
            'is_active' => ['boolean'],
        ];

        // System-defined voucher types have restricted fields
        if (!$voucherType->is_system_defined) {
            $rules['name'] = ['required', 'string', 'max:255'];
            $rules['code'] = [
                'required',
                'string',
                'max:30',
                'regex:/^[A-Z0-9_-]+$/',
                Rule::unique('voucher_types')
                    ->where('tenant_id', $tenant->id)
                    ->ignore($voucherType->id)
            ];
            $rules['affects_inventory'] = ['boolean'];
            $rules['affects_cashbank'] = ['boolean'];
        }

        $validated = $request->validate($rules, [
            'code.regex' => 'Code can only contain uppercase letters, numbers, hyphens, and underscores.',
            'abbreviation.regex' => 'Abbreviation can only contain uppercase letters.',
        ]);

        $updateData = [
            'abbreviation' => strtoupper($validated['abbreviation']),
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'],
            'numbering_method' => $validated['numbering_method'],
            'prefix' => $validated['prefix'] ?? null,
            'starting_number' => $validated['starting_number'],
            'has_reference' => $request->boolean('has_reference'),
            'is_active' => $request->boolean('is_active'),
        ];

        // Only update these fields for non-system voucher types
        if (!$voucherType->is_system_defined) {
            $updateData['name'] = $validated['name'];
            $updateData['code'] = strtoupper($validated['code']);
            $updateData['affects_inventory'] = $request->boolean('affects_inventory');
            $updateData['affects_cashbank'] = $request->boolean('affects_cashbank');
        }

        $voucherType->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Voucher type updated successfully',
            'data' => $voucherType->fresh()
        ]);
    }

    /**
     * Remove the specified voucher type.
     */
    public function destroy(Request $request, VoucherType $voucherType)
    {
        $tenant = $request->tenant;

        // Check if voucher type has any vouchers
        $voucherCount = Voucher::where('tenant_id', $tenant->id)
            ->where('voucher_type_id', $voucherType->id)
            ->count();

        if ($voucherCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete voucher type that has existing vouchers.',
                'errors' => ['voucher_type' => ['This voucher type has existing vouchers and cannot be deleted.']]
            ], 422);
        }

        // System-defined voucher types cannot be deleted
        if ($voucherType->is_system_defined) {
            return response()->json([
                'success' => false,
                'message' => 'System-defined voucher types cannot be deleted.',
                'errors' => ['voucher_type' => ['System-defined voucher types are protected and cannot be deleted.']]
            ], 422);
        }

        $voucherType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Voucher type deleted successfully'
        ]);
    }

    /**
     * Toggle the active status of a voucher type.
     */
    public function toggle(Request $request, VoucherType $voucherType)
    {
        $voucherType->update([
            'is_active' => !$voucherType->is_active
        ]);

        $status = $voucherType->is_active ? 'activated' : 'deactivated';

        return response()->json([
            'success' => true,
            'message' => "Voucher type {$status} successfully",
            'data' => $voucherType->fresh()
        ]);
    }

    /**
     * Reset the numbering sequence for a voucher type.
     */
    public function resetNumbering(Request $request, VoucherType $voucherType)
    {
        $tenant = $request->tenant;

        $validated = $request->validate([
            'reset_number' => ['required', 'integer', 'min:1']
        ]);

        // Only allow resetting for auto-numbering voucher types
        if ($voucherType->numbering_method !== 'auto') {
            return response()->json([
                'success' => false,
                'message' => 'Can only reset numbering for auto-numbered voucher types.',
                'errors' => ['numbering_method' => ['This voucher type uses manual numbering.']]
            ], 422);
        }

        $resetNumber = $validated['reset_number'];

        // Check if the reset number conflicts with existing vouchers
        $existingVoucher = Voucher::where('tenant_id', $tenant->id)
            ->where('voucher_type_id', $voucherType->id)
            ->where('voucher_number', $voucherType->prefix . str_pad($resetNumber, 4, '0', STR_PAD_LEFT))
            ->first();

        if ($existingVoucher) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot reset to this number as it conflicts with an existing voucher.',
                'errors' => ['reset_number' => ['A voucher with this number already exists.']]
            ], 422);
        }

        $voucherType->resetNumbering($resetNumber);

        return response()->json([
            'success' => true,
            'message' => 'Numbering reset successfully',
            'data' => [
                'voucher_type' => $voucherType->fresh(),
                'next_number' => $voucherType->prefix . str_pad($resetNumber, 4, '0', STR_PAD_LEFT)
            ]
        ]);
    }

    /**
     * Bulk actions for voucher types.
     */
    public function bulkAction(Request $request)
    {
        $tenant = $request->tenant;

        $validated = $request->validate([
            'action' => ['required', 'in:activate,deactivate,delete'],
            'voucher_types' => ['required', 'array', 'min:1'],
            'voucher_types.*' => ['exists:voucher_types,id']
        ]);

        $voucherTypeIds = $validated['voucher_types'];
        $action = $validated['action'];

        // Get voucher types belonging to this tenant
        $voucherTypes = VoucherType::where('tenant_id', $tenant->id)
            ->whereIn('id', $voucherTypeIds)
            ->get();

        if ($voucherTypes->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No valid voucher types selected.',
                'errors' => ['voucher_types' => ['No valid voucher types found.']]
            ], 422);
        }

        $successCount = 0;
        $errors = [];

        foreach ($voucherTypes as $voucherType) {
            try {
                switch ($action) {
                    case 'activate':
                        if (!$voucherType->is_active) {
                            $voucherType->update(['is_active' => true]);
                            $successCount++;
                        }
                        break;

                    case 'deactivate':
                        if ($voucherType->is_active) {
                            $voucherType->update(['is_active' => false]);
                            $successCount++;
                        }
                        break;

                    case 'delete':
                        // Check constraints
                        if ($voucherType->is_system_defined) {
                            $errors[] = "Cannot delete system-defined voucher type: {$voucherType->name}";
                            continue 2;
                        }

                        $voucherCount = Voucher::where('tenant_id', $tenant->id)
                            ->where('voucher_type_id', $voucherType->id)
                            ->count();

                        if ($voucherCount > 0) {
                            $errors[] = "Cannot delete voucher type with existing vouchers: {$voucherType->name}";
                            continue 2;
                        }

                        $voucherType->delete();
                        $successCount++;
                        break;
                }
            } catch (\Exception $e) {
                $errors[] = "Error processing {$voucherType->name}: " . $e->getMessage();
            }
        }

        $actionText = $action === 'activate' ? 'activated' : ($action === 'deactivate' ? 'deactivated' : 'deleted');

        return response()->json([
            'success' => $successCount > 0,
            'message' => $successCount > 0
                ? "{$successCount} voucher type(s) {$actionText} successfully."
                : "No voucher types were {$actionText}.",
            'data' => [
                'success_count' => $successCount,
                'errors' => $errors
            ]
        ], $successCount > 0 ? 200 : 422);
    }

    /**
     * Search voucher types for selection.
     */
    public function search(Request $request)
    {
        $tenant = $request->tenant;

        $query = VoucherType::where('tenant_id', $tenant->id)
            ->where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('abbreviation', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        $voucherTypes = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'message' => 'Voucher types search results',
            'data' => $voucherTypes->map(function ($voucherType) {
                return [
                    'id' => $voucherType->id,
                    'name' => $voucherType->name,
                    'code' => $voucherType->code,
                    'abbreviation' => $voucherType->abbreviation,
                    'category' => $voucherType->category,
                    'prefix' => $voucherType->prefix,
                    'numbering_method' => $voucherType->numbering_method,
                    'has_reference' => $voucherType->has_reference,
                    'affects_inventory' => $voucherType->affects_inventory,
                    'affects_cashbank' => $voucherType->affects_cashbank,
                    'next_number' => $voucherType->getNextVoucherNumber(),
                ];
            })
        ]);
    }
}
