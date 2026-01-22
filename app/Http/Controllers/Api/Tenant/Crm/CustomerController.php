<?php

namespace App\Http\Controllers\Api\Tenant\Crm;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\LedgerAccount;
use App\Models\Tenant;
use App\Models\Voucher;
use App\Models\VoucherEntry;
use App\Models\VoucherType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * List customers with filters and pagination.
     */
    public function index(Request $request, Tenant $tenant)
    {
        $query = Customer::where('tenant_id', $tenant->id)
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

        if ($request->filled('customer_type')) {
            $query->where('customer_type', $request->get('customer_type'));
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
        $customers = $query->paginate($perPage);

        $customers->getCollection()->transform(function ($customer) {
            $customer->display_name = $customer->display_name
                ?? $customer->full_name
                ?? trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''))
                ?? $customer->company_name
                ?? $customer->email;
            $customer->outstanding_balance = $customer->ledgerAccount?->getCurrentBalance()
                ?? $customer->ledgerAccount?->current_balance
                ?? 0;

            return $customer;
        });

        $statistics = [
            'total_customers' => Customer::where('tenant_id', $tenant->id)->count(),
            'active_customers' => Customer::where('tenant_id', $tenant->id)->where('status', 'active')->count(),
            'inactive_customers' => Customer::where('tenant_id', $tenant->id)->where('status', 'inactive')->count(),
            'individual_customers' => Customer::where('tenant_id', $tenant->id)->where('customer_type', 'individual')->count(),
            'business_customers' => Customer::where('tenant_id', $tenant->id)->where('customer_type', 'business')->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Customers retrieved successfully',
            'data' => $customers,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Show customer details.
     */
    public function show(Request $request, Tenant $tenant, Customer $customer)
    {
        if ($customer->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
            ], 404);
        }

        $customer->load('ledgerAccount');
        $outstandingBalance = $customer->ledgerAccount?->getCurrentBalance()
            ?? $customer->ledgerAccount?->current_balance
            ?? 0;

        return response()->json([
            'success' => true,
            'message' => 'Customer retrieved successfully',
            'data' => [
                'customer' => $customer,
                'outstanding_balance' => $outstandingBalance,
            ],
        ]);
    }

    /**
     * Create a new customer.
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
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $customer = new Customer($request->except([
                'opening_balance_amount',
                'opening_balance_type',
                'opening_balance_date',
            ]));
            $customer->tenant_id = $tenant->id;
            $customer->status = $customer->status ?? 'active';
            $customer->save();

            $customer->refresh();

            if (!$customer->ledgerAccount) {
                throw new \Exception('Customer ledger account not created.');
            }

            $openingBalanceAmount = (float) $request->input('opening_balance_amount', 0);
            $openingBalanceType = $request->input('opening_balance_type', 'none');
            $openingBalanceDate = $request->input('opening_balance_date', now()->format('Y-m-d'));

            if ($openingBalanceAmount > 0 && $openingBalanceType !== 'none') {
                $this->createOpeningBalanceVoucher($customer, $openingBalanceAmount, $openingBalanceType, $openingBalanceDate);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully',
                'data' => $customer->load('ledgerAccount'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create customer: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update customer details.
     */
    public function update(Request $request, Tenant $tenant, Customer $customer)
    {
        if ($customer->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'customer_type' => 'required|in:individual,business',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
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
            'status' => 'nullable|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $customer->fill($request->all());
        $customer->save();

        return response()->json([
            'success' => true,
            'message' => 'Customer updated successfully',
            'data' => $customer->load('ledgerAccount'),
        ]);
    }

    /**
     * Delete a customer.
     */
    public function destroy(Request $request, Tenant $tenant, Customer $customer)
    {
        if ($customer->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
            ], 404);
        }

        if (method_exists($customer, 'invoices') && $customer->invoices()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Customer cannot be deleted because it has related invoices',
            ], 422);
        }

        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully',
        ]);
    }

    /**
     * Toggle customer status.
     */
    public function toggleStatus(Request $request, Tenant $tenant, Customer $customer)
    {
        if ($customer->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
            ], 404);
        }

        $customer->status = $customer->status === 'active' ? 'inactive' : 'active';
        $customer->save();

        return response()->json([
            'success' => true,
            'message' => 'Customer status updated successfully',
            'data' => [
                'id' => $customer->id,
                'status' => $customer->status,
            ],
        ]);
    }

    /**
     * Customer statements list with balances.
     */
    public function statements(Request $request, Tenant $tenant)
    {
        $query = Customer::where('tenant_id', $tenant->id)
            ->with('ledgerAccount');

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

        if ($request->filled('customer_type')) {
            $query->where('customer_type', $request->get('customer_type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $customers = $query->get()->map(function ($customer) {
            $balance = $customer->ledgerAccount?->getCurrentBalance()
                ?? $customer->ledgerAccount?->current_balance
                ?? 0;
            $customer->running_balance = $balance;
            $customer->balance_type = $balance >= 0 ? 'receivable' : 'payable';
            $customer->display_name = $customer->display_name
                ?? $customer->full_name
                ?? trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''))
                ?? $customer->company_name
                ?? $customer->email;

            return $customer;
        });

        $totalCustomers = $customers->count();
        $totalReceivable = $customers->where('running_balance', '>', 0)->sum('running_balance');
        $totalPayable = abs($customers->where('running_balance', '<', 0)->sum('running_balance'));
        $netBalance = $customers->sum('running_balance');

        $perPage = (int) $request->get('per_page', 50);
        $currentPage = (int) $request->get('page', 1);
        $items = $customers->forPage($currentPage, $perPage)->values();

        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $totalCustomers,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return response()->json([
            'success' => true,
            'message' => 'Customer statements retrieved successfully',
            'data' => $paginated,
            'statistics' => [
                'total_customers' => $totalCustomers,
                'total_receivable' => $totalReceivable,
                'total_payable' => $totalPayable,
                'net_balance' => $netBalance,
            ],
        ]);
    }

    /**
     * Customer statement detail.
     */
    public function statement(Request $request, Tenant $tenant, Customer $customer)
    {
        if ($customer->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
            ], 404);
        }

        $customer->load('ledgerAccount');
        if (!$customer->ledgerAccount) {
            return response()->json([
                'success' => false,
                'message' => 'Customer ledger account not found',
            ], 422);
        }

        $ledgerAccount = $customer->ledgerAccount;

        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $openingBalance = VoucherEntry::where('ledger_account_id', $ledgerAccount->id)
            ->whereHas('voucher', function ($q) use ($tenant, $startDate) {
                $q->where('tenant_id', $tenant->id)
                    ->whereDate('voucher_date', '<', $startDate);
            })
            ->selectRaw('SUM(debit_amount) as total_debits, SUM(credit_amount) as total_credits')
            ->first();

        $openingBalanceAmount = ($openingBalance->total_debits ?? 0) - ($openingBalance->total_credits ?? 0);

        $transactions = VoucherEntry::with(['voucher.voucherType'])
            ->where('ledger_account_id', $ledgerAccount->id)
            ->whereHas('voucher', function ($q) use ($tenant, $startDate, $endDate) {
                $q->where('tenant_id', $tenant->id)
                    ->where('status', Voucher::STATUS_POSTED)
                    ->whereBetween('voucher_date', [$startDate, $endDate]);
            })
            ->when($ledgerAccount->opening_balance_voucher_id, function ($query) use ($ledgerAccount) {
                $query->whereHas('voucher', function ($q) use ($ledgerAccount) {
                    $q->where('id', '!=', $ledgerAccount->opening_balance_voucher_id);
                });
            })
            ->orderBy('id')
            ->get();

        $runningBalance = $openingBalanceAmount;
        $transactionsWithBalance = [];

        foreach ($transactions as $transaction) {
            $runningBalance += ($transaction->debit_amount - $transaction->credit_amount);
            $transactionsWithBalance[] = [
                'date' => $transaction->voucher->voucher_date->format('Y-m-d'),
                'particulars' => $transaction->particulars ?? ($transaction->voucher->voucherType->name ?? null),
                'voucher_type' => $transaction->voucher->voucherType->name ?? null,
                'voucher_number' => ($transaction->voucher->voucherType->prefix ?? '') . $transaction->voucher->voucher_number,
                'debit' => (float) $transaction->debit_amount,
                'credit' => (float) $transaction->credit_amount,
                'running_balance' => (float) $runningBalance,
            ];
        }

        $totalDebits = collect($transactionsWithBalance)->sum('debit');
        $totalCredits = collect($transactionsWithBalance)->sum('credit');
        $closingBalance = $runningBalance;

        return response()->json([
            'success' => true,
            'message' => 'Customer statement retrieved successfully',
            'data' => [
                'customer' => $customer,
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                'opening_balance' => $openingBalanceAmount,
                'total_debits' => $totalDebits,
                'total_credits' => $totalCredits,
                'closing_balance' => $closingBalance,
                'transactions' => $transactionsWithBalance,
            ],
        ]);
    }

    /**
     * Create opening balance voucher for customer
     */
    private function createOpeningBalanceVoucher(Customer $customer, $amount, $type, $date)
    {
        $journalVoucherType = VoucherType::where('tenant_id', $customer->tenant_id)
            ->where('code', 'JV')
            ->first();

        if (!$journalVoucherType) {
            $journalVoucherType = VoucherType::create([
                'tenant_id' => $customer->tenant_id,
                'name' => 'Journal Voucher',
                'code' => 'JV',
                'prefix' => 'JV-',
                'abbreviation' => 'JV',
                'is_active' => true,
            ]);
        }

        $openingBalanceEquity = LedgerAccount::where('tenant_id', $customer->tenant_id)
            ->whereHas('accountGroup', function ($q) {
                $q->where('code', 'OBE');
            })
            ->first();

        if (!$openingBalanceEquity) {
            throw new \Exception('Opening Balance Equity account not found.');
        }

        $voucher = Voucher::create([
            'tenant_id' => $customer->tenant_id,
            'voucher_type_id' => $journalVoucherType->id,
            'voucher_number' => $journalVoucherType->getNextVoucherNumber(),
            'voucher_date' => $date,
            'reference_number' => null,
            'narration' => 'Opening balance for customer ' . ($customer->company_name ?? $customer->first_name . ' ' . $customer->last_name),
            'total_amount' => $amount,
            'status' => 'posted',
            'created_by' => auth()->id(),
            'posted_at' => now(),
            'posted_by' => auth()->id(),
        ]);

        if ($type === 'debit') {
            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $customer->ledger_account_id,
                'debit_amount' => $amount,
                'credit_amount' => 0,
                'particulars' => 'Opening balance',
            ]);

            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $openingBalanceEquity->id,
                'debit_amount' => 0,
                'credit_amount' => $amount,
                'particulars' => 'Opening balance equity',
            ]);
        } else {
            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $openingBalanceEquity->id,
                'debit_amount' => $amount,
                'credit_amount' => 0,
                'particulars' => 'Opening balance equity',
            ]);

            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $customer->ledger_account_id,
                'debit_amount' => 0,
                'credit_amount' => $amount,
                'particulars' => 'Opening balance',
            ]);
        }

        if ($customer->ledgerAccount) {
            $customer->ledgerAccount->update([
                'opening_balance_voucher_id' => $voucher->id,
            ]);
        }

        if ($customer->ledgerAccount && method_exists($customer->ledgerAccount, 'updateCurrentBalance')) {
            $customer->ledgerAccount->updateCurrentBalance();
        }

        return $voucher;
    }
}
