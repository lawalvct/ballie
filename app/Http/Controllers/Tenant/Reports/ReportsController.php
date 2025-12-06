<?php

namespace App\Http\Controllers\Tenant\Reports;

use App\Http\Controllers\Controller;
use App\Models\LedgerAccount;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $compare = $request->get('compare', false);

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

        // Calculate stock values for the period (like Tally ERP)
        // Opening Stock: Stock value as of the day before period start
        // Closing Stock: Stock value as of period end
        $openingStockDate = date('Y-m-d', strtotime($fromDate . ' -1 day'));

        $products = Product::where('tenant_id', $tenant->id)
            ->where('maintain_stock', true)
            ->get();

        $openingStock = 0;
        $closingStock = 0;

        foreach ($products as $product) {
            // Calculate opening stock value (day before period start)
            $openingStockQty = $product->getStockAsOfDate($openingStockDate);
            $openingStock += $openingStockQty * ($product->purchase_rate ?? 0);

            // Calculate closing stock value (period end date)
            $closingStockQty = $product->getStockAsOfDate($toDate);
            $closingStock += $closingStockQty * ($product->purchase_rate ?? 0);
        }

        $netProfit = $totalIncome - $totalExpenses;

        // Comparison data
        $compareData = null;
        if ($compare) {
            $days = (strtotime($toDate) - strtotime($fromDate)) / 86400;
            $compareFromDate = date('Y-m-d', strtotime($fromDate . ' -' . ($days + 1) . ' days'));
            $compareToDate = date('Y-m-d', strtotime($fromDate . ' -1 day'));

            $compareIncome = 0;
            $compareExpenses = 0;

            foreach ($incomeAccounts as $account) {
                $compareIncome += abs($this->calculateAccountBalanceForPeriod($account, $compareFromDate, $compareToDate));
            }

            foreach ($expenseAccounts as $account) {
                $compareExpenses += abs($this->calculateAccountBalanceForPeriod($account, $compareFromDate, $compareToDate));
            }

            $compareData = [
                'fromDate' => $compareFromDate,
                'toDate' => $compareToDate,
                'totalIncome' => $compareIncome,
                'totalExpenses' => $compareExpenses,
                'netProfit' => $compareIncome - $compareExpenses,
            ];
        }

        return view('tenant.reports.profit-loss', compact(
            'incomeData',
            'expenseData',
            'totalIncome',
            'totalExpenses',
            'netProfit',
            'fromDate',
            'toDate',
            'openingStock',
            'closingStock',
            'compare',
            'compareData'
        ));
    }

    public function profitLossPdf(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());

        $incomeAccounts = LedgerAccount::where('tenant_id', $tenant->id)->where('account_type', 'income')->where('is_active', true)->get();
        $expenseAccounts = LedgerAccount::where('tenant_id', $tenant->id)->where('account_type', 'expense')->where('is_active', true)->get();

        $incomeData = [];
        $expenseData = [];
        $totalIncome = 0;
        $totalExpenses = 0;

        foreach ($incomeAccounts as $account) {
            $balance = $this->calculateAccountBalanceForPeriod($account, $fromDate, $toDate);
            if (abs($balance) >= 0.01) {
                $incomeData[] = ['account' => $account, 'amount' => abs($balance)];
                $totalIncome += abs($balance);
            }
        }

        foreach ($expenseAccounts as $account) {
            $balance = $this->calculateAccountBalanceForPeriod($account, $fromDate, $toDate);
            if (abs($balance) >= 0.01) {
                $expenseData[] = ['account' => $account, 'amount' => abs($balance)];
                $totalExpenses += abs($balance);
            }
        }

        $netProfit = $totalIncome - $totalExpenses;

        $pdf = Pdf::loadView('tenant.reports.profit-loss-pdf', compact('tenant', 'incomeData', 'expenseData', 'totalIncome', 'totalExpenses', 'netProfit', 'fromDate', 'toDate'));
        return $pdf->download('profit_loss_' . $fromDate . '_to_' . $toDate . '.pdf');
    }

    public function profitLossExcel(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());

        $incomeAccounts = LedgerAccount::where('tenant_id', $tenant->id)->where('account_type', 'income')->where('is_active', true)->get();
        $expenseAccounts = LedgerAccount::where('tenant_id', $tenant->id)->where('account_type', 'expense')->where('is_active', true)->get();

        $incomeData = [];
        $expenseData = [];
        $totalIncome = 0;
        $totalExpenses = 0;

        foreach ($incomeAccounts as $account) {
            $balance = $this->calculateAccountBalanceForPeriod($account, $fromDate, $toDate);
            if (abs($balance) >= 0.01) {
                $incomeData[] = ['account' => $account, 'amount' => abs($balance)];
                $totalIncome += abs($balance);
            }
        }

        foreach ($expenseAccounts as $account) {
            $balance = $this->calculateAccountBalanceForPeriod($account, $fromDate, $toDate);
            if (abs($balance) >= 0.01) {
                $expenseData[] = ['account' => $account, 'amount' => abs($balance)];
                $totalExpenses += abs($balance);
            }
        }

        $netProfit = $totalIncome - $totalExpenses;

        return \Excel::download(new \App\Exports\ProfitLossExport($tenant, $incomeData, $expenseData, $totalIncome, $totalExpenses, $netProfit, $fromDate, $toDate), 'profit_loss_' . $fromDate . '_to_' . $toDate . '.xlsx');
    }

    public function profitLossTable(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());
        $mode = $request->get('mode', 'detailed'); // 'condensed' or 'detailed'

        // Get income accounts with account groups
        $incomeAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'income')
            ->where('is_active', true)
            ->with('accountGroup')
            ->orderBy('code')
            ->get();

        // Get expense accounts with account groups
        $expenseAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'expense')
            ->where('is_active', true)
            ->with('accountGroup')
            ->orderBy('code')
            ->get();

        $incomeByGroup = [];
        $expenseByGroup = [];
        $totalIncome = 0;
        $totalExpenses = 0;

        // Group income accounts
        foreach ($incomeAccounts as $account) {
            $balance = $this->calculateAccountBalanceForPeriod($account, $fromDate, $toDate);
            if (abs($balance) >= 0.01) {
                $groupName = $account->accountGroup ? $account->accountGroup->name : 'Uncategorized Income';

                if (!isset($incomeByGroup[$groupName])) {
                    $incomeByGroup[$groupName] = [
                        'accounts' => [],
                        'total' => 0
                    ];
                }

                $incomeByGroup[$groupName]['accounts'][] = [
                    'account' => $account,
                    'amount' => abs($balance)
                ];
                $incomeByGroup[$groupName]['total'] += abs($balance);
                $totalIncome += abs($balance);
            }
        }

        // Group expense accounts
        foreach ($expenseAccounts as $account) {
            $balance = $this->calculateAccountBalanceForPeriod($account, $fromDate, $toDate);
            if (abs($balance) >= 0.01) {
                $groupName = $account->accountGroup ? $account->accountGroup->name : 'Uncategorized Expenses';

                if (!isset($expenseByGroup[$groupName])) {
                    $expenseByGroup[$groupName] = [
                        'accounts' => [],
                        'total' => 0
                    ];
                }

                $expenseByGroup[$groupName]['accounts'][] = [
                    'account' => $account,
                    'amount' => abs($balance)
                ];
                $expenseByGroup[$groupName]['total'] += abs($balance);
                $totalExpenses += abs($balance);
            }
        }

        $netProfit = $totalIncome - $totalExpenses;

        return view('tenant.reports.profit-loss-table', compact(
            'tenant',
            'incomeByGroup',
            'expenseByGroup',
            'totalIncome',
            'totalExpenses',
            'netProfit',
            'fromDate',
            'toDate',
            'mode'
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

        // Handle PDF download
        if ($request->get('download') === 'pdf') {
            return $this->generateTrialBalancePDF($viewData);
        }

        return view('tenant.reports.trial-balance', $viewData);
    }

    private function generateTrialBalancePDF($data)
    {
        $pdf = \PDF::loadView('tenant.reports.trial-balance-pdf', $data);

        // Generate filename
        $filename = 'trial_balance';
        if (isset($data['fromDate']) && isset($data['toDate'])) {
            $filename .= '_' . $data['fromDate'] . '_to_' . $data['toDate'];
        } else {
            $filename .= '_' . ($data['asOfDate'] ?? now()->format('Y-m-d'));
        }
        $filename .= '.pdf';

        return $pdf->download($filename);
    }

    public function cashFlow(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());

        $cashFlowData = $this->calculateCashFlowData($tenant, $fromDate, $toDate);
        $viewData = array_merge($cashFlowData, [
            'tenant' => $tenant,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
        ]);

        if ($request->get('download') === 'pdf') {
            $pdf = Pdf::loadView('tenant.reports.cash-flow-pdf', $viewData)
                ->setPaper('a4', 'portrait');
            return $pdf->download('cash_flow_' . $fromDate . '_to_' . $toDate . '.pdf');
        }

        return view('tenant.reports.cash-flow', $viewData);
    }

    private function calculateCashFlowData(Tenant $tenant, string $fromDate, string $toDate): array
    {
        $cashAccounts = $this->getCashAccounts($tenant);
        $operatingData = $this->calculateOperatingActivities($tenant, $fromDate, $toDate);
        $investingData = $this->calculateInvestingActivities($tenant, $fromDate, $toDate);
        $financingData = $this->calculateFinancingActivities($tenant, $fromDate, $toDate);

        $openingCash = $cashAccounts->sum(fn($account) => $this->calculateAccountBalance($account, $fromDate));
        $closingCash = $cashAccounts->sum(fn($account) => $this->calculateAccountBalance($account, $toDate));

        $netCashFlow = $operatingData['total'] + $investingData['total'] + $financingData['total'];

        return [
            'operatingActivities' => $operatingData['activities'],
            'investingActivities' => $investingData['activities'],
            'financingActivities' => $financingData['activities'],
            'operatingTotal' => $operatingData['total'],
            'investingTotal' => $investingData['total'],
            'financingTotal' => $financingData['total'],
            'netCashFlow' => $netCashFlow,
            'openingCash' => $openingCash,
            'closingCash' => $closingCash,
            'calculatedClosingCash' => $openingCash + $netCashFlow,
            'cashAccounts' => $cashAccounts,
        ];
    }

    private function getCashAccounts(Tenant $tenant)
    {
        return LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'asset')
            ->where('is_active', true)
            ->where(function($query) {
                $query->where('name', 'LIKE', '%cash%')
                      ->orWhere('name', 'LIKE', '%bank%')
                      ->orWhere('code', 'LIKE', '%CASH%')
                      ->orWhere('code', 'LIKE', '%BANK%');
            })
            ->get();
    }

    private function calculateOperatingActivities(Tenant $tenant, string $fromDate, string $toDate): array
    {
        $activities = [];
        $total = 0;

        $accounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->whereIn('account_type', ['income', 'expense'])
            ->where('is_active', true)
            ->get();

        foreach ($accounts as $account) {
            $periodActivity = $this->calculateAccountBalanceForPeriod($account, $fromDate, $toDate);
            if (abs($periodActivity) >= 0.01) {
                $isIncome = $account->account_type === 'income';
                $amount = $isIncome ? $periodActivity : -$periodActivity;

                $activities[] = [
                    'description' => $account->name,
                    'amount' => $amount,
                    'type' => $account->account_type
                ];

                $total += $isIncome ? $periodActivity : -$periodActivity;
            }
        }

        return ['activities' => $activities, 'total' => $total];
    }

    private function calculateInvestingActivities(Tenant $tenant, string $fromDate, string $toDate): array
    {
        $activities = [];
        $total = 0;

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
                $activities[] = [
                    'description' => "Investment in " . $account->name,
                    'amount' => -$periodActivity,
                    'type' => 'investing'
                ];
                $total -= $periodActivity;
            }
        }

        return ['activities' => $activities, 'total' => $total];
    }

    private function calculateFinancingActivities(Tenant $tenant, string $fromDate, string $toDate): array
    {
        $activities = [];
        $total = 0;

        $accounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->whereIn('account_type', ['liability', 'equity'])
            ->where('is_active', true)
            ->get();

        foreach ($accounts as $account) {
            $periodActivity = $this->calculateAccountBalanceForPeriod($account, $fromDate, $toDate);
            if (abs($periodActivity) >= 0.01) {
                $activities[] = [
                    'description' => $account->name,
                    'amount' => $periodActivity,
                    'type' => $account->account_type
                ];
                $total += $periodActivity;
            }
        }

        return ['activities' => $activities, 'total' => $total];
    }

    public function balanceSheet(Request $request, Tenant $tenant)
    {
        $asOfDate = $request->get('as_of_date', now()->toDateString());
        $compare = $request->get('compare', false);

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

        $compareData = null;
        if ($compare) {
            $compareDate = date('Y-m-d', strtotime($asOfDate . ' -1 year'));
            $compareAssets = 0;
            $compareLiabilities = 0;
            $compareEquity = 0;

            foreach ($assetAccounts as $account) {
                $compareAssets += $this->calculateAccountBalance($account, $compareDate);
            }
            foreach ($liabilityAccounts as $account) {
                $compareLiabilities += $this->calculateAccountBalance($account, $compareDate);
            }
            foreach ($equityAccounts as $account) {
                $compareEquity += $this->calculateAccountBalance($account, $compareDate);
            }

            $compareIncome = 0;
            $compareExpenses = 0;
            foreach ($incomeAccounts as $account) {
                $compareIncome += $this->calculateAccountBalance($account, $compareDate);
            }
            foreach ($expenseAccounts as $account) {
                $compareExpenses += $this->calculateAccountBalance($account, $compareDate);
            }

            $compareData = [
                'asOfDate' => $compareDate,
                'totalAssets' => $compareAssets,
                'totalLiabilities' => $compareLiabilities,
                'totalEquity' => $compareEquity + ($compareIncome - $compareExpenses),
            ];
        }

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
            'compare' => $compare,
            'compareData' => $compareData,
        ]);
    }

    public function balanceSheetPdf(Request $request, Tenant $tenant)
    {
        $asOfDate = $request->get('as_of_date', now()->toDateString());
        $data = $this->getBalanceSheetData($tenant, $asOfDate);
        $pdf = Pdf::loadView('tenant.reports.balance-sheet-pdf', $data);
        return $pdf->download('balance_sheet_' . $asOfDate . '.pdf');
    }

    public function balanceSheetExcel(Request $request, Tenant $tenant)
    {
        $asOfDate = $request->get('as_of_date', now()->toDateString());
        $data = $this->getBalanceSheetData($tenant, $asOfDate);
        return \Excel::download(new \App\Exports\BalanceSheetExport($data), 'balance_sheet_' . $asOfDate . '.xlsx');
    }

    private function getBalanceSheetData(Tenant $tenant, string $asOfDate): array
    {
        $assetAccounts = LedgerAccount::where('tenant_id', $tenant->id)->where('account_type', 'asset')->where('is_active', true)->orderBy('code')->get();
        $liabilityAccounts = LedgerAccount::where('tenant_id', $tenant->id)->where('account_type', 'liability')->where('is_active', true)->orderBy('code')->get();
        $equityAccounts = LedgerAccount::where('tenant_id', $tenant->id)->where('account_type', 'equity')->where('is_active', true)->orderBy('code')->get();

        $assets = [];
        $liabilities = [];
        $equity = [];
        $totalAssets = 0;
        $totalLiabilities = 0;
        $totalEquity = 0;

        foreach ($assetAccounts as $account) {
            $balance = $this->calculateAccountBalance($account, $asOfDate);
            if (abs($balance) >= 0.01) {
                $assets[] = ['account' => $account, 'balance' => $balance];
                $totalAssets += $balance;
            }
        }

        foreach ($liabilityAccounts as $account) {
            $balance = $this->calculateAccountBalance($account, $asOfDate);
            if (abs($balance) >= 0.01) {
                $liabilities[] = ['account' => $account, 'balance' => $balance];
                $totalLiabilities += $balance;
            }
        }

        foreach ($equityAccounts as $account) {
            $balance = $this->calculateAccountBalance($account, $asOfDate);
            if (abs($balance) >= 0.01) {
                $equity[] = ['account' => $account, 'balance' => $balance];
                $totalEquity += $balance;
            }
        }

        $incomeAccounts = LedgerAccount::where('tenant_id', $tenant->id)->where('account_type', 'income')->where('is_active', true)->get();
        $expenseAccounts = LedgerAccount::where('tenant_id', $tenant->id)->where('account_type', 'expense')->where('is_active', true)->get();

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

        return [
            'tenant' => $tenant,
            'asOfDate' => $asOfDate,
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
            'totalAssets' => $totalAssets,
            'totalLiabilities' => $totalLiabilities,
            'totalEquity' => $totalEquity,
            'retainedEarnings' => $retainedEarnings,
        ];
    }

    /**
     * Calculate account balance as of specific date
     * Uses the LedgerAccount model's getCurrentBalance method for consistency
     */
    private function calculateAccountBalance($account, $asOfDate)
    {
        // Use the model's getCurrentBalance method which respects account types
        // and provides consistent balance calculation across the system
        return $account->getCurrentBalance($asOfDate, false);
    }

    /**
     * Calculate account balance for a specific period
     * Returns the net movement during the period (not cumulative balance)
     */
    private function calculateAccountBalanceForPeriod($account, $fromDate, $toDate)
    {
        // For period reporting, we show activity during the period
        // Calculate opening balance (day before period start)
        $openingDate = date('Y-m-d', strtotime($fromDate . ' -1 day'));
        $openingBalance = $account->getCurrentBalance($openingDate, false);

        // Calculate closing balance (end of period)
        $closingBalance = $account->getCurrentBalance($toDate, false);

        // Period movement is the difference
        $periodMovement = $closingBalance - $openingBalance;

        return $periodMovement;
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

    public function balanceSheetDrCr(Request $request, Tenant $tenant)
    {
        $asOfDate = $request->get('as_of_date', now()->toDateString());

        // Get all accounts
        $assetAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'asset')
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $liabilityAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'liability')
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $equityAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'equity')
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $incomeAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'income')
            ->where('is_active', true)
            ->get();

        $expenseAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'expense')
            ->where('is_active', true)
            ->get();

        $debitSide = [];
        $creditSide = [];
        $totalDebits = 0;
        $totalCredits = 0;

        // Assets go on Debit side
        foreach ($assetAccounts as $account) {
            $balance = $this->calculateAccountBalance($account, $asOfDate);
            if (abs($balance) >= 0.01) {
                $debitSide[] = [
                    'account' => $account,
                    'balance' => abs($balance),
                    'type' => 'Asset'
                ];
                $totalDebits += abs($balance);
            }
        }

        // Liabilities go on Credit side
        foreach ($liabilityAccounts as $account) {
            $balance = $this->calculateAccountBalance($account, $asOfDate);
            if (abs($balance) >= 0.01) {
                $creditSide[] = [
                    'account' => $account,
                    'balance' => abs($balance),
                    'type' => 'Liability'
                ];
                $totalCredits += abs($balance);
            }
        }

        // Equity goes on Credit side
        foreach ($equityAccounts as $account) {
            $balance = $this->calculateAccountBalance($account, $asOfDate);
            if (abs($balance) >= 0.01) {
                $creditSide[] = [
                    'account' => $account,
                    'balance' => abs($balance),
                    'type' => 'Equity'
                ];
                $totalCredits += abs($balance);
            }
        }

        // Calculate retained earnings (Net Income/Loss)
        $totalIncome = 0;
        $totalExpenses = 0;

        foreach ($incomeAccounts as $account) {
            $totalIncome += $this->calculateAccountBalance($account, $asOfDate);
        }

        foreach ($expenseAccounts as $account) {
            $totalExpenses += $this->calculateAccountBalance($account, $asOfDate);
        }

        $retainedEarnings = $totalIncome - $totalExpenses;

        // Retained Earnings logic:
        // Profit (positive) = Credit side (increases equity)
        // Loss (negative) = Debit side (decreases equity)
        if (abs($retainedEarnings) >= 0.01) {
            if ($retainedEarnings >= 0) {
                // Profit - goes to Credit side
                $creditSide[] = [
                    'account' => (object)['name' => 'Retained Earnings (Net Profit)', 'code' => 'RE'],
                    'balance' => $retainedEarnings,
                    'type' => 'Equity'
                ];
                $totalCredits += $retainedEarnings;
            } else {
                // Loss - goes to Debit side (to balance the equation)
                $debitSide[] = [
                    'account' => (object)['name' => 'Retained Earnings (Net Loss)', 'code' => 'RE'],
                    'balance' => abs($retainedEarnings),
                    'type' => 'Equity (Contra)'
                ];
                $totalDebits += abs($retainedEarnings);
            }
        }

        $balanceCheck = abs($totalDebits - $totalCredits) < 0.01;

        return view('tenant.reports.balance-sheet-dr-cr', [
            'tenant' => $tenant,
            'asOfDate' => $asOfDate,
            'debitSide' => $debitSide,
            'creditSide' => $creditSide,
            'totalDebits' => $totalDebits,
            'totalCredits' => $totalCredits,
            'balanceCheck' => $balanceCheck,
        ]);
    }


}
