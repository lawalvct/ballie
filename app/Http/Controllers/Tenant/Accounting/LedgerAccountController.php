<?php

namespace App\Http\Controllers\Tenant\Accounting;

use App\Http\Controllers\Controller;
use App\Models\LedgerAccount;
use App\Models\AccountGroup;
use App\Models\Tenant;
use App\Services\OpeningBalanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LedgerAccountsImport;
use App\Exports\LedgerAccountsTemplateExport;
use App\Exports\AccountGroupsReferenceExport;

class LedgerAccountController extends Controller
{
    public function index(Request $request, Tenant $tenant)
    {
        // Get view type from request, default to 'list'
        $viewType = $request->get('view', 'list');

        $query = LedgerAccount::with(['accountGroup', 'parent'])
            ->where('tenant_id', $tenant->id);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by account group
        if ($request->filled('account_group_id')) {
            $query->where('account_group_id', $request->get('account_group_id'));
        }

        // Filter by account type
        if ($request->filled('account_type')) {
            $query->where('account_type', $request->get('account_type'));
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->get('is_active'));
        }

        // Filter by account nature (system/user)
        if ($request->filled('nature')) {
            $isSystem = $request->get('nature') === 'system';
            $query->where('is_system_account', $isSystem);
        }

        // Sorting
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');

        $allowedSorts = ['name', 'code', 'account_type', 'current_balance', 'created_at', 'is_active'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        }

        // For tree view, get all accounts without pagination
        if ($viewType === 'tree') {
            $accounts = $query->orderBy('code')->get();
            $ledgerAccounts = null; // Not needed for tree view
        } else {
            $ledgerAccounts = $query->paginate(100)->withQueryString();
            $accounts = $ledgerAccounts; // For list view compatibility
        }

        // Get statistics
        $totalAccounts = LedgerAccount::where('tenant_id', $tenant->id)->count();
        $activeAccounts = LedgerAccount::where('tenant_id', $tenant->id)->where('is_active', true)->count();
        $systemAccounts = LedgerAccount::where('tenant_id', $tenant->id)->where('is_system_account', true)->count();
        $userAccounts = LedgerAccount::where('tenant_id', $tenant->id)->where('is_system_account', false)->count();

        // Get account groups for filter dropdown
        $accountGroups = AccountGroup::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get account types for filter
        $accountTypes = LedgerAccount::where('tenant_id', $tenant->id)
            ->distinct()
            ->pluck('account_type')
            ->filter()
            ->sort();

        return view('tenant.accounting.ledger-accounts.index', compact(
            'tenant',
            'ledgerAccounts',
            'accounts', // Add this
            'totalAccounts',
            'activeAccounts',
            'systemAccounts',
            'userAccounts',
            'accountGroups',
            'accountTypes',
            'viewType'
        ));
    }

   public function create(Request $request, Tenant $tenant)
{
    // Create default account groups if none exist
    if (AccountGroup::where('tenant_id', $tenant->id)->count() === 0) {
        $this->createDefaultAccountGroups($tenant);
    }

    $accountGroups = AccountGroup::where('tenant_id', $tenant->id)
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

    $parentAccounts = LedgerAccount::where('tenant_id', $tenant->id)
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

    $accountTypes = ['asset', 'liability', 'income', 'expense', 'equity'];

    return view('tenant.accounting.ledger-accounts.create', compact(
        'tenant',
        'accountGroups',
        'parentAccounts',
        'accountTypes'
    ));
}

    public function store(Request $request, Tenant $tenant)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'code' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('ledger_accounts')->where(function ($query) use ($tenant) {
                        return $query->where('tenant_id', $tenant->id);
                    })
                ],
                'account_group_id' => 'required|exists:account_groups,id',
                'account_type' => 'required|in:asset,liability,income,expense,equity',
                'description' => 'nullable|string|max:500',
                'opening_balance' => 'nullable|numeric|min:0',
                'parent_id' => 'nullable|exists:ledger_accounts,id',
                'is_active' => 'boolean',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        try {
            $ledgerAccount = null;

            DB::transaction(function () use ($request, $tenant, &$ledgerAccount) {
                $openingBalance = $request->opening_balance ?? 0;

                $ledgerAccount = LedgerAccount::create([
                    'tenant_id' => $tenant->id,
                    'account_group_id' => $request->account_group_id,
                    'name' => $request->name,
                    'code' => strtoupper($request->code),
                    'account_type' => $request->account_type,
                    'description' => $request->description,
                    // Set opening_balance to 0 because the voucher will handle it
                    // This prevents double-counting
                    'opening_balance' => 0,
                    'current_balance' => 0,
                    'parent_id' => $request->parent_id,
                    'is_active' => $request->boolean('is_active', true),
                    'is_system_account' => false,
                ]);

                // If opening balance is provided and not zero, create opening balance entry
                if ($openingBalance && $openingBalance > 0) {
                    $openingBalanceService = new OpeningBalanceService();
                    $openingBalanceService->createOpeningBalanceEntry($ledgerAccount, $openingBalance);
                }

                // Log activity
                // activity()
                //     ->performedOn($ledgerAccount)
                //     ->causedBy(auth()->user())
                //     ->log('Ledger account created');
            });

            // Check if this is an AJAX request
            if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                // Load the account with its relationships for the response
                $ledgerAccount->load('accountGroup');

                return response()->json([
                    'success' => true,
                    'message' => 'Ledger account created successfully.',
                    'account' => [
                        'id' => $ledgerAccount->id,
                        'name' => $ledgerAccount->name,
                        'code' => $ledgerAccount->code,
                        'account_type' => $ledgerAccount->account_type,
                        'account_group' => [
                            'id' => $ledgerAccount->accountGroup->id,
                            'name' => $ledgerAccount->accountGroup->name,
                        ]
                    ]
                ]);
            }

            return redirect()
                ->route('tenant.accounting.ledger-accounts.index', ['tenant' => $tenant->slug])
                ->with('success', 'Ledger account created successfully.');

        } catch (\Exception $e) {
            // Check if this is an AJAX request
            if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create ledger account: ' . $e->getMessage()
                ], 422);
            }

            return back()
                ->withInput()
                ->with('error', 'Failed to create ledger account: ' . $e->getMessage());
        }
    }

public function show(Request $request, Tenant $tenant, LedgerAccount $ledgerAccount)
{
    $ledgerAccount->load(['accountGroup', 'parent', 'children']);

    // Get date filters (similar to product stock filtering)
    $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
    $toDate = $request->get('to_date', now()->toDateString());
    $asOfDate = $request->get('as_of_date', $toDate); // For balance calculation

    // Calculate opening balance (balance before from_date)
    $openingBalance = $ledgerAccount->getCurrentBalance(
        date('Y-m-d', strtotime($fromDate . ' -1 day')),
        false // Don't use cache for historical data
    );

    // Get transactions for the selected period
    $recentTransactions = $ledgerAccount->voucherEntries()
        ->with(['voucher.voucherType'])
        ->whereHas('voucher', function ($query) use ($fromDate, $toDate) {
            $query->where('status', 'posted')
                  ->whereBetween('voucher_date', [$fromDate, $toDate]);
        })
        ->orderByDesc(function ($query) {
            $query->select('voucher_date')
                  ->from('vouchers')
                  ->whereColumn('vouchers.id', 'voucher_entries.voucher_id')
                  ->limit(1);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(50);

    // Get account balance totals for the period
    $totalDebits = $ledgerAccount->voucherEntries()
        ->whereHas('voucher', function ($query) use ($fromDate, $toDate) {
            $query->where('status', 'posted')
                  ->whereBetween('voucher_date', [$fromDate, $toDate]);
        })
        ->sum('debit_amount');

    $totalCredits = $ledgerAccount->voucherEntries()
        ->whereHas('voucher', function ($query) use ($fromDate, $toDate) {
            $query->where('status', 'posted')
                  ->whereBetween('voucher_date', [$fromDate, $toDate]);
        })
        ->sum('credit_amount');

    $transactionCount = $ledgerAccount->voucherEntries()
        ->whereHas('voucher', function ($query) use ($fromDate, $toDate) {
            $query->where('status', 'posted')
                  ->whereBetween('voucher_date', [$fromDate, $toDate]);
        })
        ->count();

    $lastTransaction = $ledgerAccount->voucherEntries()
        ->whereHas('voucher', function ($query) {
            $query->where('status', 'posted');
        })
        ->latest()
        ->first();

    // Calculate closing balance as of the end date
    $closingBalance = $ledgerAccount->getCurrentBalance($asOfDate, false);

    // Determine account nature for proper balance calculation
    $accountType = $ledgerAccount->account_type ?? 'asset';

    // Calculate period movement
    if (in_array($accountType, ['asset', 'expense'])) {
        $periodMovement = $totalDebits - $totalCredits;
    } else {
        $periodMovement = $totalCredits - $totalDebits;
    }

    // Get current balance (as of today) for comparison
    $currentBalance = $ledgerAccount->getCurrentBalance(null, false);

    return view('tenant.accounting.ledger-accounts.show', compact(
        'tenant',
        'ledgerAccount',
        'recentTransactions',
        'totalDebits',
        'totalCredits',
        'transactionCount',
        'lastTransaction',
        'openingBalance',
        'closingBalance',
        'currentBalance',
        'periodMovement',
        'fromDate',
        'toDate',
        'asOfDate'
    ));
}


    public function edit(Request $request, Tenant $tenant, LedgerAccount $ledgerAccount)
    {
        $accountGroups = AccountGroup::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $parentAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->where('id', '!=', $ledgerAccount->id) // Exclude self
            ->orderBy('name')
            ->get();

        $accountTypes = ['asset', 'liability', 'income', 'expense', 'equity'];

        return view('tenant.accounting.ledger-accounts.edit', compact(
            'tenant',
            'ledgerAccount',
            'accountGroups',
            'parentAccounts',
            'accountTypes'
        ));
    }

    public function update(Request $request, Tenant $tenant, LedgerAccount $ledgerAccount)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('ledger_accounts')->where(function ($query) use ($tenant) {
                    return $query->where('tenant_id', $tenant->id);
                })->ignore($ledgerAccount->id)
            ],
            'account_group_id' => 'required|exists:account_groups,id',
            'account_type' => 'required|in:asset,liability,income,expense,equity',
            'description' => 'nullable|string|max:500',
            'opening_balance' => 'nullable|numeric',
            'parent_id' => 'nullable|exists:ledger_accounts,id',
            'is_active' => 'boolean',
        ]);

        try {
            DB::transaction(function () use ($request, $ledgerAccount) {
                $ledgerAccount->update([
                    'account_group_id' => $request->account_group_id,
                    'name' => $request->name,
                    'code' => strtoupper($request->code),
                    'account_type' => $request->account_type,
                    'description' => $request->description,
                    'opening_balance' => $request->opening_balance ?? $ledgerAccount->opening_balance,
                    'parent_id' => $request->parent_id,
                    'is_active' => $request->boolean('is_active', true),
                ]);

                // Log activity
                activity()
                    ->performedOn($ledgerAccount)
                    ->causedBy(auth()->user())
                    ->log('Ledger account updated');
            });

            return redirect()
                ->route('tenant.accounting.ledger-accounts.index', ['tenant' => $tenant->slug])
                ->with('success', 'Ledger account updated successfully.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update ledger account: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, Tenant $tenant, LedgerAccount $ledgerAccount)
    {
        try {
            // Check if account has transactions (uncomment when you have transactions)
            // if ($ledgerAccount->transactions()->exists()) {
            //     return back()->with('error', 'Cannot delete account with existing transactions.');
            // }

            // Check if account has child accounts
            if ($ledgerAccount->children()->exists()) {
                return back()->with('error', 'Cannot delete account with child accounts.');
            }

            // Don't allow deletion of system accounts
            if ($ledgerAccount->is_system_account) {
                return back()->with('error', 'Cannot delete system-defined accounts.');
            }

            DB::transaction(function () use ($ledgerAccount) {
                // Log activity before deletion
                activity()
                    ->performedOn($ledgerAccount)
                    ->causedBy(auth()->user())
                    ->log('Ledger account deleted');

                $ledgerAccount->delete();
            });

            return redirect()
                ->route('tenant.accounting.ledger-accounts.index', ['tenant' => $tenant->slug])
                ->with('success', 'Ledger account deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete ledger account: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Request $request, Tenant $tenant, LedgerAccount $ledgerAccount)
    {
        try {
            $ledgerAccount->update([
                'is_active' => !$ledgerAccount->is_active
            ]);

            $status = $ledgerAccount->is_active ? 'activated' : 'deactivated';

            return back()->with('success', "Ledger account {$status} successfully.");

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update account status: ' . $e->getMessage());
        }
    }

    public function bulkAction(Request $request, Tenant $tenant)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'ledger_accounts' => 'required|array|min:1',
            'ledger_accounts.*' => 'exists:ledger_accounts,id'
        ]);

        try {
            $accountIds = $request->ledger_accounts;
            $action = $request->action;

            DB::transaction(function () use ($accountIds, $action, $tenant) {
                $accounts = LedgerAccount::where('tenant_id', $tenant->id)
                    ->whereIn('id', $accountIds);

                switch ($action) {
                    case 'activate':
                        $accounts->update(['is_active' => true]);
                        break;
                    case 'deactivate':
                        $accounts->update(['is_active' => false]);
                        break;
                    case 'delete':
                        // Check for system accounts and accounts with transactions
                        $systemAccounts = $accounts->where('is_system_account', true)->count();
                        if ($systemAccounts > 0) {
                            throw new \Exception('Cannot delete system-defined accounts.');
                        }

                        // Check for accounts with children
                        $accountsWithChildren = $accounts->whereHas('children')->count();
                        if ($accountsWithChildren > 0) {
                            throw new \Exception('Cannot delete accounts with child accounts.');
                        }

                        // Uncomment when you have transactions
                        // $accountsWithTransactions = $accounts->whereHas('transactions')->count();
                        // if ($accountsWithTransactions > 0) {
                        //     throw new \Exception('Cannot delete accounts with existing transactions.');
                        // }

                        $accounts->delete();
                        break;
                }
            });

            $message = match($action) {
                'activate' => 'Selected accounts activated successfully.',
                'deactivate' => 'Selected accounts deactivated successfully.',
                'delete' => 'Selected accounts deleted successfully.',
            };

            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Bulk action failed: ' . $e->getMessage());
        }
    }

    public function downloadPdf(Request $request, Tenant $tenant)
    {
        $accounts = LedgerAccount::with(['accountGroup'])
            ->where('tenant_id', $tenant->id)
            ->orderBy('account_type')
            ->orderBy('code')
            ->get()
            ->groupBy('account_type');

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Chart of Accounts</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                h1 { text-align: center; color: #333; margin-bottom: 5px; }
                .company { text-align: center; color: #666; margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th { background: #f3f4f6; padding: 8px; text-align: left; border: 1px solid #ddd; font-weight: bold; }
                td { padding: 8px; border: 1px solid #ddd; }
                .section { background: #e5e7eb; font-weight: bold; padding: 8px; margin-top: 10px; }
                .amount { text-align: right; }
            </style>
        </head>
        <body>
            <h1>Chart of Accounts</h1>
            <div class="company">' . e($tenant->name) . '</div>
            <div class="company">As of ' . now()->format('F d, Y') . '</div>

            <table>
                <thead>
                    <tr>
                        <th width="15%">Code</th>
                        <th width="40%">Account Name</th>
                        <th width="25%">Account Group</th>
                        <th width="20%" class="amount">Balance</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($accounts as $type => $typeAccounts) {
            $html .= '<tr><td colspan="4" class="section">' . strtoupper($type) . '</td></tr>';

            foreach ($typeAccounts as $account) {
                $balance = number_format(abs($account->current_balance), 2);
                $html .= '<tr>
                    <td>' . e($account->code) . '</td>
                    <td>' . e($account->name) . '</td>
                    <td>' . e($account->accountGroup?->name ?? 'N/A') . '</td>
                    <td class="amount">' . $balance . '</td>
                </tr>';
            }
        }

        $html .= '</tbody></table></body></html>';

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream('chart-of-accounts-' . now()->format('Y-m-d') . '.pdf');
    }

    public function export(Request $request, Tenant $tenant)
    {
        $query = LedgerAccount::with(['accountGroup', 'parent'])
            ->where('tenant_id', $tenant->id);

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('account_group')) {
            $query->where('account_group_id', $request->get('account_group'));
        }

        if ($request->filled('account_type')) {
            $query->where('account_type', $request->get('account_type'));
        }

        if ($request->filled('status')) {
            $isActive = $request->get('status') === 'active';
            $query->where('is_active', $isActive);
        }

        $accounts = $query->orderBy('name')->get();

        $filename = 'ledger-accounts-' . now()->format('Y-m-d-H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($accounts) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Code',
                'Name',
                'Account Group',
                'Account Type',
                'Description',
                'Opening Balance',
                'Current Balance',
                'Parent Account',
                'Status',
                'System Account',
                'Created Date'
            ]);

            // CSV data
            foreach ($accounts as $account) {
                fputcsv($file, [
                    $account->code,
                    $account->name,
                    $account->accountGroup?->name,
                    ucfirst($account->account_type),
                    $account->description,
                    number_format($account->opening_balance, 2),
                    number_format($account->current_balance, 2),
                    $account->parent?->name,
                    $account->is_active ? 'Active' : 'Inactive',
                    $account->is_system_account ? 'Yes' : 'No',
                    $account->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function resetBalance(Request $request, Tenant $tenant, LedgerAccount $ledgerAccount)
    {
        $request->validate([
            'new_balance' => 'required|numeric',
            'reason' => 'required|string|max:255'
        ]);

        try {
            DB::transaction(function () use ($request, $ledgerAccount) {
                $oldBalance = $ledgerAccount->current_balance;
                $newBalance = $request->new_balance;

                $ledgerAccount->update([
                    'current_balance' => $newBalance
                ]);

                // Log the balance reset
                activity()
                    ->performedOn($ledgerAccount)
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'old_balance' => $oldBalance,
                        'new_balance' => $newBalance,
                        'reason' => $request->reason
                    ])
                    ->log('Account balance reset');
            });

            return back()->with('success', 'Account balance reset successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reset balance: ' . $e->getMessage());
        }
    }

    public function getChildren(Request $request, Tenant $tenant, LedgerAccount $ledgerAccount)
    {
        $children = $ledgerAccount->children()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'current_balance']);

        return response()->json($children);
    }

    public function moveAccount(Request $request, Tenant $tenant, LedgerAccount $ledgerAccount)
    {
        $request->validate([
            'new_parent_id' => 'nullable|exists:ledger_accounts,id',
            'new_account_group_id' => 'required|exists:account_groups,id'
        ]);

        try {
            DB::transaction(function () use ($request, $ledgerAccount) {
                $oldParentId = $ledgerAccount->parent_id;
                $oldGroupId = $ledgerAccount->account_group_id;

                $ledgerAccount->update([
                    'parent_id' => $request->new_parent_id,
                    'account_group_id' => $request->new_account_group_id
                ]);

                // Log the move
                activity()
                    ->performedOn($ledgerAccount)
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'old_parent_id' => $oldParentId,
                        'new_parent_id' => $request->new_parent_id,
                        'old_group_id' => $oldGroupId,
                        'new_group_id' => $request->new_account_group_id
                    ])
                    ->log('Account moved');
            });

            return back()->with('success', 'Account moved successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to move account: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete ledger accounts
     */
    public function bulkDelete(Tenant $tenant, Request $request)
    {
        $request->validate([
            'account_ids' => 'required|array',
            'account_ids.*' => 'exists:ledger_accounts,id'
        ]);

        try {
            $accounts = LedgerAccount::where('tenant_id', $tenant->id)
                ->whereIn('id', $request->account_ids)
                ->get();

            $deletedCount = 0;
            $errors = [];

            foreach ($accounts as $account) {
                // Check if account has transactions
                if ($account->voucherEntries()->count() > 0) {
                    $errors[] = "Account '{$account->name}' has transactions and cannot be deleted.";
                    continue;
                }

                // Check if account has children
                if ($account->children()->count() > 0) {
                    $errors[] = "Account '{$account->name}' has sub-accounts and cannot be deleted.";
                    continue;
                }

                $account->delete();
                $deletedCount++;
            }

            if ($deletedCount > 0) {
                $message = "Successfully deleted {$deletedCount} account(s).";
                if (!empty($errors)) {
                    $message .= " Some accounts could not be deleted.";
                }
                return redirect()->back()->with('success', $message)->with('bulk_errors', $errors);
            } else {
                return redirect()->back()->with('error', 'No accounts were deleted.')->with('bulk_errors', $errors);
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Bulk delete failed: ' . $e->getMessage());
        }
    }

    /**
     * Bulk activate ledger accounts
     */
    public function bulkActivate(Tenant $tenant, Request $request)
    {
        $request->validate([
            'account_ids' => 'required|array',
            'account_ids.*' => 'exists:ledger_accounts,id'
        ]);

        try {
            $updatedCount = LedgerAccount::where('tenant_id', $tenant->id)
                ->whereIn('id', $request->account_ids)
                ->update(['is_active' => true]);

            return redirect()->back()->with('success', "Successfully activated {$updatedCount} account(s).");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Bulk activation failed: ' . $e->getMessage());
        }
    }

    /**
     * Bulk deactivate ledger accounts
     */
    public function bulkDeactivate(Tenant $tenant, Request $request)
    {
        $request->validate([
            'account_ids' => 'required|array',
            'account_ids.*' => 'exists:ledger_accounts,id'
        ]);

        try {
            $updatedCount = LedgerAccount::where('tenant_id', $tenant->id)
                ->whereIn('id', $request->account_ids)
                ->update(['is_active' => false]);

            return redirect()->back()->with('success', "Successfully deactivated {$updatedCount} account(s).");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Bulk deactivation failed: ' . $e->getMessage());
        }
    }

    /**
     * Download import template
     */
    public function downloadTemplate(Tenant $tenant)
    {
        return Excel::download(new LedgerAccountsTemplateExport(), 'ledger_accounts_import_template.xlsx');
    }

    /**
     * Download account groups reference
     */
    public function downloadAccountGroupsReference(Tenant $tenant)
    {
        return Excel::download(new AccountGroupsReferenceExport($tenant->id), 'account_groups_reference.xlsx');
    }

    /**
     * Import ledger accounts from Excel/CSV file
     */
    public function import(Request $request, Tenant $tenant)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('file');
            $import = new LedgerAccountsImport();

            Excel::import($import, $file);

            $successCount = $import->getSuccessCount();
            $failedCount = $import->getFailedCount();
            $errors = $import->getErrors();

            if ($successCount > 0 && $failedCount === 0) {
                return redirect()->route('tenant.accounting.ledger-accounts.index', ['tenant' => $tenant->slug])
                    ->with('success', "{$successCount} ledger account(s) imported successfully!");
            } elseif ($successCount > 0 && $failedCount > 0) {
                return redirect()->route('tenant.accounting.ledger-accounts.index', ['tenant' => $tenant->slug])
                    ->with('warning', "{$successCount} account(s) imported successfully, but {$failedCount} failed.")
                    ->with('import_errors', $errors);
            } else {
                return redirect()->route('tenant.accounting.ledger-accounts.index', ['tenant' => $tenant->slug])
                    ->with('error', 'Import failed. No accounts were imported.')
                    ->with('import_errors', $errors);
            }
        } catch (\Exception $e) {
            Log::error('Ledger account import error: ' . $e->getMessage());
            return redirect()->route('tenant.accounting.ledger-accounts.index', ['tenant' => $tenant->slug])
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Export ledger for specific account
     */
    public function exportLedger(Tenant $tenant, LedgerAccount $ledgerAccount)
    {
        $ledgerAccount->load(['accountGroup', 'parent']);

        // Get all transactions for this account (only posted vouchers)
        $transactions = $ledgerAccount->voucherEntries()
            ->with(['voucher'])
            ->whereHas('voucher', function ($query) {
                $query->where('status', 'posted');
            })
            ->orderBy('created_at')
            ->get();

        // Prepare CSV export
        $filename = "ledger-{$ledgerAccount->code}-" . now()->format('Y-m-d-H-i-s') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($ledgerAccount, $transactions) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Date',
                'Voucher Number',
                'Description',
                'Debit Amount',
                'Credit Amount',
                'Running Balance',
                'Balance Type'
            ]);

            // Opening balance row
            fputcsv($file, [
                '-',
                'OPENING BALANCE',
                'Account Opening Balance',
                '',
                '',
                number_format($ledgerAccount->opening_balance, 2),
                $ledgerAccount->opening_balance >= 0 ? 'Dr' : 'Cr'
            ]);

            // Transaction rows with running balance
            $runningBalance = $ledgerAccount->opening_balance;
            foreach ($transactions as $transaction) {
                $runningBalance += ($transaction->debit_amount - $transaction->credit_amount);

                fputcsv($file, [
                    $transaction->voucher->voucher_date->format('Y-m-d'),
                    $transaction->voucher->voucher_number,
                    $transaction->particulars ?? 'Transaction',
                    $transaction->debit_amount > 0 ? number_format($transaction->debit_amount, 2) : '',
                    $transaction->credit_amount > 0 ? number_format($transaction->credit_amount, 2) : '',
                    number_format(abs($runningBalance), 2),
                    $runningBalance >= 0 ? 'Dr' : 'Cr'
                ]);
            }

            // Summary row
            fputcsv($file, [
                '',
                'TOTALS',
                'Ledger Summary',
                number_format($transactions->sum('debit_amount'), 2),
                number_format($transactions->sum('credit_amount'), 2),
                number_format(abs($runningBalance), 2),
                $runningBalance >= 0 ? 'Dr' : 'Cr'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Print ledger for specific account
     */
    /**
     * View account statement (similar to print but with better UI)
     */
    public function statement(Tenant $tenant, LedgerAccount $ledgerAccount)
    {
        $ledgerAccount->load(['accountGroup', 'parent']);

        // Get all transactions for this account (only posted vouchers)
        $transactions = $ledgerAccount->voucherEntries()
            ->with(['voucher'])
            ->whereHas('voucher', function ($query) {
                $query->where('status', 'posted');
            })
            ->orderBy('created_at')
            ->get();

        // Calculate running balance for each transaction
        $runningBalance = $ledgerAccount->opening_balance;
        $transactionsWithBalance = [];

        foreach ($transactions as $transaction) {
            $runningBalance += ($transaction->debit_amount - $transaction->credit_amount);
            $transactionsWithBalance[] = [
                'transaction' => $transaction,
                'running_balance' => $runningBalance
            ];
        }

        // Get totals
        $totalDebits = $transactions->sum('debit_amount');
        $totalCredits = $transactions->sum('credit_amount');
        $currentBalance = $ledgerAccount->getCurrentBalance();

        return view('tenant.accounting.ledger-accounts.statement', compact(
            'tenant',
            'ledgerAccount',
            'transactions',
            'transactionsWithBalance',
            'totalDebits',
            'totalCredits',
            'currentBalance'
        ));
    }

    /**
     * Print ledger account statement
     */
    public function printLedger(Tenant $tenant, LedgerAccount $ledgerAccount)
    {
        $ledgerAccount->load(['accountGroup', 'parent']);

        // Get all transactions for this account (only posted vouchers)
        $transactions = $ledgerAccount->voucherEntries()
            ->with(['voucher'])
            ->whereHas('voucher', function ($query) {
                $query->where('status', 'posted');
            })
            ->orderBy('created_at')
            ->get();

        // Calculate running balance for each transaction
        $runningBalance = $ledgerAccount->opening_balance;
        $transactionsWithBalance = [];

        foreach ($transactions as $transaction) {
            $runningBalance += ($transaction->debit_amount - $transaction->credit_amount);
            $transactionsWithBalance[] = [
                'transaction' => $transaction,
                'running_balance' => $runningBalance
            ];
        }

        // Get totals
        $totalDebits = $transactions->sum('debit_amount');
        $totalCredits = $transactions->sum('credit_amount');
        $currentBalance = $ledgerAccount->getCurrentBalance();

        return view('tenant.accounting.ledger-accounts.print-ledger', compact(
            'tenant',
            'ledgerAccount',
            'transactions',
            'transactionsWithBalance',
            'totalDebits',
            'totalCredits',
            'currentBalance'
        ));
    }

    /**
     * Get current balance via AJAX
     */
    public function getBalance(Tenant $tenant, LedgerAccount $ledgerAccount)
    {
        return response()->json([
            'balance' => $ledgerAccount->getCurrentBalance(),
            'opening_balance' => $ledgerAccount->opening_balance,
            'total_debits' => $ledgerAccount->getTotalDebits(),
            'total_credits' => $ledgerAccount->getTotalCredits(),
        ]);
    }

    /**
     * Search ledger accounts for autocomplete
     */
    public function search(Request $request, Tenant $tenant)
    {
        try {
            $query = $request->get('q', '');

            if (strlen($query) < 2) {
                return response()->json([]);
            }

            $accounts = LedgerAccount::where('tenant_id', $tenant->id)
                ->where('is_active', true)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('code', 'like', "%{$query}%");
                })
                ->with(['accountGroup'])
                ->orderBy('name')
                ->limit(10)
                ->get(['id', 'name', 'code', 'account_type', 'current_balance', 'account_group_id']);

            $results = $accounts->map(function ($account) use ($tenant) {
                try {
                    // Use current_balance from database or calculate if null
                    $currentBalance = $account->current_balance ?? $account->getCurrentBalance();

                    return [
                        'id' => $account->id,
                        'name' => $account->name,
                        'code' => $account->code,
                        'account_type' => ucfirst($account->account_type ?? 'asset'),
                        'current_balance' => (float) $currentBalance,
                        'account_group' => $account->accountGroup?->name ?? 'N/A',
                        'url' => route('tenant.accounting.ledger-accounts.show', [$tenant, $account])
                    ];
                } catch (\Exception $e) {
                    // Fallback for individual account errors
                    return [
                        'id' => $account->id,
                        'name' => $account->name,
                        'code' => $account->code,
                        'account_type' => ucfirst($account->account_type ?? 'asset'),
                        'current_balance' => 0.00,
                        'account_group' => 'N/A',
                        'url' => route('tenant.accounting.ledger-accounts.show', [$tenant, $account])
                    ];
                }
            });

            return response()->json($results);

        } catch (\Exception $e) {
            Log::error('Ledger account search error: ' . $e->getMessage());
            return response()->json(['error' => 'Search failed'], 500);
        }
    }

    /**
     * Reclassify opening balance equity to proper equity accounts
     */
    public function reclassifyOpeningBalance(Request $request, Tenant $tenant)
    {
        $request->validate([
            'target_account_id' => 'required|exists:ledger_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
        ]);

        try {
            $openingBalanceService = new OpeningBalanceService();

            $voucher = $openingBalanceService->reclassifyOpeningBalance(
                $tenant->id,
                $request->target_account_id,
                $request->amount,
                $request->description
            );

            return redirect()
                ->back()
                ->with('success', 'Opening balance equity reclassified successfully!');

        } catch (\Exception $e) {
            Log::error('Error reclassifying opening balance: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    private function createDefaultAccountGroups(Tenant $tenant)
    {
        $defaultGroups = [
            ['name' => 'Current Assets', 'nature' => 'assets', 'code' => 'CA'],
            ['name' => 'Fixed Assets', 'nature' => 'assets', 'code' => 'FA'],
            ['name' => 'Current Liabilities', 'nature' => 'liabilities', 'code' => 'CL'],
            ['name' => 'Long Term Liabilities', 'nature' => 'liabilities', 'code' => 'LTL'],
            ['name' => 'Owner Equity', 'nature' => 'equity', 'code' => 'OE'],
            ['name' => 'Revenue', 'nature' => 'income', 'code' => 'REV'],
            ['name' => 'Operating Expenses', 'nature' => 'expenses', 'code' => 'OPEX'],
            ['name' => 'Cost of Goods Sold', 'nature' => 'expenses', 'code' => 'COGS'],
        ];

        foreach ($defaultGroups as $group) {
            AccountGroup::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'code' => $group['code']
                ],
                [
                    'name' => $group['name'],
                    'nature' => $group['nature'],
                    'is_active' => true,
                ]
            );
        }
    }
}
