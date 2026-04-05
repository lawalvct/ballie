<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Tenant;
use App\Services\ModuleRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanySettingsController extends Controller
{
    /**
     * Display company settings page
     */
    public function index(Request $request, Tenant $tenant)
    {
        // Check if user is owner
        if (!$request->user()->isOwner()) {
            abort(403, 'Only tenant owners can access company settings.');
        }

        return view('tenant.settings.company', [
            'tenant' => $tenant,
            'allModules' => ModuleRegistry::getAllModulesWithMeta($tenant),
            'businessCategory' => $tenant->getBusinessCategory(),
            'bankAccounts' => Bank::where('tenant_id', $tenant->id)->active()->get(),
        ]);
    }

    /**
     * Update company information
     */
    public function updateCompanyInfo(Request $request, Tenant $tenant)
    {
        // Check if user is owner
        if (!$request->user()->isOwner()) {
            abort(403, 'Only tenant owners can update company settings.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
        ]);

        $tenant->update($validated);

        return redirect()
            ->route('tenant.settings.company', ['tenant' => $tenant->slug])
            ->with('success', 'Company information updated successfully!');
    }

    /**
     * Update business details
     */
    public function updateBusinessDetails(Request $request, Tenant $tenant)
    {
        // Check if user is owner
        if (!$request->user()->isOwner()) {
            abort(403, 'Only tenant owners can update business details.');
        }

        $validated = $request->validate([
            'business_type' => ['nullable', 'string', 'max:100'],
            'business_registration_number' => ['nullable', 'string', 'max:100'],
            'tax_identification_number' => ['nullable', 'string', 'max:100'],
            'fiscal_year_start' => ['nullable', 'date_format:Y-m-d'],
            'payment_terms' => ['nullable', 'integer', 'min:0', 'max:365'],
        ]);

        $tenant->update($validated);

        return redirect()
            ->route('tenant.settings.company', ['tenant' => $tenant->slug])
            ->with('success', 'Business details updated successfully!');
    }

    /**
     * Update company logo
     */
    public function updateLogo(Request $request, Tenant $tenant)
    {
        // Check if user is owner
        if (!$request->user()->isOwner()) {
            abort(403, 'Only tenant owners can update company logo.');
        }

        $request->validate([
            'logo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);

        // Delete old logo if exists
        if ($tenant->logo && Storage::disk('public')->exists($tenant->logo)) {
            Storage::disk('public')->delete($tenant->logo);
        }

        // Store new logo
        $logoPath = $request->file('logo')->store('logos', 'public');
        $tenant->update(['logo' => $logoPath]);

        return redirect()
            ->route('tenant.settings.company', ['tenant' => $tenant->slug])
            ->with('success', 'Company logo updated successfully!');
    }

    /**
     * Remove company logo
     */
    public function removeLogo(Request $request, Tenant $tenant)
    {
        // Check if user is owner
        if (!$request->user()->isOwner()) {
            abort(403, 'Only tenant owners can remove company logo.');
        }

        // Delete logo file if exists
        if ($tenant->logo && Storage::disk('public')->exists($tenant->logo)) {
            Storage::disk('public')->delete($tenant->logo);
        }

        $tenant->update(['logo' => null]);

        return redirect()
            ->route('tenant.settings.company', ['tenant' => $tenant->slug])
            ->with('success', 'Company logo removed successfully!');
    }

    /**
     * Upload company signature
     */
    public function updateSignature(Request $request, Tenant $tenant)
    {
        if (!$request->user()->isOwner()) {
            abort(403, 'Only tenant owners can update company signature.');
        }

        $request->validate([
            'signature' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Delete old signature if exists
        if ($tenant->signature && Storage::disk('public')->exists($tenant->signature)) {
            Storage::disk('public')->delete($tenant->signature);
        }

        $signaturePath = $request->file('signature')->store('signatures', 'public');
        $tenant->update(['signature' => $signaturePath]);

        return redirect()
            ->route('tenant.settings.company', ['tenant' => $tenant->slug])
            ->with('success', 'Company signature updated successfully!');
    }

    /**
     * Remove company signature
     */
    public function removeSignature(Request $request, Tenant $tenant)
    {
        if (!$request->user()->isOwner()) {
            abort(403, 'Only tenant owners can remove company signature.');
        }

        if ($tenant->signature && Storage::disk('public')->exists($tenant->signature)) {
            Storage::disk('public')->delete($tenant->signature);
        }

        $tenant->update(['signature' => null]);

        return redirect()
            ->route('tenant.settings.company', ['tenant' => $tenant->slug])
            ->with('success', 'Company signature removed successfully!');
    }

    /**
     * Update company preferences
     */
    public function updatePreferences(Request $request, Tenant $tenant)
    {
        // Check if user is owner
        if (!$request->user()->isOwner()) {
            abort(403, 'Only tenant owners can update preferences.');
        }

        $validated = $request->validate([
            'currency' => ['nullable', 'string', 'max:10'],
            'currency_symbol' => ['nullable', 'string', 'max:5'],
            'date_format' => ['nullable', 'string', 'max:20'],
            'time_format' => ['nullable', 'string', 'max:20'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'language' => ['nullable', 'string', 'max:10'],
            'invoice_template' => ['nullable', 'string', 'in:ballie,tally,zoho,sage,quickbooks'],
            'invoice_terms' => ['nullable', 'string', 'max:2000'],
            'invoice_bank_account_id' => ['nullable', 'integer', 'exists:banks,id'],
        ]);

        // Separate invoice_terms and invoice_bank_account_id from other settings
        $invoiceTerms = $validated['invoice_terms'] ?? null;
        $invoiceBankAccountId = $validated['invoice_bank_account_id'] ?? null;
        unset($validated['invoice_terms'], $validated['invoice_bank_account_id']);

        // Update settings array
        $settings = $tenant->settings ?? [];
        $settings = array_merge($settings, $validated);
        $settings['invoice_terms'] = $invoiceTerms;
        $settings['invoice_bank_account_id'] = $invoiceBankAccountId;

        $tenant->update(['settings' => $settings]);

        return redirect()
            ->route('tenant.settings.company', ['tenant' => $tenant->slug])
            ->with('success', 'Preferences updated successfully!');
    }

    /**
     * Update enabled modules
     */
    public function updateModules(Request $request, Tenant $tenant)
    {
        // Check if user is owner
        if (!$request->user()->isOwner()) {
            abort(403, 'Only tenant owners can manage modules.');
        }

        $validated = $request->validate([
            'modules'   => ['required', 'array'],
            'modules.*' => ['string', 'in:' . implode(',', ModuleRegistry::ALL_MODULES)],
        ]);

        // Always ensure core modules are included
        $enabledModules = array_values(array_unique(array_merge(
            ModuleRegistry::CORE_MODULES,
            $validated['modules']
        )));

        $tenant->update(['enabled_modules' => $enabledModules]);

        return redirect()
            ->route('tenant.settings.company', ['tenant' => $tenant->slug])
            ->with('success', 'Module settings updated successfully!');
    }

    /**
     * Reset modules to category defaults
     */
    public function resetModules(Request $request, Tenant $tenant)
    {
        // Check if user is owner
        if (!$request->user()->isOwner()) {
            abort(403, 'Only tenant owners can manage modules.');
        }

        // Setting to null reverts to category defaults
        $tenant->update(['enabled_modules' => null]);

        return redirect()
            ->route('tenant.settings.company', ['tenant' => $tenant->slug])
            ->with('success', 'Modules reset to category defaults successfully!');
    }
}
