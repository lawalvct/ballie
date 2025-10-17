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
            'monthly_revenue' => 0,
        ];

        $recentTenants = Tenant::latest()->take(5)->get();

        // Group tenants by plan slug. The legacy subscription_plan column was removed
        // in favor of plan_id (foreign key to the plans table).
        $subscriptionStats = Tenant::join('plans', 'plans.id', '=', 'tenants.plan_id')
            ->selectRaw('plans.slug as plan_slug, COUNT(*) as count')
            ->whereNotNull('tenants.plan_id')
            ->groupBy('plans.slug')
            ->pluck('count', 'plan_slug');

        return view('super-admin.dashboard', compact('stats', 'recentTenants', 'subscriptionStats'));
    }
}