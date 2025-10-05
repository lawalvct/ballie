<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Voucher;
use App\Models\VoucherEntry;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard
     */
    public function index(Request $request, Tenant $tenant)
    {
        // Get current tenant from route parameter
        $currentTenant = $tenant;

        // Get authenticated user
        $user = auth()->user();

        // Date range for calculations
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $currentYear = Carbon::now()->startOfYear();

        // Get real data from database
        // Total Products
        $totalProducts = Product::where('tenant_id', $tenant->id)->count();

        // Total Customers
        $totalCustomers = Customer::where('tenant_id', $tenant->id)->count();

        // Total Revenue (from Sales + Posted Sales Vouchers)
        $salesRevenue = Sale::where('tenant_id', $tenant->id)
            ->where('status', 'completed')
            ->sum('total_amount');

        $voucherRevenue = Voucher::where('tenant_id', $tenant->id)
            ->where('status', 'posted')
            ->whereHas('voucherType', function($q) {
                $q->where('inventory_effect', 'decrease')
                  ->where('affects_inventory', true);
            })
            ->sum('total_amount');

        $totalRevenue = $salesRevenue + $voucherRevenue;

        // Monthly Revenue
        $monthlyRevenue = Sale::where('tenant_id', $tenant->id)
            ->where('status', 'completed')
            ->whereMonth('sale_date', Carbon::now()->month)
            ->whereYear('sale_date', Carbon::now()->year)
            ->sum('total_amount');

        // Last Month Revenue
        $lastMonthRevenue = Sale::where('tenant_id', $tenant->id)
            ->where('status', 'completed')
            ->whereMonth('sale_date', Carbon::now()->subMonth()->month)
            ->whereYear('sale_date', Carbon::now()->subMonth()->year)
            ->sum('total_amount');

        // Calculate growth percentage
        $revenueGrowth = $lastMonthRevenue > 0
            ? (($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100
            : 0;

        // Chart Data - Monthly Revenue for current year
        $chartData = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'revenue' => [],
            'expenses' => []
        ];

        // Get monthly revenue data
        for ($month = 1; $month <= 12; $month++) {
            $revenue = Sale::where('tenant_id', $tenant->id)
                ->where('status', 'completed')
                ->whereMonth('sale_date', $month)
                ->whereYear('sale_date', Carbon::now()->year)
                ->sum('total_amount');

            $expenses = VoucherEntry::whereHas('voucher', function($q) use ($tenant, $month) {
                $q->where('tenant_id', $tenant->id)
                  ->where('status', 'posted')
                  ->whereMonth('voucher_date', $month)
                  ->whereYear('voucher_date', Carbon::now()->year);
            })
            ->where('debit_amount', '>', 0)
            ->whereHas('ledgerAccount', function($q) {
                $q->whereHas('accountGroup', function($q2) {
                    $q2->where('nature', 'expense');
                });
            })
            ->sum('debit_amount');

            $chartData['revenue'][] = (float) $revenue;
            $chartData['expenses'][] = (float) $expenses;
        }

        // Alerts data
        $alerts = [];

        // Check for low stock products
        $lowStockCount = Product::where('tenant_id', $tenant->id)
            ->where('maintain_stock', true)
            ->lowStock()
            ->count();

        if ($lowStockCount > 0) {
            $alerts[] = [
                'type' => 'low_stock',
                'color' => 'yellow',
                'title' => 'Low Stock Alert',
                'message' => "{$lowStockCount} product(s) are running low on inventory"
            ];
        }

        // Check for out of stock products
        $outOfStockCount = Product::where('tenant_id', $tenant->id)
            ->where('maintain_stock', true)
            ->outOfStock()
            ->count();

        if ($outOfStockCount > 0) {
            $alerts[] = [
                'type' => 'out_of_stock',
                'color' => 'red',
                'title' => 'Out of Stock Alert',
                'message' => "{$outOfStockCount} product(s) are out of stock"
            ];
        }

        // Quick stats
        $totalSalesCount = Sale::where('tenant_id', $tenant->id)
            ->where('status', 'completed')
            ->count();

        $avgSalesValue = $totalSalesCount > 0 ? $totalRevenue / $totalSalesCount : 0;

        $quickStats = [
            'monthly_sales' => $monthlyRevenue,
            'monthly_sales_percentage' => $revenueGrowth,
            'customer_growth' => $totalCustomers,
            'expense_ratio' => $monthlyRevenue > 0
                ? (($chartData['expenses'][Carbon::now()->month - 1] ?? 0) / $monthlyRevenue) * 100
                : 0
        ];

        // Recent transactions - Get latest sales and vouchers
        $recentTransactions = collect();

        // Get recent sales
        $recentSales = Sale::where('tenant_id', $tenant->id)
            ->where('status', 'completed')
            ->with('customer')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($sale) {
                return [
                    'type' => 'sale',
                    'icon_color' => 'green',
                    'description' => $sale->customer ? "Sale to {$sale->customer->company_name}" : 'Sale',
                    'reference' => "Sale #{$sale->sale_number}",
                    'amount' => (float) $sale->total_amount,
                    'date' => $sale->sale_date instanceof \Carbon\Carbon ? $sale->sale_date->toDateString() : $sale->sale_date
                ];
            })
            ->values();

        // Get recent vouchers
        $recentVouchers = Voucher::where('tenant_id', $tenant->id)
            ->where('status', 'posted')
            ->with('voucherType')
            ->latest('posted_at')
            ->take(5)
            ->get()
            ->map(function($voucher) {
                $isIncome = $voucher->voucherType && in_array($voucher->voucherType->inventory_effect, ['decrease']);

                return [
                    'type' => $isIncome ? 'income' : 'expense',
                    'icon_color' => $isIncome ? 'blue' : 'red',
                    'description' => $voucher->voucherType ? $voucher->voucherType->name : 'Voucher',
                    'reference' => "#{$voucher->voucher_number}",
                    'amount' => (float) ($isIncome ? $voucher->total_amount : -$voucher->total_amount),
                    'date' => $voucher->voucher_date instanceof \Carbon\Carbon ? $voucher->voucher_date->toDateString() : $voucher->voucher_date
                ];
            })
            ->values();

        // Merge and convert to plain array before sorting
        $allTransactions = $recentSales->merge($recentVouchers)->toArray();
        
        // Sort using usort
        usort($allTransactions, function($a, $b) {
            $dateA = strtotime($a['date']);
            $dateB = strtotime($b['date']);
            return $dateB - $dateA; // Descending order
        });
        
        $recentTransactions = array_slice($allTransactions, 0, 4);

        // Recent activities - combine multiple sources
        $recentActivities = collect();

        // Recent customers
        $newCustomers = Customer::where('tenant_id', $tenant->id)
            ->latest()
            ->take(3)
            ->get()
            ->map(function($customer) {
                $name = $customer->company_name ?: ($customer->first_name . ' ' . $customer->last_name);
                return [
                    'type' => 'customer_added',
                    'icon_color' => 'blue',
                    'description' => 'New customer added',
                    'details' => "{$name} was added to your customer list",
                    'date' => $customer->created_at->toDateTimeString()
                ];
            })
            ->values();

        // Recent sales
        $newSales = Sale::where('tenant_id', $tenant->id)
            ->where('status', 'completed')
            ->with('customer')
            ->latest()
            ->take(3)
            ->get()
            ->map(function($sale) {
                $customerName = $sale->customer ? $sale->customer->company_name : 'Customer';
                return [
                    'type' => 'payment_received',
                    'icon_color' => 'green',
                    'description' => 'Sale completed',
                    'details' => '₦' . number_format($sale->total_amount, 2) . " from {$customerName}",
                    'date' => $sale->sale_date instanceof \Carbon\Carbon ? $sale->sale_date->toDateString() : $sale->sale_date
                ];
            })
            ->values();

        // Recent stock movements
        $stockAlerts = StockMovement::where('tenant_id', $tenant->id)
            ->with('product')
            ->latest()
            ->take(2)
            ->get()
            ->map(function($movement) {
                return [
                    'type' => 'stock_movement',
                    'icon_color' => $movement->quantity > 0 ? 'green' : 'red',
                    'description' => $movement->quantity > 0 ? 'Stock added' : 'Stock reduced',
                    'details' => "{$movement->product->name}: " . abs($movement->quantity) . " units",
                    'date' => $movement->created_at->toDateTimeString()
                ];
            })
            ->values();

        // Merge and convert to plain array before sorting
        $allActivities = $newCustomers->merge($newSales)->merge($stockAlerts)->toArray();
        
        // Sort using usort
        usort($allActivities, function($a, $b) {
            $dateA = strtotime($a['date']);
            $dateB = strtotime($b['date']);
            return $dateB - $dateA; // Descending order
        });
        
        $recentActivities = array_slice($allActivities, 0, 4);

        // Upcoming due dates - for now just show pending vouchers
        $upcomingDueDates = Voucher::where('tenant_id', $tenant->id)
            ->where('status', 'draft')
            ->with('voucherType')
            ->orderBy('voucher_date')
            ->take(3)
            ->get()
            ->map(function($voucher) {
                $dueDate = Carbon::parse($voucher->voucher_date);
                $daysUntilDue = $dueDate->diffInDays(Carbon::now(), false);

                if ($daysUntilDue < 0) {
                    $type = 'overdue';
                    $color = 'red';
                    $status = 'Overdue';
                } elseif ($daysUntilDue <= 3) {
                    $type = 'due_soon';
                    $color = 'yellow';
                    $status = 'Due Soon';
                } else {
                    $type = 'upcoming';
                    $color = 'blue';
                    $status = 'Upcoming';
                }

                return [
                    'type' => $type,
                    'color' => $color,
                    'status' => $status,
                    'title' => "Voucher #{$voucher->voucher_number}",
                    'client' => $voucher->voucherType ? $voucher->voucherType->name : 'N/A',
                    'amount' => (float) $voucher->total_amount,
                    'due_date' => $dueDate->format('Y-m-d')
                ];
            })
            ->values()
            ->toArray();

        // Open invoices/sales count
        $openInvoices = Sale::where('tenant_id', $currentTenant->id)
            ->where('status', '!=', 'completed')
            ->count();

        // Average payment days (calculated from completed sales)
        $completedSales = Sale::where('tenant_id', $currentTenant->id)
            ->where('status', 'completed')
            ->whereNotNull('updated_at')
            ->get();

        $avgPaymentDays = $completedSales->count() > 0
            ? $completedSales->avg(function($sale) {
                return Carbon::parse($sale->created_at)->diffInDays(Carbon::parse($sale->updated_at));
            })
            : 0;

        // Top selling products (based on sales items and stock movements)
        $topProducts = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.tenant_id', $tenant->id)
            ->where('sales.status', 'completed')
            ->whereMonth('sales.sale_date', Carbon::now()->month)
            ->whereYear('sales.sale_date', Carbon::now()->year)
            ->select(
                'products.name',
                DB::raw('COUNT(sale_items.id) as sales_count'),
                DB::raw('SUM(sale_items.line_total) as total_revenue'),
                DB::raw('SUM(sale_items.quantity) as total_quantity')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->name,
                    'sales' => (int) $item->sales_count,
                    'revenue' => (float) $item->total_revenue,
                    'growth' => 0 // Can be calculated by comparing with last month
                ];
            })
            ->values()
            ->toArray();

        // Top customers (based on sales)
        $topCustomers = DB::table('sales')
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            ->where('sales.tenant_id', $tenant->id)
            ->where('sales.status', 'completed')
            ->whereMonth('sales.sale_date', Carbon::now()->month)
            ->whereYear('sales.sale_date', Carbon::now()->year)
            ->select(
                'customers.company_name',
                'customers.first_name',
                'customers.last_name',
                DB::raw('COUNT(sales.id) as order_count'),
                DB::raw('SUM(sales.total_amount) as total_spent')
            )
            ->groupBy('customers.id', 'customers.company_name', 'customers.first_name', 'customers.last_name')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get()
            ->map(function($item) {
                $name = $item->company_name ?: trim($item->first_name . ' ' . $item->last_name);
                return [
                    'name' => $name ?: 'Unknown Customer',
                    'orders' => (int) $item->order_count,
                    'spent' => (float) $item->total_spent,
                    'growth' => 0 // Can be calculated by comparing with last month
                ];
            })
            ->values()
            ->toArray();

        return view('tenant.dashboard.index', [
            'currentTenant' => $currentTenant,
            'user' => $user,
            'tenant' => $currentTenant,
            'chartData' => $chartData,
            'alerts' => $alerts,
            'quickStats' => $quickStats,
            'recentTransactions' => $recentTransactions,
            'recentActivities' => $recentActivities,
            'upcomingDueDates' => $upcomingDueDates,
            'totalCustomers' => $totalCustomers,
            'totalRevenue' => $totalRevenue,
            'openInvoices' => $openInvoices,
            'avgPaymentDays' => $avgPaymentDays,
            'totalProducts' => $totalProducts,
            'topProducts' => $topProducts,
            'topCustomers' => $topCustomers,
            'totalSalesCount' => $totalSalesCount,
            'avgSalesValue' => $avgSalesValue,
        ]);
    }
}
