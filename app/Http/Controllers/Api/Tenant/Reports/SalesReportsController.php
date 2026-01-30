<?php

namespace App\Http\Controllers\Api\Tenant\Reports;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\InvoiceItem;
use App\Models\LedgerAccount;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\Voucher;
use App\Models\VoucherType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesReportsController extends Controller
{
    /**
     * Sales summary report.
     */
    public function summary(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfYear()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());
        $groupBy = $request->get('group_by', 'month');

        $salesVoucherTypes = $this->getSalesVoucherTypes($tenant);

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

        $topProducts = InvoiceItem::whereHas('voucher', function ($query) use ($tenant, $salesVoucherTypes, $fromDate, $toDate) {
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

        $topCustomers = Voucher::where('vouchers.tenant_id', $tenant->id)
            ->whereIn('vouchers.voucher_type_id', $salesVoucherTypes)
            ->where('vouchers.status', 'posted')
            ->whereBetween('vouchers.voucher_date', [$fromDate, $toDate])
            ->join('voucher_entries', function ($join) {
                $join->on('voucher_entries.voucher_id', '=', 'vouchers.id')
                    ->where('voucher_entries.debit_amount', '>', 0);
            })
            ->join('ledger_accounts', 'ledger_accounts.id', '=', 'voucher_entries.ledger_account_id')
            ->select('ledger_accounts.id', 'ledger_accounts.name', DB::raw('SUM(vouchers.total_amount) as total_sales'), DB::raw('COUNT(DISTINCT vouchers.id) as invoice_count'))
            ->groupBy('ledger_accounts.id', 'ledger_accounts.name')
            ->orderByDesc('total_sales')
            ->limit(10)
            ->get();

        $salesTrend = $this->getSalesTrend($tenant, $salesVoucherTypes, $fromDate, $toDate, $groupBy);
        $paymentStatus = $this->getPaymentStatus($tenant, $salesVoucherTypes, $fromDate, $toDate);
        $previousPeriod = $this->getPreviousPeriodComparison($tenant, $salesVoucherTypes, $fromDate, $toDate);

        return response()->json([
            'success' => true,
            'message' => 'Sales summary retrieved successfully',
            'data' => [
                'filters' => [
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'group_by' => $groupBy,
                ],
                'summary' => [
                    'total_sales' => (float) $totalSales,
                    'sales_count' => $salesCount,
                    'average_sale_value' => (float) $averageSaleValue,
                ],
                'top_products' => $topProducts,
                'top_customers' => $topCustomers,
                'trend' => $salesTrend,
                'payment_status' => $paymentStatus,
                'previous_period' => $previousPeriod,
            ],
        ]);
    }

    /**
     * Customer sales report.
     */
    public function customerSales(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());
        $customerId = $request->get('customer_id');
        $sortBy = $request->get('sort_by', 'total_sales');
        $sortOrder = $request->get('sort_order', 'desc');

        $salesVoucherTypes = $this->getSalesVoucherTypes($tenant);

        $query = Voucher::where('vouchers.tenant_id', $tenant->id)
            ->whereIn('vouchers.voucher_type_id', $salesVoucherTypes)
            ->where('vouchers.status', 'posted')
            ->whereBetween('vouchers.voucher_date', [$fromDate, $toDate])
            ->join('voucher_entries', function ($join) {
                $join->on('voucher_entries.voucher_id', '=', 'vouchers.id')
                    ->where('voucher_entries.type', 'credit');
            })
            ->join('ledger_accounts', 'ledger_accounts.id', '=', 'voucher_entries.ledger_account_id');

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
                DB::raw('SUM(vouchers.total_amount) as total_sales'),
                DB::raw('COUNT(DISTINCT vouchers.id) as invoice_count'),
                DB::raw('MIN(vouchers.voucher_date) as first_sale_date'),
                DB::raw('MAX(vouchers.voucher_date) as last_sale_date')
            )
            ->groupBy('ledger_accounts.id', 'ledger_accounts.name', 'ledger_accounts.email', 'ledger_accounts.phone', 'ledger_accounts.current_balance')
            ->orderBy($sortBy, $sortOrder)
            ->paginate(20);

        $totalRevenue = Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $salesVoucherTypes)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$fromDate, $toDate])
            ->sum('total_amount');

        $customers = Customer::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->with('ledgerAccount')
            ->orderBy('company_name')
            ->orderBy('first_name')
            ->get();

        $customerLedgerIds = $customers->pluck('ledgerAccount.id')->filter();
        $totalOutstanding = LedgerAccount::whereIn('id', $customerLedgerIds)->sum('current_balance');

        return response()->json([
            'success' => true,
            'message' => 'Customer sales retrieved successfully',
            'data' => [
                'filters' => [
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'customer_id' => $customerId,
                    'sort_by' => $sortBy,
                    'sort_order' => $sortOrder,
                ],
                'summary' => [
                    'total_customers' => $customerSales->total(),
                    'total_revenue' => (float) $totalRevenue,
                    'total_outstanding' => (float) $totalOutstanding,
                ],
                'records' => $customerSales,
            ],
        ]);
    }

    /**
     * Product sales report.
     */
    public function productSales(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());
        $productId = $request->get('product_id');
        $categoryId = $request->get('category_id');
        $sortBy = $request->get('sort_by', 'total_revenue');
        $sortOrder = $request->get('sort_order', 'desc');

        $salesVoucherTypes = $this->getSalesVoucherTypes($tenant);

        $query = InvoiceItem::whereHas('voucher', function ($voucherQuery) use ($tenant, $salesVoucherTypes, $fromDate, $toDate) {
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
                DB::raw('AVG(invoice_items.unit_price) as avg_selling_price'),
                DB::raw('SUM(invoice_items.cost_price * invoice_items.quantity) as total_cost'),
                DB::raw('COUNT(DISTINCT invoice_items.voucher_id) as invoice_count')
            )
            ->groupBy('products.id', 'products.name', 'products.sku', 'product_categories.name')
            ->orderBy($sortBy, $sortOrder)
            ->paginate(20);

        $productSales->getCollection()->transform(function ($item) {
            $totalRevenue = (float) $item->total_revenue;
            $totalCost = (float) $item->total_cost;
            $item->gross_profit = $totalRevenue - $totalCost;
            $item->profit_margin = $totalRevenue > 0 ? ($item->gross_profit / $totalRevenue) * 100 : 0;
            return $item;
        });

        $baseQuery = InvoiceItem::whereHas('voucher', function ($voucherQuery) use ($tenant, $salesVoucherTypes, $fromDate, $toDate) {
                $voucherQuery->where('tenant_id', $tenant->id)
                    ->whereIn('voucher_type_id', $salesVoucherTypes)
                    ->where('status', 'posted')
                    ->whereBetween('voucher_date', [$fromDate, $toDate]);
            });

        if ($productId) {
            $baseQuery->where('invoice_items.product_id', $productId);
        }

        if ($categoryId) {
            $baseQuery->whereHas('product', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        $totalRevenue = (clone $baseQuery)->sum('invoice_items.amount');
        $totalCost = (clone $baseQuery)
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->sum(DB::raw('products.cost_price * invoice_items.quantity'));

        $totalProfit = $totalRevenue - $totalCost;

        return response()->json([
            'success' => true,
            'message' => 'Product sales retrieved successfully',
            'data' => [
                'filters' => [
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'product_id' => $productId,
                    'category_id' => $categoryId,
                    'sort_by' => $sortBy,
                    'sort_order' => $sortOrder,
                ],
                'summary' => [
                    'total_products' => $productSales->total(),
                    'total_revenue' => (float) $totalRevenue,
                    'total_profit' => (float) $totalProfit,
                ],
                'records' => $productSales,
            ],
        ]);
    }

    /**
     * Sales by period report.
     */
    public function byPeriod(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());
        $periodType = $request->get('period_type', 'daily');
        $compareWith = $request->get('compare_with');

        $salesVoucherTypes = $this->getSalesVoucherTypes($tenant);

        $periodSales = $this->getPeriodSales($tenant, $salesVoucherTypes, $fromDate, $toDate, $periodType);
        $comparisonData = null;
        if ($compareWith) {
            $comparisonData = $this->getComparisonPeriodSales($tenant, $salesVoucherTypes, $fromDate, $toDate, $periodType, $compareWith);
        }

        if ($comparisonData) {
            foreach ($periodSales as $index => $period) {
                $comparison = $comparisonData[$index] ?? null;
                $periodSales[$index]['growth_rate'] = $comparison && $comparison['total_sales'] > 0
                    ? (($period['total_sales'] - $comparison['total_sales']) / $comparison['total_sales']) * 100
                    : null;
            }
        }

        $totalSales = array_sum(array_column($periodSales, 'total_sales'));
        $totalInvoices = array_sum(array_column($periodSales, 'invoice_count'));
        $averagePerPeriod = count($periodSales) > 0 ? $totalSales / count($periodSales) : 0;

        $bestPeriod = collect($periodSales)->sortByDesc('total_sales')->first();
        $worstPeriod = collect($periodSales)->sortBy('total_sales')->first();

        return response()->json([
            'success' => true,
            'message' => 'Sales by period retrieved successfully',
            'data' => [
                'filters' => [
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'period_type' => $periodType,
                    'compare_with' => $compareWith,
                ],
                'summary' => [
                    'total_sales' => (float) $totalSales,
                    'total_invoices' => (int) $totalInvoices,
                    'average_per_period' => (float) $averagePerPeriod,
                    'best_period' => $bestPeriod,
                    'worst_period' => $worstPeriod,
                ],
                'records' => $periodSales,
            ],
        ]);
    }

    private function getSalesVoucherTypes(Tenant $tenant)
    {
        return VoucherType::where('tenant_id', $tenant->id)
            ->where('affects_inventory', true)
            ->where('inventory_effect', 'decrease')
            ->pluck('id');
    }

    private function getSalesTrend(Tenant $tenant, $salesVoucherTypes, $fromDate, $toDate, $groupBy)
    {
        if ($groupBy === 'month') {
            $year = Carbon::parse($fromDate)->year;
            $trend = collect();
            for ($i = 1; $i <= 12; $i++) {
                $trend->push([
                    'period' => Carbon::create($year, $i, 1)->format('Y-m'),
                    'label' => Carbon::create($year, $i, 1)->format('M'),
                    'total_sales' => 0,
                    'invoice_count' => 0,
                ]);
            }

            $results = Voucher::where('tenant_id', $tenant->id)
                ->whereIn('voucher_type_id', $salesVoucherTypes)
                ->where('status', 'posted')
                ->whereYear('voucher_date', $year)
                ->select(DB::raw('MONTH(voucher_date) as month'), DB::raw('SUM(total_amount) as total_sales'), DB::raw('COUNT(*) as invoice_count'))
                ->groupBy(DB::raw('MONTH(voucher_date)'))
                ->get();

            $trend = $trend->map(function ($item) use ($results) {
                $match = $results->firstWhere('month', (int) substr($item['period'], 5, 2));
                if ($match) {
                    $item['total_sales'] = (float) $match->total_sales;
                    $item['invoice_count'] = (int) $match->invoice_count;
                }
                return $item;
            });

            return $trend->values();
        }

        $dateFormat = match ($groupBy) {
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            default => '%Y-%m',
        };

        return Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $salesVoucherTypes)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$fromDate, $toDate])
            ->select(DB::raw("DATE_FORMAT(voucher_date, '{$dateFormat}') as period"), DB::raw('SUM(total_amount) as total_sales'), DB::raw('COUNT(*) as invoice_count'))
            ->groupBy(DB::raw("DATE_FORMAT(voucher_date, '{$dateFormat}')"))
            ->orderBy('period')
            ->get();
    }

    private function getPaymentStatus(Tenant $tenant, $salesVoucherTypes, $fromDate, $toDate)
    {
        $totalSales = Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $salesVoucherTypes)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$fromDate, $toDate])
            ->sum('total_amount');

        $received = 0;
        $outstanding = (float) $totalSales;

        return [
            'total_sales' => (float) $totalSales,
            'received' => (float) $received,
            'outstanding' => (float) $outstanding,
        ];
    }

    private function getPreviousPeriodComparison(Tenant $tenant, $salesVoucherTypes, $fromDate, $toDate)
    {
        $from = Carbon::parse($fromDate);
        $to = Carbon::parse($toDate);
        $daysDiff = $from->diffInDays($to) + 1;

        $previousFrom = $from->copy()->subDays($daysDiff);
        $previousTo = $to->copy()->subDays($daysDiff);

        $previousSales = Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $salesVoucherTypes)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$previousFrom, $previousTo])
            ->sum('total_amount');

        $currentSales = Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $salesVoucherTypes)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$fromDate, $toDate])
            ->sum('total_amount');

        $growthRate = $previousSales > 0 ? (($currentSales - $previousSales) / $previousSales) * 100 : 0;

        return [
            'previous_from' => $previousFrom->toDateString(),
            'previous_to' => $previousTo->toDateString(),
            'previous_sales' => (float) $previousSales,
            'current_sales' => (float) $currentSales,
            'growth_rate' => (float) $growthRate,
        ];
    }

    private function getPeriodSales(Tenant $tenant, $salesVoucherTypes, $fromDate, $toDate, $periodType)
    {
        $dateFormat = match ($periodType) {
            'weekly' => '%x-%v',
            'monthly' => '%Y-%m',
            'quarterly' => '%Y-Q%q',
            'yearly' => '%Y',
            default => '%Y-%m-%d',
        };

        $selectFormat = $periodType === 'quarterly'
            ? DB::raw("CONCAT(YEAR(voucher_date), '-Q', QUARTER(voucher_date))")
            : DB::raw("DATE_FORMAT(voucher_date, '{$dateFormat}')");

        return Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $salesVoucherTypes)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$fromDate, $toDate])
            ->select($selectFormat . ' as period', DB::raw('MIN(voucher_date) as start_date'), DB::raw('MAX(voucher_date) as end_date'), DB::raw('SUM(total_amount) as total_sales'), DB::raw('COUNT(*) as invoice_count'))
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->map(function ($row) {
                return [
                    'period' => $row->period,
                    'start_date' => Carbon::parse($row->start_date)->toDateString(),
                    'end_date' => Carbon::parse($row->end_date)->toDateString(),
                    'total_sales' => (float) $row->total_sales,
                    'invoice_count' => (int) $row->invoice_count,
                    'avg_sale' => $row->invoice_count > 0 ? (float) $row->total_sales / $row->invoice_count : 0,
                ];
            })
            ->values()
            ->all();
    }

    private function getComparisonPeriodSales(Tenant $tenant, $salesVoucherTypes, $fromDate, $toDate, $periodType, $compareWith)
    {
        $from = Carbon::parse($fromDate);
        $to = Carbon::parse($toDate);

        if ($compareWith === 'previous_period') {
            $daysDiff = $from->diffInDays($to) + 1;
            $comparisonFrom = $from->copy()->subDays($daysDiff);
            $comparisonTo = $to->copy()->subDays($daysDiff);
        } else {
            $comparisonFrom = $from->copy()->subYear();
            $comparisonTo = $to->copy()->subYear();
        }

        return $this->getPeriodSales($tenant, $salesVoucherTypes, $comparisonFrom->toDateString(), $comparisonTo->toDateString(), $periodType);
    }
}
