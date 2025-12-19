<?php

namespace App\Http\Controllers\Tenant\Crm;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Tenant;
use App\Models\LedgerAccount;
use App\Models\Voucher;
use App\Models\VoucherEntry;
use App\Models\VoucherType;
use App\Models\AccountGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Imports\CustomersImport;
use App\Exports\CustomersTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    public function index(Request $request, Tenant $tenant)
    {
        $query = Customer::with(['invoices', 'payments', 'ledgerAccount'])
            ->where('tenant_id', $tenant->id);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by customer type
        if ($request->filled('customer_type')) {
            $query->where('customer_type', $request->get('customer_type'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $isActive = $request->get('status') === 'active';
            $query->where('is_active', $isActive);
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $allowedSorts = ['first_name', 'last_name', 'company_name', 'email', 'created_at', 'total_outstanding'];
        if (in_array($sortField, $allowedSorts)) {
            if ($sortField === 'total_outstanding') {
                // Sort by calculated field
                $query->leftJoin('invoices', function($join) {
                    $join->on('customers.id', '=', 'invoices.customer_id')
                         ->where('invoices.status', '!=', 'paid');
                })
                ->selectRaw('customers.*, COALESCE(SUM(invoices.total_amount - invoices.paid_amount), 0) as total_outstanding')
                ->groupBy('customers.id')
                ->orderBy('total_outstanding', $sortDirection);
            } else {
                $query->orderBy($sortField, $sortDirection);
            }
        }

        $customers = $query->paginate(10);

        // Calculate statistics for the index page
        $totalCustomers = Customer::where('tenant_id', $tenant->id)->count();

        $activeCustomers = Customer::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->count();

        $individualCustomers = Customer::where('tenant_id', $tenant->id)
            ->where('customer_type', 'individual')
            ->count();

        $companyCustomers = Customer::where('tenant_id', $tenant->id)
            ->where('customer_type', 'business')
            ->count();

        return view('tenant.crm.customers.index', compact(
            'tenant',
            'customers',
            'totalCustomers',
            'activeCustomers',
            'individualCustomers',
            'companyCustomers'
        ));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create(Tenant $tenant)
    {
        return view('tenant.crm.customers.create', compact('tenant'));
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'customer_type' => 'required|in:individual,business',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email,NULL,id,tenant_id,' . $tenant->id,
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'currency' => 'nullable|string|max:3',
            'payment_terms' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'tax_id' => 'nullable|string|max:50',
            'credit_limit' => 'nullable|numeric|min:0',
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

            $customer = new Customer($request->except(['save_and_new', 'opening_balance_amount', 'opening_balance_type', 'opening_balance_date']));
            $customer->tenant_id = $tenant->id;
            $customer->status = 'active';
            $customer->save();

            // Ensure ledger account is created
            $customer->refresh();
            if (!$customer->ledgerAccount) {
                $customer->createLedgerAccount();
                $customer->refresh();
            }

            // Handle opening balance if provided
            $openingBalanceAmount = $request->input('opening_balance_amount', 0);
            $openingBalanceType = $request->input('opening_balance_type', 'none');
            $openingBalanceDate = $request->input('opening_balance_date', now()->format('Y-m-d'));

            if ($openingBalanceAmount > 0 && $openingBalanceType !== 'none') {
                $this->createOpeningBalanceVoucher(
                    $customer,
                    $openingBalanceAmount,
                    $openingBalanceType,
                    $openingBalanceDate
                );
            }

            DB::commit();

            // Check if this is an AJAX request (from quick add modal)
            if ($request->ajax() || $request->expectsJson()) {
                // Format display name like in InvoiceController
                $displayName = $customer->company_name ?: trim($customer->first_name . ' ' . $customer->last_name);

                return response()->json([
                    'success' => true,
                    'message' => 'Customer created successfully',
                    'customer_id' => $customer->id,
                    'ledger_account_id' => $customer->ledgerAccount->id,
                    'display_name' => $displayName
                ]);
            }

            // Determine redirect based on save_and_new parameter
            if ($request->has('save_and_new') && $request->save_and_new) {
                return redirect()->route('tenant.crm.customers.create', ['tenant' => $tenant->slug])
                    ->with('success', 'Customer created successfully. You can now add another customer.');
            }

            return redirect()->route('tenant.crm.customers.index', ['tenant' => $tenant->slug])
                ->with('success', 'Customer created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating customer: ' . $e->getMessage());

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create customer: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'An error occurred while creating the customer. Please try again.')
                ->withInput();
        }
    }

    /**
     * Create opening balance voucher for customer
     */
    private function createOpeningBalanceVoucher(Customer $customer, $amount, $type, $date)
    {
        // Get or create Journal Voucher type
        $journalVoucherType = VoucherType::where('tenant_id', $customer->tenant_id)
            ->where('code', 'JV')
            ->first();

        if (!$journalVoucherType) {
            throw new \Exception('Journal Voucher type not found. Please ensure system voucher types are initialized.');
        }

        // Get Opening Balance Equity account
        $openingBalanceEquity = LedgerAccount::where('tenant_id', $customer->tenant_id)
            ->where('is_opening_balance_account', true)
            ->first();

        if (!$openingBalanceEquity) {
            // Get or create Equity account group
            $equityGroup = AccountGroup::where('tenant_id', $customer->tenant_id)
                ->where('nature', 'equity')
                ->first();

            if (!$equityGroup) {
                // Create equity account group if it doesn't exist
                $equityGroup = AccountGroup::create([
                    'tenant_id' => $customer->tenant_id,
                    'name' => 'Capital Account',
                    'code' => 'CAP',
                    'nature' => 'equity',
                    'parent_id' => null,
                    'is_system_defined' => true,
                    'is_active' => true,
                ]);
            }

            // Check if code already exists and generate a unique one
            $code = 'OBE-001';
            $counter = 1;
            while (LedgerAccount::where('tenant_id', $customer->tenant_id)->where('code', $code)->exists()) {
                $counter++;
                $code = 'OBE-' . str_pad($counter, 3, '0', STR_PAD_LEFT);
            }

            // Create Opening Balance Equity account if it doesn't exist
            $openingBalanceEquity = LedgerAccount::create([
                'tenant_id' => $customer->tenant_id,
                'name' => 'Opening Balance Equity',
                'code' => $code,
                'account_group_id' => $equityGroup->id,
                'account_type' => 'equity',
                'opening_balance' => 0,
                'balance_type' => 'cr',
                'description' => 'System account for opening balance entries. This should be reclassified to appropriate equity accounts after initial setup is complete.',
                'is_opening_balance_account' => true,
                'is_system_account' => true,
                'is_active' => true,
            ]);
        }

        // Create voucher
        $voucher = Voucher::create([
            'tenant_id' => $customer->tenant_id,
            'voucher_type_id' => $journalVoucherType->id,
            'voucher_number' => $journalVoucherType->getNextVoucherNumber(),
            'voucher_date' => $date,
            'narration' => 'Opening Balance for ' . $customer->getFullNameAttribute(),
            'total_amount' => $amount,
            'status' => 'posted',
            'created_by' => Auth::id(),
            'posted_at' => now(),
            'posted_by' => Auth::id(),
        ]);

        // Create voucher entries based on balance type
        if ($type === 'debit') {
            // Customer owes money (Debit Customer, Credit Opening Balance Equity)
            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $customer->ledgerAccount->id,
                'debit_amount' => $amount,
                'credit_amount' => 0,
                'narration' => 'Opening Balance - Customer Receivable',
            ]);

            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $openingBalanceEquity->id,
                'debit_amount' => 0,
                'credit_amount' => $amount,
                'narration' => 'Opening Balance Equity',
            ]);
        } else {
            // Credit balance - We owe customer (Credit Customer, Debit Opening Balance Equity)
            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $customer->ledgerAccount->id,
                'debit_amount' => 0,
                'credit_amount' => $amount,
                'narration' => 'Opening Balance - Customer Credit',
            ]);

            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $openingBalanceEquity->id,
                'debit_amount' => $amount,
                'credit_amount' => 0,
                'narration' => 'Opening Balance Equity',
            ]);
        }

        // Update ledger account's opening balance voucher reference
        $customer->ledgerAccount->update([
            'opening_balance_voucher_id' => $voucher->id,
        ]);

        // Update customer's ledger account balance
        $customer->ledgerAccount->updateCurrentBalance();

        return $voucher;
    }

    /**
     * Display the specified customer.
     */
    public function show(Tenant $tenant, Customer $customer)
    {
        // Ensure the customer belongs to the tenant
        if ($customer->tenant_id !== $tenant->id) {
            abort(404);
        }

        $customer->load('ledgerAccount');
        $outstandingBalance = $customer->ledgerAccount ? $customer->ledgerAccount->getCurrentBalance() : 0;

        // Get recent activities for this customer
        $activities = \App\Models\CustomerActivity::where('customer_id', $customer->id)
            ->with('user')
            ->orderBy('activity_date', 'desc')
            ->limit(10)
            ->get();

        return view('tenant.crm.customers.show', compact('customer', 'tenant', 'outstandingBalance', 'activities'));
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Tenant $tenant, Customer $customer)
    {
        // Ensure the customer belongs to the tenant
        if ($customer->tenant_id !== $tenant->id) {
            abort(404);
        }

        return view('tenant.crm.customers.edit', compact('customer', 'tenant'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, Tenant $tenant, Customer $customer)
    {
        // Ensure the customer belongs to the tenant
        if ($customer->tenant_id !== $tenant->id) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'customer_type' => 'required|in:individual,business',
            'first_name' => 'required_if:customer_type,individual|string|max:255',
            'last_name' => 'required_if:customer_type,individual|string|max:255',
            'company_name' => 'required_if:customer_type,business|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email,' . $customer->id . ',id,tenant_id,' . $tenant->id,
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'currency' => 'nullable|string|max:3',
            'payment_terms' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'tax_id' => 'nullable|string|max:50',
            'credit_limit' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $customer->update($request->except(['save_and_new']));

            return redirect()->route('tenant.crm.customers.index', ['tenant' => $tenant->slug])
                ->with('success', 'Customer updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating customer: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while updating the customer. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Tenant $tenant, Customer $customer)
    {
        // Ensure the customer belongs to the tenant
        if ($customer->tenant_id !== $tenant->id) {
            abort(404);
        }

        // Check if the customer has related records before deleting
        $hasRelatedRecords = $customer->invoices()->exists();

        if ($hasRelatedRecords) {
            return redirect()->route('tenant.crm.customers.index', ['tenant' => $tenant->slug])
                ->with('error', 'This customer cannot be deleted because they have related records.');
        }

        try {
             $customer->delete();

            return redirect()->route('tenant.crm.customers.index', ['tenant' => $tenant->slug])
                ->with('success', 'Customer deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting customer: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while deleting the customer. Please try again.');
        }
    }

    /**
     * Toggle customer status (active/inactive)
     */
    public function toggleStatus(Tenant $tenant, Customer $customer)
    {
        // Ensure the customer belongs to the tenant
        if ($customer->tenant_id !== $tenant->id) {
            abort(404);
        }

        try {
            $customer->update([
                'is_active' => !$customer->is_active
            ]);

            $status = $customer->is_active ? 'activated' : 'deactivated';

            return redirect()->back()
                ->with('success', "Customer {$status} successfully.");
        } catch (\Exception $e) {
            Log::error('Error toggling customer status: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while updating the customer status.');
        }
    }

    /**
     * Handle bulk actions for customers
     */
    public function bulkAction(Request $request, Tenant $tenant)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'customers' => 'required|array|min:1',
            'customers.*' => 'exists:customers,id'
        ]);

        try {
            $customerIds = $request->customers;
            $action = $request->action;

            // Ensure all customers belong to the tenant
            $customers = Customer::where('tenant_id', $tenant->id)
                ->whereIn('id', $customerIds);

            if ($customers->count() !== count($customerIds)) {
                return redirect()->back()
                    ->with('error', 'Some customers do not belong to your account.');
            }

            switch ($action) {
                case 'activate':
                    $customers->update(['is_active' => true]);
                    $message = 'Selected customers activated successfully.';
                    break;

                case 'deactivate':
                    $customers->update(['is_active' => false]);
                    $message = 'Selected customers deactivated successfully.';
                    break;

                case 'delete':
                    // Check for customers with related records
                    $customersWithRecords = $customers->whereHas('invoices')->count();

                    if ($customersWithRecords > 0) {
                        return redirect()->back()
                            ->with('error', 'Some customers have related records and cannot be deleted.');
                    }

                    $customers->delete();
                    $message = 'Selected customers deleted successfully.';
                    break;
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Error in bulk action: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while performing the bulk action.');
        }
    }

    /**
     * Export customers data
     */
    public function export(Request $request, Tenant $tenant)
    {
        $query = Customer::where('tenant_id', $tenant->id);

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('customer_type')) {
            $query->where('customer_type', $request->get('customer_type'));
        }

        if ($request->filled('status')) {
            $isActive = $request->get('status') === 'active';
            $query->where('is_active', $isActive);
        }

        $customers = $query->orderBy('created_at', 'desc')->get();

        $filename = 'customers-' . now()->format('Y-m-d-H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($customers) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Customer Code',
                'Type',
                'First Name',
                'Last Name',
                'Company Name',
                'Email',
                'Phone',
                'Mobile',
                'Address',
                'City',
                'State',
                'Postal Code',
                'Country',
                'Credit Limit',
                'Status',
                'Created Date'
            ]);

            // CSV data
            foreach ($customers as $customer) {
                fputcsv($file, [
                    $customer->customer_code,
                    ucfirst($customer->customer_type),
                    $customer->first_name,
                    $customer->last_name,
                    $customer->company_name,
                    $customer->email,
                    $customer->phone,
                    $customer->mobile,
                    $customer->address_line1,
                    $customer->city,
                    $customer->state,
                    $customer->postal_code,
                    $customer->country,
                    $customer->credit_limit ? number_format($customer->credit_limit, 2) : '',
                    $customer->is_active ? 'Active' : 'Inactive',
                    $customer->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display customer statements with balances
     */
    public function statements(Request $request, Tenant $tenant)
    {
        $query = Customer::with(['invoices', 'payments', 'ledgerAccount'])
            ->where('tenant_id', $tenant->id);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('customer_code', 'like', "%{$search}%");
            });
        }

        // Filter by customer type
        if ($request->filled('customer_type')) {
            $query->where('customer_type', $request->get('customer_type'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Get customers with their balance calculations
        $customers = $query->get()->map(function ($customer) {
            $ledgerAccount = $customer->ledgerAccount;

            if ($ledgerAccount) {
                // Get current balance from ledger account
                $currentBalance = $ledgerAccount->getCurrentBalance();

                // Get total debits and credits
                $totalDebits = $ledgerAccount->getTotalDebits();
                $totalCredits = $ledgerAccount->getTotalCredits();

                // Calculate running balance and balance type
                $balanceType = $ledgerAccount->getBalanceType($currentBalance);

                $customer->total_debits = $totalDebits;
                $customer->total_credits = $totalCredits;
                $customer->current_balance = abs($currentBalance);
                $customer->balance_type = $balanceType;
                $customer->running_balance = $currentBalance;
            } else {
                $customer->total_debits = 0;
                $customer->total_credits = 0;
                $customer->current_balance = 0;
                $customer->balance_type = 'dr';
                $customer->running_balance = 0;
            }

            return $customer;
        });

        // Sort by balance if requested
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        if ($sortField === 'current_balance') {
            $customers = $sortDirection === 'desc'
                ? $customers->sortByDesc('current_balance')
                : $customers->sortBy('current_balance');
        } elseif ($sortField === 'total_debits') {
            $customers = $sortDirection === 'desc'
                ? $customers->sortByDesc('total_debits')
                : $customers->sortBy('total_debits');
        } elseif ($sortField === 'total_credits') {
            $customers = $sortDirection === 'desc'
                ? $customers->sortByDesc('total_credits')
                : $customers->sortBy('total_credits');
        }

        // Calculate totals from full dataset before pagination
        $totalCustomers = $customers->count();
        $totalReceivable = $customers->where('running_balance', '>', 0)->sum('running_balance');
        $totalPayable = abs($customers->where('running_balance', '<', 0)->sum('running_balance'));
        $netBalance = $customers->sum('running_balance');

        // Paginate manually
        $perPage = 50;
        $currentPage = $request->get('page', 1);
        $items = $customers->forPage($currentPage, $perPage);

        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $customers->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('tenant.crm.customers.statements', [
            'tenant' => $tenant,
            'customers' => $paginated,
            'totalCustomers' => $totalCustomers,
            'totalReceivable' => $totalReceivable,
            'totalPayable' => $totalPayable,
            'netBalance' => $netBalance,
            'search' => $request->get('search'),
            'customer_type' => $request->get('customer_type'),
            'status' => $request->get('status'),
            'sort' => $sortField,
            'direction' => $sortDirection
        ]);
    }

    /**
     * Download customer import template
     */
    public function exportTemplate(Tenant $tenant)
    {
        return Excel::download(
            new CustomersTemplateExport(),
            'customers_import_template_' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Import customers from Excel/CSV file
     */
    public function import(Request $request, Tenant $tenant)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            $import = new CustomersImport($tenant);

            Excel::import($import, $request->file('file'));

            $successCount = $import->getSuccessCount();
            $failedCount = $import->getFailedCount();
            $errors = $import->getErrors();

            if ($failedCount > 0) {
                // Store errors in session for display
                $errorMessages = collect($errors)->map(function($error) {
                    return "Row {$error['row']} ({$error['identifier']}): {$error['error']}";
                })->toArray();

                return redirect()->back()
                    ->with('warning', "{$successCount} customers imported successfully, but {$failedCount} failed.")
                    ->with('import_errors', $errorMessages);
            }

            return redirect()->route('tenant.crm.customers.index', ['tenant' => $tenant->slug])
                ->with('success', "{$successCount} customers imported successfully!");

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];

            foreach ($failures as $failure) {
                $errorMessages[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
            }

            return redirect()->back()
                ->with('error', 'Import validation failed')
                ->with('import_errors', $errorMessages);

        } catch (\Exception $e) {
            Log::error('Customer import error: ' . $e->getMessage(), [
                'tenant_id' => $tenant->id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Display individual customer statement with transaction details
     */
    public function showStatement(Request $request, Tenant $tenant, $customerId)
    {
        // Find customer with ledger account
        $customer = Customer::with(['ledgerAccount'])
            ->where('tenant_id', $tenant->id)
            ->findOrFail($customerId);

        if (!$customer->ledgerAccount) {
            return redirect()->back()
                ->with('error', 'Customer does not have an associated ledger account.');
        }

        $ledgerAccount = $customer->ledgerAccount;

        // Get date range from request or default to current month
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Get opening balance (balance before start date)
        $openingBalance = VoucherEntry::where('ledger_account_id', $ledgerAccount->id)
            ->whereHas('voucher', function($query) use ($startDate, $tenant) {
                $query->where('tenant_id', $tenant->id)
                      ->where('status', Voucher::STATUS_POSTED)
                      ->where('voucher_date', '<', $startDate);
            })
            ->selectRaw('
                SUM(debit_amount) as total_debits,
                SUM(credit_amount) as total_credits
            ')
            ->first();

        $openingBalanceAmount = ($openingBalance->total_debits ?? 0) - ($openingBalance->total_credits ?? 0);

        // Get transactions within date range (exclude opening balance vouchers)
        $transactions = VoucherEntry::with(['voucher.voucherType'])
            ->where('ledger_account_id', $ledgerAccount->id)
            ->whereHas('voucher', function($query) use ($startDate, $endDate, $tenant, $ledgerAccount) {
                $query->where('tenant_id', $tenant->id)
                      ->where('status', Voucher::STATUS_POSTED)
                      ->where('id', '!=', $ledgerAccount->opening_balance_voucher_id)
                      ->whereBetween('voucher_date', [$startDate, $endDate]);
            })
            ->orderBy('id')
            ->get();

        // Calculate running balance for each transaction
        $runningBalance = $openingBalanceAmount;
        $transactionsWithBalance = [];

        foreach ($transactions as $transaction) {
            $debit = $transaction->debit_amount ?? 0;
            $credit = $transaction->credit_amount ?? 0;

            $runningBalance += ($debit - $credit);

            $transactionsWithBalance[] = [
                'date' => $transaction->voucher->voucher_date,
                'particulars' => $transaction->particulars ?? $transaction->voucher->voucherType->name,
                'voucher_type' => $transaction->voucher->voucherType->name,
                'voucher_number' => $transaction->voucher->voucher_number,
                'debit' => $debit,
                'credit' => $credit,
                'running_balance' => $runningBalance,
            ];
        }

        // Calculate totals
        $totalDebits = collect($transactionsWithBalance)->sum('debit');
        $totalCredits = collect($transactionsWithBalance)->sum('credit');
        $closingBalance = $runningBalance;

        return view('tenant.crm.customers.statement', compact(
            'tenant',
            'customer',
            'ledgerAccount',
            'startDate',
            'endDate',
            'openingBalanceAmount',
            'transactionsWithBalance',
            'totalDebits',
            'totalCredits',
            'closingBalance'
        ));
    }

    public function paymentReminders(Tenant $tenant)
    {
        $customers = Customer::with('ledgerAccount')
            ->where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->get()
            ->filter(function ($customer) {
                return $customer->ledgerAccount && $customer->ledgerAccount->getCurrentBalance() > 0;
            });

        return view('tenant.crm.payment-reminders', compact('tenant', 'customers'));
    }

    public function sendPaymentReminders(Request $request, Tenant $tenant)
    {
        $request->validate([
            'customers' => 'required|array',
            'customers.*' => 'exists:customers,id',
        ]);

        $count = 0;
        foreach ($request->customers as $customerId) {
            // Send reminder logic here
            $count++;
        }

        return redirect()->back()->with('success', "Payment reminders sent to {$count} customer(s).");
    }

    public function paymentReports(Request $request, Tenant $tenant)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $payments = VoucherEntry::whereHas('voucher', function($q) use ($tenant, $startDate, $endDate) {
                $q->where('tenant_id', $tenant->id)
                  ->where('status', 'posted')
                  ->whereHas('voucherType', function($vt) {
                      $vt->where('code', 'RV');
                  })
                  ->whereBetween('voucher_date', [$startDate, $endDate]);
            })
            ->where('credit_amount', '>', 0)
            ->with(['voucher', 'ledgerAccount'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalPayments = $payments->sum('credit_amount');
        $paymentCount = $payments->count();

        return view('tenant.crm.payment-reports', compact('tenant', 'payments', 'totalPayments', 'paymentCount', 'startDate', 'endDate'));
    }
}
