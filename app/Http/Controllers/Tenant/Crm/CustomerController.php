<?php

namespace App\Http\Controllers\Tenant\Crm;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $customer = new Customer($request->except(['save_and_new']));
            $customer->tenant_id = $tenant->id;
            $customer->status = 'active';
            $customer->save();

            // Determine redirect based on save_and_new parameter
            if ($request->has('save_and_new') && $request->save_and_new) {
                return redirect()->route('tenant.crm.customers.create', ['tenant' => $tenant->slug])
                    ->with('success', 'Customer created successfully. You can now add another customer.');
            }

            return redirect()->route('tenant.crm.customers.index', ['tenant' => $tenant->slug])
                ->with('success', 'Customer created successfully.');
        } catch (\Exception $e) {
            \Log::error('Error creating customer: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while creating the customer. Please try again.')
                ->withInput();
        }
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

        return view('tenant.crm.customers.show', compact('customer', 'tenant'));
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
            \Log::error('Error updating customer: ' . $e->getMessage());

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
            \Log::error('Error deleting customer: ' . $e->getMessage());

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
            \Log::error('Error toggling customer status: ' . $e->getMessage());

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
            \Log::error('Error in bulk action: ' . $e->getMessage());

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

        // Paginate manually
        $perPage = 20;
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
            'search' => $request->get('search'),
            'customer_type' => $request->get('customer_type'),
            'status' => $request->get('status'),
            'sort' => $sortField,
            'direction' => $sortDirection
        ]);
    }
}
