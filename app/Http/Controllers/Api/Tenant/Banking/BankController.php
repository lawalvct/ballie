<?php

namespace App\Http\Controllers\Api\Tenant\Banking;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BankController extends Controller
{
    /**
     * List bank accounts with filters and pagination.
     */
    public function index(Request $request, Tenant $tenant)
    {
        $query = Bank::where('tenant_id', $tenant->id)
            ->with(['ledgerAccount']);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('bank_name', 'like', "%{$search}%")
                    ->orWhere('account_number', 'like', "%{$search}%")
                    ->orWhere('account_name', 'like', "%{$search}%")
                    ->orWhere('branch_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('bank_name')) {
            $query->where('bank_name', $request->get('bank_name'));
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['bank_name', 'account_name', 'account_number', 'status', 'created_at', 'current_balance'];

        if (in_array($sortBy, $allowedSorts, true)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $perPage = (int) $request->get('per_page', 20);
        $banks = $query->paginate($perPage);

        $banks->getCollection()->transform(function (Bank $bank) {
            return $this->formatBank($bank);
        });

        $bankNames = Bank::where('tenant_id', $tenant->id)
            ->select('bank_name')
            ->distinct()
            ->orderBy('bank_name')
            ->pluck('bank_name');

        $stats = [
            'total_banks' => Bank::where('tenant_id', $tenant->id)->count(),
            'active_banks' => Bank::where('tenant_id', $tenant->id)->where('status', 'active')->count(),
            'total_balance' => (float) Bank::where('tenant_id', $tenant->id)
                ->where('status', 'active')
                ->sum('current_balance'),
            'needs_reconciliation' => Bank::where('tenant_id', $tenant->id)
                ->get()
                ->filter(function (Bank $bank) {
                    return $bank->needsReconciliation();
                })
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Bank accounts retrieved successfully',
            'data' => $banks,
            'bank_names' => $bankNames,
            'statistics' => $stats,
        ]);
    }

    /**
     * Get create form options.
     */
    public function create(Request $request, Tenant $tenant)
    {
        return response()->json([
            'success' => true,
            'message' => 'Bank form data retrieved successfully',
            'data' => [
                'account_types' => [
                    ['value' => 'savings', 'label' => 'Savings'],
                    ['value' => 'current', 'label' => 'Current/Checking'],
                    ['value' => 'fixed_deposit', 'label' => 'Fixed Deposit'],
                    ['value' => 'credit_card', 'label' => 'Credit Card'],
                    ['value' => 'loan', 'label' => 'Loan Account'],
                    ['value' => 'investment', 'label' => 'Investment'],
                    ['value' => 'other', 'label' => 'Other'],
                ],
                'currencies' => [
                    ['value' => 'NGN', 'label' => 'NGN - Nigerian Naira'],
                    ['value' => 'USD', 'label' => 'USD - US Dollar'],
                    ['value' => 'EUR', 'label' => 'EUR - Euro'],
                    ['value' => 'GBP', 'label' => 'GBP - British Pound'],
                ],
                'statuses' => [
                    ['value' => 'active', 'label' => 'Active'],
                    ['value' => 'inactive', 'label' => 'Inactive'],
                    ['value' => 'closed', 'label' => 'Closed'],
                    ['value' => 'suspended', 'label' => 'Suspended'],
                ],
            ],
        ]);
    }

    /**
     * Store a new bank account.
     */
    public function store(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255|unique:banks,account_number,NULL,id,tenant_id,' . $tenant->id,
            'account_type' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'branch_code' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:255',
            'iban' => 'nullable|string|max:255',
            'routing_number' => 'nullable|string|max:255',
            'sort_code' => 'nullable|string|max:255',
            'branch_address' => 'nullable|string|max:500',
            'branch_city' => 'nullable|string|max:255',
            'branch_state' => 'nullable|string|max:255',
            'branch_phone' => 'nullable|string|max:255',
            'branch_email' => 'nullable|email|max:255',
            'relationship_manager' => 'nullable|string|max:255',
            'manager_phone' => 'nullable|string|max:255',
            'manager_email' => 'nullable|email|max:255',
            'currency' => 'required|string|size:3',
            'opening_balance' => 'nullable|numeric|min:0',
            'minimum_balance' => 'nullable|numeric|min:0',
            'overdraft_limit' => 'nullable|numeric|min:0',
            'account_opening_date' => 'nullable|date',
            'online_banking_url' => 'nullable|url|max:255',
            'online_banking_username' => 'nullable|string|max:255',
            'online_banking_notes' => 'nullable|string',
            'monthly_maintenance_fee' => 'nullable|numeric|min:0',
            'transaction_limit_daily' => 'nullable|numeric|min:0',
            'transaction_limit_monthly' => 'nullable|numeric|min:0',
            'free_transactions_per_month' => 'nullable|integer|min:0',
            'excess_transaction_fee' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive,closed,suspended',
            'is_primary' => 'nullable|boolean',
            'is_payroll_account' => 'nullable|boolean',
            'enable_reconciliation' => 'nullable|boolean',
            'enable_auto_import' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $validated['tenant_id'] = $tenant->id;
        $validated['current_balance'] = $validated['opening_balance'] ?? 0;
        $validated['is_primary'] = $request->boolean('is_primary');
        $validated['is_payroll_account'] = $request->boolean('is_payroll_account');
        $validated['enable_reconciliation'] = $request->boolean('enable_reconciliation');
        $validated['enable_auto_import'] = $request->boolean('enable_auto_import');

        $bank = Bank::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Bank account created successfully',
            'data' => [
                'bank' => $this->formatBank($bank->fresh(['ledgerAccount'])),
            ],
        ], 201);
    }

    /**
     * Show bank details.
     */
    public function show(Request $request, Tenant $tenant, Bank $bank)
    {
        if ($bank->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bank account not found',
            ], 404);
        }

        $bank->load(['ledgerAccount']);

        $recentTransactions = [];
        if ($bank->ledgerAccount) {
            $recentTransactions = $bank->ledgerAccount->voucherEntries()
                ->with(['voucher.voucherType'])
                ->whereHas('voucher', function ($q) {
                    $q->where('status', 'posted');
                })
                ->latest()
                ->limit(10)
                ->get()
                ->map(function ($entry) {
                    return [
                        'id' => $entry->id,
                        'voucher_id' => $entry->voucher_id,
                        'voucher_number' => $entry->voucher?->voucher_number,
                        'voucher_date' => $entry->voucher?->voucher_date?->format('Y-m-d'),
                        'voucher_type' => $entry->voucher?->voucherType?->name,
                        'particulars' => $entry->particulars ?? $entry->voucher?->voucherType?->name,
                        'debit' => (float) ($entry->debit_amount ?? 0),
                        'credit' => (float) ($entry->credit_amount ?? 0),
                    ];
                })
                ->values();
        }

        $monthlyStats = [
            'transactions_count' => $bank->getMonthlyTransactionsCount(),
            'reconciliation_status' => $bank->getReconciliationStatus(),
            'account_age_days' => $bank->getAccountAge(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Bank account retrieved successfully',
            'data' => [
                'bank' => $this->formatBank($bank),
                'recent_transactions' => $recentTransactions,
                'monthly_stats' => $monthlyStats,
            ],
        ]);
    }

    /**
     * Update a bank account.
     */
    public function update(Request $request, Tenant $tenant, Bank $bank)
    {
        if ($bank->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bank account not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255|unique:banks,account_number,' . $bank->id . ',id,tenant_id,' . $tenant->id,
            'account_type' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'branch_code' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:255',
            'iban' => 'nullable|string|max:255',
            'routing_number' => 'nullable|string|max:255',
            'sort_code' => 'nullable|string|max:255',
            'branch_address' => 'nullable|string|max:500',
            'branch_city' => 'nullable|string|max:255',
            'branch_state' => 'nullable|string|max:255',
            'branch_phone' => 'nullable|string|max:255',
            'branch_email' => 'nullable|email|max:255',
            'relationship_manager' => 'nullable|string|max:255',
            'manager_phone' => 'nullable|string|max:255',
            'manager_email' => 'nullable|email|max:255',
            'currency' => 'required|string|size:3',
            'minimum_balance' => 'nullable|numeric|min:0',
            'overdraft_limit' => 'nullable|numeric|min:0',
            'account_opening_date' => 'nullable|date',
            'online_banking_url' => 'nullable|url|max:255',
            'online_banking_username' => 'nullable|string|max:255',
            'online_banking_notes' => 'nullable|string',
            'monthly_maintenance_fee' => 'nullable|numeric|min:0',
            'transaction_limit_daily' => 'nullable|numeric|min:0',
            'transaction_limit_monthly' => 'nullable|numeric|min:0',
            'free_transactions_per_month' => 'nullable|integer|min:0',
            'excess_transaction_fee' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive,closed,suspended',
            'is_primary' => 'nullable|boolean',
            'is_payroll_account' => 'nullable|boolean',
            'enable_reconciliation' => 'nullable|boolean',
            'enable_auto_import' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $validated['is_primary'] = $request->boolean('is_primary');
        $validated['is_payroll_account'] = $request->boolean('is_payroll_account');
        $validated['enable_reconciliation'] = $request->boolean('enable_reconciliation');
        $validated['enable_auto_import'] = $request->boolean('enable_auto_import');

        $bank->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Bank account updated successfully',
            'data' => [
                'bank' => $this->formatBank($bank->fresh(['ledgerAccount'])),
            ],
        ]);
    }

    /**
     * Delete a bank account.
     */
    public function destroy(Request $request, Tenant $tenant, Bank $bank)
    {
        if ($bank->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bank account not found',
            ], 404);
        }

        if (!$bank->canBeDeleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete bank account with transactions or non-zero balance.',
            ], 422);
        }

        $bank->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bank account deleted successfully',
        ]);
    }

    /**
     * Bank statement data.
     */
    public function statement(Request $request, Tenant $tenant, Bank $bank)
    {
        if ($bank->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bank account not found',
            ], 404);
        }

        $bank->load(['ledgerAccount']);

        if (!$bank->ledgerAccount) {
            return response()->json([
                'success' => false,
                'message' => 'Bank does not have an associated ledger account.',
            ], 422);
        }

        $ledgerAccount = $bank->ledgerAccount;

        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $openingBalance = $ledgerAccount->getBalanceAsOf(date('Y-m-d', strtotime($startDate . ' -1 day')));

        $transactions = $ledgerAccount->voucherEntries()
            ->with(['voucher.voucherType'])
            ->whereHas('voucher', function ($q) use ($startDate, $endDate) {
                $q->where('status', 'posted')
                    ->whereBetween('voucher_date', [$startDate, $endDate]);
            })
            ->get();

        $runningBalance = $openingBalance;
        $transactionsWithBalance = [];

        foreach ($transactions as $transaction) {
            $debit = $transaction->debit_amount ?? 0;
            $credit = $transaction->credit_amount ?? 0;
            $runningBalance += ($debit - $credit);

            $transactionsWithBalance[] = [
                'date' => $transaction->voucher?->voucher_date?->format('Y-m-d'),
                'particulars' => $transaction->particulars ?? $transaction->voucher?->voucherType?->name,
                'voucher_type' => $transaction->voucher?->voucherType?->name,
                'voucher_number' => $transaction->voucher?->voucher_number,
                'debit' => (float) $debit,
                'credit' => (float) $credit,
                'running_balance' => (float) $runningBalance,
            ];
        }

        $totalDebits = collect($transactionsWithBalance)->sum('debit');
        $totalCredits = collect($transactionsWithBalance)->sum('credit');
        $closingBalance = $runningBalance;

        return response()->json([
            'success' => true,
            'message' => 'Bank statement retrieved successfully',
            'data' => [
                'bank' => $this->formatBank($bank),
                'date_range' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                'opening_balance' => (float) $openingBalance,
                'transactions' => $transactionsWithBalance,
                'totals' => [
                    'total_debits' => (float) $totalDebits,
                    'total_credits' => (float) $totalCredits,
                    'closing_balance' => (float) $closingBalance,
                ],
            ],
        ]);
    }

    private function formatBank(Bank $bank): array
    {
        return [
            'id' => $bank->id,
            'bank_name' => $bank->bank_name,
            'account_name' => $bank->account_name,
            'account_number' => $bank->account_number,
            'masked_account_number' => $bank->masked_account_number,
            'display_name' => $bank->display_name,
            'account_type' => $bank->account_type,
            'account_type_display' => $bank->account_type_display,
            'branch_name' => $bank->branch_name,
            'branch_code' => $bank->branch_code,
            'branch_address' => $bank->branch_address,
            'branch_city' => $bank->branch_city,
            'branch_state' => $bank->branch_state,
            'branch_phone' => $bank->branch_phone,
            'branch_email' => $bank->branch_email,
            'relationship_manager' => $bank->relationship_manager,
            'manager_phone' => $bank->manager_phone,
            'manager_email' => $bank->manager_email,
            'swift_code' => $bank->swift_code,
            'iban' => $bank->iban,
            'routing_number' => $bank->routing_number,
            'sort_code' => $bank->sort_code,
            'currency' => $bank->currency,
            'opening_balance' => (float) ($bank->opening_balance ?? 0),
            'current_balance' => (float) $bank->getCurrentBalance(),
            'available_balance' => (float) $bank->getAvailableBalance(),
            'minimum_balance' => (float) ($bank->minimum_balance ?? 0),
            'overdraft_limit' => (float) ($bank->overdraft_limit ?? 0),
            'account_opening_date' => $bank->account_opening_date?->format('Y-m-d'),
            'last_reconciliation_date' => $bank->last_reconciliation_date?->format('Y-m-d'),
            'last_reconciled_balance' => (float) ($bank->last_reconciled_balance ?? 0),
            'monthly_maintenance_fee' => (float) ($bank->monthly_maintenance_fee ?? 0),
            'transaction_limit_daily' => (float) ($bank->transaction_limit_daily ?? 0),
            'transaction_limit_monthly' => (float) ($bank->transaction_limit_monthly ?? 0),
            'free_transactions_per_month' => (int) ($bank->free_transactions_per_month ?? 0),
            'excess_transaction_fee' => (float) ($bank->excess_transaction_fee ?? 0),
            'online_banking_url' => $bank->online_banking_url,
            'online_banking_username' => $bank->online_banking_username,
            'online_banking_notes' => $bank->online_banking_notes,
            'description' => $bank->description,
            'notes' => $bank->notes,
            'status' => $bank->status,
            'status_color' => $bank->status_color,
            'is_primary' => (bool) $bank->is_primary,
            'is_payroll_account' => (bool) $bank->is_payroll_account,
            'enable_reconciliation' => (bool) $bank->enable_reconciliation,
            'enable_auto_import' => (bool) $bank->enable_auto_import,
            'reconciliation_status' => $bank->getReconciliationStatus(),
            'ledger_account_id' => $bank->ledger_account_id,
            'created_at' => $bank->created_at?->toDateTimeString(),
            'updated_at' => $bank->updated_at?->toDateTimeString(),
        ];
    }
}
