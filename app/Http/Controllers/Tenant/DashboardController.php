<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Voucher;
use App\Models\VoucherType;
use App\Models\VoucherEntry;
use App\Models\StockMovement;
use App\Models\InvoiceItem;
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

        // Get sales voucher types (SV, Sales, etc.)
        $salesVoucherTypes = VoucherType::where('tenant_id', $tenant->id)
            ->where('affects_inventory', true)
            ->where('inventory_effect', 'decrease')
            ->whereIn('code', ['SV', 'SALES'])
            ->pluck('id');

        // Total Revenue from Sales Invoices (Posted only)
        $totalRevenue = Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $salesVoucherTypes)
            ->where('status', 'posted')
            ->sum('total_amount');

        // Monthly Revenue (current month)
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

        // Get monthly revenue and expense data using ledger accounts
        for ($month = 1; $month <= 12; $month++) {
            // Calculate month end date
            $monthEnd = Carbon::create(Carbon::now()->year, $month, 1)->endOfMonth()->toDateString();
            $monthStart = Carbon::create(Carbon::now()->year, $month, 1)->startOfMonth()->toDateString();
            $prevMonthEnd = Carbon::create(Carbon::now()->year, $month, 1)->subDay()->toDateString();

            // Get all income accounts and calculate total revenue for the month
            $incomeAccounts = \App\Models\LedgerAccount::where('tenant_id', $tenant->id)
                ->where('account_type', 'income')
                ->where('is_active', true)
                ->get();

            $monthRevenue = 0;
            foreach ($incomeAccounts as $account) {
                $openingBalance = $account->getCurrentBalance($prevMonthEnd, false);
                $closingBalance = $account->getCurrentBalance($monthEnd, false);
                // For income accounts, credit increases balance (shown as negative in our system)
                // So period revenue = closing - opening (absolute value)
                $monthRevenue += abs($closingBalance - $openingBalance);
            }

            // Get all expense accounts and calculate total expenses for the month
            $expenseAccounts = \App\Models\LedgerAccount::where('tenant_id', $tenant->id)
                ->where('account_type', 'expense')
                ->where('is_active', true)
                ->get();

            $monthExpenses = 0;
            foreach ($expenseAccounts as $account) {
                $openingBalance = $account->getCurrentBalance($prevMonthEnd, false);
                $closingBalance = $account->getCurrentBalance($monthEnd, false);
                // For expense accounts, debit increases balance
                // So period expense = closing - opening (if positive)
                $periodExpense = $closingBalance - $openingBalance;
                $monthExpenses += max(0, $periodExpense);
            }

            $chartData['revenue'][] = (float) $monthRevenue;
            $chartData['expenses'][] = (float) $monthExpenses;
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
        // Total sales/invoices count for THIS MONTH
        $totalSalesCount = Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $salesVoucherTypes)
            ->where('status', 'posted')
            ->whereMonth('voucher_date', Carbon::now()->month)
            ->whereYear('voucher_date', Carbon::now()->year)
            ->count();

        // Get purchase voucher types (PUR, PURCHASE, etc.)
        $purchaseVoucherTypes = VoucherType::where('tenant_id', $tenant->id)
            ->where('affects_inventory', true)
            ->where('inventory_effect', 'increase')
            ->whereIn('code', ['PUR', 'PURCHASE'])
            ->pluck('id');

        // Total purchases this month
        $totalPurchase = Voucher::where('tenant_id', $tenant->id)
            ->whereIn('voucher_type_id', $purchaseVoucherTypes)
            ->where('status', 'posted')
            ->whereMonth('voucher_date', Carbon::now()->month)
            ->whereYear('voucher_date', Carbon::now()->year)
            ->sum('total_amount');

        // Calculate current month expenses from expense accounts
        $currentMonthExpenses = $chartData['expenses'][Carbon::now()->month - 1] ?? 0;

        $quickStats = [
            'monthly_sales' => $monthlyRevenue,
            'monthly_sales_percentage' => $revenueGrowth,
            'customer_growth' => $totalCustomers,
            'expense_ratio' => $monthlyRevenue > 0
                ? ($currentMonthExpenses / $monthlyRevenue) * 100
                : 0
        ];

        // Recent transactions - Using direct DB queries
        $recentTransactions = [];

        // Get recent sales invoices using DB query
        $salesInvoices = DB::table('vouchers')
            ->join('voucher_types', 'vouchers.voucher_type_id', '=', 'voucher_types.id')
            ->leftJoin('voucher_entries', function($join) {
                $join->on('vouchers.id', '=', 'voucher_entries.voucher_id')
                     ->where('voucher_entries.debit_amount', '>', 0);
            })
            ->leftJoin('ledger_accounts', 'voucher_entries.ledger_account_id', '=', 'ledger_accounts.id')
            ->where('vouchers.tenant_id', $tenant->id)
            ->where('vouchers.status', 'posted')
            ->whereIn('vouchers.voucher_type_id', $salesVoucherTypes->toArray())
            ->select(
                'vouchers.voucher_number',
                'vouchers.total_amount',
                'vouchers.voucher_date',
                'voucher_types.prefix',
                'ledger_accounts.name as customer_name'
            )
            ->orderBy('vouchers.created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($salesInvoices as $invoice) {
            $recentTransactions[] = [
                'type' => 'sale',
                'icon_color' => 'green',
                'description' => $invoice->customer_name ? "Sale to {$invoice->customer_name}" : 'Sale',
                'reference' => "{$invoice->prefix}{$invoice->voucher_number}",
                'amount' => (float) $invoice->total_amount,
                'date' => $invoice->voucher_date
            ];
        }

        // Get recent vouchers using DB query
        $vouchersData = DB::table('vouchers')
            ->leftJoin('voucher_types', 'vouchers.voucher_type_id', '=', 'voucher_types.id')
            ->where('vouchers.tenant_id', $tenant->id)
            ->where('vouchers.status', 'posted')
            ->select(
                'vouchers.voucher_number',
                'vouchers.total_amount',
                'vouchers.voucher_date',
                'vouchers.posted_at',
                'voucher_types.name as type_name',
                'voucher_types.inventory_effect'
            )
            ->orderBy('vouchers.posted_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($vouchersData as $voucher) {
            $isIncome = $voucher->inventory_effect === 'decrease';
            $recentTransactions[] = [
                'type' => $isIncome ? 'income' : 'expense',
                'icon_color' => $isIncome ? 'blue' : 'red',
                'description' => $voucher->type_name ?: 'Voucher',
                'reference' => "#{$voucher->voucher_number}",
                'amount' => (float) ($isIncome ? $voucher->total_amount : -$voucher->total_amount),
                'date' => $voucher->voucher_date
            ];
        }

        // Sort by date descending
        usort($recentTransactions, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        $recentTransactions = array_slice($recentTransactions, 0, 4);

        // Recent activities - Using direct DB queries to avoid Eloquent issues
        $recentActivities = [];

        // Recent customers using DB query
        $customersData = DB::table('customers')
            ->where('tenant_id', $tenant->id)
            ->select('company_name', 'first_name', 'last_name', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        foreach ($customersData as $customer) {
            $name = $customer->company_name ?: trim($customer->first_name . ' ' . $customer->last_name);
            $recentActivities[] = [
                'type' => 'customer_added',
                'icon_color' => 'blue',
                'description' => 'New customer added',
                'details' => "{$name} was added to your customer list",
                'date' => $customer->created_at
            ];
        }

        // Recent sales invoices using DB query
        $recentSalesInvoices = DB::table('vouchers')
            ->join('voucher_types', 'vouchers.voucher_type_id', '=', 'voucher_types.id')
            ->leftJoin('voucher_entries', function($join) {
                $join->on('vouchers.id', '=', 'voucher_entries.voucher_id')
                     ->where('voucher_entries.debit_amount', '>', 0);
            })
            ->leftJoin('ledger_accounts', 'voucher_entries.ledger_account_id', '=', 'ledger_accounts.id')
            ->where('vouchers.tenant_id', $tenant->id)
            ->where('vouchers.status', 'posted')
            ->whereIn('vouchers.voucher_type_id', $salesVoucherTypes->toArray())
            ->select(
                'vouchers.total_amount',
                'vouchers.voucher_date',
                'vouchers.created_at',
                'voucher_types.prefix',
                'vouchers.voucher_number',
                'ledger_accounts.name as customer_name'
            )
            ->orderBy('vouchers.created_at', 'desc')
            ->limit(3)
            ->get();

        foreach ($recentSalesInvoices as $invoice) {
            $customerName = $invoice->customer_name ?: 'Customer';
            $recentActivities[] = [
                'type' => 'payment_received',
                'icon_color' => 'green',
                'description' => 'Invoice posted',
                'details' => '₦' . number_format($invoice->total_amount, 2) . " - {$invoice->prefix}{$invoice->voucher_number}",
                'date' => $invoice->created_at
            ];
        }

        // Recent stock movements using DB query
        $stockMovementsData = DB::table('stock_movements')
            ->join('products', 'stock_movements.product_id', '=', 'products.id')
            ->where('stock_movements.tenant_id', $tenant->id)
            ->select(
                'stock_movements.quantity',
                'stock_movements.created_at',
                'products.name as product_name'
            )
            ->orderBy('stock_movements.created_at', 'desc')
            ->limit(2)
            ->get();

        foreach ($stockMovementsData as $movement) {
            $recentActivities[] = [
                'type' => 'stock_movement',
                'icon_color' => $movement->quantity > 0 ? 'green' : 'red',
                'description' => $movement->quantity > 0 ? 'Stock added' : 'Stock reduced',
                'details' => "{$movement->product_name}: " . abs($movement->quantity) . " units",
                'date' => $movement->created_at
            ];
        }

        // Sort by date descending
        usort($recentActivities, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        $recentActivities = array_slice($recentActivities, 0, 4);

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

        // Open invoices/sales count (draft + posted pending payment)
        $openInvoices = Voucher::where('tenant_id', $currentTenant->id)
            ->whereIn('voucher_type_id', $salesVoucherTypes)
            ->where('status', '!=', 'cancelled')
            ->count();

        // Average payment days - not applicable for voucher system
        // Can be implemented later based on payment vouchers linked to sales invoices
        $avgPaymentDays = 0;

        // Top selling products (based on invoice items)
        $topProducts = DB::table('invoice_items')
            ->join('vouchers', 'invoice_items.voucher_id', '=', 'vouchers.id')
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->where('vouchers.tenant_id', $tenant->id)
            ->where('vouchers.status', 'posted')
            ->whereIn('vouchers.voucher_type_id', $salesVoucherTypes->toArray())
            ->whereMonth('vouchers.voucher_date', Carbon::now()->month)
            ->whereYear('vouchers.voucher_date', Carbon::now()->year)
            ->select(
                'products.name',
                DB::raw('COUNT(invoice_items.id) as sales_count'),
                DB::raw('SUM(invoice_items.amount) as total_revenue'),
                DB::raw('SUM(invoice_items.quantity) as total_quantity')
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

        // Top customers (based on sales invoices)
        // Join customers via ledger_account_id (customers.ledger_account_id -> ledger_accounts.id)
        $topCustomers = DB::table('vouchers')
            ->join('voucher_entries', 'vouchers.id', '=', 'voucher_entries.voucher_id')
            ->join('ledger_accounts', 'voucher_entries.ledger_account_id', '=', 'ledger_accounts.id')
            ->leftJoin('customers', 'customers.ledger_account_id', '=', 'ledger_accounts.id')
            ->where('vouchers.tenant_id', $tenant->id)
            ->where('vouchers.status', 'posted')
            ->whereIn('vouchers.voucher_type_id', $salesVoucherTypes->toArray())
            ->where('voucher_entries.debit_amount', '>', 0) // Customer entry is debit
            ->whereNotNull('customers.id') // Only include entries linked to customers
            ->whereMonth('vouchers.voucher_date', Carbon::now()->month)
            ->whereYear('vouchers.voucher_date', Carbon::now()->year)
            ->select(
                'ledger_accounts.name as customer_name',
                'customers.company_name',
                'customers.first_name',
                'customers.last_name',
                DB::raw('COUNT(DISTINCT vouchers.id) as order_count'),
                DB::raw('SUM(vouchers.total_amount) as total_spent')
            )
            ->groupBy('ledger_accounts.id', 'ledger_accounts.name', 'customers.id', 'customers.company_name', 'customers.first_name', 'customers.last_name')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get()
            ->map(function($item) {
                $name = $item->customer_name ?: ($item->company_name ?: trim($item->first_name . ' ' . $item->last_name));
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
            'totalPurchase' => $totalPurchase,
            'showTour' => !$user->tour_completed,
        ]);
    }
}
