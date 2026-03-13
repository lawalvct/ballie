<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\SubscriptionPayment;
use App\Models\SupportTicket;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        // Core counts
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('subscription_status', 'active')->count();
        $trialTenants = Tenant::where('subscription_status', 'trial')->count();
        $suspendedTenants = Tenant::where('subscription_status', 'suspended')->count();
        $totalUsers = User::count();

        // Revenue: successful payments this month (amount stored in kobo)
        $monthlyRevenue = SubscriptionPayment::where('status', 'successful')
            ->where('paid_at', '>=', $startOfMonth)
            ->sum('amount');

        $lastMonthRevenue = SubscriptionPayment::where('status', 'successful')
            ->whereBetween('paid_at', [$startOfLastMonth, $endOfLastMonth])
            ->sum('amount');

        $revenueGrowth = $lastMonthRevenue > 0
            ? round((($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : ($monthlyRevenue > 0 ? 100 : 0);

        // Tenant growth: new this month vs last month
        $newTenantsThisMonth = Tenant::where('created_at', '>=', $startOfMonth)->count();
        $newTenantsLastMonth = Tenant::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();

        $tenantGrowth = $newTenantsLastMonth > 0
            ? round((($newTenantsThisMonth - $newTenantsLastMonth) / $newTenantsLastMonth) * 100, 1)
            : ($newTenantsThisMonth > 0 ? 100 : 0);

        $stats = [
            'total_tenants' => $totalTenants,
            'active_tenants' => $activeTenants,
            'trial_tenants' => $trialTenants,
            'suspended_tenants' => $suspendedTenants,
            'total_users' => $totalUsers,
            'monthly_revenue' => $monthlyRevenue / 100, // Convert kobo to naira
            'last_month_revenue' => $lastMonthRevenue / 100,
            'revenue_growth' => $revenueGrowth,
            'tenant_growth' => $tenantGrowth,
            'new_tenants_this_month' => $newTenantsThisMonth,
        ];

        // Tenant registration trend (last 12 months)
        $tenantTrend = collect(range(11, 0))->map(function ($monthsAgo) {
            $date = Carbon::now()->subMonths($monthsAgo);
            return [
                'month' => $date->format('M Y'),
                'label' => $date->format('M'),
                'count' => Tenant::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        });

        // Revenue trend (last 6 months)
        $revenueTrend = collect(range(5, 0))->map(function ($monthsAgo) {
            $date = Carbon::now()->subMonths($monthsAgo);
            $revenue = SubscriptionPayment::where('status', 'successful')
                ->whereYear('paid_at', $date->year)
                ->whereMonth('paid_at', $date->month)
                ->sum('amount');
            return [
                'month' => $date->format('M Y'),
                'label' => $date->format('M'),
                'amount' => round($revenue / 100, 2), // kobo to naira
            ];
        });

        // Subscription status distribution (for doughnut chart)
        $statusDistribution = Tenant::selectRaw('subscription_status, COUNT(*) as count')
            ->whereNotNull('subscription_status')
            ->groupBy('subscription_status')
            ->pluck('count', 'subscription_status');

        // Plan distribution
        $subscriptionStats = Tenant::join('plans', 'plans.id', '=', 'tenants.plan_id')
            ->selectRaw('plans.name as plan_name, plans.slug as plan_slug, COUNT(*) as count')
            ->whereNotNull('tenants.plan_id')
            ->groupBy('plans.name', 'plans.slug')
            ->get();

        // Support ticket stats
        $ticketStats = [
            'open' => SupportTicket::whereIn('status', ['new', 'open', 'in_progress'])->count(),
            'unresolved' => SupportTicket::whereNotIn('status', ['resolved', 'closed'])->count(),
            'resolved_this_month' => SupportTicket::where('status', 'resolved')
                ->where('resolved_at', '>=', $startOfMonth)
                ->count(),
        ];

        // Expiring soon (within 7 days)
        $expiringSoon = Tenant::where('subscription_status', 'active')
            ->whereBetween('subscription_ends_at', [$now, $now->copy()->addDays(7)])
            ->orderBy('subscription_ends_at')
            ->take(5)
            ->get();

        $recentTenants = Tenant::with('plan')->latest()->take(5)->get();

        return view('super-admin.dashboard', compact(
            'stats',
            'recentTenants',
            'subscriptionStats',
            'tenantTrend',
            'revenueTrend',
            'statusDistribution',
            'ticketStats',
            'expiringSoon'
        ));
    }
}
