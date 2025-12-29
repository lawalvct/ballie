<?php

namespace App\Http\Controllers\Tenant\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EcommerceReportsController extends Controller
{
    /**
     * Order Reports - Display comprehensive order analytics
     */
    public function orders(Request $request)
    {
        $tenant = tenant();

        // Date range filter
        $dateFrom = $request->input('date_from', Carbon::now()->subDays(30)->toDateString());
        $dateTo = $request->input('date_to', Carbon::now()->toDateString());

        // Order statistics by status
        $ordersByStatus = Order::where('tenant_id', $tenant->id)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->select('status', DB::raw('count(*) as count'), DB::raw('sum(total_amount) as total'))
            ->groupBy('status')
            ->get();

        // Order statistics by payment status
        $ordersByPayment = Order::where('tenant_id', $tenant->id)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->select('payment_status', DB::raw('count(*) as count'), DB::raw('sum(total_amount) as total'))
            ->groupBy('payment_status')
            ->get();

        // Daily order trends
        $dailyTrends = Order::where('tenant_id', $tenant->id)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as orders'),
                DB::raw('sum(total_amount) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Payment method breakdown
        $paymentMethods = Order::where('tenant_id', $tenant->id)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereNotNull('payment_method')
            ->select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(total_amount) as total'))
            ->groupBy('payment_method')
            ->get();

        // Top performing products
        $topProducts = OrderItem::whereHas('order', function($query) use ($tenant, $dateFrom, $dateTo) {
                $query->where('tenant_id', $tenant->id)
                    ->whereBetween('created_at', [$dateFrom, $dateTo])
                    ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered']);
            })
            ->with('product')
            ->select('product_id', DB::raw('sum(quantity) as total_quantity'), DB::raw('sum(total) as total_revenue'))
            ->groupBy('product_id')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        // Overall statistics
        $stats = [
            'total_orders' => Order::where('tenant_id', $tenant->id)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->count(),
            'total_revenue' => Order::where('tenant_id', $tenant->id)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
                ->sum('total_amount'),
            'average_order_value' => Order::where('tenant_id', $tenant->id)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
                ->avg('total_amount'),
            'cancelled_orders' => Order::where('tenant_id', $tenant->id)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->where('status', 'cancelled')
                ->count(),
        ];

        return view('tenant.ecommerce.reports.orders', compact(
            'tenant',
            'ordersByStatus',
            'ordersByPayment',
            'dailyTrends',
            'paymentMethods',
            'topProducts',
            'stats',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Revenue Analysis - Display revenue analytics and trends
     */
    public function revenue(Request $request)
    {
        $tenant = tenant();

        // Date range filter
        $dateFrom = $request->input('date_from', Carbon::now()->subMonths(6)->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', Carbon::now()->toDateString());

        // Monthly revenue trends
        $monthlyRevenue = Order::where('tenant_id', $tenant->id)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as orders'),
                DB::raw('sum(subtotal) as subtotal'),
                DB::raw('sum(tax_amount) as tax'),
                DB::raw('sum(shipping_amount) as shipping'),
                DB::raw('sum(discount_amount) as discount'),
                DB::raw('sum(total_amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Revenue by payment status
        $revenueByPayment = Order::where('tenant_id', $tenant->id)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->select('payment_status', DB::raw('sum(total_amount) as total'))
            ->groupBy('payment_status')
            ->get();

        // Revenue by payment method
        $revenueByMethod = Order::where('tenant_id', $tenant->id)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->whereNotNull('payment_method')
            ->select('payment_method', DB::raw('sum(total_amount) as total'), DB::raw('count(*) as count'))
            ->groupBy('payment_method')
            ->get();

        // Overall statistics
        $currentPeriodRevenue = Order::where('tenant_id', $tenant->id)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->sum('total_amount');

        $previousFrom = Carbon::parse($dateFrom)->subDays(Carbon::parse($dateTo)->diffInDays(Carbon::parse($dateFrom)))->toDateString();
        $previousTo = $dateFrom;

        $previousPeriodRevenue = Order::where('tenant_id', $tenant->id)
            ->whereBetween('created_at', [$previousFrom, $previousTo])
            ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->sum('total_amount');

        $growthRate = $previousPeriodRevenue > 0
            ? (($currentPeriodRevenue - $previousPeriodRevenue) / $previousPeriodRevenue) * 100
            : 0;

        $stats = [
            'current_revenue' => $currentPeriodRevenue,
            'previous_revenue' => $previousPeriodRevenue,
            'growth_rate' => round($growthRate, 2),
            'total_orders' => Order::where('tenant_id', $tenant->id)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
                ->count(),
            'average_order_value' => Order::where('tenant_id', $tenant->id)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
                ->avg('total_amount'),
        ];

        return view('tenant.ecommerce.reports.revenue', compact(
            'tenant',
            'monthlyRevenue',
            'revenueByPayment',
            'revenueByMethod',
            'stats',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Product Performance - Display product analytics
     */
    public function products(Request $request)
    {
        $tenant = tenant();

        // Date range filter
        $dateFrom = $request->input('date_from', Carbon::now()->subDays(30)->toDateString());
        $dateTo = $request->input('date_to', Carbon::now()->toDateString());

        // Top selling products by revenue
        $topByRevenue = OrderItem::whereHas('order', function($query) use ($tenant, $dateFrom, $dateTo) {
                $query->where('tenant_id', $tenant->id)
                    ->whereBetween('created_at', [$dateFrom, $dateTo])
                    ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered']);
            })
            ->with(['product', 'product.category'])
            ->select('product_id',
                DB::raw('sum(quantity) as total_quantity'),
                DB::raw('sum(total) as total_revenue'),
                DB::raw('count(DISTINCT order_id) as order_count')
            )
            ->groupBy('product_id')
            ->orderByDesc('total_revenue')
            ->limit(20)
            ->get();

        // Top selling products by quantity
        $topByQuantity = OrderItem::whereHas('order', function($query) use ($tenant, $dateFrom, $dateTo) {
                $query->where('tenant_id', $tenant->id)
                    ->whereBetween('created_at', [$dateFrom, $dateTo])
                    ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered']);
            })
            ->with(['product', 'product.category'])
            ->select('product_id',
                DB::raw('sum(quantity) as total_quantity'),
                DB::raw('sum(total) as total_revenue')
            )
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(20)
            ->get();

        // Product category performance
        $categoryPerformance = OrderItem::whereHas('order', function($query) use ($tenant, $dateFrom, $dateTo) {
                $query->where('tenant_id', $tenant->id)
                    ->whereBetween('created_at', [$dateFrom, $dateTo])
                    ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered']);
            })
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->select('product_categories.name as category',
                DB::raw('sum(order_items.quantity) as total_quantity'),
                DB::raw('sum(order_items.total) as total_revenue'),
                DB::raw('count(DISTINCT order_items.order_id) as order_count')
            )
            ->groupBy('product_categories.id', 'product_categories.name')
            ->orderByDesc('total_revenue')
            ->get();

        // Low stock products that are selling
        $lowStockProducts = OrderItem::whereHas('order', function($query) use ($tenant, $dateFrom, $dateTo) {
                $query->where('tenant_id', $tenant->id)
                    ->whereBetween('created_at', [$dateFrom, $dateTo])
                    ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered']);
            })
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.tenant_id', $tenant->id)
            ->whereRaw('products.quantity <= products.reorder_level')
            ->select('order_items.product_id',
                'products.name',
                'products.quantity as current_stock',
                'products.reorder_level',
                DB::raw('sum(order_items.quantity) as sold_quantity')
            )
            ->groupBy('order_items.product_id', 'products.name', 'products.quantity', 'products.reorder_level')
            ->orderBy('products.quantity')
            ->limit(10)
            ->get();

        // Overall statistics
        $stats = [
            'total_products_sold' => OrderItem::whereHas('order', function($query) use ($tenant, $dateFrom, $dateTo) {
                    $query->where('tenant_id', $tenant->id)
                        ->whereBetween('created_at', [$dateFrom, $dateTo])
                        ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered']);
                })
                ->distinct('product_id')
                ->count('product_id'),
            'total_quantity_sold' => OrderItem::whereHas('order', function($query) use ($tenant, $dateFrom, $dateTo) {
                    $query->where('tenant_id', $tenant->id)
                        ->whereBetween('created_at', [$dateFrom, $dateTo])
                        ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered']);
                })
                ->sum('quantity'),
            'total_revenue' => OrderItem::whereHas('order', function($query) use ($tenant, $dateFrom, $dateTo) {
                    $query->where('tenant_id', $tenant->id)
                        ->whereBetween('created_at', [$dateFrom, $dateTo])
                        ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered']);
                })
                ->sum('total'),
            'low_stock_count' => $lowStockProducts->count(),
        ];

        return view('tenant.ecommerce.reports.products', compact(
            'tenant',
            'topByRevenue',
            'topByQuantity',
            'categoryPerformance',
            'lowStockProducts',
            'stats',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Customer Analytics - Display customer insights
     */
    public function customers(Request $request)
    {
        $tenant = tenant();

        // Date range filter
        $dateFrom = $request->input('date_from', Carbon::now()->subDays(90)->toDateString());
        $dateTo = $request->input('date_to', Carbon::now()->toDateString());

        // Top customers by revenue
        $topCustomers = Order::where('tenant_id', $tenant->id)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->whereNotNull('customer_id')
            ->with('customer')
            ->select('customer_id',
                DB::raw('count(*) as order_count'),
                DB::raw('sum(total_amount) as total_spent'),
                DB::raw('avg(total_amount) as avg_order_value'),
                DB::raw('MAX(created_at) as last_order_date')
            )
            ->groupBy('customer_id')
            ->orderByDesc('total_spent')
            ->limit(20)
            ->get();

        // New vs Returning customers
        $newCustomers = Order::where('tenant_id', $tenant->id)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereNotNull('customer_id')
            ->select('customer_id', DB::raw('MIN(created_at) as first_order'))
            ->groupBy('customer_id')
            ->havingRaw('MIN(created_at) BETWEEN ? AND ?', [$dateFrom, $dateTo])
            ->count();

        $returningCustomers = Order::where('tenant_id', $tenant->id)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereNotNull('customer_id')
            ->whereIn('customer_id', function($query) use ($tenant, $dateFrom) {
                $query->select('customer_id')
                    ->from('orders')
                    ->where('tenant_id', $tenant->id)
                    ->where('created_at', '<', $dateFrom)
                    ->whereNotNull('customer_id');
            })
            ->distinct('customer_id')
            ->count('customer_id');

        // Customer lifetime value distribution
        $lifetimeValueSegments = Order::where('tenant_id', $tenant->id)
            ->whereNotNull('customer_id')
            ->select('customer_id', DB::raw('sum(total_amount) as lifetime_value'))
            ->groupBy('customer_id')
            ->get()
            ->groupBy(function($item) {
                if ($item->lifetime_value < 10000) return '< ₦10,000';
                if ($item->lifetime_value < 50000) return '₦10,000 - ₦50,000';
                if ($item->lifetime_value < 100000) return '₦50,000 - ₦100,000';
                if ($item->lifetime_value < 500000) return '₦100,000 - ₦500,000';
                return '₦500,000+';
            })
            ->map(fn($group) => $group->count());

        // Monthly customer acquisition
        $monthlyCustomers = Order::where('tenant_id', $tenant->id)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereNotNull('customer_id')
            ->select('customer_id', DB::raw('MIN(created_at) as first_order'))
            ->groupBy('customer_id')
            ->get()
            ->groupBy(function($item) {
                return Carbon::parse($item->first_order)->format('Y-m');
            })
            ->map(fn($group) => $group->count());

        // Guest checkout statistics
        $guestOrders = Order::where('tenant_id', $tenant->id)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereNull('customer_id')
            ->count();

        $registeredOrders = Order::where('tenant_id', $tenant->id)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereNotNull('customer_id')
            ->count();

        // Overall statistics
        $stats = [
            'total_customers' => Order::where('tenant_id', $tenant->id)
                ->whereNotNull('customer_id')
                ->distinct('customer_id')
                ->count('customer_id'),
            'new_customers' => $newCustomers,
            'returning_customers' => $returningCustomers,
            'guest_orders' => $guestOrders,
            'registered_orders' => $registeredOrders,
            'average_lifetime_value' => Order::where('tenant_id', $tenant->id)
                ->whereNotNull('customer_id')
                ->select('customer_id', DB::raw('sum(total_amount) as lifetime_value'))
                ->groupBy('customer_id')
                ->get()
                ->avg('lifetime_value'),
        ];

        return view('tenant.ecommerce.reports.customers', compact(
            'tenant',
            'topCustomers',
            'lifetimeValueSegments',
            'monthlyCustomers',
            'stats',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Abandoned Carts - Display cart abandonment analytics
     */
    public function abandonedCarts(Request $request)
    {
        $tenant = tenant();

        // Date range filter
        $dateFrom = $request->input('date_from', Carbon::now()->subDays(30)->toDateString());
        $dateTo = $request->input('date_to', Carbon::now()->toDateString());

        // Abandoned carts (no order created, last updated > 1 hour ago)
        $abandonedCarts = Cart::where('tenant_id', $tenant->id)
            ->whereBetween('updated_at', [$dateFrom, $dateTo])
            ->where('updated_at', '<=', Carbon::now()->subHour())
            ->whereDoesntHave('order')
            ->with(['customer', 'items.product'])
            ->orderByDesc('updated_at')
            ->paginate(20);

        // Daily abandonment trends
        $dailyTrends = Cart::where('tenant_id', $tenant->id)
            ->whereBetween('updated_at', [$dateFrom, $dateTo])
            ->where('updated_at', '<=', Carbon::now()->subHour())
            ->whereDoesntHave('order')
            ->select(
                DB::raw('DATE(updated_at) as date'),
                DB::raw('count(*) as abandoned_carts')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Potential revenue from abandoned carts
        $potentialRevenue = Cart::where('tenant_id', $tenant->id)
            ->whereBetween('updated_at', [$dateFrom, $dateTo])
            ->where('updated_at', '<=', Carbon::now()->subHour())
            ->whereDoesntHave('order')
            ->with('items')
            ->get()
            ->sum(function($cart) {
                return $cart->items->sum(function($item) {
                    return $item->price * $item->quantity;
                });
            });

        // Most abandoned products
        $mostAbandonedProducts = DB::table('cart_items')
            ->join('carts', 'cart_items.cart_id', '=', 'carts.id')
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->where('carts.tenant_id', $tenant->id)
            ->whereBetween('carts.updated_at', [$dateFrom, $dateTo])
            ->where('carts.updated_at', '<=', Carbon::now()->subHour())
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('orders')
                    ->whereRaw('orders.id = carts.order_id');
            })
            ->select('products.id', 'products.name', 'products.price',
                DB::raw('sum(cart_items.quantity) as total_quantity'),
                DB::raw('count(DISTINCT carts.id) as cart_count')
            )
            ->groupBy('products.id', 'products.name', 'products.price')
            ->orderByDesc('cart_count')
            ->limit(10)
            ->get();

        // Cart recovery rate
        $totalCartsCreated = Cart::where('tenant_id', $tenant->id)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->count();

        $convertedCarts = Cart::where('tenant_id', $tenant->id)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereHas('order')
            ->count();

        $recoveryRate = $totalCartsCreated > 0
            ? ($convertedCarts / $totalCartsCreated) * 100
            : 0;

        // Overall statistics
        $stats = [
            'abandoned_carts' => Cart::where('tenant_id', $tenant->id)
                ->whereBetween('updated_at', [$dateFrom, $dateTo])
                ->where('updated_at', '<=', Carbon::now()->subHour())
                ->whereDoesntHave('order')
                ->count(),
            'potential_revenue' => $potentialRevenue,
            'recovery_rate' => round($recoveryRate, 2),
            'average_cart_value' => Cart::where('tenant_id', $tenant->id)
                ->whereBetween('updated_at', [$dateFrom, $dateTo])
                ->where('updated_at', '<=', Carbon::now()->subHour())
                ->whereDoesntHave('order')
                ->with('items')
                ->get()
                ->avg(function($cart) {
                    return $cart->items->sum(function($item) {
                        return $item->price * $item->quantity;
                    });
                }),
        ];

        return view('tenant.ecommerce.reports.abandoned-carts', compact(
            'tenant',
            'abandonedCarts',
            'dailyTrends',
            'mostAbandonedProducts',
            'stats',
            'dateFrom',
            'dateTo'
        ));
    }
}
