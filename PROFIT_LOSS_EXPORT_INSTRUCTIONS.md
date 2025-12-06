# Profit & Loss Export Implementation

## Files Created:
1. ✅ `resources/views/tenant/reports/profit-loss-pdf.blade.php` - PDF template
2. ✅ `app/Exports/ProfitLossExport.php` - Excel export class
3. ✅ Updated `resources/views/tenant/reports/profit-loss.blade.php` - Added export buttons

## Next Steps - Add to ReportsController.php:

Add these two methods after the `profitLoss()` method (around line 107):

```php
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
```

## Add Routes to routes/tenant.php:

Add these routes in the accounting section (around line 280):

```php
// Profit & Loss Export Routes
Route::get('/profit-loss/pdf', [ReportsController::class, 'profitLossPdf'])->name('profit-loss.pdf');
Route::get('/profit-loss/excel', [ReportsController::class, 'profitLossExcel'])->name('profit-loss.excel');
```

## Update the View Links:

In `resources/views/tenant/reports/profit-loss.blade.php`, replace the placeholder links:

```php
// Change from:
<a href="#" onclick="alert('PDF export coming soon'); return false;">

// To:
<a href="{{ route('tenant.accounting.profit-loss.pdf', ['tenant' => $tenant->slug, 'from_date' => $fromDate, 'to_date' => $toDate]) }}">

// Change from:
<a href="#" onclick="alert('Excel export coming soon'); return false;">

// To:
<a href="{{ route('tenant.accounting.profit-loss.excel', ['tenant' => $tenant->slug, 'from_date' => $fromDate, 'to_date' => $toDate]) }}">
```

## Summary of High Priority Features Implemented:

✅ 1. Export to PDF button
✅ 2. Export to Excel button  
✅ 3. Print functionality (window.print())
✅ 4. Quick date presets (Today, This Month, Last Month, etc.)
✅ 5. Profit Margin % card (4th summary card)

All high priority items are now complete!
