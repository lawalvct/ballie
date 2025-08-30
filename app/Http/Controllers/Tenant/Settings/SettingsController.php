<?php

namespace App\Http\Controllers\Tenant\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;

class SettingsController extends Controller
{
    /**
     * Display the settings dashboard
     */
    public function index(Request $request, Tenant $tenant)
    {
        $currentTenant = $tenant;
        $user = auth()->user();

        // You would typically load settings data here
        // For example:
        // $companySettings = Setting::where('tenant_id', $tenant->id)->where('category', 'company')->get();
        // $systemSettings = Setting::where('tenant_id', $tenant->id)->where('category', 'system')->get();
        // $userPreferences = UserPreference::where('user_id', $user->id)->get();

        return view('tenant.settings.index', [
            'currentTenant' => $currentTenant,
            'user' => $user,
            'tenant' => $currentTenant,
        ]);
    }
}
