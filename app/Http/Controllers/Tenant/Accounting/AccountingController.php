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
use Illuminate\Support\Facades\Cache;

class AccountingController extends Controller
{
    /**
     * Display the accounting dashboard
     */
    public function index(Request $request, Tenant $tenant)
    {
        $currentTenant = $tenant;
        $user = auth()->user();

        // Cache key for dashboard metrics
        $cacheKey = "dashboard_metrics_{$tenant->id}_" . Carbon::now()->format('Y-m-d-H');

        // Get cached or fresh data
        $dashboardData = Cache::remember($cacheKey, 300, function () use ($tenant) {
            return [
                'totalRevenue' => $this->getTotalRevenue($tenant),
                'totalExpenses' => $this->getTotalExpenses($tenant),
                'outstandingInvoices' => $this->getOutstandingInvoices($tenant),
                'pendingInvoicesCount' => $this->getPendingInvoicesCount($tenant),
                'recentTransactions' => $this->getRecentTransactions($tenant),
                'voucherSummary' => $this->getVoucherSummary($tenant),
                'revenueChange' => $this->getRevenueChange($tenant),
                'expenseChange' => $this->getExpenseChange($tenant),
                'profitChange' => $this->getProfitChange($tenant),
                'chartData' => $this->getChartData($tenant),
            ];
        });

        return view('tenant.accounting.index', array_merge([
            'currentTenant' => $currentTenant,
            'user' => $user,
            'tenant' => $currentTenant,
        ], $dashboardData));
    }

    /**
     * Get chart data for AJAX requests
     */
    public function getChartDataApi(Request $request, Tenant $tenant)
    {
        $period = $request->get('period', '6m'); // 6m or 1y
        $chartData = $this->getChartData($tenant, $period);

        return response()->json($chartData);
    }

    private function getOutstandingInvoices(Tenant $tenant)
    {
        // Get outstanding amount from Sales (where paid_amount < total_amount)
        $outstandingSales = DB::table('sales')
            ->where('tenant_id', $tenant->id)
            ->where('status', 'completed')
            ->whereRaw('paid_amount < total_amount')
            ->sum(DB::raw('total_amount - paid_amount'));

        // Get outstanding from posted vouchers with invoice items (assuming these are sales invoices)
        $outstandingVouchers = DB::table('vouchers')
            ->where('tenant_id', $tenant->id)
            ->where('status', Voucher::STATUS_POSTED)
            ->whereExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('invoice_items')
                    ->whereColumn('invoice_items.voucher_id', 'vouchers.id');
            })
            ->sum('total_amount');

        return (float) ($outstandingSales + $outstandingVouchers);
    }

    private function getPendingInvoicesCount(Tenant $tenant)
    {
        // Count draft vouchers with invoice items
        $pendingVouchers = Voucher::where('tenant_id', $tenant->id)
            ->where('status', Voucher::STATUS_DRAFT)
            ->whereHas('items')
            ->count();

        // Count pending sales
        $pendingSales = DB::table('sales')
            ->where('tenant_id', $tenant->id)
            ->where('status', 'pending')
            ->count();

        return $pendingVouchers + $pendingSales;
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
        // Get voucher summary grouped by voucher type for current month
        return VoucherType::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->with(['vouchers' => function($query) {
                $query->where('status', Voucher::STATUS_POSTED)
                    ->whereMonth('voucher_date', Carbon::now()->month)
                    ->whereYear('voucher_date', Carbon::now()->year);
            }])
            ->get()
            ->map(function($voucherType) {
                $vouchers = $voucherType->vouchers;
                return [
                    'type' => $voucherType->name,
                    'code' => $voucherType->code,
                    'count' => $vouchers->count(),
                    'total' => $vouchers->sum('total_amount'),
                    'color' => $this->getVoucherTypeColor($voucherType->code),
                ];
            })
            ->filter(function($summary) {
                return $summary['count'] > 0; // Only show types with vouchers
            })
            ->values();
    }

    private function getVoucherTypeColor($code)
    {
        // Assign colors based on voucher type code
        $colors = [
            'PAY' => 'red',
            'RCV' => 'green',
            'JV' => 'blue',
            'SAL' => 'purple',
            'PUR' => 'orange',
            'CN' => 'yellow',
            'DN' => 'pink',
        ];

        return $colors[$code] ?? 'gray';
    }

    /**
     * Calculate revenue change percentage compared to last month
     */
    private function getRevenueChange(Tenant $tenant)
    {
        $currentRevenue = $this->getTotalRevenue($tenant);
        $lastMonthRevenue = $this->getTotalRevenue($tenant, Carbon::now()->subMonth());

        if ($lastMonthRevenue == 0) {
            return ['percentage' => 0, 'direction' => 'neutral'];
        }

        $change = (($currentRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;

        return [
            'percentage' => round(abs($change), 1),
            'direction' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'neutral')
        ];
    }

    /**
     * Calculate expense change percentage compared to last month
     */
    private function getExpenseChange(Tenant $tenant)
    {
        $currentExpenses = $this->getTotalExpenses($tenant);
        $lastMonthExpenses = $this->getTotalExpenses($tenant, Carbon::now()->subMonth());

        if ($lastMonthExpenses == 0) {
            return ['percentage' => 0, 'direction' => 'neutral'];
        }

        $change = (($currentExpenses - $lastMonthExpenses) / $lastMonthExpenses) * 100;

        return [
            'percentage' => round(abs($change), 1),
            'direction' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'neutral')
        ];
    }

    /**
     * Calculate profit change percentage compared to last month
     */
    private function getProfitChange(Tenant $tenant)
    {
        $currentProfit = $this->getTotalRevenue($tenant) - $this->getTotalExpenses($tenant);
        $lastMonthProfit = $this->getTotalRevenue($tenant, Carbon::now()->subMonth()) -
                          $this->getTotalExpenses($tenant, Carbon::now()->subMonth());

        if ($lastMonthProfit == 0) {
            return ['percentage' => 0, 'direction' => 'neutral'];
        }

        $change = (($currentProfit - $lastMonthProfit) / abs($lastMonthProfit)) * 100;

        return [
            'percentage' => round(abs($change), 1),
            'direction' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'neutral')
        ];
    }

    /**
     * Get chart data for financial overview
     */
    private function getChartData(Tenant $tenant, $period = '6m')
    {
        $months = $period === '1y' ? 12 : 6;
        $data = [
            'labels' => [],
            'revenue' => [],
            'expenses' => [],
            'profit' => []
        ];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $data['labels'][] = $date->format('M Y');

            $revenue = $this->getTotalRevenue($tenant, $date);
            $expenses = $this->getTotalExpenses($tenant, $date);

            $data['revenue'][] = round($revenue, 2);
            $data['expenses'][] = round($expenses, 2);
            $data['profit'][] = round($revenue - $expenses, 2);
        }

        return $data;
    }

    /**
     * Modified getTotalRevenue to accept optional date parameter
     */
    private function getTotalRevenue(Tenant $tenant, Carbon $date = null)
    {
        $date = $date ?? Carbon::now();

        return Voucher::forTenant($tenant->id)
            ->where('status', Voucher::STATUS_POSTED)
            ->whereMonth('voucher_date', $date->month)
            ->whereYear('voucher_date', $date->year)
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

    /**
     * Modified getTotalExpenses to accept optional date parameter
     */
    private function getTotalExpenses(Tenant $tenant, Carbon $date = null)
    {
        $date = $date ?? Carbon::now();

        return Voucher::forTenant($tenant->id)
            ->where('status', Voucher::STATUS_APPROVED)
            ->whereMonth('voucher_date', $date->month)
            ->whereYear('voucher_date', $date->year)
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
}
