<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\InvoiceItem;
use App\Models\LedgerAccount;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Tenant;
use App\Models\Voucher;
use App\Models\VoucherEntry;
use App\Models\VoucherType;
use App\Services\ModuleRegistry;
use App\Services\TerminologyService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request, Tenant $tenant)
    {
        $user = auth()->user();
        $category = $tenant->getBusinessCategory();
        $term = new TerminologyService($tenant);
        $now = Carbon::now();

        // Cache dashboard for 5 minutes per tenant
        $cacheKey = "dashboard_v2_{$tenant->id}_{$now->format('Y-m-d-H-i')}";

        $data = Cache::remember($cacheKey, 300, function () use ($tenant, $category, $term, $now) {
            return $this->buildDashboardData($tenant, $category, $term, $now);
        });

        $data['tenant'] = $tenant;
        $data['businessCategory'] = $category;
        $data['term'] = $term;
        $data['showTour'] = !$user->tour_completed;
        $data['enabledModules'] = $this->getEnabledModuleFlags($tenant);

        return view('tenant.dashboard.index', $data);
    }

    // ─── Data Builder ─────────────────────────────────────────

    private function buildDashboardData(Tenant $tenant, string $category, TerminologyService $term, Carbon $now): array
    {
        $tid = $tenant->id;

        // ── Voucher type IDs ──
        $salesTypeIds = VoucherType::where('tenant_id', $tid)
            ->whereIn('code', ['SV', 'SALES'])->pluck('id');
        $purchaseTypeIds = VoucherType::where('tenant_id', $tid)
            ->whereIn('code', ['PUR'])->pluck('id');
        $expenseTypeIds = VoucherType::where('tenant_id', $tid)
            ->whereIn('code', ['EXP', 'PV'])->pluck('id');
        $receiptTypeIds = VoucherType::where('tenant_id', $tid)
            ->whereIn('code', ['RV'])->pluck('id');

        // ── Revenue ──
        $monthlyRevenue = Voucher::where('tenant_id', $tid)
            ->whereIn('voucher_type_id', $salesTypeIds)
            ->posted()->thisMonth()
            ->sum('total_amount');

        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();
        $lastMonthRevenue = Voucher::where('tenant_id', $tid)
            ->whereIn('voucher_type_id', $salesTypeIds)
            ->posted()
            ->dateRange($lastMonthStart, $lastMonthEnd)
            ->sum('total_amount');

        $revenueGrowth = $lastMonthRevenue > 0
            ? round((($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : ($monthlyRevenue > 0 ? 100 : 0);

        $totalRevenue = Voucher::where('tenant_id', $tid)
            ->whereIn('voucher_type_id', $salesTypeIds)
            ->posted()->sum('total_amount');

        // ── Expenses (this month) ──
        $monthlyExpenses = Voucher::where('tenant_id', $tid)
            ->whereIn('voucher_type_id', $expenseTypeIds)
            ->posted()->thisMonth()
            ->sum('total_amount');

        // ── Purchase (this month) ──
        $monthlyPurchase = Voucher::where('tenant_id', $tid)
            ->whereIn('voucher_type_id', $purchaseTypeIds)
            ->posted()->thisMonth()
            ->sum('total_amount');

        // ── Net Profit ──
        $netProfit = $monthlyRevenue - $monthlyExpenses - $monthlyPurchase;

        // ── Sales count this month ──
        $totalSalesCount = Voucher::where('tenant_id', $tid)
            ->whereIn('voucher_type_id', $salesTypeIds)
            ->posted()->thisMonth()->count();

        // ── Receipts this month ──
        $monthlyReceipts = Voucher::where('tenant_id', $tid)
            ->whereIn('voucher_type_id', $receiptTypeIds)
            ->posted()->thisMonth()
            ->sum('total_amount');

        // ── Counts ──
        $totalCustomers = Customer::where('tenant_id', $tid)->count();
        $activeCustomers = Customer::where('tenant_id', $tid)->active()->count();
        $newCustomersThisMonth = Customer::where('tenant_id', $tid)
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();
        $totalProducts = Product::where('tenant_id', $tid)->active()->count();

        // ── Account Balances ──
        $cashBalance = LedgerAccount::where('tenant_id', $tid)
            ->where('code', 'CASH-001')->value('current_balance') ?? 0;
        $receivables = LedgerAccount::where('tenant_id', $tid)
            ->where('code', 'AR-001')->value('current_balance') ?? 0;
        $payables = LedgerAccount::where('tenant_id', $tid)
            ->where('code', 'AP-001')->value('current_balance') ?? 0;

        // ── Chart: 6-month revenue vs expenses ──
        $chartData = $this->getMonthlyChartData($tid, $salesTypeIds, $expenseTypeIds, $purchaseTypeIds, $now);

        // ── Alerts ──
        $alerts = $this->getAlerts($tenant, $category, $term);

        // ── Top Products (if inventory module on) ──
        $topProducts = [];
        if (ModuleRegistry::isModuleEnabled($tenant, 'inventory')) {
            $topProducts = $this->getTopProducts($tid, $salesTypeIds, 5);
        }

        // ── Top Customers ──
        $topCustomers = Customer::where('tenant_id', $tid)
            ->active()
            ->where('total_spent', '>', 0)
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get()
            ->map(fn($c) => [
                'name' => $c->full_name ?? $c->company_name ?? $c->first_name,
                'spent' => $c->total_spent,
                'outstanding' => $c->outstanding_balance,
                'orders' => 0,
            ])->toArray();

        // ── Outstanding Invoices ──
        $outstandingInvoices = $this->getOutstandingInvoices($tid, $salesTypeIds);

        // ── Recent Transactions ──
        $recentTransactions = Voucher::where('tenant_id', $tid)
            ->posted()
            ->with(['voucherType'])
            ->orderByDesc('voucher_date')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get()
            ->map(fn($v) => [
                'number' => $v->getDisplayNumber(),
                'type' => $v->voucherType?->name ?? 'Transaction',
                'type_code' => $v->voucherType?->code ?? '',
                'amount' => $v->total_amount,
                'date' => $v->voucher_date,
                'narration' => $v->narration,
            ])->toArray();

        // ── Inventory stats ──
        $lowStockCount = 0;
        $outOfStockCount = 0;
        if (ModuleRegistry::isModuleEnabled($tenant, 'inventory')) {
            $lowStockCount = Product::where('tenant_id', $tid)
                ->where('type', 'item')->lowStock()->count();
            $outOfStockCount = Product::where('tenant_id', $tid)
                ->where('type', 'item')->outOfStock()->count();
        }

        // ── POS Today's Sales ──
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

        // ── Revenue by voucher type (for doughnut chart) ──
        $revenueBreakdown = $this->getRevenueBreakdown($tid, $now);

        // ── Daily revenue for sparkline (last 14 days) ──
        $dailyRevenue = $this->getDailyRevenue($tid, $salesTypeIds, $now, 14);

        return compact(
            'totalRevenue', 'monthlyRevenue', 'revenueGrowth',
            'monthlyExpenses', 'monthlyPurchase', 'netProfit',
            'totalSalesCount', 'monthlyReceipts',
            'totalCustomers', 'activeCustomers', 'newCustomersThisMonth',
            'totalProducts',
            'cashBalance', 'receivables', 'payables',
            'chartData', 'alerts',
            'topProducts', 'topCustomers',
            'outstandingInvoices',
            'recentTransactions',
            'lowStockCount', 'outOfStockCount',
            'todaySales', 'todaySalesCount',
            'revenueBreakdown', 'dailyRevenue'
        );
    }

    // ─── Chart: 6-Month Revenue vs Expenses ───────────────────

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

            $exp = (float) Voucher::where('tenant_id', $tid)
                ->whereIn('voucher_type_id', $expenseTypeIds->merge($purchaseTypeIds))
                ->posted()
                ->whereMonth('voucher_date', $month->month)
                ->whereYear('voucher_date', $month->year)
                ->sum('total_amount');
            $expenses[] = $exp;
        }

        return compact('labels', 'revenue', 'expenses');
    }

    // ─── Daily Revenue (sparkline) ────────────────────────────

    private function getDailyRevenue($tid, $salesTypeIds, Carbon $now, int $days): array
    {
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $data[] = [
                'date' => $date->format('M d'),
                'amount' => (float) Voucher::where('tenant_id', $tid)
                    ->whereIn('voucher_type_id', $salesTypeIds)
                    ->posted()
                    ->whereDate('voucher_date', $date->toDateString())
                    ->sum('total_amount'),
            ];
        }
        return $data;
    }

    // ─── Revenue breakdown by type (doughnut) ─────────────────

    private function getRevenueBreakdown($tid, Carbon $now): array
    {
        return Voucher::where('vouchers.tenant_id', $tid)
            ->posted()
            ->thisMonth()
            ->join('voucher_types', 'voucher_types.id', '=', 'vouchers.voucher_type_id')
            ->whereIn('voucher_types.code', ['SV', 'RV', 'MI'])
            ->groupBy('voucher_types.name', 'voucher_types.code')
            ->selectRaw('voucher_types.name, voucher_types.code, SUM(vouchers.total_amount) as total')
            ->get()
            ->map(fn($r) => ['name' => $r->name, 'code' => $r->code, 'total' => (float)$r->total])
            ->toArray();
    }

    // ─── Outstanding Invoices ─────────────────────────────────

    private function getOutstandingInvoices($tid, $salesTypeIds): array
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
            'total' => $invoices->sum('total_amount'),
            'items' => $invoices->map(fn($v) => [
                'number' => $v->getDisplayNumber(),
                'amount' => $v->total_amount,
                'date' => $v->voucher_date,
            ])->toArray(),
        ];
    }

    // ─── Top Selling Products ─────────────────────────────────

    private function getTopProducts($tid, $salesTypeIds, int $limit): array
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
                'name' => $p->name,
                'sales' => (int) $p->total_qty,
                'revenue' => (float) $p->total_revenue,
            ])->toArray();
    }

    // ─── Alerts ───────────────────────────────────────────────

    private function getAlerts(Tenant $tenant, string $category, TerminologyService $term): array
    {
        $tid = $tenant->id;
        $alerts = [];

        // Stock alerts (only for non-service businesses with inventory module)
        if (!$term->isHidden('low_stock') && ModuleRegistry::isModuleEnabled($tenant, 'inventory')) {
            $lowStock = Product::where('tenant_id', $tid)
                ->where('type', 'item')->lowStock()->count();
            if ($lowStock > 0) {
                $alerts[] = [
                    'type' => 'low_stock', 'color' => 'yellow',
                    'title' => $term->label('low_stock') ?: 'Low Stock Alert',
                    'message' => "{$lowStock} product(s) are running low on inventory",
                    'icon' => 'exclamation',
                ];
            }

            $outOfStock = Product::where('tenant_id', $tid)
                ->where('type', 'item')->outOfStock()->count();
            if ($outOfStock > 0) {
                $alerts[] = [
                    'type' => 'out_of_stock', 'color' => 'red',
                    'title' => 'Out of Stock',
                    'message' => "{$outOfStock} product(s) are completely out of stock",
                    'icon' => 'x-circle',
                ];
            }
        }

        // Receivables alert
        $receivable = LedgerAccount::where('tenant_id', $tid)
            ->where('code', 'AR-001')->value('current_balance') ?? 0;
        if ($receivable > 0) {
            $alerts[] = [
                'type' => 'receivables', 'color' => 'blue',
                'title' => 'Outstanding Receivables',
                'message' => '₦' . number_format($receivable, 2) . ' in outstanding receivables',
                'icon' => 'cash',
            ];
        }

        return $alerts;
    }

    // ─── Module Flags ─────────────────────────────────────────

    private function getEnabledModuleFlags(Tenant $tenant): array
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
