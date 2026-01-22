<?php

namespace App\Http\Controllers\Api\Tenant\Crm;

use App\Http\Controllers\Controller;
use App\Models\AccountGroup;
use App\Models\LedgerAccount;
use App\Models\Tenant;
use App\Models\Vendor;
use App\Models\Voucher;
use App\Models\VoucherEntry;
use App\Models\VoucherType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VendorController extends Controller
{
    /**
     * List vendors with filters and pagination.
     */
    public function index(Request $request, Tenant $tenant)
    {
        $query = Vendor::where('tenant_id', $tenant->id)
            ->with('ledgerAccount');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        if ($request->filled('vendor_type')) {
            $query->where('vendor_type', $request->get('vendor_type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $allowedSorts = ['first_name', 'last_name', 'company_name', 'email', 'created_at', 'status'];

        if (in_array($sortField, $allowedSorts, true)) {
            $query->orderBy($sortField, $sortDirection);
        }

        $perPage = (int) $request->get('per_page', 15);
        $vendors = $query->paginate($perPage);

        $vendors->getCollection()->transform(function ($vendor) {
            $vendor->display_name = $vendor->company_name
                ?? trim(($vendor->first_name ?? '') . ' ' . ($vendor->last_name ?? ''))
                ?? $vendor->email;
            $vendor->outstanding_balance = $vendor->ledgerAccount?->getCurrentBalance()
                ?? $vendor->ledgerAccount?->current_balance
                ?? 0;

            return $vendor;
        });

        $statistics = [
            'total_vendors' => Vendor::where('tenant_id', $tenant->id)->count(),
            'active_vendors' => Vendor::where('tenant_id', $tenant->id)->where('status', 'active')->count(),
            'total_purchases' => Vendor::where('tenant_id', $tenant->id)->sum('total_purchases'),
            'total_outstanding' => Vendor::where('tenant_id', $tenant->id)->sum('outstanding_balance'),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Vendors retrieved successfully',
            'data' => $vendors,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Show vendor details.
     */
    public function show(Request $request, Tenant $tenant, Vendor $vendor)
    {
        if ($vendor->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found',
            ], 404);
        }

        $vendor->load('ledgerAccount');
        $outstandingBalance = $vendor->ledgerAccount?->getCurrentBalance()
            ?? $vendor->ledgerAccount?->current_balance
            ?? 0;

        return response()->json([
            'success' => true,
            'message' => 'Vendor retrieved successfully',
            'data' => [
                'vendor' => $vendor,
                'outstanding_balance' => $outstandingBalance,
            ],
        ]);
    }

    /**
     * Create a new vendor.
     */
    public function store(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'vendor_type' => 'required|in:individual,business',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:vendors,email,NULL,id,tenant_id,' . $tenant->id,
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'tax_id' => 'nullable|string|max:50',
            'registration_number' => 'nullable|string|max:50',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'currency' => 'nullable|string|max:3',
            'payment_terms' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'opening_balance_amount' => 'nullable|numeric|min:0',
            'opening_balance_type' => 'nullable|in:none,debit,credit',
            'opening_balance_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $vendor = new Vendor($request->except([
                'opening_balance_amount',
                'opening_balance_type',
                'opening_balance_date',
            ]));
            $vendor->tenant_id = $tenant->id;
            $vendor->status = $vendor->status ?? 'active';
            $vendor->save();

            $vendor->refresh();

            if (!$vendor->ledgerAccount) {
                $vendor->createLedgerAccount();
                $vendor->refresh();
            }

            $openingBalanceAmount = (float) $request->input('opening_balance_amount', 0);
            $openingBalanceType = $request->input('opening_balance_type', 'none');
            $openingBalanceDate = $request->input('opening_balance_date', now()->format('Y-m-d'));

            if ($openingBalanceAmount > 0 && $openingBalanceType !== 'none') {
                $this->createOpeningBalanceVoucher($vendor, $openingBalanceAmount, $openingBalanceType, $openingBalanceDate);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vendor created successfully',
                'data' => $vendor->load('ledgerAccount'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create vendor: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update vendor details.
     */
    public function update(Request $request, Tenant $tenant, Vendor $vendor)
    {
        if ($vendor->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'vendor_type' => 'required|in:individual,business',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:vendors,email,' . $vendor->id . ',id,tenant_id,' . $tenant->id,
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'tax_id' => 'nullable|string|max:50',
            'registration_number' => 'nullable|string|max:50',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'currency' => 'nullable|string|max:3',
            'payment_terms' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $vendor->fill($request->all());
        $vendor->save();

        return response()->json([
            'success' => true,
            'message' => 'Vendor updated successfully',
            'data' => $vendor->load('ledgerAccount'),
        ]);
    }

    /**
     * Delete a vendor.
     */
    public function destroy(Request $request, Tenant $tenant, Vendor $vendor)
    {
        if ($vendor->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found',
            ], 404);
        }

        if ($vendor->outstanding_balance > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor cannot be deleted because it has outstanding balance',
            ], 422);
        }

        $vendor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vendor deleted successfully',
        ]);
    }

    /**
     * Toggle vendor status.
     */
    public function toggleStatus(Request $request, Tenant $tenant, Vendor $vendor)
    {
        if ($vendor->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found',
            ], 404);
        }

        $vendor->status = $vendor->status === 'active' ? 'inactive' : 'active';
        $vendor->save();

        return response()->json([
            'success' => true,
            'message' => 'Vendor status updated successfully',
            'data' => [
                'id' => $vendor->id,
                'status' => $vendor->status,
            ],
        ]);
    }

    /**
     * Create opening balance voucher for vendor
     */
    private function createOpeningBalanceVoucher(Vendor $vendor, $amount, $type, $date)
    {
        $journalVoucherType = VoucherType::where('tenant_id', $vendor->tenant_id)
            ->where('code', 'JV')
            ->first();

        if (!$journalVoucherType) {
            throw new \Exception('Journal Voucher type not found. Please ensure system voucher types are initialized.');
        }

        $openingBalanceEquity = LedgerAccount::where('tenant_id', $vendor->tenant_id)
            ->where('is_opening_balance_account', true)
            ->first();

        if (!$openingBalanceEquity) {
            $equityGroup = AccountGroup::where('tenant_id', $vendor->tenant_id)
                ->where('nature', 'equity')
                ->first();

            if (!$equityGroup) {
                $equityGroup = AccountGroup::create([
                    'tenant_id' => $vendor->tenant_id,
                    'name' => 'Equity',
                    'nature' => 'equity',
                    'code' => 'EQ',
                    'description' => 'Equity accounts',
                    'parent_id' => null,
                    'is_active' => true,
                ]);
            }

            $code = 'OBE-001';
            $counter = 1;
            while (LedgerAccount::where('tenant_id', $vendor->tenant_id)->where('code', $code)->exists()) {
                $counter++;
                $code = 'OBE-' . str_pad($counter, 3, '0', STR_PAD_LEFT);
            }

            $openingBalanceEquity = LedgerAccount::create([
                'tenant_id' => $vendor->tenant_id,
                'name' => 'Opening Balance Equity',
                'code' => $code,
                'account_group_id' => $equityGroup->id,
                'description' => 'Opening balance equity account',
                'opening_balance' => 0,
                'current_balance' => 0,
                'nature' => 'equity',
                'is_opening_balance_account' => true,
                'is_active' => true,
            ]);
        }

        $vendorName = $vendor->company_name ?: trim(($vendor->first_name ?? '') . ' ' . ($vendor->last_name ?? ''));

        $voucher = Voucher::create([
            'tenant_id' => $vendor->tenant_id,
            'voucher_type_id' => $journalVoucherType->id,
            'voucher_number' => $journalVoucherType->getNextVoucherNumber(),
            'voucher_date' => $date,
            'narration' => 'Opening Balance for ' . $vendorName,
            'total_amount' => $amount,
            'status' => 'posted',
            'created_by' => auth()->id(),
            'posted_at' => now(),
            'posted_by' => auth()->id(),
        ]);

        if ($type === 'credit') {
            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $vendor->ledgerAccount->id,
                'credit_amount' => $amount,
                'debit_amount' => 0,
                'narration' => 'Opening Balance - Vendor Payable',
            ]);

            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $openingBalanceEquity->id,
                'debit_amount' => $amount,
                'credit_amount' => 0,
                'narration' => 'Opening Balance Equity',
            ]);
        } else {
            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $vendor->ledgerAccount->id,
                'debit_amount' => $amount,
                'credit_amount' => 0,
                'narration' => 'Opening Balance - Vendor Advance',
            ]);

            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $openingBalanceEquity->id,
                'credit_amount' => $amount,
                'debit_amount' => 0,
                'narration' => 'Opening Balance Equity',
            ]);
        }

        $vendor->ledgerAccount->update([
            'opening_balance_voucher_id' => $voucher->id,
            'opening_balance' => $type === 'credit' ? $amount : -$amount,
        ]);

        if (method_exists($vendor->ledgerAccount, 'updateCurrentBalance')) {
            $vendor->ledgerAccount->updateCurrentBalance();
        }

        return $voucher;
    }
}
