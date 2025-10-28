<?php

namespace App\Http\Controllers\Tenant\Crm;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\Tenant;
use App\Models\Voucher;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

        // Recent activities from database
        $activities = collect();

        // Get recent customers
        $recentCustomersActivity = Customer::where('tenant_id', $tenant->id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function($customer) {
                return (object)[
                    'type' => 'customer_added',
                    'description' => 'New customer ' . $customer->company_name . ' was added',
                    'date' => $customer->created_at,
                    'icon' => 'user-plus',
                    'timestamp' => $customer->created_at->timestamp
                ];
            });

        // Get recent vendors
        $recentVendorsActivity = Vendor::where('tenant_id', $tenant->id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function($vendor) {
                return (object)[
                    'type' => 'vendor_added',
                    'description' => 'New vendor ' . $vendor->company_name . ' was added',
                    'date' => $vendor->created_at,
                    'icon' => 'building-office',
                    'timestamp' => $vendor->created_at->timestamp
                ];
            });

        // Get recent invoices (vouchers with invoice items)
        $recentInvoicesActivity = Voucher::where('tenant_id', $tenant->id)
            ->where('status', Voucher::STATUS_POSTED)
            ->whereHas('items') // Only vouchers with invoice items
            ->with(['voucherType', 'items'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($voucher) {
                $totalAmount = $voucher->items->sum('total');
                return (object)[
                    'type' => 'invoice_created',
                    'description' => $voucher->voucherType->name . ' #' . $voucher->voucher_number . ' - ₦' . number_format($totalAmount, 2),
                    'date' => $voucher->created_at,
                    'icon' => 'document',
                    'timestamp' => $voucher->created_at->timestamp
                ];
            });

        // Get recent payment vouchers (receipts)
        $recentPaymentsActivity = Voucher::where('tenant_id', $tenant->id)
            ->where('status', Voucher::STATUS_POSTED)
            ->whereNotNull('posted_at')
            ->whereHas('voucherType', function($query) {
                $query->where('code', 'RCV') // Receipt vouchers
                      ->orWhere('affects_cashbank', true);
            })
            ->with('voucherType')
            ->orderBy('posted_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($voucher) {
                return (object)[
                    'type' => 'payment_received',
                    'description' => 'Payment received - ' . $voucher->voucherType->name . ' #' . $voucher->voucher_number . ' - ₦' . number_format($voucher->total_amount, 2),
                    'date' => $voucher->posted_at,
                    'icon' => 'cash',
                    'timestamp' => $voucher->posted_at->timestamp
                ];
            });

        // Get recent vouchers
        $recentVouchersActivity = Voucher::where('tenant_id', $tenant->id)
            ->where('status', Voucher::STATUS_POSTED)
            ->whereNotNull('posted_at')
            ->with('voucherType')
            ->orderBy('posted_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($voucher) {
                return (object)[
                    'type' => 'voucher_posted',
                    'description' => $voucher->voucherType->name . ' #' . $voucher->voucher_number . ' was posted',
                    'date' => $voucher->posted_at,
                    'icon' => 'document-text',
                    'timestamp' => $voucher->posted_at->timestamp
                ];
            });

        // Merge all activities and sort by date
        $recentActivities = $activities
            ->merge($recentCustomersActivity)
            ->merge($recentVendorsActivity)
            ->merge($recentInvoicesActivity)
            ->merge($recentPaymentsActivity)
            ->merge($recentVouchersActivity)
            ->sortByDesc('timestamp')
            ->take(10)
            ->values();

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
