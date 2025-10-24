<?php

namespace App\Http\Controllers\Tenant\Reports;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Voucher;
use App\Models\VoucherType;
use App\Models\InvoiceItem;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\VoucherEntry;
use App\Models\LedgerAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchaseReportsController extends Controller
{
    /**
     * Purchase Summary Report
     * Overview of total purchases, costs, and key metrics
     */
    public function purchaseSummary(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfYear()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());
        $groupBy = $request->get('group_by', 'month'); // day, week, month

        // Get purchase voucher types
        $purchaseVoucherTypes = VoucherType::where('tenant_id', $tenant->id)
            ->where('affects_inventory', true)
            ->where('inventory_effect', 'increase')
            ->pluck('id');

        // Total Purchase Metrics
        $totalPurchases = Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $purchaseVoucherTypes)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$fromDate, $toDate])
            ->sum('total_amount');

        $purchaseCount = Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $purchaseVoucherTypes)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$fromDate, $toDate])
            ->count();

        $averagePurchaseValue = $purchaseCount > 0 ? $totalPurchases / $purchaseCount : 0;

        // Get top purchased products
        $topProducts = InvoiceItem::whereHas('voucher', function($query) use ($tenant, $purchaseVoucherTypes, $fromDate, $toDate) {
                $query->where('tenant_id', $tenant->id)
                    ->whereIn('voucher_type_id', $purchaseVoucherTypes)
                    ->where('status', 'posted')
                    ->whereBetween('voucher_date', [$fromDate, $toDate]);
            })
            ->select('product_id', 'product_name', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(amount) as total_amount'))
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_amount')
            ->limit(10)
            ->get();

        // Get top vendors
        $topVendors = Voucher::where('vouchers.tenant_id', $tenant->id)
            ->whereIn('vouchers.voucher_type_id', $purchaseVoucherTypes)
            ->where('vouchers.status', 'posted')
            ->whereBetween('vouchers.voucher_date', [$fromDate, $toDate])
            ->join('voucher_entries', function($join) {
                $join->on('vouchers.id', '=', 'voucher_entries.voucher_id')
                    ->where('voucher_entries.credit_amount', '>', 0);
            })
            ->join('ledger_accounts', function($join) use ($tenant) {
                $join->on('voucher_entries.ledger_account_id', '=', 'ledger_accounts.id')
                    ->where('ledger_accounts.tenant_id', '=', $tenant->id);
            })
            ->select('ledger_accounts.id', 'ledger_accounts.name', DB::raw('SUM(vouchers.total_amount) as total_purchases'), DB::raw('COUNT(DISTINCT vouchers.id) as purchase_count'))
            ->groupBy('ledger_accounts.id', 'ledger_accounts.name')
            ->orderByDesc('total_purchases')
            ->limit(10)
            ->get();

        // Purchase trend data
        $purchaseTrend = $this->getPurchaseTrend($tenant, $purchaseVoucherTypes, $fromDate, $toDate, $groupBy);

        // Payment status breakdown
        $paymentStatus = $this->getPaymentStatus($tenant, $purchaseVoucherTypes, $fromDate, $toDate);

        // Compare with previous period
        $previousPeriod = $this->getPreviousPeriodComparison($tenant, $purchaseVoucherTypes, $fromDate, $toDate);

        return view('tenant.reports.purchase.summary', compact(
            'tenant',
            'fromDate',
            'toDate',
            'totalPurchases',
            'purchaseCount',
            'averagePurchaseValue',
            'topProducts',
            'topVendors',
            'purchaseTrend',
            'paymentStatus',
            'previousPeriod',
            'groupBy'
        ));
    }

    /**
     * Vendor Purchase Report
     * Detailed purchase analysis by vendor
     */
    public function vendorPurchases(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());
        $vendorId = $request->get('vendor_id');
        $sortBy = $request->get('sort_by', 'total_purchases'); // total_purchases, purchase_count, avg_purchase
        $sortOrder = $request->get('sort_order', 'desc');

        // Get purchase voucher types
        $purchaseVoucherTypes = VoucherType::where('tenant_id', $tenant->id)
            ->where('affects_inventory', true)
            ->where('inventory_effect', 'increase')
            ->pluck('id');

        // Build query for vendor purchases
        $query = Voucher::where('vouchers.tenant_id', $tenant->id)
            ->whereIn('vouchers.voucher_type_id', $purchaseVoucherTypes)
            ->where('vouchers.status', 'posted')
            ->whereBetween('vouchers.voucher_date', [$fromDate, $toDate])
            ->join('voucher_entries', function($join) {
                $join->on('vouchers.id', '=', 'voucher_entries.voucher_id')
                    ->where('voucher_entries.credit_amount', '>', 0);
            })
            ->join('ledger_accounts', function($join) use ($tenant) {
                $join->on('voucher_entries.ledger_account_id', '=', 'ledger_accounts.id')
                    ->where('ledger_accounts.tenant_id', '=', $tenant->id);
            });

        if ($vendorId) {
            $query->where('ledger_accounts.id', $vendorId);
        }

        $vendorPurchases = $query
            ->select(
                'ledger_accounts.id as vendor_id',
                'ledger_accounts.name as vendor_name',
                'ledger_accounts.email',
                'ledger_accounts.phone',
                'ledger_accounts.current_balance as outstanding_balance',
                DB::raw('COUNT(DISTINCT vouchers.id) as purchase_count'),
                DB::raw('SUM(vouchers.total_amount) as total_purchases'),
                DB::raw('MIN(vouchers.voucher_date) as first_purchase_date'),
                DB::raw('MAX(vouchers.voucher_date) as last_purchase_date')
            )
            ->groupBy('ledger_accounts.id', 'ledger_accounts.name', 'ledger_accounts.email', 'ledger_accounts.phone', 'ledger_accounts.current_balance')
            ->orderBy($sortBy, $sortOrder)
            ->paginate(20);

        // Get all vendors for filter
        $vendors = Vendor::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->with('ledgerAccount')
            ->orderBy('company_name')
            ->orderBy('first_name')
            ->get();

        // Summary stats
        $totalVendors = $vendorPurchases->total();
        $totalExpense = Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $purchaseVoucherTypes)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$fromDate, $toDate])
            ->sum('total_amount');

        // Calculate total outstanding balance from vendor ledger accounts
        $vendorLedgerIds = $vendors->pluck('ledgerAccount.id')->filter();
        $totalOutstanding = LedgerAccount::whereIn('id', $vendorLedgerIds)
            ->sum('current_balance');

        return view('tenant.reports.purchase.vendor-purchases', compact(
            'tenant',
            'fromDate',
            'toDate',
            'vendorPurchases',
            'vendors',
            'vendorId',
            'sortBy',
            'sortOrder',
            'totalVendors',
            'totalExpense',
            'totalOutstanding'
        ));
    }

    /**
     * Product Purchase Report
     * Detailed purchase analysis by product
     */
    public function productPurchases(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());
        $productId = $request->get('product_id');
        $categoryId = $request->get('category_id');
        $sortBy = $request->get('sort_by', 'total_cost'); // total_cost, quantity_purchased
        $sortOrder = $request->get('sort_order', 'desc');

        // Get purchase voucher types
        $purchaseVoucherTypes = VoucherType::where('tenant_id', $tenant->id)
            ->where('affects_inventory', true)
            ->where('inventory_effect', 'increase')
            ->pluck('id');

        // Build query for product purchases
        $query = InvoiceItem::whereHas('voucher', function($voucherQuery) use ($tenant, $purchaseVoucherTypes, $fromDate, $toDate) {
                $voucherQuery->where('tenant_id', $tenant->id)
                    ->whereIn('voucher_type_id', $purchaseVoucherTypes)
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

        $productPurchases = $query
            ->select(
                'products.id as product_id',
                'products.name as product_name',
                'products.sku',
                'product_categories.name as category_name',
                DB::raw('SUM(invoice_items.quantity) as quantity_purchased'),
                DB::raw('SUM(invoice_items.amount) as total_cost'),
                DB::raw('AVG(invoice_items.rate) as avg_purchase_price'),
                DB::raw('COUNT(DISTINCT invoice_items.voucher_id) as purchase_count')
            )
            ->groupBy('products.id', 'products.name', 'products.sku', 'product_categories.name')
            ->orderBy($sortBy, $sortOrder)
            ->paginate(20);

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
        $totalProducts = $productPurchases->total();

        // Calculate total cost based on filters
        $baseQuery = InvoiceItem::whereHas('voucher', function($voucherQuery) use ($tenant, $purchaseVoucherTypes, $fromDate, $toDate) {
                $voucherQuery->where('tenant_id', $tenant->id)
                    ->whereIn('voucher_type_id', $purchaseVoucherTypes)
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

        $totalCost = (clone $baseQuery)->sum('invoice_items.amount');
        $totalQuantity = (clone $baseQuery)->sum('invoice_items.quantity');

        return view('tenant.reports.purchase.product-purchases', compact(
            'tenant',
            'fromDate',
            'toDate',
            'productPurchases',
            'products',
            'categories',
            'productId',
            'categoryId',
            'sortBy',
            'sortOrder',
            'totalProducts',
            'totalCost',
            'totalQuantity'
        ));
    }

    /**
     * Purchases by Period Report
     * Time-based purchase analysis with trends
     */
    public function purchasesByPeriod(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());
        $periodType = $request->get('period_type', 'daily'); // daily, weekly, monthly, quarterly, yearly
        $compareWith = $request->get('compare_with'); // previous_period, previous_year

        // Get purchase voucher types
        $purchaseVoucherTypes = VoucherType::where('tenant_id', $tenant->id)
            ->where('affects_inventory', true)
            ->where('inventory_effect', 'increase')
            ->pluck('id');

        // Get period-based purchase data
        $periodPurchases = $this->getPeriodPurchases($tenant, $purchaseVoucherTypes, $fromDate, $toDate, $periodType);

        // Get comparison data if requested
        $comparisonData = null;
        if ($compareWith) {
            $comparisonData = $this->getComparisonPeriodPurchases($tenant, $purchaseVoucherTypes, $fromDate, $toDate, $periodType, $compareWith);
        }

        // Calculate growth rates
        foreach ($periodPurchases as $index => $period) {
            if ($comparisonData && isset($comparisonData[$index])) {
                $previousAmount = $comparisonData[$index]['total_purchases'];
                if ($previousAmount > 0) {
                    $period['growth_rate'] = (($period['total_purchases'] - $previousAmount) / $previousAmount) * 100;
                } else {
                    $period['growth_rate'] = $period['total_purchases'] > 0 ? 100 : 0;
                }
            } else {
                $period['growth_rate'] = null;
            }
        }

        // Summary statistics
        $totalPurchases = array_sum(array_column($periodPurchases, 'total_purchases'));
        $totalOrders = array_sum(array_column($periodPurchases, 'purchase_count'));
        $averagePerPeriod = count($periodPurchases) > 0 ? $totalPurchases / count($periodPurchases) : 0;

        // Find best and worst performing periods
        $bestPeriod = collect($periodPurchases)->sortByDesc('total_purchases')->first();
        $worstPeriod = collect($periodPurchases)->sortBy('total_purchases')->first();

        return view('tenant.reports.purchase.by-period', compact(
            'tenant',
            'fromDate',
            'toDate',
            'periodType',
            'compareWith',
            'periodPurchases',
            'comparisonData',
            'totalPurchases',
            'totalOrders',
            'averagePerPeriod',
            'bestPeriod',
            'worstPeriod'
        ));
    }

    // Helper Methods

    private function getPurchaseTrend($tenant, $purchaseVoucherTypes, $fromDate, $toDate, $groupBy)
    {
        // For monthly grouping, always show full year (12 months)
        if ($groupBy === 'month') {
            $currentYear = now()->year;

            // Get actual purchase data
            $purchaseData = Voucher::where('tenant_id', $tenant->id)
                ->whereIn('voucher_type_id', $purchaseVoucherTypes)
                ->where('status', 'posted')
                ->whereYear('voucher_date', $currentYear)
                ->select(
                    DB::raw("DATE_FORMAT(voucher_date, '%Y-%m') as period"),
                    DB::raw('SUM(total_amount) as total_purchases'),
                    DB::raw('COUNT(*) as purchase_count')
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

                if (isset($purchaseData[$periodKey])) {
                    $fullYearData->push((object)[
                        'period' => $monthName,
                        'period_key' => $periodKey,
                        'total_purchases' => $purchaseData[$periodKey]->total_purchases,
                        'purchase_count' => $purchaseData[$periodKey]->purchase_count
                    ]);
                } else {
                    $fullYearData->push((object)[
                        'period' => $monthName,
                        'period_key' => $periodKey,
                        'total_purchases' => 0,
                        'purchase_count' => 0
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
            ->whereIn('voucher_type_id', $purchaseVoucherTypes)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$fromDate, $toDate])
            ->select(
                DB::raw("DATE_FORMAT(voucher_date, '{$dateFormat}') as period"),
                DB::raw('SUM(total_amount) as total_purchases'),
                DB::raw('COUNT(*) as purchase_count')
            )
            ->groupBy('period')
            ->orderBy('period')
            ->get();
    }

    private function getPaymentStatus($tenant, $purchaseVoucherTypes, $fromDate, $toDate)
    {
        // Get total purchases
        $totalPurchases = Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $purchaseVoucherTypes)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$fromDate, $toDate])
            ->sum('total_amount') ?? 0;

        // Get total payments made (Payment Vouchers linked to purchases)
        try {
            $totalPaid = \App\Models\VoucherEntry::whereHas('voucher', function($query) use ($tenant, $fromDate, $toDate) {
                    $query->where('tenant_id', $tenant->id)
                        ->whereHas('voucherType', function($q) {
                            $q->where('code', 'PV'); // Payment Voucher
                        })
                        ->where('status', 'posted')
                        ->whereBetween('voucher_date', [$fromDate, $toDate]);
                })
                ->where('debit_amount', '>', 0)
                ->sum('debit_amount') ?? 0;
        } catch (\Exception $e) {
            $totalPaid = 0;
        }

        return [
            'total_purchases' => $totalPurchases,
            'total_paid' => $totalPaid,
            'outstanding' => $totalPurchases - $totalPaid,
            'paid_percentage' => $totalPurchases > 0 ? ($totalPaid / $totalPurchases) * 100 : 0
        ];
    }

    private function getPreviousPeriodComparison($tenant, $purchaseVoucherTypes, $fromDate, $toDate)
    {
        $from = Carbon::parse($fromDate);
        $to = Carbon::parse($toDate);
        $daysDiff = $from->diffInDays($to) + 1;

        $previousFrom = $from->copy()->subDays($daysDiff);
        $previousTo = $to->copy()->subDays($daysDiff);

        $previousPurchases = Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $purchaseVoucherTypes)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$previousFrom->toDateString(), $previousTo->toDateString()])
            ->sum('total_amount');

        $currentPurchases = Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $purchaseVoucherTypes)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$fromDate, $toDate])
            ->sum('total_amount');

        $growthRate = $previousPurchases > 0 ? (($currentPurchases - $previousPurchases) / $previousPurchases) * 100 : 0;

        return [
            'previous_purchases' => $previousPurchases,
            'current_purchases' => $currentPurchases,
            'growth_rate' => $growthRate,
            'previous_from' => $previousFrom->toDateString(),
            'previous_to' => $previousTo->toDateString()
        ];
    }

    private function getPeriodPurchases($tenant, $purchaseVoucherTypes, $fromDate, $toDate, $periodType)
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
            ->whereIn('voucher_type_id', $purchaseVoucherTypes)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$fromDate, $toDate])
            ->select(
                $selectFormat,
                DB::raw('SUM(total_amount) as total_purchases'),
                DB::raw('COUNT(*) as purchase_count'),
                DB::raw('AVG(total_amount) as avg_purchase'),
                DB::raw('MIN(voucher_date) as period_start'),
                DB::raw('MAX(voucher_date) as period_end')
            )
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->toArray();

        return $results;
    }

    private function getComparisonPeriodPurchases($tenant, $purchaseVoucherTypes, $fromDate, $toDate, $periodType, $compareWith)
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

        return $this->getPeriodPurchases(
            $tenant,
            $purchaseVoucherTypes,
            $comparisonFrom->toDateString(),
            $comparisonTo->toDateString(),
            $periodType
        );
    }
}
