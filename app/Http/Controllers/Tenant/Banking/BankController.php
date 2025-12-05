<?php

namespace App\Http\Controllers\Tenant\Banking;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Tenant;
use App\Models\AccountGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BanksExport;

class BankController extends Controller
{
    /**
     * Display a listing of bank accounts
     */
    public function index(Request $request, Tenant $tenant)
    {
        $query = Bank::where('tenant_id', $tenant->id)
            ->with(['ledgerAccount']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('bank_name', 'like', "%{$search}%")
                  ->orWhere('account_number', 'like', "%{$search}%")
                  ->orWhere('account_name', 'like', "%{$search}%")
                  ->orWhere('branch_name', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Bank name filter
        if ($request->filled('bank_name')) {
            $query->where('bank_name', $request->bank_name);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Export to Excel
        if ($request->has('export') && $request->export === 'excel') {
            return Excel::download(new BanksExport($query->get()), 'bank-accounts-' . now()->format('Y-m-d') . '.xlsx');
        }

        $banks = $query->paginate(20)->withQueryString();

        // Get unique bank names for filter
        $bankNames = Bank::where('tenant_id', $tenant->id)
            ->select('bank_name')
            ->distinct()
            ->orderBy('bank_name')
            ->pluck('bank_name');

        // Calculate summary statistics
        $stats = [
            'total_banks' => Bank::where('tenant_id', $tenant->id)->count(),
            'active_banks' => Bank::where('tenant_id', $tenant->id)->where('status', 'active')->count(),
            'total_balance' => Bank::where('tenant_id', $tenant->id)
                ->where('status', 'active')
                ->sum('current_balance'),
            'needs_reconciliation' => Bank::where('tenant_id', $tenant->id)
                ->where('enable_reconciliation', true)
                ->where(function($q) {
                    $q->whereNull('last_reconciliation_date')
                      ->orWhereDate('last_reconciliation_date', '<', now()->subDays(30));
                })
                ->count(),
        ];

        return view('tenant.banking.banks.index', compact('tenant', 'banks', 'bankNames', 'stats'));
    }

    /**
     * Show the form for creating a new bank account
     */
    public function create(Tenant $tenant)
    {
        // Get account groups for reference
        $accountGroups = AccountGroup::where('tenant_id', $tenant->id)
            ->where('nature', 'assets')
            ->get();

        return view('tenant.banking.banks.create', compact('tenant', 'accountGroups'));
    }

    /**
     * Store a newly created bank account
     */
    public function store(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
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

        $validated['tenant_id'] = $tenant->id;
        $validated['current_balance'] = $validated['opening_balance'] ?? 0;
        $validated['is_primary'] = $request->has('is_primary');
        $validated['is_payroll_account'] = $request->has('is_payroll_account');
        $validated['enable_reconciliation'] = $request->has('enable_reconciliation');
        $validated['enable_auto_import'] = $request->has('enable_auto_import');

        try {
            $bank = Bank::create($validated);

            Log::info('Bank account created', [
                'tenant_id' => $tenant->id,
                'bank_id' => $bank->id,
                'bank_name' => $bank->bank_name,
                'account_number' => $bank->account_number,
            ]);

            return redirect()
                ->route('tenant.banking.banks.show', ['tenant' => $tenant->slug, 'bank' => $bank->id])
                ->with('success', 'Bank account created successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to create bank account', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to create bank account. ' . $e->getMessage());
        }
    }

    /**
     * Display the specified bank account
     */
    public function show(Tenant $tenant, $bankId)
    {
        $bank = Bank::with(['ledgerAccount', 'tenant'])
            ->where('tenant_id', $tenant->id)
            ->findOrFail($bankId);

        // Get recent transactions
        $recentTransactions = [];
        if ($bank->ledgerAccount) {
            $recentTransactions = $bank->ledgerAccount->voucherEntries()
                ->with(['voucher.voucherType'])
                ->whereHas('voucher', function($q) {
                    $q->where('status', 'posted');
                })
                ->latest()
                ->limit(10)
                ->get();
        }

        // Get monthly stats
        $monthlyStats = [
            'transactions_count' => $bank->getMonthlyTransactionsCount(),
            'reconciliation_status' => $bank->getReconciliationStatus(),
            'account_age_days' => $bank->getAccountAge(),
        ];

        return view('tenant.banking.banks.show', compact('tenant', 'bank', 'recentTransactions', 'monthlyStats'));
    }

    /**
     * Show the form for editing the specified bank account
     */
    public function edit(Tenant $tenant, $bankId)
    {
        $bank = Bank::where('tenant_id', $tenant->id)->findOrFail($bankId);

        $accountGroups = AccountGroup::where('tenant_id', $tenant->id)
            ->where('nature', 'assets')
            ->get();

        return view('tenant.banking.banks.edit', compact('tenant', 'bank', 'accountGroups'));
    }

    /**
     * Update the specified bank account
     */
    public function update(Request $request, Tenant $tenant, $bankId)
    {
        $bank = Bank::where('tenant_id', $tenant->id)->findOrFail($bankId);

        $validated = $request->validate([
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

        $validated['is_primary'] = $request->has('is_primary');
        $validated['is_payroll_account'] = $request->has('is_payroll_account');
        $validated['enable_reconciliation'] = $request->has('enable_reconciliation');
        $validated['enable_auto_import'] = $request->has('enable_auto_import');

        try {
            $bank->update($validated);

            Log::info('Bank account updated', [
                'tenant_id' => $tenant->id,
                'bank_id' => $bank->id,
            ]);

            return redirect()
                ->route('tenant.banking.banks.show', ['tenant' => $tenant->slug, 'bank' => $bank->id])
                ->with('success', 'Bank account updated successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to update bank account', [
                'tenant_id' => $tenant->id,
                'bank_id' => $bank->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to update bank account. ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified bank account
     */
    public function destroy(Tenant $tenant, $bankId)
    {
        $bank = Bank::where('tenant_id', $tenant->id)->findOrFail($bankId);

        if (!$bank->canBeDeleted()) {
            return back()->with('error', 'Cannot delete bank account with transactions or non-zero balance.');
        }

        try {
            $bank->delete();

            Log::info('Bank account deleted', [
                'tenant_id' => $tenant->id,
                'bank_id' => $bank->id,
            ]);

            return redirect()
                ->route('tenant.banking.banks.index', ['tenant' => $tenant->slug])
                ->with('success', 'Bank account deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to delete bank account', [
                'tenant_id' => $tenant->id,
                'bank_id' => $bank->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to delete bank account. ' . $e->getMessage());
        }
    }

    /**
     * Get bank statement (transactions)
     */
    public function statement(Request $request, Tenant $tenant, $bankId)
    {
        $bank = Bank::with(['ledgerAccount'])
            ->where('tenant_id', $tenant->id)
            ->findOrFail($bankId);

        if (!$bank->ledgerAccount) {
            return redirect()->back()
                ->with('error', 'Bank does not have an associated ledger account.');
        }

        $ledgerAccount = $bank->ledgerAccount;

        // Get date range
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Get opening balance
        $openingBalance = $ledgerAccount->getBalanceAsOf(date('Y-m-d', strtotime($startDate . ' -1 day')));

        // Get transactions
        $transactions = $ledgerAccount->voucherEntries()
            ->with(['voucher.voucherType'])
            ->whereHas('voucher', function($q) use ($startDate, $endDate) {
                $q->where('status', 'posted')
                  ->whereBetween('voucher_date', [$startDate, $endDate]);
            })
            ->get();

        // Calculate running balance
        $runningBalance = $openingBalance;
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

        return view('tenant.banking.banks.statement', compact(
            'tenant',
            'bank',
            'ledgerAccount',
            'startDate',
            'endDate',
            'openingBalance',
            'transactionsWithBalance',
            'totalDebits',
            'totalCredits',
            'closingBalance'
        ));
    }

    /**
     * Print bank statement
     */
    public function statementPrint(Request $request, Tenant $tenant, $bankId)
    {
        $bank = Bank::with(['ledgerAccount'])
            ->where('tenant_id', $tenant->id)
            ->findOrFail($bankId);

        if (!$bank->ledgerAccount) {
            return redirect()->back()
                ->with('error', 'Bank does not have an associated ledger account.');
        }

        $ledgerAccount = $bank->ledgerAccount;
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        $openingBalanceAmount = $ledgerAccount->getBalanceAsOf(date('Y-m-d', strtotime($startDate . ' -1 day')));

        $transactions = $ledgerAccount->voucherEntries()
            ->with(['voucher.voucherType'])
            ->whereHas('voucher', function($q) use ($startDate, $endDate) {
                $q->where('status', 'posted')
                  ->whereBetween('voucher_date', [$startDate, $endDate]);
            })
            ->get();

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

        $totalDebits = collect($transactionsWithBalance)->sum('debit');
        $totalCredits = collect($transactionsWithBalance)->sum('credit');
        $closingBalance = $runningBalance;

        return view('tenant.banking.banks.statement-print', compact(
            'tenant',
            'bank',
            'startDate',
            'endDate',
            'openingBalanceAmount',
            'transactionsWithBalance',
            'totalDebits',
            'totalCredits',
            'closingBalance'
        ));
    }
}
