<?php

namespace App\Http\Controllers\Api\Tenant\Reports;

use App\Http\Controllers\Controller;
use App\Models\InvoiceItem;
use App\Models\LedgerAccount;
use App\Models\Tenant;
use App\Models\Vendor;
use App\Models\Voucher;
use App\Models\VoucherType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseReportsController extends Controller
{
    /**
     * Purchase summary report.
     */
    public function summary(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfYear()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());
        $groupBy = $request->get('group_by', 'month');

        $purchaseVoucherTypes = $this->getPurchaseVoucherTypes($tenant);

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

        $topProducts = InvoiceItem::whereHas('voucher', function ($query) use ($tenant, $purchaseVoucherTypes, $fromDate, $toDate) {
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

        $topVendors = Voucher::where('vouchers.tenant_id', $tenant->id)
            ->whereIn('vouchers.voucher_type_id', $purchaseVoucherTypes)
            ->where('vouchers.status', 'posted')
            ->whereBetween('vouchers.voucher_date', [$fromDate, $toDate])
            ->join('voucher_entries', function ($join) {
                $join->on('voucher_entries.voucher_id', '=', 'vouchers.id')
                    ->where('voucher_entries.debit_amount', '>', 0);
            })
            ->join('ledger_accounts', 'ledger_accounts.id', '=', 'voucher_entries.ledger_account_id')
            ->select('ledger_accounts.id', 'ledger_accounts.name', DB::raw('SUM(vouchers.total_amount) as total_purchases'), DB::raw('COUNT(DISTINCT vouchers.id) as purchase_count'))
            ->groupBy('ledger_accounts.id', 'ledger_accounts.name')
            ->orderByDesc('total_purchases')
            ->limit(10)
            ->get();

        $purchaseTrend = $this->getPurchaseTrend($tenant, $purchaseVoucherTypes, $fromDate, $toDate, $groupBy);
        $paymentStatus = $this->getPaymentStatus($tenant, $purchaseVoucherTypes, $fromDate, $toDate);
        $previousPeriod = $this->getPreviousPeriodComparison($tenant, $purchaseVoucherTypes, $fromDate, $toDate);

        return response()->json([
            'success' => true,
            'message' => 'Purchase summary retrieved successfully',
            'data' => [
                'filters' => [
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'group_by' => $groupBy,
                ],
                'summary' => [
                    'total_purchases' => (float) $totalPurchases,
                    'purchase_count' => $purchaseCount,
                    'average_purchase_value' => (float) $averagePurchaseValue,
                ],
                'top_products' => $topProducts,
                'top_vendors' => $topVendors,
                'trend' => $purchaseTrend,
                'payment_status' => $paymentStatus,
                'previous_period' => $previousPeriod,
            ],
        ]);
    }

    /**
     * Vendor purchases report.
     */
    public function vendorPurchases(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());
        $vendorId = $request->get('vendor_id');
        $sortBy = $request->get('sort_by', 'total_purchases');
        $sortOrder = $request->get('sort_order', 'desc');

        $purchaseVoucherTypes = $this->getPurchaseVoucherTypes($tenant);

        $query = Voucher::where('vouchers.tenant_id', $tenant->id)
            ->whereIn('vouchers.voucher_type_id', $purchaseVoucherTypes)
            ->where('vouchers.status', 'posted')
            ->whereBetween('vouchers.voucher_date', [$fromDate, $toDate])
            ->join('voucher_entries', function ($join) {
                $join->on('voucher_entries.voucher_id', '=', 'vouchers.id')
                    ->where('voucher_entries.debit_amount', '>', 0);
            })
            ->join('ledger_accounts', 'ledger_accounts.id', '=', 'voucher_entries.ledger_account_id');

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
                DB::raw('SUM(vouchers.total_amount) as total_purchases'),
                DB::raw('COUNT(DISTINCT vouchers.id) as purchase_count'),
                DB::raw('MIN(vouchers.voucher_date) as first_purchase_date'),
                DB::raw('MAX(vouchers.voucher_date) as last_purchase_date')
            )
            ->groupBy('ledger_accounts.id', 'ledger_accounts.name', 'ledger_accounts.email', 'ledger_accounts.phone', 'ledger_accounts.current_balance')
            ->orderBy($sortBy, $sortOrder)
            ->paginate(20);

        $totalExpense = Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $purchaseVoucherTypes)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$fromDate, $toDate])
            ->sum('total_amount');

        $vendors = Vendor::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->with('ledgerAccount')
            ->orderBy('company_name')
            ->orderBy('first_name')
            ->get();

        $vendorLedgerIds = $vendors->pluck('ledgerAccount.id')->filter();
        $totalOutstanding = LedgerAccount::whereIn('id', $vendorLedgerIds)->sum('current_balance');

        return response()->json([
            'success' => true,
            'message' => 'Vendor purchases retrieved successfully',
            'data' => [
                'filters' => [
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'vendor_id' => $vendorId,
                    'sort_by' => $sortBy,
                    'sort_order' => $sortOrder,
                ],
                'summary' => [
                    'total_vendors' => $vendorPurchases->total(),
                    'total_purchases' => (float) $totalExpense,
                    'total_outstanding' => (float) $totalOutstanding,
                ],
                'records' => $vendorPurchases,
            ],
        ]);
    }

    /**
     * Product purchases report.
     */
    public function productPurchases(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());
        $productId = $request->get('product_id');
        $categoryId = $request->get('category_id');
        $sortBy = $request->get('sort_by', 'total_cost');
        $sortOrder = $request->get('sort_order', 'desc');

        $purchaseVoucherTypes = $this->getPurchaseVoucherTypes($tenant);

        $query = InvoiceItem::whereHas('voucher', function ($voucherQuery) use ($tenant, $purchaseVoucherTypes, $fromDate, $toDate) {
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
                DB::raw('MIN(invoice_items.rate) as min_purchase_price'),
                DB::raw('MAX(invoice_items.rate) as max_purchase_price'),
                DB::raw('COUNT(DISTINCT invoice_items.voucher_id) as purchase_count')
            )
            ->groupBy('products.id', 'products.name', 'products.sku', 'product_categories.name')
            ->orderBy($sortBy, $sortOrder)
            ->paginate(20);

        $baseQuery = InvoiceItem::whereHas('voucher', function ($voucherQuery) use ($tenant, $purchaseVoucherTypes, $fromDate, $toDate) {
                $voucherQuery->where('tenant_id', $tenant->id)
                    ->whereIn('voucher_type_id', $purchaseVoucherTypes)
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

        $totalCost = (clone $baseQuery)->sum('invoice_items.amount');
        $totalQuantity = (clone $baseQuery)->sum('invoice_items.quantity');

        return response()->json([
            'success' => true,
            'message' => 'Product purchases retrieved successfully',
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
                    'total_products' => $productPurchases->total(),
                    'total_cost' => (float) $totalCost,
                    'total_quantity' => (float) $totalQuantity,
                ],
                'records' => $productPurchases,
            ],
        ]);
    }

    /**
     * Purchases by period report.
     */
    public function byPeriod(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());
        $periodType = $request->get('period_type', 'daily');
        $compareWith = $request->get('compare_with');

        $purchaseVoucherTypes = $this->getPurchaseVoucherTypes($tenant);

        $periodPurchases = $this->getPeriodPurchases($tenant, $purchaseVoucherTypes, $fromDate, $toDate, $periodType);
        $comparisonData = null;
        if ($compareWith) {
            $comparisonData = $this->getComparisonPeriodPurchases($tenant, $purchaseVoucherTypes, $fromDate, $toDate, $periodType, $compareWith);
        }

        if ($comparisonData) {
            foreach ($periodPurchases as $index => $period) {
                $comparison = $comparisonData[$index] ?? null;
                $periodPurchases[$index]['growth_rate'] = $comparison && $comparison['total_purchases'] > 0
                    ? (($period['total_purchases'] - $comparison['total_purchases']) / $comparison['total_purchases']) * 100
                    : null;
            }
        }

        $totalPurchases = array_sum(array_column($periodPurchases, 'total_purchases'));
        $totalOrders = array_sum(array_column($periodPurchases, 'purchase_count'));
        $averagePerPeriod = count($periodPurchases) > 0 ? $totalPurchases / count($periodPurchases) : 0;

        $bestPeriod = collect($periodPurchases)->sortByDesc('total_purchases')->first();
        $worstPeriod = collect($periodPurchases)->sortBy('total_purchases')->first();

        return response()->json([
            'success' => true,
            'message' => 'Purchases by period retrieved successfully',
            'data' => [
                'filters' => [
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'period_type' => $periodType,
                    'compare_with' => $compareWith,
                ],
                'summary' => [
                    'total_purchases' => (float) $totalPurchases,
                    'total_orders' => (int) $totalOrders,
                    'average_per_period' => (float) $averagePerPeriod,
                    'best_period' => $bestPeriod,
                    'worst_period' => $worstPeriod,
                ],
                'records' => $periodPurchases,
            ],
        ]);
    }

    private function getPurchaseVoucherTypes(Tenant $tenant)
    {
        return VoucherType::where('tenant_id', $tenant->id)
            ->where('affects_inventory', true)
            ->where('inventory_effect', 'increase')
            ->pluck('id');
    }

    private function getPurchaseTrend(Tenant $tenant, $purchaseVoucherTypes, $fromDate, $toDate, $groupBy)
    {
        if ($groupBy === 'month') {
            $year = Carbon::parse($fromDate)->year;
            $trend = collect();
            for ($i = 1; $i <= 12; $i++) {
                $trend->push([
                    'period' => Carbon::create($year, $i, 1)->format('Y-m'),
                    'label' => Carbon::create($year, $i, 1)->format('M'),
                    'total_purchases' => 0,
                    'purchase_count' => 0,
                ]);
            }

            $results = Voucher::where('tenant_id', $tenant->id)
                ->whereIn('voucher_type_id', $purchaseVoucherTypes)
                ->where('status', 'posted')
                ->whereYear('voucher_date', $year)
                ->select(DB::raw('MONTH(voucher_date) as month'), DB::raw('SUM(total_amount) as total_purchases'), DB::raw('COUNT(*) as purchase_count'))
                ->groupBy(DB::raw('MONTH(voucher_date)'))
                ->get();

            $trend = $trend->map(function ($item) use ($results) {
                $match = $results->firstWhere('month', (int) substr($item['period'], 5, 2));
                if ($match) {
                    $item['total_purchases'] = (float) $match->total_purchases;
                    $item['purchase_count'] = (int) $match->purchase_count;
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
            ->whereIn('voucher_type_id', $purchaseVoucherTypes)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$fromDate, $toDate])
            ->select(DB::raw("DATE_FORMAT(voucher_date, '{$dateFormat}') as period"), DB::raw('SUM(total_amount) as total_purchases'), DB::raw('COUNT(*) as purchase_count'))
            ->groupBy(DB::raw("DATE_FORMAT(voucher_date, '{$dateFormat}')"))
            ->orderBy('period')
            ->get();
    }

    private function getPaymentStatus(Tenant $tenant, $purchaseVoucherTypes, $fromDate, $toDate)
    {
        $totalPurchases = Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $purchaseVoucherTypes)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$fromDate, $toDate])
            ->sum('total_amount');

        $paid = 0;
        $outstanding = (float) $totalPurchases;

        return [
            'total_purchases' => (float) $totalPurchases,
            'paid' => (float) $paid,
            'outstanding' => (float) $outstanding,
        ];
    }

    private function getPreviousPeriodComparison(Tenant $tenant, $purchaseVoucherTypes, $fromDate, $toDate)
    {
        $from = Carbon::parse($fromDate);
        $to = Carbon::parse($toDate);
        $daysDiff = $from->diffInDays($to) + 1;

        $previousFrom = $from->copy()->subDays($daysDiff);
        $previousTo = $to->copy()->subDays($daysDiff);

        $previousPurchases = Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $purchaseVoucherTypes)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$previousFrom, $previousTo])
            ->sum('total_amount');

        $currentPurchases = Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $purchaseVoucherTypes)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$fromDate, $toDate])
            ->sum('total_amount');

        $growthRate = $previousPurchases > 0 ? (($currentPurchases - $previousPurchases) / $previousPurchases) * 100 : 0;

        return [
            'previous_from' => $previousFrom->toDateString(),
            'previous_to' => $previousTo->toDateString(),
            'previous_purchases' => (float) $previousPurchases,
            'current_purchases' => (float) $currentPurchases,
            'growth_rate' => (float) $growthRate,
        ];
    }

    private function getPeriodPurchases(Tenant $tenant, $purchaseVoucherTypes, $fromDate, $toDate, $periodType)
    {
        $dateFormat = match ($periodType) {
            'weekly' => '%x-%v',
            'monthly' => '%Y-%m',
            'quarterly' => '%Y-Q%q',
            'yearly' => '%Y',
            default => '%Y-%m-%d',
        };

        $selectFormat = $periodType === 'quarterly'
            ? "CONCAT(YEAR(voucher_date), '-Q', QUARTER(voucher_date))"
            : "DATE_FORMAT(voucher_date, '{$dateFormat}')";

        return Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $purchaseVoucherTypes)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$fromDate, $toDate])
            ->selectRaw("{$selectFormat} as period")
            ->selectRaw('MIN(voucher_date) as start_date')
            ->selectRaw('MAX(voucher_date) as end_date')
            ->selectRaw('SUM(total_amount) as total_purchases')
            ->selectRaw('COUNT(*) as purchase_count')
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->map(function ($row) {
                return [
                    'period' => $row->period,
                    'start_date' => Carbon::parse($row->start_date)->toDateString(),
                    'end_date' => Carbon::parse($row->end_date)->toDateString(),
                    'total_purchases' => (float) $row->total_purchases,
                    'purchase_count' => (int) $row->purchase_count,
                    'avg_purchase' => $row->purchase_count > 0 ? (float) $row->total_purchases / $row->purchase_count : 0,
                ];
            })
            ->values()
            ->all();
    }

    private function getComparisonPeriodPurchases(Tenant $tenant, $purchaseVoucherTypes, $fromDate, $toDate, $periodType, $compareWith)
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

        return $this->getPeriodPurchases($tenant, $purchaseVoucherTypes, $comparisonFrom->toDateString(), $comparisonTo->toDateString(), $periodType);
    }
}
