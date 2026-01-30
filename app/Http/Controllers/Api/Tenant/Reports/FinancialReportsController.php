<?php

namespace App\Http\Controllers\Api\Tenant\Reports;

use App\Http\Controllers\Controller;
use App\Models\LedgerAccount;
use App\Models\Product;
use App\Models\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class FinancialReportsController extends Controller
{
    public function profitLoss(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());
        $compare = (bool) $request->get('compare', false);

        $incomeAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'income')
            ->where('is_active', true)
            ->get();

        $expenseAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'expense')
            ->where('is_active', true)
            ->get();

        $incomeData = [];
        $expenseData = [];
        $totalIncome = 0;
        $totalExpenses = 0;

        foreach ($incomeAccounts as $account) {
            $balance = $this->calculateAccountBalanceForPeriod($account, $fromDate, $toDate);
            if (abs($balance) >= 0.01) {
                $incomeData[] = [
                    'account_id' => $account->id,
                    'name' => $account->name,
                    'code' => $account->code,
                    'amount' => (float) abs($balance),
                ];
                $totalIncome += abs($balance);
            }
        }

        foreach ($expenseAccounts as $account) {
            $balance = $this->calculateAccountBalanceForPeriod($account, $fromDate, $toDate);
            if (abs($balance) >= 0.01) {
                $expenseData[] = [
                    'account_id' => $account->id,
                    'name' => $account->name,
                    'code' => $account->code,
                    'amount' => (float) abs($balance),
                ];
                $totalExpenses += abs($balance);
            }
        }

        $openingStockDate = date('Y-m-d', strtotime($fromDate . ' -1 day'));
        $products = Product::where('tenant_id', $tenant->id)
            ->where('maintain_stock', true)
            ->get();

        $openingStock = 0;
        $closingStock = 0;

        foreach ($products as $product) {
            $openingStockQty = $product->getStockAsOfDate($openingStockDate);
            $openingStock += $openingStockQty * ($product->purchase_rate ?? 0);

            $closingStockQty = $product->getStockAsOfDate($toDate);
            $closingStock += $closingStockQty * ($product->purchase_rate ?? 0);
        }

        $netProfit = $totalIncome - $totalExpenses;
        $profitMargin = $totalIncome > 0 ? ($netProfit / $totalIncome) * 100 : 0;

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

            $compareNetProfit = $compareIncome - $compareExpenses;
            $compareMargin = $compareIncome > 0 ? ($compareNetProfit / $compareIncome) * 100 : 0;

            $compareData = [
                'previous_from' => $compareFromDate,
                'previous_to' => $compareToDate,
                'total_income' => (float) $compareIncome,
                'total_expenses' => (float) $compareExpenses,
                'net_profit' => (float) $compareNetProfit,
                'profit_margin' => (float) $compareMargin,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Profit & loss report retrieved successfully',
            'data' => [
                'filters' => [
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'compare' => $compare,
                ],
                'summary' => [
                    'total_income' => (float) $totalIncome,
                    'total_expenses' => (float) $totalExpenses,
                    'net_profit' => (float) $netProfit,
                    'profit_margin' => (float) $profitMargin,
                ],
                'income' => $incomeData,
                'expenses' => $expenseData,
                'stock' => [
                    'opening_stock' => (float) $openingStock,
                    'closing_stock' => (float) $closingStock,
                ],
                'compare' => $compareData,
            ],
        ]);
    }

    public function profitLossTable(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());
        $mode = $request->get('mode', 'detailed');

        $incomeAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'income')
            ->where('is_active', true)
            ->with('accountGroup')
            ->orderBy('code')
            ->get();

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

        foreach ($incomeAccounts as $account) {
            $balance = $this->calculateAccountBalanceForPeriod($account, $fromDate, $toDate);
            if (abs($balance) >= 0.01) {
                $groupName = $account->accountGroup ? $account->accountGroup->name : 'Uncategorized Income';

                if (!isset($incomeByGroup[$groupName])) {
                    $incomeByGroup[$groupName] = [
                        'group' => $groupName,
                        'total' => 0,
                        'accounts' => [],
                    ];
                }

                $incomeByGroup[$groupName]['accounts'][] = [
                    'account_id' => $account->id,
                    'name' => $account->name,
                    'code' => $account->code,
                    'amount' => (float) abs($balance),
                ];
                $incomeByGroup[$groupName]['total'] += abs($balance);
                $totalIncome += abs($balance);
            }
        }

        foreach ($expenseAccounts as $account) {
            $balance = $this->calculateAccountBalanceForPeriod($account, $fromDate, $toDate);
            if (abs($balance) >= 0.01) {
                $groupName = $account->accountGroup ? $account->accountGroup->name : 'Uncategorized Expenses';

                if (!isset($expenseByGroup[$groupName])) {
                    $expenseByGroup[$groupName] = [
                        'group' => $groupName,
                        'total' => 0,
                        'accounts' => [],
                    ];
                }

                $expenseByGroup[$groupName]['accounts'][] = [
                    'account_id' => $account->id,
                    'name' => $account->name,
                    'code' => $account->code,
                    'amount' => (float) abs($balance),
                ];
                $expenseByGroup[$groupName]['total'] += abs($balance);
                $totalExpenses += abs($balance);
            }
        }

        $netProfit = $totalIncome - $totalExpenses;

        return response()->json([
            'success' => true,
            'message' => 'Profit & loss table retrieved successfully',
            'data' => [
                'filters' => [
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'mode' => $mode,
                ],
                'summary' => [
                    'total_income' => (float) $totalIncome,
                    'total_expenses' => (float) $totalExpenses,
                    'net_profit' => (float) $netProfit,
                ],
                'income_groups' => array_values($incomeByGroup),
                'expense_groups' => array_values($expenseByGroup),
            ],
        ]);
    }

    public function balanceSheet(Request $request, Tenant $tenant)
    {
        $asOfDate = $request->get('as_of_date', now()->toDateString());
        $compare = (bool) $request->get('compare', false);

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

        $assets = [];
        $liabilities = [];
        $equity = [];
        $totalAssets = 0;
        $totalLiabilities = 0;
        $totalEquity = 0;

        foreach ($assetAccounts as $account) {
            $balance = $this->calculateAccountBalance($account, $asOfDate);
            if (abs($balance) >= 0.01) {
                $assets[] = $this->formatBalanceAccount($account, $balance);
                $totalAssets += $balance;
            }
        }

        foreach ($liabilityAccounts as $account) {
            $balance = $this->calculateAccountBalance($account, $asOfDate);
            if (abs($balance) >= 0.01) {
                $liabilities[] = $this->formatBalanceAccount($account, $balance);
                $totalLiabilities += $balance;
            }
        }

        foreach ($equityAccounts as $account) {
            $balance = $this->calculateAccountBalance($account, $asOfDate);
            if (abs($balance) >= 0.01) {
                $equity[] = $this->formatBalanceAccount($account, $balance);
                $totalEquity += $balance;
            }
        }

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
                'as_of_date' => $compareDate,
                'total_assets' => (float) $compareAssets,
                'total_liabilities' => (float) $compareLiabilities,
                'total_equity' => (float) ($compareEquity + ($compareIncome - $compareExpenses)),
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Balance sheet retrieved successfully',
            'data' => [
                'filters' => [
                    'as_of_date' => $asOfDate,
                    'compare' => $compare,
                ],
                'assets' => $assets,
                'liabilities' => $liabilities,
                'equity' => $equity,
                'summary' => [
                    'total_assets' => (float) $totalAssets,
                    'total_liabilities' => (float) $totalLiabilities,
                    'total_equity' => (float) $totalEquity,
                    'total_liabilities_and_equity' => (float) $totalLiabilitiesAndEquity,
                    'retained_earnings' => (float) $retainedEarnings,
                    'balance_check' => $balanceCheck,
                ],
                'compare' => $compareData,
            ],
        ]);
    }

    public function profitLossPdf(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());

        $incomeAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'income')
            ->where('is_active', true)
            ->get();

        $expenseAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'expense')
            ->where('is_active', true)
            ->get();

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

        $pdf = Pdf::loadView('tenant.reports.profit-loss-pdf', compact(
            'tenant',
            'incomeData',
            'expenseData',
            'totalIncome',
            'totalExpenses',
            'netProfit',
            'fromDate',
            'toDate'
        ));

        return $pdf->download('profit_loss_' . $fromDate . '_to_' . $toDate . '.pdf');
    }

    public function balanceSheetPdf(Request $request, Tenant $tenant)
    {
        $asOfDate = $request->get('as_of_date', now()->toDateString());
        $data = $this->getBalanceSheetData($tenant, $asOfDate);
        $pdf = Pdf::loadView('tenant.reports.balance-sheet-pdf', $data);
        return $pdf->download('balance_sheet_' . $asOfDate . '.pdf');
    }

    public function trialBalancePdf(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $asOfDate = $request->get('as_of_date');

        if (!$fromDate && !$toDate && !$asOfDate) {
            $toDate = now()->toDateString();
            $fromDate = now()->startOfMonth()->toDateString();
        } elseif ($asOfDate && !$fromDate && !$toDate) {
            $toDate = $asOfDate;
            $fromDate = null;
        } elseif (!$toDate) {
            $toDate = now()->toDateString();
        }

        $accounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->with(['accountGroup', 'voucherEntries' => function ($query) use ($fromDate, $toDate) {
                $query->whereHas('voucher', function ($voucherQuery) use ($fromDate, $toDate) {
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
            if ($fromDate) {
                $balance = $this->calculateAccountBalanceForPeriod($account, $fromDate, $toDate);
            } else {
                $balance = $this->calculateAccountBalance($account, $toDate);
            }

            if (abs($balance) >= 0.01) {
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

        usort($trialBalanceData, function ($a, $b) {
            return strcmp((string) $a['account']->code, (string) $b['account']->code);
        });

        $viewData = compact('trialBalanceData', 'totalDebits', 'totalCredits', 'tenant');

        if ($fromDate) {
            $viewData['fromDate'] = $fromDate;
            $viewData['toDate'] = $toDate;
        } else {
            $viewData['asOfDate'] = $toDate;
        }

        $filename = 'trial_balance';
        if (isset($viewData['fromDate']) && isset($viewData['toDate'])) {
            $filename .= '_' . $viewData['fromDate'] . '_to_' . $viewData['toDate'];
        } else {
            $filename .= '_' . ($viewData['asOfDate'] ?? now()->format('Y-m-d'));
        }
        $filename .= '.pdf';

        $pdf = Pdf::loadView('tenant.reports.trial-balance-pdf', $viewData);
        return $pdf->download($filename);
    }

    public function cashFlowPdf(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());

        $cashFlowData = $this->calculateCashFlowData($tenant, $fromDate, $toDate);
        $viewData = array_merge($cashFlowData, [
            'tenant' => $tenant,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
        ]);

        $pdf = Pdf::loadView('tenant.reports.cash-flow-pdf', $viewData)
            ->setPaper('a4', 'portrait');

        return $pdf->download('cash_flow_' . $fromDate . '_to_' . $toDate . '.pdf');
    }

    public function trialBalance(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $asOfDate = $request->get('as_of_date');

        if (!$fromDate && !$toDate && !$asOfDate) {
            $toDate = now()->toDateString();
            $fromDate = now()->startOfMonth()->toDateString();
        } elseif ($asOfDate && !$fromDate && !$toDate) {
            $toDate = $asOfDate;
            $fromDate = null;
        } elseif (!$toDate) {
            $toDate = now()->toDateString();
        }

        $accounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->with(['accountGroup', 'voucherEntries' => function ($query) use ($fromDate, $toDate) {
                $query->whereHas('voucher', function ($voucherQuery) use ($fromDate, $toDate) {
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
            if ($fromDate) {
                $balance = $this->calculateAccountBalanceForPeriod($account, $fromDate, $toDate);
            } else {
                $balance = $this->calculateAccountBalance($account, $toDate);
            }

            if (abs($balance) >= 0.01) {
                $naturalBalanceSide = $this->getNaturalBalanceSide($account->account_type);

                if ($naturalBalanceSide === 'debit') {
                    $debitAmount = $balance >= 0 ? $balance : 0;
                    $creditAmount = $balance < 0 ? abs($balance) : 0;
                } else {
                    $creditAmount = $balance >= 0 ? $balance : 0;
                    $debitAmount = $balance < 0 ? abs($balance) : 0;
                }

                $trialBalanceData[] = [
                    'account_id' => $account->id,
                    'code' => $account->code,
                    'name' => $account->name,
                    'account_type' => $account->account_type,
                    'group' => $account->accountGroup?->name,
                    'opening_balance' => (float) ($account->opening_balance ?? 0),
                    'current_balance' => (float) $balance,
                    'debit_amount' => (float) $debitAmount,
                    'credit_amount' => (float) $creditAmount,
                ];

                $totalDebits += $debitAmount;
                $totalCredits += $creditAmount;
            }
        }

        usort($trialBalanceData, function ($a, $b) {
            return strcmp((string) $a['code'], (string) $b['code']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Trial balance retrieved successfully',
            'data' => [
                'filters' => [
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'as_of_date' => $fromDate ? null : $toDate,
                ],
                'summary' => [
                    'total_debits' => (float) $totalDebits,
                    'total_credits' => (float) $totalCredits,
                    'difference' => (float) abs($totalDebits - $totalCredits),
                    'balanced' => abs($totalDebits - $totalCredits) < 0.01,
                ],
                'records' => $trialBalanceData,
            ],
        ]);
    }

    public function cashFlow(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());

        $cashFlowData = $this->calculateCashFlowData($tenant, $fromDate, $toDate);

        return response()->json([
            'success' => true,
            'message' => 'Cash flow report retrieved successfully',
            'data' => [
                'filters' => [
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                ],
                'operating' => $cashFlowData['operatingActivities'],
                'investing' => $cashFlowData['investingActivities'],
                'financing' => $cashFlowData['financingActivities'],
                'summary' => [
                    'operating_total' => (float) $cashFlowData['operatingTotal'],
                    'investing_total' => (float) $cashFlowData['investingTotal'],
                    'financing_total' => (float) $cashFlowData['financingTotal'],
                    'net_cash_flow' => (float) $cashFlowData['netCashFlow'],
                    'opening_cash' => (float) $cashFlowData['openingCash'],
                    'closing_cash' => (float) $cashFlowData['closingCash'],
                    'calculated_closing_cash' => (float) $cashFlowData['calculatedClosingCash'],
                ],
            ],
        ]);
    }

    private function formatBalanceAccount($account, float $balance): array
    {
        return [
            'account_id' => $account->id,
            'name' => $account->name,
            'code' => $account->code,
            'balance' => (float) $balance,
        ];
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
        ];
    }

    private function getCashAccounts(Tenant $tenant)
    {
        return LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_type', 'asset')
            ->where('is_active', true)
            ->where(function ($query) {
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
                    'amount' => (float) $amount,
                    'type' => $account->account_type,
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
            ->where(function ($query) {
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
                    'description' => 'Investment in ' . $account->name,
                    'amount' => (float) (-$periodActivity),
                    'type' => 'investing',
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
                    'amount' => (float) $periodActivity,
                    'type' => $account->account_type,
                ];
                $total += $periodActivity;
            }
        }

        return ['activities' => $activities, 'total' => $total];
    }

    private function calculateAccountBalance($account, $asOfDate)
    {
        return $account->getCurrentBalance($asOfDate, false);
    }

    private function calculateAccountBalanceForPeriod($account, $fromDate, $toDate)
    {
        $openingDate = date('Y-m-d', strtotime($fromDate . ' -1 day'));
        $openingBalance = $account->getCurrentBalance($openingDate, false);
        $closingBalance = $account->getCurrentBalance($toDate, false);

        return $closingBalance - $openingBalance;
    }

    private function getNaturalBalanceSide($accountType)
    {
        return match ($accountType) {
            'asset', 'expense' => 'debit',
            'liability', 'equity', 'income' => 'credit',
            default => 'debit',
        };
    }

    private function getBalanceSheetData(Tenant $tenant, string $asOfDate): array
    {
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
}
