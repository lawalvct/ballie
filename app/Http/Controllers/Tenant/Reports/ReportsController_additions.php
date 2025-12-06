<?php
// Add these methods to ReportsController.php after the profitLoss method

public function profitLossPdf(Request $request, Tenant $tenant)
{
    $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
    $toDate = $request->get('to_date', now()->toDateString());

    // Reuse the same calculation logic
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
                'account' => $account,
                'amount' => abs($balance),
            ];
            $totalIncome += abs($balance);
        }
    }

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
    ))->setPaper('a4', 'portrait');

    return $pdf->download('profit_loss_' . $fromDate . '_to_' . $toDate . '.pdf');
}

public function profitLossExcel(Request $request, Tenant $tenant)
{
    $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
    $toDate = $request->get('to_date', now()->toDateString());

    // Reuse the same calculation logic
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
                'account' => $account,
                'amount' => abs($balance),
            ];
            $totalIncome += abs($balance);
        }
    }

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

    $netProfit = $totalIncome - $totalExpenses;

    return \Maatwebsite\Excel\Facades\Excel::download(
        new \App\Exports\ProfitLossExport($tenant, $incomeData, $expenseData, $totalIncome, $totalExpenses, $netProfit, $fromDate, $toDate),
        'profit_loss_' . $fromDate . '_to_' . $toDate . '.xlsx'
    );
}
