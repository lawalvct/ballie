<?php

namespace App\Http\Controllers\Tenant\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Voucher;
use App\Models\VoucherType;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AccountingController extends Controller
{
    /**
     * Display the accounting dashboard
     */
    public function index(Request $request, Tenant $tenant)
    {
        $currentTenant = $tenant;
        $user = auth()->user();

        // Get financial overview data
        $totalRevenue = $this->getTotalRevenue($tenant);
        $totalExpenses = $this->getTotalExpenses($tenant);
        $outstandingInvoices = $this->getOutstandingInvoices($tenant);
        $pendingInvoicesCount = $this->getPendingInvoicesCount($tenant);

        // Get recent transactions (vouchers)
        $recentTransactions = $this->getRecentTransactions($tenant);

        // Get voucher summary by type
        $voucherSummary = $this->getVoucherSummary($tenant);

        return view('tenant.accounting.index', [
            'currentTenant' => $currentTenant,
            'user' => $user,
            'tenant' => $currentTenant,
            'totalRevenue' => $totalRevenue,
            'totalExpenses' => $totalExpenses,
            'outstandingInvoices' => $outstandingInvoices,
            'pendingInvoicesCount' => $pendingInvoicesCount,
            'recentTransactions' => $recentTransactions,
            'voucherSummary' => $voucherSummary,
        ]);
    }

    private function getTotalRevenue(Tenant $tenant)
    {
        // Get revenue from approved vouchers (credit entries for income accounts)
        return Voucher::forTenant($tenant->id)
            ->where('status', Voucher::STATUS_POSTED)
            ->thisMonth()
            ->whereHas('entries', function($query) {
                $query->whereHas('account', function($accountQuery) {
                    $accountQuery->where('account_type', 'income');
                });
            })
            ->with('entries.account')
            ->get()
            ->sum(function($voucher) {
                return $voucher->entries
                    ->where('account.account_type', 'income')
                    ->sum('credit_amount');
            });
    }

    private function getTotalExpenses(Tenant $tenant)
    {
        // Get expenses from approved vouchers (debit entries for expense accounts)
        return Voucher::forTenant($tenant->id)
            ->where('status', Voucher::STATUS_APPROVED)
            ->thisMonth()
            ->whereHas('entries', function($query) {
                $query->whereHas('account', function($accountQuery) {
                    $accountQuery->where('account_type', 'expense');
                });
            })
            ->with('entries.account')
            ->get()
            ->sum(function($voucher) {
                return $voucher->entries
                    ->where('account.account_type', 'expense')
                    ->sum('debit_amount');
            });
    }

    private function getOutstandingInvoices(Tenant $tenant)
    {
        // Assuming you have an Invoice model
        return 1520 ?? 0;
    }

    private function getPendingInvoicesCount(Tenant $tenant)
    {
        return 5622?? 0;
    }

    private function getRecentTransactions(Tenant $tenant)
    {
        return Voucher::forTenant($tenant->id)
            ->with(['voucherType', 'entries.account'])
            ->orderBy('voucher_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($voucher) {
                $totalDebit = $voucher->entries->sum('debit_amount');
                $totalCredit = $voucher->entries->sum('credit_amount');

                // Determine if this is primarily income or expense based on account types
                $expenseAmount = $voucher->entries
                    ->whereIn('account.account_type', ['expense', 'asset'])
                    ->sum('debit_amount');

                $incomeAmount = $voucher->entries
                    ->whereIn('account.account_type', ['income', 'liability'])
                    ->sum('credit_amount');

                return (object) [
                    'id' => $voucher->id,
                    'description' => $voucher->narration ?: $voucher->voucherType->name . ' - ' . $voucher->voucher_number,
                    'amount' => max($totalDebit, $totalCredit),
                    'type' => $incomeAmount > $expenseAmount ? 'income' : 'expense',
                    'date' => $voucher->voucher_date,
                    'voucher_number' => $voucher->voucher_number,
                    'status' => $voucher->status
                ];
            });
    }

    private function getVoucherSummary(Tenant $tenant)
    {
        return 56;


    }
}
