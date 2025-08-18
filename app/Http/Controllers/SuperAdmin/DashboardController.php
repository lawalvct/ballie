<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\SuperAdmin;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_tenants' => Tenant::count(),
            'active_tenants' => Tenant::where('subscription_status', 'active')->count(),
            'trial_tenants' => Tenant::where('subscription_status', 'trial')->count(),
            'monthly_revenue' => Tenant::where('subscription_status', 'active')
                ->whereMonth('created_at', now()->month)
                ->sum('subscription_amount') ?? 0,
        ];

        $recentTenants = Tenant::latest()->take(5)->get();

        $subscriptionStats = Tenant::selectRaw('subscription_plan, COUNT(*) as count')
            ->whereNotNull('subscription_plan')
            ->groupBy('subscription_plan')
            ->pluck('count', 'subscription_plan');

        return view('super-admin.dashboard', compact('stats', 'recentTenants', 'subscriptionStats'));
    }
}
