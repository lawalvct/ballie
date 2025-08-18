<?php

namespace App\Http\Controllers\Tenant\Crm;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CrmController extends Controller
{
    /**
     * Display the CRM dashboard
     */
    public function index(Request $request, Tenant $tenant)
    {
        // Get recent customers for the dashboard
        $recentCustomers = Customer::where('tenant_id', $tenant->id)
         
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Calculate statistics
        $totalCustomers = Customer::where('tenant_id', $tenant->id)->count();

        $activeCustomers = Customer::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->count();

        $totalVendors = Vendor::where('tenant_id', $tenant->id)->count();

        $activeVendors = Vendor::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->count();

        // Mock data for financial metrics (replace with actual calculations)
        $totalRevenue = 0; // Calculate from invoices
        $outstandingReceivables = 0; // Calculate from unpaid invoices
        $totalPayables = 0; // Calculate from vendor invoices
        $avgPaymentDays = 0; // Calculate average payment time

        // Recent activities (mock data - replace with actual activity log)
        $recentActivities = collect([
            (object)[
                'type' => 'customer_added',
                'description' => 'New customer John Doe was added',
                'date' => now()->subHours(2),
                'icon' => 'user-plus'
            ],
            (object)[
                'type' => 'quote_sent',
                'description' => 'Quote #QT-001 sent to Alice Smith',
                'date' => now()->subHours(4),
                'icon' => 'document-text'
            ],
            (object)[
                'type' => 'payment_received',
                'description' => 'Payment received from Mike Brown - ₦15,000',
                'date' => now()->subHours(6),
                'icon' => 'cash'
            ],
            (object)[
                'type' => 'vendor_added',
                'description' => 'New vendor Tech Solutions Inc. was added',
                'date' => now()->subDay(),
                'icon' => 'building-office'
            ],
            (object)[
                'type' => 'invoice_created',
                'description' => 'Invoice #INV-001 created for Sarah Johnson',
                'date' => now()->subDays(2),
                'icon' => 'document'
            ]
        ]);

        return view('tenant.crm.index', compact(
            'tenant',
            'recentCustomers',
            'totalCustomers',
            'activeCustomers',
            'totalVendors',
            'activeVendors',
            'totalRevenue',
            'outstandingReceivables',
            'totalPayables',
            'avgPaymentDays',
            'recentActivities'
        ));
    }
}
