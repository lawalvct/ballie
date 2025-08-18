<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Customer;
use App\Models\Product;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard
     */
    public function index(Request $request, Tenant $tenant)
    {
        // Get current tenant from route parameter
        $currentTenant = $tenant;
      // Load dashboard data

      $totalProducts = Product::where('tenant_id', $tenant->id)->count();

        // Get authenticated user
        $user = auth()->user();

        // Sample data for dashboard - replace with actual data queries
        $chartData = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'revenue' => [120000, 190000, 300000, 500000, 200000, 300000, 450000, 600000, 750000, 850000, 900000, 1000000],
            'expenses' => [80000, 120000, 180000, 250000, 150000, 200000, 280000, 350000, 400000, 450000, 500000, 550000]
        ];

        // Sample alerts data
        $alerts = [
            [
                'type' => 'overdue',
                'color' => 'red',
                'title' => 'Overdue Invoices',
                'message' => '5 invoices are overdue totaling ₦234,500'
            ],
            [
                'type' => 'low_stock',
                'color' => 'yellow',
                'title' => 'Low Stock Alert',
                'message' => '3 products are running low on inventory'
            ],
            [
                'type' => 'tax_reminder',
                'color' => 'blue',
                'title' => 'Tax Reminder',
                'message' => 'VAT filing due in 7 days'
            ]
        ];

        // Sample quick stats
        $quickStats = [
            'monthly_sales' => 847230,
            'monthly_sales_percentage' => 68,
            'customer_growth' => 24,
            'expense_ratio' => 43
        ];

        // Sample recent transactions
        $recentTransactions = [
            [
                'type' => 'income',
                'icon_color' => 'green',
                'description' => 'Payment from Adebayo Ltd',
                'reference' => 'Invoice #INV-2024-001',
                'amount' => 125000,
                'date' => Carbon::now()->subHours(2)
            ],
            [
                'type' => 'expense',
                'icon_color' => 'red',
                'description' => 'Office Rent Payment',
                'reference' => 'Monthly expense',
                'amount' => -85000,
                'date' => Carbon::now()->subHours(5)
            ],
            [
                'type' => 'invoice',
                'icon_color' => 'blue',
                'description' => 'New Invoice Created',
                'reference' => 'Kemi Enterprises',
                'amount' => 67500,
                'date' => Carbon::now()->subDay()
            ],
            [
                'type' => 'purchase',
                'icon_color' => 'purple',
                'description' => 'Inventory Purchase',
                'reference' => 'Office supplies',
                'amount' => -23450,
                'date' => Carbon::now()->subDays(2)
            ]
        ];

        // Sample recent activities
        $recentActivities = [
            [
                'type' => 'customer_added',
                'icon_color' => 'blue',
                'description' => 'New customer added',
                'details' => 'Tunde Bakare was added to your customer list',
                'date' => Carbon::now()->subMinutes(30)
            ],
            [
                'type' => 'payment_received',
                'icon_color' => 'green',
                'description' => 'Invoice payment received',
                'details' => '₦125,000 payment for Invoice #INV-2024-001',
                'date' => Carbon::now()->subHours(2)
            ],
            [
                'type' => 'low_stock',
                'icon_color' => 'yellow',
                'description' => 'Low stock alert',
                'details' => 'Office Paper is running low (5 units remaining)',
                'date' => Carbon::now()->subHours(4)
            ],
            [
                'type' => 'report_generated',
                'icon_color' => 'purple',
                'description' => 'Monthly report generated',
                'details' => 'Financial report for November 2024 is ready',
                'date' => Carbon::now()->subDay()
            ]
        ];

        // Sample upcoming due dates
        $upcomingDueDates = [
            [
                'type' => 'overdue',
                'color' => 'red',
                'status' => 'Overdue',
                'title' => 'Invoice #INV-2024-045',
                'client' => 'Emeka Trading Company',
                'amount' => 89500,
                'due_date' => Carbon::now()->subDays(5)
            ],
            [
                'type' => 'due_soon',
                'color' => 'yellow',
                'status' => 'Due Soon',
                'title' => 'VAT Filing',
                'client' => 'Federal Inland Revenue Service',
                'amount' => null,
                'due_date' => Carbon::now()->addDays(3)
            ],
            [
                'type' => 'upcoming',
                'color' => 'blue',
                'status' => 'Upcoming',
                'title' => 'Rent Payment',
                'client' => 'Office Space Rental',
                'amount' => 150000,
                'due_date' => Carbon::now()->addDays(7)
            ]
        ];

        // Get actual customer count
        $totalCustomers = Customer::where('tenant_id', $currentTenant->id)->count();
        $totalRevenue = Customer::where('tenant_id', $currentTenant->id)->sum('total_spent');
        $openInvoices = 0; // This would come from your Invoice model when implemented
        $avgPaymentDays = 0; // This would be calculated from your payment data when implemented

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
        ]);
    }
}
