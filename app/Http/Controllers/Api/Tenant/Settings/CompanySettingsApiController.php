<?php

namespace App\Http\Controllers\Api\Tenant\Settings;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Tenant;
use App\Services\ModuleRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CompanySettingsApiController extends BaseApiController
{
    /**
     * Get all company settings.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->forbidden('Only tenant owners can access company settings.');
        }

        $tenant = $user->tenant;

        return $this->success([
            'company' => $this->formatCompanyInfo($tenant),
            'business' => $this->formatBusinessDetails($tenant),
            'branding' => $this->formatBranding($tenant),
            'preferences' => $this->formatPreferences($tenant),
            'modules' => ModuleRegistry::getAllModulesWithMeta($tenant),
            'business_category' => $tenant->getBusinessCategory(),
        ]);
    }

    /**
     * Get company information only.
     */
    public function companyInfo(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->forbidden('Only tenant owners can access company settings.');
        }

        return $this->success([
            'company' => $this->formatCompanyInfo($user->tenant),
        ]);
    }

    /**
     * Update company information.
     */
    public function updateCompanyInfo(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->forbidden('Only tenant owners can update company settings.');
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $tenant = $user->tenant;
        $tenant->update($validator->validated());

        return $this->success([
            'company' => $this->formatCompanyInfo($tenant->fresh()),
        ], 'Company information updated successfully.');
    }

    /**
     * Get business details only.
     */
    public function businessDetails(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->forbidden('Only tenant owners can access company settings.');
        }

        return $this->success([
            'business' => $this->formatBusinessDetails($user->tenant),
        ]);
    }

    /**
     * Update business details.
     */
    public function updateBusinessDetails(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->forbidden('Only tenant owners can update business details.');
        }

        $validator = Validator::make($request->all(), [
            'business_type' => ['nullable', 'string', 'max:100'],
            'business_registration_number' => ['nullable', 'string', 'max:100'],
            'tax_identification_number' => ['nullable', 'string', 'max:100'],
            'fiscal_year_start' => ['nullable', 'date_format:Y-m-d'],
            'payment_terms' => ['nullable', 'integer', 'min:0', 'max:365'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $tenant = $user->tenant;
        $tenant->update($validator->validated());

        return $this->success([
            'business' => $this->formatBusinessDetails($tenant->fresh()),
        ], 'Business details updated successfully.');
    }

    /**
     * Get branding info (logo).
     */
    public function branding(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->forbidden('Only tenant owners can access company settings.');
        }

        return $this->success([
            'branding' => $this->formatBranding($user->tenant),
        ]);
    }

    /**
     * Upload company logo.
     */
    public function updateLogo(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->forbidden('Only tenant owners can update company logo.');
        }

        $validator = Validator::make($request->all(), [
            'logo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $tenant = $user->tenant;

        // Delete old logo if exists
        if ($tenant->logo && Storage::disk('public')->exists($tenant->logo)) {
            Storage::disk('public')->delete($tenant->logo);
        }

        $logoPath = $request->file('logo')->store('logos', 'public');
        $tenant->update(['logo' => $logoPath]);

        return $this->success([
            'branding' => $this->formatBranding($tenant->fresh()),
        ], 'Company logo updated successfully.');
    }

    /**
     * Remove company logo.
     */
    public function removeLogo(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->forbidden('Only tenant owners can remove company logo.');
        }

        $tenant = $user->tenant;

        if ($tenant->logo && Storage::disk('public')->exists($tenant->logo)) {
            Storage::disk('public')->delete($tenant->logo);
        }

        $tenant->update(['logo' => null]);

        return $this->success([
            'branding' => $this->formatBranding($tenant->fresh()),
        ], 'Company logo removed successfully.');
    }

    /**
     * Get preferences only.
     */
    public function preferences(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->forbidden('Only tenant owners can access company settings.');
        }

        return $this->success([
            'preferences' => $this->formatPreferences($user->tenant),
        ]);
    }

    /**
     * Update preferences.
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->forbidden('Only tenant owners can update preferences.');
        }

        $validator = Validator::make($request->all(), [
            'currency' => ['nullable', 'string', 'max:10'],
            'currency_symbol' => ['nullable', 'string', 'max:5'],
            'date_format' => ['nullable', 'string', 'max:20'],
            'time_format' => ['nullable', 'string', 'max:20'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'language' => ['nullable', 'string', 'max:10'],
            'invoice_template' => ['nullable', 'string', 'in:ballie,tally,zoho,sage,quickbooks'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $tenant = $user->tenant;
        $settings = $tenant->settings ?? [];
        $settings = array_merge($settings, $validator->validated());
        $tenant->update(['settings' => $settings]);

        return $this->success([
            'preferences' => $this->formatPreferences($tenant->fresh()),
        ], 'Preferences updated successfully.');
    }

    /**
     * Get modules list with metadata.
     */
    public function modules(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->forbidden('Only tenant owners can access module settings.');
        }

        $tenant = $user->tenant;

        return $this->success([
            'modules' => ModuleRegistry::getAllModulesWithMeta($tenant),
            'business_category' => $tenant->getBusinessCategory(),
        ]);
    }

    /**
     * Update enabled modules.
     */
    public function updateModules(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->forbidden('Only tenant owners can manage modules.');
        }

        $validator = Validator::make($request->all(), [
            'modules' => ['required', 'array'],
            'modules.*' => ['string', 'in:' . implode(',', ModuleRegistry::ALL_MODULES)],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $tenant = $user->tenant;

        // Always ensure core modules are included
        $enabledModules = array_values(array_unique(array_merge(
            ModuleRegistry::CORE_MODULES,
            $validator->validated()['modules']
        )));

        $tenant->update(['enabled_modules' => $enabledModules]);

        return $this->success([
            'modules' => ModuleRegistry::getAllModulesWithMeta($tenant->fresh()),
            'business_category' => $tenant->getBusinessCategory(),
        ], 'Module settings updated successfully.');
    }

    /**
     * Reset modules to category defaults.
     */
    public function resetModules(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->forbidden('Only tenant owners can manage modules.');
        }

        $tenant = $user->tenant;
        $tenant->update(['enabled_modules' => null]);

        return $this->success([
            'modules' => ModuleRegistry::getAllModulesWithMeta($tenant->fresh()),
            'business_category' => $tenant->getBusinessCategory(),
        ], 'Modules reset to category defaults successfully.');
    }

    // ─── Formatters ──────────────────────────────────────────

    private function formatCompanyInfo(Tenant $tenant): array
    {
        return [
            'name' => $tenant->name,
            'slug' => $tenant->slug,
            'email' => $tenant->email,
            'phone' => $tenant->phone,
            'website' => $tenant->website,
            'address' => $tenant->address,
            'city' => $tenant->city,
            'state' => $tenant->state,
            'country' => $tenant->country,
        ];
    }

    private function formatBusinessDetails(Tenant $tenant): array
    {
        return [
            'business_type' => $tenant->business_type,
            'business_registration_number' => $tenant->business_registration_number,
            'tax_identification_number' => $tenant->tax_identification_number,
            'fiscal_year_start' => $tenant->fiscal_year_start,
            'payment_terms' => $tenant->payment_terms,
        ];
    }

    private function formatBranding(Tenant $tenant): array
    {
        return [
            'logo' => $tenant->logo ? asset('storage/' . $tenant->logo) : null,
            'logo_path' => $tenant->logo,
        ];
    }

    private function formatPreferences(Tenant $tenant): array
    {
        $settings = $tenant->settings ?? [];

        return [
            'currency' => $settings['currency'] ?? 'NGN',
            'currency_symbol' => $settings['currency_symbol'] ?? '₦',
            'date_format' => $settings['date_format'] ?? 'd/m/Y',
            'time_format' => $settings['time_format'] ?? '12',
            'timezone' => $settings['timezone'] ?? 'Africa/Lagos',
            'language' => $settings['language'] ?? 'en',
        ];
    }
}
