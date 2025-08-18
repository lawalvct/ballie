<?php

namespace App\Http\Controllers\Tenant\Reports;

use App\Http\Controllers\Controller;
use App\Models\LedgerAccount;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Http\Request;

class ReportsController extends Controller

    /**
     * Display the balance sheet in standard table format
     */

{
    public function index(Tenant $tenant)
    {
        return view('tenant.reports.index', compact('tenant'));
    }

    public function profitLoss(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());

        // Get income accounts
        $incomeAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'income')
            ->where('is_active', true)
            ->get();

        // Get expense accounts
        $expenseAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'expense')
            ->where('is_active', true)
            ->get();

        $incomeData = [];
        $expenseData = [];
        $totalIncome = 0;
        $totalExpenses = 0;

        // Calculate income for the period
        foreach ($incomeAccounts as $account) {
            $balance = $this->calculateAccountBalanceForPeriod($account, $fromDate, $toDate);
            if (abs($balance) >= 0.01) {
                $incomeData[] = [
                    'account' => $account,
                    'amount' => abs($balance),
                ];
                $totalIncome += abs($balance);
            }
        }

        // Calculate expenses for the period
        foreach ($expenseAccounts as $account) {
            $balance = $this->calculateAccountBalanceForPeriod($account, $fromDate, $toDate);
            if (abs($balance) >= 0.01) {
                $expenseData[] = [
                    'account' => $account,
                    'amount' => abs($balance),
                ];
                $totalExpenses += abs($balance);
            }
        }

        // Calculate stock values for the period
        $openingStock = Product::where('tenant_id', $tenant->id)
            ->where('maintain_stock', true)
            ->sum('opening_stock_value');

        $closingStock = Product::where('tenant_id', $tenant->id)
            ->where('maintain_stock', true)
            ->sum('current_stock_value');

        // Add stock to P&L calculation
        if ($openingStock > 0) {
            $expenseData[] = [
                'account' => (object)['name' => 'Opening Stock', 'code' => 'OPENING_STOCK'],
                'amount' => $openingStock,
            ];
            $totalExpenses += $openingStock;
        }

        if ($closingStock > 0) {
            $incomeData[] = [
                'account' => (object)['name' => 'Closing Stock', 'code' => 'CLOSING_STOCK'],
                'amount' => $closingStock,
            ];
            $totalIncome += $closingStock;
        }

        $netProfit = $totalIncome - $totalExpenses;

        return view('tenant.reports.profit-loss', compact(
            'incomeData',
            'expenseData',
            'totalIncome',
            'totalExpenses',
            'netProfit',
            'fromDate',
            'toDate',
            'openingStock',
            'closingStock'
        ));
    }

    public function trialBalance(Request $request, Tenant $tenant)
    {
        // Handle both new date range and legacy single date
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $asOfDate = $request->get('as_of_date');

        // Set defaults if no dates provided
        if (!$fromDate && !$toDate && !$asOfDate) {
            $toDate = now()->toDateString();
            $fromDate = now()->startOfMonth()->toDateString();
        } elseif ($asOfDate && !$fromDate && !$toDate) {
            // Legacy single date mode
            $toDate = $asOfDate;
            $fromDate = null;
        } elseif (!$toDate) {
            $toDate = now()->toDateString();
        }

        // Get all active accounts with their relationships
        $accounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->with(['accountGroup', 'voucherEntries' => function($query) use ($fromDate, $toDate) {
                $query->whereHas('voucher', function($voucherQuery) use ($fromDate, $toDate) {
                    $voucherQuery->where('voucher_date', '<=', $toDate)
                             ->where('status', 'posted');
                    if ($fromDate) {
                        $voucherQuery->where('voucher_date', '>=', $fromDate);
                    }
                });
            }])
            ->orderBy('code')
            ->get();

        $trialBalanceData = [];
        $totalDebits = 0;
        $totalCredits = 0;

        foreach ($accounts as $account) {
            // Calculate balance for the specified period
            if ($fromDate) {
                // Period balance: calculate balance for the date range
                $balance = $this->calculateAccountBalanceForPeriod($account, $fromDate, $toDate);
            } else {
                // Point-in-time balance: calculate balance as of specific date
                $balance = $this->calculateAccountBalance($account, $toDate);
            }

            if (abs($balance) >= 0.01) { // Show accounts with balance >= 1 cent
                // Determine the natural balance side for this account type
                $naturalBalanceSide = $this->getNaturalBalanceSide($account->account_type);

                if ($naturalBalanceSide === 'debit') {
                    $debitAmount = $balance >= 0 ? $balance : 0;
                    $creditAmount = $balance < 0 ? abs($balance) : 0;
                } else {
                    $creditAmount = $balance >= 0 ? $balance : 0;
                    $debitAmount = $balance < 0 ? abs($balance) : 0;
                }

                $trialBalanceData[] = [
                    'account' => $account,
                    'opening_balance' => $account->opening_balance ?? 0,
                    'current_balance' => $balance,
                    'debit_amount' => $debitAmount,
                    'credit_amount' => $creditAmount,
                ];

                $totalDebits += $debitAmount;
                $totalCredits += $creditAmount;
            }
        }

        // Sort by account code
        usort($trialBalanceData, function($a, $b) {
            return strcmp($a['account']->code, $b['account']->code);
        });

        $viewData = compact(
            'trialBalanceData',
            'totalDebits',
            'totalCredits',
            'tenant'
        );

        // Add the appropriate date variables to the view
        if ($fromDate) {
            $viewData['fromDate'] = $fromDate;
            $viewData['toDate'] = $toDate;
        } else {
            $viewData['asOfDate'] = $toDate;
        }

        return view('tenant.reports.trial-balance', $viewData);
    }

    public function cashFlow(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());

        // Get cash and bank accounts (typically asset accounts with 'cash' or 'bank' in name or code)
        $cashAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'asset')
            ->where('is_active', true)
            ->where(function($query) {
                $query->where('name', 'LIKE', '%cash%')
                      ->orWhere('name', 'LIKE', '%bank%')
                      ->orWhere('code', 'LIKE', '%CASH%')
                      ->orWhere('code', 'LIKE', '%BANK%');
            })
            ->get();

        // Calculate cash flow from operating activities
        $operatingActivities = [];
        $operatingTotal = 0;

        // Get income and expense accounts for operating activities
        $incomeAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'income')
            ->where('is_active', true)
            ->get();

        $expenseAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'expense')
            ->where('is_active', true)
            ->get();

        // Calculate income for the period
        foreach ($incomeAccounts as $account) {
            $periodActivity = $this->calculateAccountBalanceForPeriod($account, $fromDate, $toDate);
            if (abs($periodActivity) >= 0.01) {
                $operatingActivities[] = [
                    'description' => $account->name,
                    'amount' => $periodActivity,
                    'type' => 'income'
                ];
                $operatingTotal += $periodActivity;
            }
        }

        // Calculate expenses for the period
        foreach ($expenseAccounts as $account) {
            $periodActivity = $this->calculateAccountBalanceForPeriod($account, $fromDate, $toDate);
            if (abs($periodActivity) >= 0.01) {
                $operatingActivities[] = [
                    'description' => $account->name,
                    'amount' => -$periodActivity, // Expenses reduce cash
                    'type' => 'expense'
                ];
                $operatingTotal -= $periodActivity;
            }
        }

        // Calculate cash flow from investing activities (typically fixed asset purchases/sales)
        $investingActivities = [];
        $investingTotal = 0;

        $fixedAssetAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'asset')
            ->where('is_active', true)
            ->where(function($query) {
                $query->where('name', 'LIKE', '%equipment%')
                      ->orWhere('name', 'LIKE', '%building%')
                      ->orWhere('name', 'LIKE', '%furniture%')
                      ->orWhere('name', 'LIKE', '%vehicle%')
                      ->orWhere('code', 'LIKE', '%FIXED%');
            })
            ->get();

        foreach ($fixedAssetAccounts as $account) {
            $periodActivity = $this->calculateAccountBalanceForPeriod($account, $fromDate, $toDate);
            if (abs($periodActivity) >= 0.01) {
                $investingActivities[] = [
                    'description' => "Investment in " . $account->name,
                    'amount' => -$periodActivity, // Asset purchases reduce cash
                    'type' => 'investing'
                ];
                $investingTotal -= $periodActivity;
            }
        }

        // Calculate cash flow from financing activities (loans, capital, etc.)
        $financingActivities = [];
        $financingTotal = 0;

        // Get liability and equity accounts for financing activities
        $liabilityAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'liability')
            ->where('is_active', true)
            ->get();

        $equityAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'equity')
            ->where('is_active', true)
            ->get();

        foreach ($liabilityAccounts as $account) {
            $periodActivity = $this->calculateAccountBalanceForPeriod($account, $fromDate, $toDate);
            if (abs($periodActivity) >= 0.01) {
                $financingActivities[] = [
                    'description' => $account->name,
                    'amount' => $periodActivity, // Borrowing increases cash
                    'type' => 'liability'
                ];
                $financingTotal += $periodActivity;
            }
        }

        foreach ($equityAccounts as $account) {
            $periodActivity = $this->calculateAccountBalanceForPeriod($account, $fromDate, $toDate);
            if (abs($periodActivity) >= 0.01) {
                $financingActivities[] = [
                    'description' => $account->name,
                    'amount' => $periodActivity, // Capital injection increases cash
                    'type' => 'equity'
                ];
                $financingTotal += $periodActivity;
            }
        }

        // Calculate opening and closing cash positions
        $openingCash = 0;
        $closingCash = 0;

        foreach ($cashAccounts as $account) {
            $openingCash += $this->calculateAccountBalance($account, $fromDate);
            $closingCash += $this->calculateAccountBalance($account, $toDate);
        }

        $netCashFlow = $operatingTotal + $investingTotal + $financingTotal;
        $calculatedClosingCash = $openingCash + $netCashFlow;

        return view('tenant.reports.cash-flow', compact(
            'tenant',
            'fromDate',
            'toDate',
            'operatingActivities',
            'investingActivities',
            'financingActivities',
            'operatingTotal',
            'investingTotal',
            'financingTotal',
            'netCashFlow',
            'openingCash',
            'closingCash',
            'calculatedClosingCash',
            'cashAccounts'
        ));
    }

    public function balanceSheet(Request $request, Tenant $tenant)
    {
        $asOfDate = $request->get('as_of_date', now()->toDateString());

        // Get asset accounts
        $assetAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'asset')
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        // Get liability accounts
        $liabilityAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'liability')
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        // Get equity accounts
        $equityAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'equity')
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $assets = [];
        $liabilities = [];
        $equity = [];
        $totalAssets = 0;
        $totalLiabilities = 0;
        $totalEquity = 0;

        // Calculate assets
        foreach ($assetAccounts as $account) {
            $balance = $this->calculateAccountBalance($account, $asOfDate);
            if (abs($balance) >= 0.01) {
                $assets[] = [
                    'account' => $account,
                    'balance' => $balance,
                ];
                $totalAssets += $balance;
            }
        }

        // Calculate liabilities
        foreach ($liabilityAccounts as $account) {
            $balance = $this->calculateAccountBalance($account, $asOfDate);
            if (abs($balance) >= 0.01) {
                $liabilities[] = [
                    'account' => $account,
                    'balance' => $balance,
                ];
                $totalLiabilities += $balance;
            }
        }

        // Calculate equity
        foreach ($equityAccounts as $account) {
            $balance = $this->calculateAccountBalance($account, $asOfDate);
            if (abs($balance) >= 0.01) {
                $equity[] = [
                    'account' => $account,
                    'balance' => $balance,
                ];
                $totalEquity += $balance;
            }
        }

        // Calculate retained earnings (net income)
        $incomeAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'income')
            ->where('is_active', true)
            ->get();

        $expenseAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'expense')
            ->where('is_active', true)
            ->get();

        $totalIncome = 0;
        $totalExpenses = 0;

        foreach ($incomeAccounts as $account) {
            $totalIncome += $this->calculateAccountBalance($account, $asOfDate);
        }

        foreach ($expenseAccounts as $account) {
            $totalExpenses += $this->calculateAccountBalance($account, $asOfDate);
        }

        $retainedEarnings = $totalIncome - $totalExpenses;
        $totalEquity += $retainedEarnings;

        $totalLiabilitiesAndEquity = $totalLiabilities + $totalEquity;
        $balanceCheck = abs($totalAssets - $totalLiabilitiesAndEquity) < 0.01;

        return view('tenant.reports.balance-sheet', [
            'tenant' => $tenant,
            'asOfDate' => $asOfDate,
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
            'totalAssets' => $totalAssets,
            'totalLiabilities' => $totalLiabilities,
            'totalEquity' => $totalEquity,
            'totalLiabilitiesAndEquity' => $totalLiabilitiesAndEquity,
            'retainedEarnings' => $retainedEarnings,
            'balanceCheck' => $balanceCheck,
        ]);
    }

    /**
     * Calculate account balance as of specific date
     */
    private function calculateAccountBalance($account, $asOfDate)
    {
        // Start with opening balance
        $balance = $account->opening_balance ?? 0;

        // Add all transactions up to the specified date
        $totalDebits = $account->voucherEntries()->whereHas('voucher', function($query) use ($asOfDate) {
            $query->where('voucher_date', '<=', $asOfDate)
                  ->where('status', 'posted');
        })->sum('debit_amount');

        $totalCredits = $account->voucherEntries()->whereHas('voucher', function($query) use ($asOfDate) {
            $query->where('voucher_date', '<=', $asOfDate)
                  ->where('status', 'posted');
        })->sum('credit_amount');

        // For accounting: Debit increases assets and expenses, Credit increases liabilities, equity, and income
        if (in_array($account->account_type, ['asset', 'expense'])) {
            // Assets and Expenses: Debit increases, Credit decreases
            $balance = $balance + $totalDebits - $totalCredits;
        } else {
            // Liabilities, Equity, Income: Credit increases, Debit decreases
            $balance = $balance + $totalCredits - $totalDebits;
        }

        return $balance;
    }

    /**
     * Calculate account balance for a specific period
     */
    private function calculateAccountBalanceForPeriod($account, $fromDate, $toDate)
    {
        // For period reporting, we typically show activity during the period
        // rather than cumulative balance

        $totalDebits = $account->voucherEntries()->whereHas('voucher', function($query) use ($fromDate, $toDate) {
            $query->whereBetween('voucher_date', [$fromDate, $toDate])
                  ->where('status', 'posted');
        })->sum('debit_amount');

        $totalCredits = $account->voucherEntries()->whereHas('voucher', function($query) use ($fromDate, $toDate) {
            $query->whereBetween('voucher_date', [$fromDate, $toDate])
                  ->where('status', 'posted');
        })->sum('credit_amount');

        // For period reports, show net activity during the period
        if (in_array($account->account_type, ['asset', 'expense'])) {
            // Assets and Expenses: Debit increases, Credit decreases
            $balance = $totalDebits - $totalCredits;
        } else {
            // Liabilities, Equity, Income: Credit increases, Debit decreases
            $balance = $totalCredits - $totalDebits;
        }

        return $balance;
    }

    /**
     * Get the natural balance side for an account type
     */
    private function getNaturalBalanceSide($accountType)
    {
        return match($accountType) {
            'asset', 'expense' => 'debit',
            'liability', 'equity', 'income' => 'credit',
            default => 'debit'
        };
    }

     public function balanceSheetTable(Request $request, Tenant $tenant)
    {
        $asOfDate = $request->get('as_of_date', now()->toDateString());

        // Get asset accounts
        $assetAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'asset')
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        // Get liability accounts
        $liabilityAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'liability')
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        // Get equity accounts
        $equityAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'equity')
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $assets = [];
        $liabilities = [];
        $equity = [];
        $totalAssets = 0;
        $totalLiabilities = 0;
        $totalEquity = 0;

        // Calculate assets
        foreach ($assetAccounts as $account) {
            $balance = $this->calculateAccountBalance($account, $asOfDate);
            if (abs($balance) >= 0.01) {
                $assets[] = [
                    'account' => $account,
                    'balance' => $balance,
                ];
                $totalAssets += $balance;
            }
        }

        // Calculate liabilities
        foreach ($liabilityAccounts as $account) {
            $balance = $this->calculateAccountBalance($account, $asOfDate);
            if (abs($balance) >= 0.01) {
                $liabilities[] = [
                    'account' => $account,
                    'balance' => $balance,
                ];
                $totalLiabilities += $balance;
            }
        }

        // Calculate equity
        foreach ($equityAccounts as $account) {
            $balance = $this->calculateAccountBalance($account, $asOfDate);
            if (abs($balance) >= 0.01) {
                $equity[] = [
                    'account' => $account,
                    'balance' => $balance,
                ];
                $totalEquity += $balance;
            }
        }

        // Calculate retained earnings (net income)
        $incomeAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'income')
            ->where('is_active', true)
            ->get();

        $expenseAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'expense')
            ->where('is_active', true)
            ->get();

        $totalIncome = 0;
        $totalExpenses = 0;

        foreach ($incomeAccounts as $account) {
            $totalIncome += $this->calculateAccountBalance($account, $asOfDate);
        }

        foreach ($expenseAccounts as $account) {
            $totalExpenses += $this->calculateAccountBalance($account, $asOfDate);
        }

        $retainedEarnings = $totalIncome - $totalExpenses;
        $totalEquity += $retainedEarnings;

        $totalLiabilitiesAndEquity = $totalLiabilities + $totalEquity;
        $balanceCheck = abs($totalAssets - $totalLiabilitiesAndEquity) < 0.01;

        return view('tenant.reports.balance-sheet-table', [
            'tenant' => $tenant,
            'asOfDate' => $asOfDate,
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
            'totalAssets' => $totalAssets,
            'totalLiabilities' => $totalLiabilities,
            'totalEquity' => $totalEquity,
            'totalLiabilitiesAndEquity' => $totalLiabilitiesAndEquity,
            'retainedEarnings' => $retainedEarnings,
            'balanceCheck' => $balanceCheck,
        ]);
    }

    //push go online
}
