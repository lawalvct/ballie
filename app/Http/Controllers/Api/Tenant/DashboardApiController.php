<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Customer;
use App\Models\InvoiceItem;
use App\Models\LedgerAccount;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Voucher;
use App\Models\VoucherType;
use App\Services\ModuleRegistry;
use App\Services\TerminologyService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardApiController extends BaseApiController
{
    /**
     * Get full dashboard data in a single call.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $tenant = $user->tenant;
        $category = $tenant->getBusinessCategory();
        $term = new TerminologyService($tenant);
        $now = Carbon::now();

        $cacheKey = "dashboard_api_v2_{$tenant->id}_{$now->format('Y-m-d-H-i')}";

        $data = Cache::remember($cacheKey, 300, function () use ($tenant, $category, $term, $now) {
            return $this->buildDashboardData($tenant, $category, $term, $now);
        });

        $data['business_category'] = $category;
        $data['terminology'] = $term->all();
        $data['enabled_modules'] = $this->getEnabledModuleFlags($tenant);
        $data['show_tour'] = !$user->tour_completed;

        return $this->success($data);
    }

    /**
     * Get summary metrics only (lightweight).
     */
    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();
        $tenant = $user->tenant;
        $tid = $tenant->id;
        $now = Carbon::now();

        $salesTypeIds = VoucherType::where('tenant_id', $tid)
            ->whereIn('code', ['SV', 'SALES'])->pluck('id');
        $expenseTypeIds = VoucherType::where('tenant_id', $tid)
            ->whereIn('code', ['EXP', 'PV'])->pluck('id');
        $purchaseTypeIds = VoucherType::where('tenant_id', $tid)
            ->whereIn('code', ['PUR'])->pluck('id');

        $monthlyRevenue = Voucher::where('tenant_id', $tid)
            ->whereIn('voucher_type_id', $salesTypeIds)
            ->posted()->thisMonth()->sum('total_amount');

        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();
        $lastMonthRevenue = Voucher::where('tenant_id', $tid)
            ->whereIn('voucher_type_id', $salesTypeIds)
            ->posted()->dateRange($lastMonthStart, $lastMonthEnd)
            ->sum('total_amount');

        $revenueGrowth = $lastMonthRevenue > 0
            ? round((($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : ($monthlyRevenue > 0 ? 100 : 0);

        $monthlyExpenses = Voucher::where('tenant_id', $tid)
            ->whereIn('voucher_type_id', $expenseTypeIds)
            ->posted()->thisMonth()->sum('total_amount');

        $monthlyPurchase = Voucher::where('tenant_id', $tid)
            ->whereIn('voucher_type_id', $purchaseTypeIds)
            ->posted()->thisMonth()->sum('total_amount');

        $totalSalesCount = Voucher::where('tenant_id', $tid)
            ->whereIn('voucher_type_id', $salesTypeIds)
            ->posted()->thisMonth()->count();

        return $this->success([
            'monthly_revenue' => (float) $monthlyRevenue,
            'revenue_growth' => (float) $revenueGrowth,
            'monthly_expenses' => (float) $monthlyExpenses,
            'monthly_purchase' => (float) $monthlyPurchase,
            'net_profit' => (float) ($monthlyRevenue - $monthlyExpenses - $monthlyPurchase),
            'total_sales_count' => (int) $totalSalesCount,
            'total_customers' => Customer::where('tenant_id', $tid)->count(),
            'total_products' => Product::where('tenant_id', $tid)->active()->count(),
        ]);
    }

    /**
     * Get chart data: 6-month revenue vs expenses.
     */
    public function revenueChart(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;
        $tid = $tenant->id;
        $now = Carbon::now();

        $salesTypeIds = VoucherType::where('tenant_id', $tid)
            ->whereIn('code', ['SV', 'SALES'])->pluck('id');
        $expenseTypeIds = VoucherType::where('tenant_id', $tid)
            ->whereIn('code', ['EXP', 'PV'])->pluck('id');
        $purchaseTypeIds = VoucherType::where('tenant_id', $tid)
            ->whereIn('code', ['PUR'])->pluck('id');

        return $this->success([
            'chart' => $this->getMonthlyChartData($tid, $salesTypeIds, $expenseTypeIds, $purchaseTypeIds, $now),
        ]);
    }

    /**
     * Get daily revenue sparkline (last 14 days).
     */
    public function dailyRevenue(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;
        $tid = $tenant->id;
        $now = Carbon::now();
        $days = (int) $request->query('days', 14);
        $days = min(max($days, 7), 90);

        $salesTypeIds = VoucherType::where('tenant_id', $tid)
            ->whereIn('code', ['SV', 'SALES'])->pluck('id');

        return $this->success([
            'daily_revenue' => $this->getDailyRevenueData($tid, $salesTypeIds, $now, $days),
        ]);
    }

    /**
     * Get revenue breakdown by voucher type (for doughnut/pie chart).
     */
    public function revenueBreakdown(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;
        $tid = $tenant->id;
        $now = Carbon::now();

        return $this->success([
            'breakdown' => $this->getRevenueBreakdownData($tid, $now),
        ]);
    }

    /**
     * Get account balances (cash, receivables, payables).
     */
    public function balances(Request $request): JsonResponse
    {
        $tid = $request->user()->tenant->id;

        $cashBalance = LedgerAccount::where('tenant_id', $tid)
            ->where('code', 'CASH-001')->value('current_balance') ?? 0;
        $receivables = LedgerAccount::where('tenant_id', $tid)
            ->where('code', 'AR-001')->value('current_balance') ?? 0;
        $payables = LedgerAccount::where('tenant_id', $tid)
            ->where('code', 'AP-001')->value('current_balance') ?? 0;

        return $this->success([
            'cash_balance' => (float) $cashBalance,
            'receivables' => (float) $receivables,
            'payables' => (float) $payables,
        ]);
    }

    /**
     * Get top selling products.
     */
    public function topProducts(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;

        if (!ModuleRegistry::isModuleEnabled($tenant, 'inventory')) {
            return $this->success(['top_products' => []], 'Inventory module is disabled.');
        }

        $tid = $tenant->id;
        $limit = min((int) $request->query('limit', 5), 20);
        $salesTypeIds = VoucherType::where('tenant_id', $tid)
            ->whereIn('code', ['SV', 'SALES'])->pluck('id');

        return $this->success([
            'top_products' => $this->getTopProductsData($tid, $salesTypeIds, $limit),
        ]);
    }

    /**
     * Get top customers by spending.
     */
    public function topCustomers(Request $request): JsonResponse
    {
        $tid = $request->user()->tenant->id;
        $limit = min((int) $request->query('limit', 5), 20);

        $topCustomers = Customer::where('tenant_id', $tid)
            ->active()
            ->where('total_spent', '>', 0)
            ->orderByDesc('total_spent')
            ->limit($limit)
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->full_name ?? $c->company_name ?? $c->first_name,
                'spent' => (float) $c->total_spent,
                'outstanding' => (float) $c->outstanding_balance,
            ]);

        return $this->success(['top_customers' => $topCustomers]);
    }

    /**
     * Get recent transactions.
     */
    public function recentTransactions(Request $request): JsonResponse
    {
        $tid = $request->user()->tenant->id;
        $limit = min((int) $request->query('limit', 8), 30);

        $transactions = Voucher::where('tenant_id', $tid)
            ->posted()
            ->with(['voucherType'])
            ->orderByDesc('voucher_date')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(fn($v) => [
                'id' => $v->id,
                'number' => $v->getDisplayNumber(),
                'type' => $v->voucherType?->name ?? 'Transaction',
                'type_code' => $v->voucherType?->code ?? '',
                'amount' => (float) $v->total_amount,
                'date' => $v->voucher_date?->toIso8601String(),
                'narration' => $v->narration,
            ]);

        return $this->success(['transactions' => $transactions]);
    }

    /**
     * Get outstanding invoices.
     */
    public function outstandingInvoices(Request $request): JsonResponse
    {
        $tid = $request->user()->tenant->id;
        $salesTypeIds = VoucherType::where('tenant_id', $tid)
            ->whereIn('code', ['SV', 'SALES'])->pluck('id');

        $invoices = Voucher::where('tenant_id', $tid)
            ->whereIn('voucher_type_id', $salesTypeIds)
            ->posted()
            ->where('total_amount', '>', 0)
            ->orderByDesc('voucher_date')
            ->limit(10)
            ->get();

        return $this->success([
            'count' => $invoices->count(),
            'total' => (float) $invoices->sum('total_amount'),
            'items' => $invoices->map(fn($v) => [
                'id' => $v->id,
                'number' => $v->getDisplayNumber(),
                'amount' => (float) $v->total_amount,
                'date' => $v->voucher_date?->toIso8601String(),
            ]),
        ]);
    }

    /**
     * Get alerts (low stock, out of stock, receivables).
     */
    public function alerts(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;
        $category = $tenant->getBusinessCategory();
        $term = new TerminologyService($tenant);

        return $this->success([
            'alerts' => $this->getAlertsData($tenant, $category, $term),
        ]);
    }

    /**
     * Get POS today's sales summary.
     */
    public function posTodaySales(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;

        if (!ModuleRegistry::isModuleEnabled($tenant, 'pos')) {
            return $this->success([
                'today_sales' => 0,
                'today_sales_count' => 0,
            ], 'POS module is disabled.');
        }

        $tid = $tenant->id;
        $now = Carbon::now();

        return $this->success([
            'today_sales' => (float) Sale::where('tenant_id', $tid)
                ->whereDate('sale_date', $now->toDateString())
                ->where('status', 'completed')
                ->sum('total_amount'),
            'today_sales_count' => (int) Sale::where('tenant_id', $tid)
                ->whereDate('sale_date', $now->toDateString())
                ->where('status', 'completed')
                ->count(),
        ]);
    }

    /**
     * Get inventory stats (low stock, out of stock counts).
     */
    public function inventoryStats(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;

        if (!ModuleRegistry::isModuleEnabled($tenant, 'inventory')) {
            return $this->success([
                'low_stock_count' => 0,
                'out_of_stock_count' => 0,
            ], 'Inventory module is disabled.');
        }

        $tid = $tenant->id;

        return $this->success([
            'low_stock_count' => Product::where('tenant_id', $tid)
                ->where('type', 'item')->lowStock()->count(),
            'out_of_stock_count' => Product::where('tenant_id', $tid)
                ->where('type', 'item')->outOfStock()->count(),
        ]);
    }

    /**
     * Dismiss the tour banner.
     */
    public function dismissTour(Request $request): JsonResponse
    {
        $request->user()->update(['tour_completed' => true]);

        return $this->success(null, 'Tour dismissed.');
    }

    // ─── Private Helpers ──────────────────────────────────────

    private function buildDashboardData($tenant, string $category, TerminologyService $term, Carbon $now): array
    {
        $tid = $tenant->id;

        $salesTypeIds = VoucherType::where('tenant_id', $tid)
            ->whereIn('code', ['SV', 'SALES'])->pluck('id');
        $purchaseTypeIds = VoucherType::where('tenant_id', $tid)
            ->whereIn('code', ['PUR'])->pluck('id');
        $expenseTypeIds = VoucherType::where('tenant_id', $tid)
            ->whereIn('code', ['EXP', 'PV'])->pluck('id');
        $receiptTypeIds = VoucherType::where('tenant_id', $tid)
            ->whereIn('code', ['RV'])->pluck('id');

        // Revenue
        $monthlyRevenue = Voucher::where('tenant_id', $tid)
            ->whereIn('voucher_type_id', $salesTypeIds)
            ->posted()->thisMonth()->sum('total_amount');

        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();
        $lastMonthRevenue = Voucher::where('tenant_id', $tid)
            ->whereIn('voucher_type_id', $salesTypeIds)
            ->posted()->dateRange($lastMonthStart, $lastMonthEnd)
            ->sum('total_amount');

        $revenueGrowth = $lastMonthRevenue > 0
            ? round((($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : ($monthlyRevenue > 0 ? 100 : 0);

        $totalRevenue = Voucher::where('tenant_id', $tid)
            ->whereIn('voucher_type_id', $salesTypeIds)
            ->posted()->sum('total_amount');

        $monthlyExpenses = Voucher::where('tenant_id', $tid)
            ->whereIn('voucher_type_id', $expenseTypeIds)
            ->posted()->thisMonth()->sum('total_amount');

        $monthlyPurchase = Voucher::where('tenant_id', $tid)
            ->whereIn('voucher_type_id', $purchaseTypeIds)
            ->posted()->thisMonth()->sum('total_amount');

        $netProfit = $monthlyRevenue - $monthlyExpenses - $monthlyPurchase;

        $totalSalesCount = Voucher::where('tenant_id', $tid)
            ->whereIn('voucher_type_id', $salesTypeIds)
            ->posted()->thisMonth()->count();

        $monthlyReceipts = Voucher::where('tenant_id', $tid)
            ->whereIn('voucher_type_id', $receiptTypeIds)
            ->posted()->thisMonth()->sum('total_amount');

        // Counts
        $totalCustomers = Customer::where('tenant_id', $tid)->count();
        $activeCustomers = Customer::where('tenant_id', $tid)->active()->count();
        $newCustomersThisMonth = Customer::where('tenant_id', $tid)
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();
        $totalProducts = Product::where('tenant_id', $tid)->active()->count();

        // Balances
        $cashBalance = LedgerAccount::where('tenant_id', $tid)
            ->where('code', 'CASH-001')->value('current_balance') ?? 0;
        $receivables = LedgerAccount::where('tenant_id', $tid)
            ->where('code', 'AR-001')->value('current_balance') ?? 0;
        $payables = LedgerAccount::where('tenant_id', $tid)
            ->where('code', 'AP-001')->value('current_balance') ?? 0;

        // Charts
        $chartData = $this->getMonthlyChartData($tid, $salesTypeIds, $expenseTypeIds, $purchaseTypeIds, $now);
        $revenueBreakdown = $this->getRevenueBreakdownData($tid, $now);
        $dailyRevenue = $this->getDailyRevenueData($tid, $salesTypeIds, $now, 14);

        // Lists
        $alerts = $this->getAlertsData($tenant, $category, $term);

        $topProducts = [];
        if (ModuleRegistry::isModuleEnabled($tenant, 'inventory')) {
            $topProducts = $this->getTopProductsData($tid, $salesTypeIds, 5);
        }

        $topCustomers = Customer::where('tenant_id', $tid)
            ->active()
            ->where('total_spent', '>', 0)
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->full_name ?? $c->company_name ?? $c->first_name,
                'spent' => (float) $c->total_spent,
                'outstanding' => (float) $c->outstanding_balance,
            ])->toArray();

        $outstandingInvoices = $this->getOutstandingInvoicesData($tid, $salesTypeIds);

        $recentTransactions = Voucher::where('tenant_id', $tid)
            ->posted()
            ->with(['voucherType'])
            ->orderByDesc('voucher_date')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get()
            ->map(fn($v) => [
                'id' => $v->id,
                'number' => $v->getDisplayNumber(),
                'type' => $v->voucherType?->name ?? 'Transaction',
                'type_code' => $v->voucherType?->code ?? '',
                'amount' => (float) $v->total_amount,
                'date' => $v->voucher_date?->toIso8601String(),
                'narration' => $v->narration,
            ])->toArray();

        // Inventory stats
        $lowStockCount = 0;
        $outOfStockCount = 0;
        if (ModuleRegistry::isModuleEnabled($tenant, 'inventory')) {
            $lowStockCount = Product::where('tenant_id', $tid)
                ->where('type', 'item')->lowStock()->count();
            $outOfStockCount = Product::where('tenant_id', $tid)
                ->where('type', 'item')->outOfStock()->count();
        }

        // POS today
        $todaySales = 0;
        $todaySalesCount = 0;
        if (ModuleRegistry::isModuleEnabled($tenant, 'pos')) {
            $todaySales = Sale::where('tenant_id', $tid)
                ->whereDate('sale_date', $now->toDateString())
                ->where('status', 'completed')
                ->sum('total_amount');
            $todaySalesCount = Sale::where('tenant_id', $tid)
                ->whereDate('sale_date', $now->toDateString())
                ->where('status', 'completed')
                ->count();
        }

        return [
            'metrics' => [
                'total_revenue' => (float) $totalRevenue,
                'monthly_revenue' => (float) $monthlyRevenue,
                'revenue_growth' => (float) $revenueGrowth,
                'monthly_expenses' => (float) $monthlyExpenses,
                'monthly_purchase' => (float) $monthlyPurchase,
                'net_profit' => (float) $netProfit,
                'total_sales_count' => (int) $totalSalesCount,
                'monthly_receipts' => (float) $monthlyReceipts,
            ],
            'counts' => [
                'total_customers' => $totalCustomers,
                'active_customers' => $activeCustomers,
                'new_customers_this_month' => $newCustomersThisMonth,
                'total_products' => $totalProducts,
            ],
            'balances' => [
                'cash_balance' => (float) $cashBalance,
                'receivables' => (float) $receivables,
                'payables' => (float) $payables,
            ],
            'charts' => [
                'revenue_vs_expenses' => $chartData,
                'revenue_breakdown' => $revenueBreakdown,
                'daily_revenue' => $dailyRevenue,
            ],
            'inventory' => [
                'low_stock_count' => $lowStockCount,
                'out_of_stock_count' => $outOfStockCount,
            ],
            'pos' => [
                'today_sales' => (float) $todaySales,
                'today_sales_count' => (int) $todaySalesCount,
            ],
            'alerts' => $alerts,
            'top_products' => $topProducts,
            'top_customers' => $topCustomers,
            'outstanding_invoices' => $outstandingInvoices,
            'recent_transactions' => $recentTransactions,
        ];
    }

    private function getMonthlyChartData($tid, $salesTypeIds, $expenseTypeIds, $purchaseTypeIds, Carbon $now): array
    {
        $labels = [];
        $revenue = [];
        $expenses = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $labels[] = $month->format('M Y');

            $revenue[] = (float) Voucher::where('tenant_id', $tid)
                ->whereIn('voucher_type_id', $salesTypeIds)
                ->posted()
                ->whereMonth('voucher_date', $month->month)
                ->whereYear('voucher_date', $month->year)
                ->sum('total_amount');

            $expenses[] = (float) Voucher::where('tenant_id', $tid)
                ->whereIn('voucher_type_id', $expenseTypeIds->merge($purchaseTypeIds))
                ->posted()
                ->whereMonth('voucher_date', $month->month)
                ->whereYear('voucher_date', $month->year)
                ->sum('total_amount');
        }

        return compact('labels', 'revenue', 'expenses');
    }

    private function getDailyRevenueData($tid, $salesTypeIds, Carbon $now, int $days): array
    {
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $data[] = [
                'date' => $date->format('M d'),
                'date_iso' => $date->toIso8601String(),
                'amount' => (float) Voucher::where('tenant_id', $tid)
                    ->whereIn('voucher_type_id', $salesTypeIds)
                    ->posted()
                    ->whereDate('voucher_date', $date->toDateString())
                    ->sum('total_amount'),
            ];
        }
        return $data;
    }

    private function getRevenueBreakdownData($tid, Carbon $now): array
    {
        return Voucher::where('vouchers.tenant_id', $tid)
            ->posted()
            ->thisMonth()
            ->join('voucher_types', 'voucher_types.id', '=', 'vouchers.voucher_type_id')
            ->whereIn('voucher_types.code', ['SV', 'RV', 'MI'])
            ->groupBy('voucher_types.name', 'voucher_types.code')
            ->selectRaw('voucher_types.name, voucher_types.code, SUM(vouchers.total_amount) as total')
            ->get()
            ->map(fn($r) => ['name' => $r->name, 'code' => $r->code, 'total' => (float) $r->total])
            ->toArray();
    }

    private function getOutstandingInvoicesData($tid, $salesTypeIds): array
    {
        $invoices = Voucher::where('tenant_id', $tid)
            ->whereIn('voucher_type_id', $salesTypeIds)
            ->posted()
            ->where('total_amount', '>', 0)
            ->orderByDesc('voucher_date')
            ->limit(5)
            ->get();

        return [
            'count' => $invoices->count(),
            'total' => (float) $invoices->sum('total_amount'),
            'items' => $invoices->map(fn($v) => [
                'id' => $v->id,
                'number' => $v->getDisplayNumber(),
                'amount' => (float) $v->total_amount,
                'date' => $v->voucher_date?->toIso8601String(),
            ])->toArray(),
        ];
    }

    private function getTopProductsData($tid, $salesTypeIds, int $limit): array
    {
        return InvoiceItem::whereHas('voucher', function ($q) use ($tid, $salesTypeIds) {
                $q->where('tenant_id', $tid)
                    ->whereIn('voucher_type_id', $salesTypeIds)
                    ->posted();
            })
            ->whereNotNull('product_id')
            ->join('products', 'products.id', '=', 'invoice_items.product_id')
            ->groupBy('invoice_items.product_id', 'products.name')
            ->selectRaw('invoice_items.product_id, products.name, SUM(invoice_items.quantity) as total_qty, SUM(invoice_items.total) as total_revenue')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get()
            ->map(fn($p) => [
                'product_id' => $p->product_id,
                'name' => $p->name,
                'quantity_sold' => (int) $p->total_qty,
                'revenue' => (float) $p->total_revenue,
            ])->toArray();
    }

    private function getAlertsData($tenant, string $category, TerminologyService $term): array
    {
        $tid = $tenant->id;
        $alerts = [];

        if (!$term->isHidden('low_stock') && ModuleRegistry::isModuleEnabled($tenant, 'inventory')) {
            $lowStock = Product::where('tenant_id', $tid)
                ->where('type', 'item')->lowStock()->count();
            if ($lowStock > 0) {
                $alerts[] = [
                    'type' => 'low_stock',
                    'severity' => 'warning',
                    'title' => $term->label('low_stock') ?: 'Low Stock Alert',
                    'message' => "{$lowStock} product(s) are running low on inventory",
                    'count' => $lowStock,
                ];
            }

            $outOfStock = Product::where('tenant_id', $tid)
                ->where('type', 'item')->outOfStock()->count();
            if ($outOfStock > 0) {
                $alerts[] = [
                    'type' => 'out_of_stock',
                    'severity' => 'critical',
                    'title' => 'Out of Stock',
                    'message' => "{$outOfStock} product(s) are completely out of stock",
                    'count' => $outOfStock,
                ];
            }
        }

        $receivable = LedgerAccount::where('tenant_id', $tid)
            ->where('code', 'AR-001')->value('current_balance') ?? 0;
        if ($receivable > 0) {
            $alerts[] = [
                'type' => 'receivables',
                'severity' => 'info',
                'title' => 'Outstanding Receivables',
                'message' => 'You have outstanding receivables to collect',
                'amount' => (float) $receivable,
            ];
        }

        return $alerts;
    }

    private function getEnabledModuleFlags($tenant): array
    {
        return [
            'inventory' => ModuleRegistry::isModuleEnabled($tenant, 'inventory'),
            'crm' => ModuleRegistry::isModuleEnabled($tenant, 'crm'),
            'pos' => ModuleRegistry::isModuleEnabled($tenant, 'pos'),
            'payroll' => ModuleRegistry::isModuleEnabled($tenant, 'payroll'),
            'banking' => ModuleRegistry::isModuleEnabled($tenant, 'banking'),
            'ecommerce' => ModuleRegistry::isModuleEnabled($tenant, 'ecommerce'),
            'projects' => ModuleRegistry::isModuleEnabled($tenant, 'projects'),
            'procurement' => ModuleRegistry::isModuleEnabled($tenant, 'procurement'),
        ];
    }
}
