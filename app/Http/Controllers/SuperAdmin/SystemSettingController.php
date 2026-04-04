<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::getAllGrouped();

        return view('super-admin.system.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $tab = $request->input('tab', 'general');

        $rules = $this->getValidationRules($tab);
        $validated = $request->validate($rules);

        // Remove the tab field
        unset($validated['tab']);

        // Handle boolean fields (unchecked checkboxes won't be in request)
        $booleanFields = $this->getBooleanFields($tab);
        foreach ($booleanFields as $field) {
            $validated[$field] = $request->has($field) ? '1' : '0';
        }

        SystemSetting::bulkUpdate($validated);

        return redirect()->route('super-admin.system.settings', ['tab' => $tab])
            ->with('success', ucfirst($tab) . ' settings updated successfully.');
    }

    protected function getValidationRules(string $tab): array
    {
        return match ($tab) {
            'general' => [
                'tab' => 'required|string',
                'app_name' => 'required|string|max:100',
                'app_tagline' => 'nullable|string|max:255',
                'support_email' => 'nullable|email|max:255',
                'support_phone' => 'nullable|string|max:30',
                'default_currency' => 'required|string|max:5',
                'default_timezone' => 'required|string|max:50',
            ],
            'registration' => [
                'tab' => 'required|string',
                'default_trial_days' => 'required|integer|min:0|max:365',
                'max_companies_per_user' => 'required|integer|min:1|max:50',
            ],
            'payment' => [
                'tab' => 'required|string',
            ],
            'maintenance' => [
                'tab' => 'required|string',
                'maintenance_message' => 'nullable|string|max:1000',
                'maintenance_allowed_ips' => 'nullable|string|max:1000',
            ],
            'security' => [
                'tab' => 'required|string',
                'max_login_attempts' => 'required|integer|min:1|max:20',
                'lockout_duration_minutes' => 'required|integer|min:1|max:1440',
                'force_password_change_days' => 'required|integer|min:0|max:365',
            ],
            default => ['tab' => 'required|string'],
        };
    }

    protected function getBooleanFields(string $tab): array
    {
        return match ($tab) {
            'registration' => ['registration_enabled', 'affiliate_registration_enabled', 'require_email_verification'],
            'payment' => ['paystack_enabled', 'nomba_enabled'],
            'maintenance' => ['maintenance_mode'],
            'security' => ['two_factor_enforcement'],
            default => [],
        };
    }
}
