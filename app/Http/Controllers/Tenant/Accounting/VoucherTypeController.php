<?php

namespace App\Http\Controllers\Tenant\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\VoucherType;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VoucherTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'tenant']);
    }

    /**
     * Display a listing of voucher types.
     */
    public function index(Request $request, Tenant $tenant)
    {
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

        // Sort
        $sortBy = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');


        $allowedSorts = ['name', 'code', 'created_at', 'is_active'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $voucherTypes = $query->paginate(15)->withQueryString();

        // Get voucher counts for each type
        $voucherCounts = Voucher::where('tenant_id', $tenant->id)
            ->select('voucher_type_id', DB::raw('count(*) as count'))
            ->groupBy('voucher_type_id')
            ->pluck('count', 'voucher_type_id');

        // Get statistics for the dashboard cards
        $totalVoucherTypes = VoucherType::where('tenant_id', $tenant->id)->count();
        $activeVoucherTypes = VoucherType::where('tenant_id', $tenant->id)->where('is_active', true)->count();
        $systemVoucherTypes = VoucherType::where('tenant_id', $tenant->id)->where('is_system_defined', true)->count();
        $customVoucherTypes = VoucherType::where('tenant_id', $tenant->id)->where('is_system_defined', false)->count();

        return view('tenant.accounting.voucher-types.index', compact(
            'tenant',
            'voucherTypes',
            'voucherCounts',
            'totalVoucherTypes',
            'activeVoucherTypes',
            'systemVoucherTypes',
            'customVoucherTypes'
        ));
    }

    /**
     * Show the form for creating a new voucher type.
     */
    public function create(Tenant $tenant)
    {
        // Fetch all system-defined voucher types for this tenant
        $primaryVoucherTypes = VoucherType::where('tenant_id', $tenant->id)
            ->where('is_system_defined', true)
            ->orderBy('name')
            ->get();

        return view('tenant.accounting.voucher-types.create', compact(
            'tenant',
            'primaryVoucherTypes'
        ));
    }

    /**
     * Store a newly created voucher type.
     */
    public function store(Request $request, Tenant $tenant)
    {
        $request->validate([
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
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'abbreviation' => strtoupper($request->abbreviation),
            'description' => $request->description,
            'numbering_method' => $request->numbering_method,
            'prefix' => $request->prefix,
            'starting_number' => $request->starting_number,
            'current_number' => $request->starting_number - 1,
            'has_reference' => $request->boolean('has_reference'),
            'affects_inventory' => $request->boolean('affects_inventory'),
            'affects_cashbank' => $request->boolean('affects_cashbank'),
            'is_system_defined' => false,
            'is_active' => $request->boolean('is_active', true),
        ]);

       return redirect()
    ->route('tenant.accounting.voucher-types.show', [
        'tenant' => $tenant->slug,
        'voucherType' => $voucherType->id
    ])
    ->with('success', 'Voucher type created successfully.');
    }

    /**
     * Display the specified voucher type.
     */
    public function show(Tenant $tenant, VoucherType $voucherType)
    {
       // $this->authorize('view', $voucherType);

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

        return view('tenant.accounting.voucher-types.show', compact(
            'tenant',
            'voucherType',
            'voucherCount',
            'recentVouchers'
        ));
    }

    /**
     * Show the form for editing the specified voucher type.
     */
    public function edit(Tenant $tenant, VoucherType $voucherType)
    {
     //   $this->authorize('update', $voucherType);

        return view('tenant.accounting.voucher-types.edit', compact(
            'tenant',
            'voucherType'
        ));
    }

    /**
     * Update the specified voucher type.
     */
    public function update(Request $request, Tenant $tenant, VoucherType $voucherType)
    {
        //$this->authorize('update', $voucherType);

        $rules = [
            'abbreviation' => ['required', 'string', 'max:5', 'regex:/^[A-Z]+$/'],
            'description' => ['nullable', 'string'],
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

        $request->validate($rules, [
            'code.regex' => 'Code can only contain uppercase letters, numbers, hyphens, and underscores.',
            'abbreviation.regex' => 'Abbreviation can only contain uppercase letters.',
        ]);

        $updateData = [
            'abbreviation' => strtoupper($request->abbreviation),
            'description' => $request->description,
            'numbering_method' => $request->numbering_method,
            'prefix' => $request->prefix,
            'starting_number' => $request->starting_number,
            'has_reference' => $request->boolean('has_reference'),
            'is_active' => $request->boolean('is_active'),
        ];

        // Only update these fields for non-system voucher types
        if (!$voucherType->is_system_defined) {
            $updateData['name'] = $request->name;
            $updateData['code'] = strtoupper($request->code);
            $updateData['affects_inventory'] = $request->boolean('affects_inventory');
            $updateData['affects_cashbank'] = $request->boolean('affects_cashbank');
        }

        $voucherType->update($updateData);

        return redirect()
            ->route('tenant.accounting.voucher-types.show',['tenant' => $tenant->slug, 'voucherType' => $voucherType->id])

            ->with('success', 'Voucher type updated successfully.');
    }

    /**
     * Remove the specified voucher type.
     */
    public function destroy(Tenant $tenant, VoucherType $voucherType)
    {
     //   $this->authorize('delete', $voucherType);
          // Check if voucher type has any vouchers
        $voucherCount = Voucher::where('tenant_id', $tenant->id)
            ->where('voucher_type_id', $voucherType->id)
            ->count();

        if ($voucherCount > 0) {
            return redirect()
                ->route('tenant.accounting.voucher-types.show', [
                    'tenant' => $tenant->slug,
                    'voucher_type' => $voucherType->id
                ])
                ->with('error', 'Cannot delete voucher type that has existing vouchers.');
        }

        // System-defined voucher types cannot be deleted
        if ($voucherType->is_system_defined) {
            return redirect()
                ->route('tenant.accounting.voucher-types.show', [
                    'tenant' => $tenant->slug,
                    'voucher_type' => $voucherType->id
                ])
                ->with('error', 'System-defined voucher types cannot be deleted.');
        }

        $voucherType->delete();

        return redirect()
            ->route('tenant.accounting.voucher-types.index', ['tenant' => $tenant->slug])
            ->with('success', 'Voucher type deleted successfully.');
    }

    /**
     * Toggle the active status of a voucher type.
     */
    public function toggle(Tenant $tenant, VoucherType $voucherType)
    {
        $this->authorize('update', $voucherType);

        $voucherType->update([
            'is_active' => !$voucherType->is_active
        ]);

        $status = $voucherType->is_active ? 'activated' : 'deactivated';

        return redirect()
            ->route('tenant.accounting.voucher-types.show', [
                'tenant' => $tenant->slug,
                'voucher_type' => $voucherType->id
            ])
            ->with('success', "Voucher type {$status} successfully.");
    }

    /**
     * Reset the numbering sequence for a voucher type.
     */
    public function resetNumbering(Request $request, Tenant $tenant, VoucherType $voucherType)
    {
        $this->authorize('update', $voucherType);

        $request->validate([
            'reset_number' => ['required', 'integer', 'min:1']
        ]);

        // Only allow resetting for auto-numbering voucher types
        if ($voucherType->numbering_method !== 'auto') {
            return redirect()
                ->route('tenant.accounting.voucher-types.show', [
                    'tenant' => $tenant->slug,
                    'voucher_type' => $voucherType->id
                ])
                ->with('error', 'Can only reset numbering for auto-numbered voucher types.');
        }

        $resetNumber = $request->reset_number;

        // Check if the reset number conflicts with existing vouchers
        $existingVoucher = Voucher::where('tenant_id', $tenant->id)
            ->where('voucher_type_id', $voucherType->id)
            ->where('voucher_number', $voucherType->prefix . str_pad($resetNumber, 4, '0', STR_PAD_LEFT))
            ->first();

        if ($existingVoucher) {
            return redirect()
                ->route('tenant.accounting.voucher-types.show', [
                    'tenant' => $tenant->slug,
                    'voucher_type' => $voucherType->id
                ])
                ->with('error', 'Cannot reset to this number as it conflicts with an existing voucher.');
        }

        $voucherType->resetNumbering($resetNumber);

        return redirect()
            ->route('tenant.accounting.voucher-types.show', [
                'tenant' => $tenant->slug,
                'voucher_type' => $voucherType->id
            ])
            ->with('success', "Numbering reset successfully. Next voucher will be {$voucherType->prefix}" . str_pad($resetNumber, 4, '0', STR_PAD_LEFT));
    }

    /**
     * Bulk actions for voucher types.
     */
    public function bulkAction(Request $request, Tenant $tenant)
    {
        $request->validate([
            'action' => ['required', 'in:activate,deactivate,delete'],
            'voucher_types' => ['required', 'array', 'min:1'],
            'voucher_types.*' => ['exists:voucher_types,id']
        ]);

        $voucherTypeIds = $request->voucher_types;
        $action = $request->action;

        // Get voucher types belonging to this tenant
        $voucherTypes = VoucherType::where('tenant_id', $tenant->id)
            ->whereIn('id', $voucherTypeIds)
            ->get();

        if ($voucherTypes->isEmpty()) {
            return redirect()
                ->route('tenant.accounting.voucher-types.index', ['tenant' => $tenant->slug])
                ->with('error', 'No valid voucher types selected.');
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

        $message = '';
        if ($successCount > 0) {
            $actionText = $action === 'activate' ? 'activated' : ($action === 'deactivate' ? 'deactivated' : 'deleted');
            $message = "{$successCount} voucher type(s) {$actionText} successfully.";
        }

        if (!empty($errors)) {
            $message .= ' ' . implode(' ', $errors);
        }

        return redirect()
            ->route('tenant.accounting.voucher-types.index', ['tenant' => $tenant->slug])
            ->with($successCount > 0 ? 'success' : 'error', $message);
    }

    /**
     * Get voucher type data for API/AJAX requests.
     */
    public function apiIndex(Request $request, Tenant $tenant)
    {
        $query = VoucherType::where('tenant_id', $tenant->id)
            ->where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $voucherTypes = $query->orderBy('name')->get();

        return response()->json([
            'data' => $voucherTypes->map(function ($voucherType) {
                return [
                    'id' => $voucherType->id,
                    'name' => $voucherType->name,
                    'code' => $voucherType->code,
                    'abbreviation' => $voucherType->abbreviation,
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
