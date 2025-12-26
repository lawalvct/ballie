<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Voucher;
use App\Models\VoucherType;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request, Tenant $tenant)
    {
        $user = auth()->user();

        // Total counts
        $totalProducts = Product::where('tenant_id', $tenant->id)->count();
        $totalCustomers = Customer::where('tenant_id', $tenant->id)->count();

        // Get sales voucher types
        $salesVoucherTypes = VoucherType::where('tenant_id', $tenant->id)
            ->where('affects_inventory', true)
            ->where('inventory_effect', 'decrease')
            ->whereIn('code', ['SV', 'SALES'])
            ->pluck('id');

        // Total Revenue
        $totalRevenue = Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $salesVoucherTypes)
            ->where('status', 'posted')
            ->sum('total_amount');

        // Monthly Revenue
        $monthlyRevenue = Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $salesVoucherTypes)
            ->where('status', 'posted')
            ->whereMonth('voucher_date', Carbon::now()->month)
            ->whereYear('voucher_date', Carbon::now()->year)
            ->sum('total_amount');

        // Last Month Revenue
        $lastMonthRevenue = Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $salesVoucherTypes)
            ->where('status', 'posted')
            ->whereMonth('voucher_date', Carbon::now()->subMonth()->month)
            ->whereYear('voucher_date', Carbon::now()->subMonth()->year)
            ->sum('total_amount');

        $revenueGrowth = $lastMonthRevenue > 0
            ? (($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100
            : 0;

        // Chart Data
        $chartData = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'revenue' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'expenses' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
        ];

        // Alerts
        $alerts = [];
        $lowStockCount = Product::where('tenant_id', $tenant->id)
            ->where('maintain_stock', true)
            ->where('type', 'item') // Exclude services
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

        $outOfStockCount = Product::where('tenant_id', $tenant->id)
            ->where('maintain_stock', true)
            ->where('type', 'item') // Exclude services
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

        // Sales count
        $totalSalesCount = Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $salesVoucherTypes)
            ->where('status', 'posted')
            ->whereMonth('voucher_date', Carbon::now()->month)
            ->whereYear('voucher_date', Carbon::now()->year)
            ->count();

        $totalPurchase = 0;

        $quickStats = [
            'monthly_sales' => $monthlyRevenue,
            'monthly_sales_percentage' => $revenueGrowth,
            'customer_growth' => $totalCustomers,
            'expense_ratio' => 0
        ];

        return view('tenant.dashboard.index', [
            'tenant' => $tenant,
            'chartData' => $chartData,
            'alerts' => $alerts,
            'quickStats' => $quickStats,
            'recentActivities' => [],
            'totalCustomers' => $totalCustomers,
            'totalRevenue' => $totalRevenue,
            'totalProducts' => $totalProducts,
            'topProducts' => [],
            'topCustomers' => [],
            'totalSalesCount' => $totalSalesCount,
            'totalPurchase' => $totalPurchase,
            'showTour' => !$user->tour_completed,
        ]);
    }
}
