<?php

namespace App\Http\Controllers\Tenant\Reports;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Voucher;
use App\Models\VoucherType;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\VoucherEntry;
use App\Models\LedgerAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesReportsController extends Controller
{
    /**
     * Sales Summary Report
     * Overview of total sales, revenue, and key metrics
     */
    public function salesSummary(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfYear()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());
        $groupBy = $request->get('group_by', 'month'); // day, week, month

        // Get sales voucher types
        $salesVoucherTypes = VoucherType::where('tenant_id', $tenant->id)
            ->where('affects_inventory', true)
            ->where('inventory_effect', 'decrease')
            ->pluck('id');

        // Total Sales Metrics
        $totalSales = Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $salesVoucherTypes)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$fromDate, $toDate])
            ->sum('total_amount');

        $salesCount = Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $salesVoucherTypes)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$fromDate, $toDate])
            ->count();

        $averageSaleValue = $salesCount > 0 ? $totalSales / $salesCount : 0;

        // Get top selling products
        $topProducts = InvoiceItem::whereHas('voucher', function($query) use ($tenant, $salesVoucherTypes, $fromDate, $toDate) {
                $query->where('tenant_id', $tenant->id)
                    ->whereIn('voucher_type_id', $salesVoucherTypes)
                    ->where('status', 'posted')
                    ->whereBetween('voucher_date', [$fromDate, $toDate]);
            })
            ->select('product_id', 'product_name', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(amount) as total_amount'))
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_amount')
            ->limit(10)
            ->get();

        // Get top customers
        $topCustomers = Voucher::where('vouchers.tenant_id', $tenant->id)
            ->whereIn('vouchers.voucher_type_id', $salesVoucherTypes)
            ->where('vouchers.status', 'posted')
            ->whereBetween('vouchers.voucher_date', [$fromDate, $toDate])
            ->join('voucher_entries', function($join) {
                $join->on('vouchers.id', '=', 'voucher_entries.voucher_id')
                    ->where('voucher_entries.debit_amount', '>', 0);
            })
            ->join('ledger_accounts', function($join) use ($tenant) {
                $join->on('voucher_entries.ledger_account_id', '=', 'ledger_accounts.id')
                    ->where('ledger_accounts.tenant_id', '=', $tenant->id);
            })
            ->select('ledger_accounts.id', 'ledger_accounts.name', DB::raw('SUM(vouchers.total_amount) as total_sales'), DB::raw('COUNT(DISTINCT vouchers.id) as invoice_count'))
            ->groupBy('ledger_accounts.id', 'ledger_accounts.name')
            ->orderByDesc('total_sales')
            ->limit(10)
            ->get();

        // Sales trend data
        $salesTrend = $this->getSalesTrend($tenant, $salesVoucherTypes, $fromDate, $toDate, $groupBy);

        // Payment status breakdown
        $paymentStatus = $this->getPaymentStatus($tenant, $salesVoucherTypes, $fromDate, $toDate);

        // Compare with previous period
        $previousPeriod = $this->getPreviousPeriodComparison($tenant, $salesVoucherTypes, $fromDate, $toDate);

        return view('tenant.reports.sales.summary', compact(
            'tenant',
            'fromDate',
            'toDate',
            'totalSales',
            'salesCount',
            'averageSaleValue',
            'topProducts',
            'topCustomers',
            'salesTrend',
            'paymentStatus',
            'previousPeriod',
            'groupBy'
        ));
    }

    /**
     * Customer Sales Report
     * Detailed sales analysis by customer
     */
    public function customerSales(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());
        $customerId = $request->get('customer_id');
        $sortBy = $request->get('sort_by', 'total_sales'); // total_sales, invoice_count, avg_sale
        $sortOrder = $request->get('sort_order', 'desc');

        // Get sales voucher types
        $salesVoucherTypes = VoucherType::where('tenant_id', $tenant->id)
            ->where('affects_inventory', true)
            ->where('inventory_effect', 'decrease')
            ->pluck('id');

        // Build query for customer sales
        $query = Voucher::where('vouchers.tenant_id', $tenant->id)
            ->whereIn('vouchers.voucher_type_id', $salesVoucherTypes)
            ->where('vouchers.status', 'posted')
            ->whereBetween('vouchers.voucher_date', [$fromDate, $toDate])
            ->join('voucher_entries', function($join) {
                $join->on('vouchers.id', '=', 'voucher_entries.voucher_id')
                    ->where('voucher_entries.debit_amount', '>', 0);
            })
            ->join('ledger_accounts', function($join) use ($tenant) {
                $join->on('voucher_entries.ledger_account_id', '=', 'ledger_accounts.id')
                    ->where('ledger_accounts.tenant_id', '=', $tenant->id);
            });

        if ($customerId) {
            $query->where('ledger_accounts.id', $customerId);
        }

        $customerSales = $query
            ->select(
                'ledger_accounts.id as customer_id',
                'ledger_accounts.name as customer_name',
                'ledger_accounts.email',
                'ledger_accounts.phone',
                'ledger_accounts.current_balance as outstanding_balance',
                DB::raw('COUNT(DISTINCT vouchers.id) as invoice_count'),
                DB::raw('SUM(vouchers.total_amount) as total_sales'),
                DB::raw('MIN(vouchers.voucher_date) as first_sale_date'),
                DB::raw('MAX(vouchers.voucher_date) as last_sale_date')
            )
            ->groupBy('ledger_accounts.id', 'ledger_accounts.name', 'ledger_accounts.email', 'ledger_accounts.phone', 'ledger_accounts.current_balance')
            ->orderBy($sortBy, $sortOrder)
            ->paginate(20);

        // Get all customers for filter
        $customers = Customer::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->with('ledgerAccount')
            ->orderBy('company_name')
            ->orderBy('first_name')
            ->get();

        // Summary stats
        $totalCustomers = $customerSales->total();
        $totalRevenue = Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $salesVoucherTypes)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$fromDate, $toDate])
            ->sum('total_amount');

        // Calculate total outstanding balance from customer ledger accounts
        $customerLedgerIds = $customers->pluck('ledgerAccount.id')->filter();
        $totalOutstanding = LedgerAccount::whereIn('id', $customerLedgerIds)
            ->sum('current_balance');

        return view('tenant.reports.sales.customer-sales', compact(
            'tenant',
            'fromDate',
            'toDate',
            'customerSales',
            'customers',
            'customerId',
            'sortBy',
            'sortOrder',
            'totalCustomers',
            'totalRevenue',
            'totalOutstanding'
        ));
    }

    /**
     * Product Sales Report
     * Detailed sales analysis by product
     */
    public function productSales(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());
        $productId = $request->get('product_id');
        $categoryId = $request->get('category_id');
        $sortBy = $request->get('sort_by', 'total_revenue'); // total_revenue, quantity_sold, profit
        $sortOrder = $request->get('sort_order', 'desc');

        // Get sales voucher types
        $salesVoucherTypes = VoucherType::where('tenant_id', $tenant->id)
            ->where('affects_inventory', true)
            ->where('inventory_effect', 'decrease')
            ->pluck('id');

        // Build query for product sales
        $query = InvoiceItem::whereHas('voucher', function($voucherQuery) use ($tenant, $salesVoucherTypes, $fromDate, $toDate) {
                $voucherQuery->where('tenant_id', $tenant->id)
                    ->whereIn('voucher_type_id', $salesVoucherTypes)
                    ->where('status', 'posted')
                    ->whereBetween('voucher_date', [$fromDate, $toDate]);
            })
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->leftJoin('product_categories', 'products.category_id', '=', 'product_categories.id');

        if ($productId) {
            $query->where('products.id', $productId);
        }

        if ($categoryId) {
            $query->where('products.category_id', $categoryId);
        }

        $productSales = $query
            ->select(
                'products.id as product_id',
                'products.name as product_name',
                'products.sku',
                'product_categories.name as category_name',
                DB::raw('SUM(invoice_items.quantity) as quantity_sold'),
                DB::raw('SUM(invoice_items.amount) as total_revenue'),
                DB::raw('AVG(invoice_items.rate) as avg_selling_price'),
                DB::raw('COUNT(DISTINCT invoice_items.voucher_id) as invoice_count'),
                DB::raw('SUM(invoice_items.quantity * COALESCE(invoice_items.purchase_rate, products.purchase_rate, 0)) as total_cost'),
                DB::raw('SUM(invoice_items.amount) - SUM(invoice_items.quantity * COALESCE(invoice_items.purchase_rate, products.purchase_rate, 0)) as gross_profit')
            )
            ->groupBy('products.id', 'products.name', 'products.sku', 'product_categories.name')
            ->orderBy($sortBy, $sortOrder)
            ->paginate(20);

        // Calculate profit margin for each product
        foreach ($productSales as $product) {
            $product->profit_margin = $product->total_revenue > 0
                ? ($product->gross_profit / $product->total_revenue) * 100
                : 0;
        }

        // Get all products for filter
        $products = Product::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get categories for filter
        $categories = DB::table('product_categories')
            ->where('tenant_id', $tenant->id)
            ->orderBy('name')
            ->get();

        // Summary stats
        $totalProducts = $productSales->total();

        // Calculate total revenue and total profit based on filters
        $baseQuery = InvoiceItem::whereHas('voucher', function($voucherQuery) use ($tenant, $salesVoucherTypes, $fromDate, $toDate) {
                $voucherQuery->where('tenant_id', $tenant->id)
                    ->whereIn('voucher_type_id', $salesVoucherTypes)
                    ->where('status', 'posted')
                    ->whereBetween('voucher_date', [$fromDate, $toDate]);
            })
            ->join('products', 'invoice_items.product_id', '=', 'products.id');

        if ($productId) {
            $baseQuery->where('products.id', $productId);
        }

        if ($categoryId) {
            $baseQuery->where('products.category_id', $categoryId);
        }

        $totalRevenue = (clone $baseQuery)->sum('invoice_items.amount');

        $totalCost = (clone $baseQuery)
            ->selectRaw('SUM(invoice_items.quantity * COALESCE(invoice_items.purchase_rate, products.purchase_rate, 0)) as total_cost')
            ->value('total_cost') ?? 0;

        $totalProfit = $totalRevenue - $totalCost;

        return view('tenant.reports.sales.product-sales', compact(
            'tenant',
            'fromDate',
            'toDate',
            'productSales',
            'products',
            'categories',
            'productId',
            'categoryId',
            'sortBy',
            'sortOrder',
            'totalProducts',
            'totalRevenue',
            'totalProfit'
        ));
    }

    /**
     * Sales by Period Report
     * Time-based sales analysis with trends
     */
    public function salesByPeriod(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());
        $periodType = $request->get('period_type', 'daily'); // daily, weekly, monthly, quarterly, yearly
        $compareWith = $request->get('compare_with'); // previous_period, previous_year

        // Get sales voucher types
        $salesVoucherTypes = VoucherType::where('tenant_id', $tenant->id)
            ->where('affects_inventory', true)
            ->where('inventory_effect', 'decrease')
            ->pluck('id');

        // Get period-based sales data
        $periodSales = $this->getPeriodSales($tenant, $salesVoucherTypes, $fromDate, $toDate, $periodType);

        // Get comparison data if requested
        $comparisonData = null;
        if ($compareWith) {
            $comparisonData = $this->getComparisonPeriodSales($tenant, $salesVoucherTypes, $fromDate, $toDate, $periodType, $compareWith);
        }

        // Calculate growth rates
        foreach ($periodSales as $index => $period) {
            if ($comparisonData && isset($comparisonData[$index])) {
                $previousAmount = $comparisonData[$index]['total_sales'];
                if ($previousAmount > 0) {
                    $period['growth_rate'] = (($period['total_sales'] - $previousAmount) / $previousAmount) * 100;
                } else {
                    $period['growth_rate'] = $period['total_sales'] > 0 ? 100 : 0;
                }
            } else {
                $period['growth_rate'] = null;
            }
        }

        // Summary statistics
        $totalSales = array_sum(array_column($periodSales, 'total_sales'));
        $totalInvoices = array_sum(array_column($periodSales, 'invoice_count'));
        $averagePerPeriod = count($periodSales) > 0 ? $totalSales / count($periodSales) : 0;

        // Find best and worst performing periods
        $bestPeriod = collect($periodSales)->sortByDesc('total_sales')->first();
        $worstPeriod = collect($periodSales)->sortBy('total_sales')->first();

        return view('tenant.reports.sales.by-period', compact(
            'tenant',
            'fromDate',
            'toDate',
            'periodType',
            'compareWith',
            'periodSales',
            'comparisonData',
            'totalSales',
            'totalInvoices',
            'averagePerPeriod',
            'bestPeriod',
            'worstPeriod'
        ));
    }

    // Helper Methods

    private function getSalesTrend($tenant, $salesVoucherTypes, $fromDate, $toDate, $groupBy)
    {
        // For monthly grouping, always show full year (12 months)
        if ($groupBy === 'month') {
            $currentYear = now()->year;

            // Get actual sales data
            $salesData = Voucher::where('tenant_id', $tenant->id)
                ->whereIn('voucher_type_id', $salesVoucherTypes)
                ->where('status', 'posted')
                ->whereYear('voucher_date', $currentYear)
                ->select(
                    DB::raw("DATE_FORMAT(voucher_date, '%Y-%m') as period"),
                    DB::raw('SUM(total_amount) as total_sales'),
                    DB::raw('COUNT(*) as invoice_count')
                )
                ->groupBy('period')
                ->orderBy('period')
                ->get()
                ->keyBy('period');

            // Create full 12 months array
            $fullYearData = collect();
            for ($month = 1; $month <= 12; $month++) {
                $periodKey = sprintf('%d-%02d', $currentYear, $month);
                $monthName = date('F', mktime(0, 0, 0, $month, 1));

                if (isset($salesData[$periodKey])) {
                    $fullYearData->push((object)[
                        'period' => $monthName,
                        'period_key' => $periodKey,
                        'total_sales' => $salesData[$periodKey]->total_sales,
                        'invoice_count' => $salesData[$periodKey]->invoice_count
                    ]);
                } else {
                    $fullYearData->push((object)[
                        'period' => $monthName,
                        'period_key' => $periodKey,
                        'total_sales' => 0,
                        'invoice_count' => 0
                    ]);
                }
            }

            return $fullYearData;
        }

        // For other groupings, use original logic
        $dateFormat = match($groupBy) {
            'week' => '%Y-%u',
            'year' => '%Y',
            default => '%Y-%m-%d'
        };

        return Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $salesVoucherTypes)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$fromDate, $toDate])
            ->select(
                DB::raw("DATE_FORMAT(voucher_date, '{$dateFormat}') as period"),
                DB::raw('SUM(total_amount) as total_sales'),
                DB::raw('COUNT(*) as invoice_count')
            )
            ->groupBy('period')
            ->orderBy('period')
            ->get();
    }

    private function getPaymentStatus($tenant, $salesVoucherTypes, $fromDate, $toDate)
    {
        // Get total sales
        $totalSales = Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $salesVoucherTypes)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$fromDate, $toDate])
            ->sum('total_amount') ?? 0;

        // Get total payments received (Receipt Vouchers linked to invoices)
        try {
            $totalPaid = \App\Models\VoucherEntry::whereHas('voucher', function($query) use ($tenant, $fromDate, $toDate) {
                    $query->where('tenant_id', $tenant->id)
                        ->whereHas('voucherType', function($q) {
                            $q->where('code', 'RV'); // Receipt Voucher
                        })
                        ->where('status', 'posted')
                        ->whereBetween('voucher_date', [$fromDate, $toDate]);
                })
                ->where('credit_amount', '>', 0)
                ->sum('credit_amount') ?? 0;
        } catch (\Exception $e) {
            $totalPaid = 0;
        }

        return [
            'total_sales' => $totalSales,
            'total_paid' => $totalPaid,
            'outstanding' => $totalSales - $totalPaid,
            'paid_percentage' => $totalSales > 0 ? ($totalPaid / $totalSales) * 100 : 0
        ];
    }

    private function getPreviousPeriodComparison($tenant, $salesVoucherTypes, $fromDate, $toDate)
    {
        $from = Carbon::parse($fromDate);
        $to = Carbon::parse($toDate);
        $daysDiff = $from->diffInDays($to) + 1;

        $previousFrom = $from->copy()->subDays($daysDiff);
        $previousTo = $to->copy()->subDays($daysDiff);

        $previousSales = Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $salesVoucherTypes)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$previousFrom->toDateString(), $previousTo->toDateString()])
            ->sum('total_amount');

        $currentSales = Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $salesVoucherTypes)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$fromDate, $toDate])
            ->sum('total_amount');

        $growthRate = $previousSales > 0 ? (($currentSales - $previousSales) / $previousSales) * 100 : 0;

        return [
            'previous_sales' => $previousSales,
            'current_sales' => $currentSales,
            'growth_rate' => $growthRate,
            'previous_from' => $previousFrom->toDateString(),
            'previous_to' => $previousTo->toDateString()
        ];
    }

    private function getPeriodSales($tenant, $salesVoucherTypes, $fromDate, $toDate, $periodType)
    {
        $dateFormat = match($periodType) {
            'weekly' => '%Y-%u',
            'monthly' => '%Y-%m',
            'quarterly' => 'CONCAT(YEAR(voucher_date), "-Q", QUARTER(voucher_date))',
            'yearly' => '%Y',
            default => '%Y-%m-%d'
        };

        $selectFormat = $periodType === 'quarterly'
            ? DB::raw("CONCAT(YEAR(voucher_date), '-Q', QUARTER(voucher_date)) as period")
            : DB::raw("DATE_FORMAT(voucher_date, '{$dateFormat}') as period");

        $results = Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $salesVoucherTypes)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$fromDate, $toDate])
            ->select(
                $selectFormat,
                DB::raw('SUM(total_amount) as total_sales'),
                DB::raw('COUNT(*) as invoice_count'),
                DB::raw('AVG(total_amount) as avg_sale'),
                DB::raw('MIN(voucher_date) as period_start'),
                DB::raw('MAX(voucher_date) as period_end')
            )
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->toArray();

        return $results;
    }

    private function getComparisonPeriodSales($tenant, $salesVoucherTypes, $fromDate, $toDate, $periodType, $compareWith)
    {
        $from = Carbon::parse($fromDate);
        $to = Carbon::parse($toDate);

        if ($compareWith === 'previous_period') {
            $daysDiff = $from->diffInDays($to) + 1;
            $comparisonFrom = $from->copy()->subDays($daysDiff);
            $comparisonTo = $to->copy()->subDays($daysDiff);
        } else { // previous_year
            $comparisonFrom = $from->copy()->subYear();
            $comparisonTo = $to->copy()->subYear();
        }

        return $this->getPeriodSales(
            $tenant,
            $salesVoucherTypes,
            $comparisonFrom->toDateString(),
            $comparisonTo->toDateString(),
            $periodType
        );
    }
}
