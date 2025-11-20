<?php

namespace App\Http\Controllers\Tenant\Crm;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\LedgerAccount;
use App\Models\Voucher;
use App\Models\VoucherEntry;
use App\Models\VoucherType;
use App\Models\AccountGroup;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\VendorsImport;
use App\Exports\VendorsTemplateExport;

class VendorController extends Controller
{
    public function index(Tenant $tenant)
    {
        $vendors = Vendor::where('tenant_id', $tenant->id)
            ->with('ledgerAccount')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $totalVendors = Vendor::where('tenant_id', tenant()->id)->count();
        $activeVendors = Vendor::where('tenant_id', tenant()->id)->where('status', 'active')->count();
        $totalPurchases = Vendor::where('tenant_id', tenant()->id)->sum('total_purchases');
        $totalOutstanding = Vendor::where('tenant_id', tenant()->id)->sum('outstanding_balance');
        $avgPaymentDays = 0; // Calculate based on your payment data

        return view('tenant.crm.vendors.index', compact(
            'vendors',
            'totalVendors',
            'activeVendors',
            'totalPurchases',
            'totalOutstanding',
            'avgPaymentDays'
        ));
    }

    public function create(Tenant $tenant)
    {
        return view('tenant.crm.vendors.create', compact('tenant'));
    }

    public function store(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'vendor_type' => 'required|in:individual,business',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $vendor = new Vendor($request->except(['save_and_new', 'opening_balance_amount', 'opening_balance_type', 'opening_balance_date']));
            $vendor->tenant_id = tenant()->id;
            $vendor->status = 'active';
            $vendor->save();

            // Ensure ledger account is created
            $vendor->refresh();
            if (!$vendor->ledgerAccount) {
                $vendor->createLedgerAccount();
                $vendor->refresh();
            }

            // Handle opening balance if provided
            $openingBalanceAmount = $request->input('opening_balance_amount', 0);
            $openingBalanceType = $request->input('opening_balance_type', 'none');
            $openingBalanceDate = $request->input('opening_balance_date', now()->format('Y-m-d'));

            if ($openingBalanceAmount > 0 && $openingBalanceType !== 'none') {
                $this->createOpeningBalanceVoucher(
                    $vendor,
                    $openingBalanceAmount,
                    $openingBalanceType,
                    $openingBalanceDate
                );
            }

            DB::commit();

            // Check if this is an AJAX request (from quick add modal)
            if ($request->ajax() || $request->expectsJson()) {
                // Format display name like in InvoiceController
                $displayName = $vendor->company_name ?: trim($vendor->first_name . ' ' . $vendor->last_name);

                return response()->json([
                    'success' => true,
                    'message' => 'Vendor created successfully',
                    'vendor_id' => $vendor->id,
                    'ledger_account_id' => $vendor->ledgerAccount->id,
                    'display_name' => $displayName
                ]);
            }

            return redirect()->route('tenant.crm.vendors.index', ['tenant' => tenant()->slug])
                ->with('success', 'Vendor created successfully with ledger account.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating vendor: ' . $e->getMessage());

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create vendor: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'An error occurred while creating the vendor. Please try again.')
                ->withInput();
        }
    }

    /**
     * Create opening balance voucher for vendor
     */
    private function createOpeningBalanceVoucher(Vendor $vendor, $amount, $type, $date)
    {
        // Get or create Journal Voucher type
        $journalVoucherType = VoucherType::where('tenant_id', $vendor->tenant_id)
            ->where('code', 'JV')
            ->first();

        if (!$journalVoucherType) {
            throw new \Exception('Journal Voucher type not found. Please ensure system voucher types are initialized.');
        }

        // Get Opening Balance Equity account
        $openingBalanceEquity = LedgerAccount::where('tenant_id', $vendor->tenant_id)
            ->where('is_opening_balance_account', true)
            ->first();

        if (!$openingBalanceEquity) {
            // Get or create Equity account group
            $equityGroup = AccountGroup::where('tenant_id', $vendor->tenant_id)
                ->where('nature', 'equity')
                ->first();

            if (!$equityGroup) {
                // Create equity account group if it doesn't exist
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

            // Check if code already exists and generate a unique one
            $code = 'OBE-001';
            $counter = 1;
            while (LedgerAccount::where('tenant_id', $vendor->tenant_id)->where('code', $code)->exists()) {
                $counter++;
                $code = 'OBE-' . str_pad($counter, 3, '0', STR_PAD_LEFT);
            }

            // Create Opening Balance Equity account if it doesn't exist
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

        // Get vendor name for narration
        $vendorName = $vendor->company_name ?: trim($vendor->first_name . ' ' . $vendor->last_name);

        // Create voucher
        $voucher = Voucher::create([
            'tenant_id' => $vendor->tenant_id,
            'voucher_type_id' => $journalVoucherType->id,
            'voucher_number' => $journalVoucherType->getNextVoucherNumber(),
            'voucher_date' => $date,
            'narration' => 'Opening Balance for ' . $vendorName,
            'total_amount' => $amount,
            'status' => 'posted',
            'created_by' => Auth::id(),
            'posted_at' => now(),
            'posted_by' => Auth::id(),
        ]);

        // Create voucher entries based on balance type
        if ($type === 'credit') {
            // We owe vendor money (Credit Vendor, Debit Opening Balance Equity)
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
            // Debit balance - Vendor owes us (Debit Vendor, Credit Opening Balance Equity)
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

        // Update ledger account's opening balance voucher reference
        $vendor->ledgerAccount->update([
            'opening_balance_voucher_id' => $voucher->id,
            'opening_balance' => $type === 'credit' ? $amount : -$amount,
        ]);

        // Update vendor's ledger account balance
        $vendor->ledgerAccount->updateCurrentBalance();

        return $voucher;
    }

    public function show(Tenant $tenant, $id)
    {
        $vendor = Vendor::where('tenant_id', $tenant->id)
            ->with(['ledgerAccount.accountGroup'])
            ->findOrFail($id);

        // Update outstanding balance from ledger
        $vendor->updateOutstandingBalance();

        return view('tenant.crm.vendors.show', compact('vendor'));
    }

    public function edit(Tenant $tenant, $id)
    {
        $vendor = Vendor::where('tenant_id', $tenant->id)
            ->findOrFail($id);

        return view('tenant.crm.vendors.edit', compact('vendor'));
    }

    public function update(Request $request, $id)
    {
        $vendor = Vendor::where('tenant_id', tenant()->id)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'vendor_type' => 'required|in:individual,business',
            'first_name' => 'required_if:vendor_type,individual|string|max:255',
            'last_name' => 'required_if:vendor_type,individual|string|max:255',
            'company_name' => 'required_if:vendor_type,business|string|max:255',
            'email' => 'required|email|max:255',
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $vendor->update($request->all());

        return redirect()->route('tenant.crm.vendors.index', ['tenant' => tenant()->slug])
            ->with('success', 'Vendor updated successfully.');
    }

    public function destroy($id)
    {
        $vendor = Vendor::where('tenant_id', tenant()->id)
            ->findOrFail($id);

        // Check if vendor has outstanding balance
        if ($vendor->outstanding_balance > 0) {
            return redirect()->route('tenant.crm.vendors.index', ['tenant' => tenant()->slug])
                ->with('error', 'Cannot delete vendor with outstanding balance.');
        }

        $vendor->delete();

        return redirect()->route('tenant.crm.vendors.index', ['tenant' => tenant()->slug])
            ->with('success', 'Vendor deleted successfully.');
    }

    /**
     * Download vendor import template
     */
    public function exportTemplate(Tenant $tenant)
    {
        return Excel::download(new VendorsTemplateExport(), 'vendors_import_template.xlsx');
    }

    /**
     * Import vendors from Excel/CSV file
     */
    public function import(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Invalid file. Please upload a valid Excel or CSV file (max 10MB).')
                ->withErrors($validator);
        }

        try {
            $file = $request->file('file');
            $import = new VendorsImport();

            Excel::import($import, $file);

            $successCount = $import->getSuccessCount();
            $failedCount = $import->getFailedCount();
            $errors = $import->getErrors();

            if ($successCount > 0 && $failedCount === 0) {
                return redirect()->route('tenant.crm.vendors.index', ['tenant' => tenant()->slug])
                    ->with('success', "{$successCount} vendor(s) imported successfully!");
            } elseif ($successCount > 0 && $failedCount > 0) {
                return redirect()->route('tenant.crm.vendors.index', ['tenant' => tenant()->slug])
                    ->with('warning', "{$successCount} vendor(s) imported successfully, but {$failedCount} failed.")
                    ->with('import_errors', $errors);
            } else {
                return redirect()->route('tenant.crm.vendors.index', ['tenant' => tenant()->slug])
                    ->with('error', 'Import failed. No vendors were imported.')
                    ->with('import_errors', $errors);
            }
        } catch (\Exception $e) {
            Log::error('Vendor import error: ' . $e->getMessage());
            return redirect()->route('tenant.crm.vendors.index', ['tenant' => tenant()->slug])
                ->with('error', 'An error occurred during import: ' . $e->getMessage());
        }
    }

    /**
     * Search vendors for dropdown/autocomplete
     */
    public function search(Request $request, Tenant $tenant)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $vendors = Vendor::where('tenant_id', $tenant->id)
            ->with('ledgerAccount')
            ->where(function($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('company_name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%");
            })
            ->limit(20)
            ->get()
            ->map(function($vendor) {
                $ledgerAccount = $vendor->ledgerAccount;

                return [
                    'id' => $vendor->id,
                    'ledger_account_id' => $ledgerAccount?->id,
                    'ledger_account_name' => $ledgerAccount?->name,
                    'display_name' => $vendor->company_name ?: trim($vendor->first_name . ' ' . $vendor->last_name),
                    'email' => $vendor->email,
                    'phone' => $vendor->phone,
                ];
            })
            ->filter(function($vendor) {
                return !empty($vendor['ledger_account_id']);
            })
            ->values();

        return response()->json($vendors);
    }
}
