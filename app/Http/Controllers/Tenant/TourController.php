<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TourController extends Controller
{
    /**
     * Start the tour
     */
    public function start(Request $request, Tenant $tenant)
    {
        $user = Auth::user();

        // Mark tour as started
        $user->update(['tour_completed' => false]);

        return redirect()->route('tenant.tour.dashboard', ['tenant' => $tenant->slug]);
    }

    /**
     * Dashboard tour step
     */
    public function dashboard(Request $request, Tenant $tenant)
    {
        return view('tenant.tour.dashboard', [
            'tenant' => $tenant,
            'currentStep' => 1,
            'totalSteps' => 8
        ]);
    }

    /**
     * Customers tour step
     */
    public function customers(Request $request, Tenant $tenant)
    {
        return view('tenant.tour.customers', [
            'tenant' => $tenant,
            'currentStep' => 2,
            'totalSteps' => 8
        ]);
    }

    /**
     * Products tour step
     */
    public function products(Request $request, Tenant $tenant)
    {
        return view('tenant.tour.products', [
            'tenant' => $tenant,
            'currentStep' => 3,
            'totalSteps' => 8
        ]);
    }

    /**
     * Sales tour step
     */
    public function sales(Request $request, Tenant $tenant)
    {
        return view('tenant.tour.sales', [
            'tenant' => $tenant,
            'currentStep' => 4,
            'totalSteps' => 8
        ]);
    }

    /**
     * Inventory tour step
     */
    public function inventory(Request $request, Tenant $tenant)
    {
        return view('tenant.tour.inventory', [
            'tenant' => $tenant,
            'currentStep' => 5,
            'totalSteps' => 8
        ]);
    }

    /**
     * Accounting tour step
     */
    public function accounting(Request $request, Tenant $tenant)
    {
        return view('tenant.tour.accounting', [
            'tenant' => $tenant,
            'currentStep' => 6,
            'totalSteps' => 8
        ]);
    }

    /**
     * Reports tour step
     */
    public function reports(Request $request, Tenant $tenant)
    {
        return view('tenant.tour.reports', [
            'tenant' => $tenant,
            'currentStep' => 7,
            'totalSteps' => 8
        ]);
    }

    /**
     * Settings tour step
     */
    public function settings(Request $request, Tenant $tenant)
    {
        return view('tenant.tour.settings', [
            'tenant' => $tenant,
            'currentStep' => 8,
            'totalSteps' => 8
        ]);
    }

    /**
     * Complete the tour
     */
    public function complete(Request $request, Tenant $tenant)
    {
        $user = Auth::user();

        // Mark tour as completed
        $user->update(['tour_completed' => true]);

        return redirect()->route('tenant.dashboard', ['tenant' => $tenant->slug])
            ->with('success', 'Congratulations! You\'ve completed the tour. Start managing your business now!');
    }

    /**
     * Skip the tour
     */
    public function skip(Request $request, Tenant $tenant)
    {
        $user = Auth::user();

        // Mark tour as completed (skipped)
        $user->update(['tour_completed' => true]);

        return redirect()->route('tenant.dashboard', ['tenant' => $tenant->slug])
            ->with('info', 'Tour skipped. You can restart it anytime from Settings.');
    }
}
