<?php

namespace App\Http\Controllers\Api\Tenant\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\EcommerceSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class SettingsController extends Controller
{
    /**
     * Get e-commerce settings
     */
    public function index(Request $request, Tenant $tenant)
    {
        try {
            $settings = EcommerceSetting::where('tenant_id', $tenant->id)->first()
                ?? new EcommerceSetting(['tenant_id' => $tenant->id]);

            $storeUrl = url('/' . $tenant->slug . '/store');

            return response()->json([
                'success' => true,
                'data' => [
                    'is_store_enabled' => (bool) $settings->is_store_enabled,
                    'store_name' => $settings->store_name,
                    'store_description' => $settings->store_description,
                    'store_logo_url' => $settings->store_logo ? Storage::disk('public')->url($settings->store_logo) : null,
                    'store_banner_url' => $settings->store_banner ? Storage::disk('public')->url($settings->store_banner) : null,
                    'store_url' => $storeUrl,
                    'allow_guest_checkout' => (bool) $settings->allow_guest_checkout,
                    'allow_email_registration' => (bool) $settings->allow_email_registration,
                    'allow_google_login' => (bool) $settings->allow_google_login,
                    'require_phone_number' => (bool) $settings->require_phone_number,
                    'default_currency' => $settings->default_currency,
                    'tax_enabled' => (bool) $settings->tax_enabled,
                    'tax_percentage' => $settings->tax_percentage,
                    'shipping_enabled' => (bool) $settings->shipping_enabled,
                    'meta_title' => $settings->meta_title,
                    'meta_description' => $settings->meta_description,
                    'social_facebook' => $settings->social_facebook,
                    'social_instagram' => $settings->social_instagram,
                    'social_twitter' => $settings->social_twitter,
                    'theme_primary_color' => $settings->theme_primary_color,
                    'theme_secondary_color' => $settings->theme_secondary_color,
                ],
            ]);
        } catch (Exception $e) {
            Log::error('E-commerce settings API error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to load settings.',
            ], 500);
        }
    }

    /**
     * Update e-commerce settings
     */
    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'is_store_enabled' => 'nullable|boolean',
            'store_name' => 'required|string|max:255',
            'store_description' => 'nullable|string',
            'store_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'store_banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'allow_guest_checkout' => 'nullable|boolean',
            'allow_email_registration' => 'nullable|boolean',
            'allow_google_login' => 'nullable|boolean',
            'require_phone_number' => 'nullable|boolean',
            'default_currency' => 'required|string|max:3',
            'tax_enabled' => 'nullable|boolean',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'shipping_enabled' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'social_facebook' => 'nullable|url',
            'social_instagram' => 'nullable|url',
            'social_twitter' => 'nullable|url',
            'theme_primary_color' => 'nullable|string|max:7',
            'theme_secondary_color' => 'nullable|string|max:7',
        ]);

        try {
            $settings = EcommerceSetting::where('tenant_id', $tenant->id)->first()
                ?? new EcommerceSetting(['tenant_id' => $tenant->id]);

            // Handle booleans from JSON (already boolean, no checkbox logic)
            $validated['is_store_enabled'] = $request->boolean('is_store_enabled', false);
            $validated['allow_guest_checkout'] = $request->boolean('allow_guest_checkout', false);
            $validated['allow_email_registration'] = $request->boolean('allow_email_registration', false);
            $validated['allow_google_login'] = $request->boolean('allow_google_login', false);
            $validated['require_phone_number'] = $request->boolean('require_phone_number', false);
            $validated['tax_enabled'] = $request->boolean('tax_enabled', false);
            $validated['shipping_enabled'] = $request->boolean('shipping_enabled', false);

            // Handle logo upload
            if ($request->hasFile('store_logo')) {
                if ($settings->store_logo) {
                    Storage::disk('public')->delete($settings->store_logo);
                }
                $validated['store_logo'] = $request->file('store_logo')->store('ecommerce/logos', 'public');
            }

            // Handle banner upload
            if ($request->hasFile('store_banner')) {
                if ($settings->store_banner) {
                    Storage::disk('public')->delete($settings->store_banner);
                }
                $validated['store_banner'] = $request->file('store_banner')->store('ecommerce/banners', 'public');
            }

            $settings = EcommerceSetting::updateOrCreate(
                ['tenant_id' => $tenant->id],
                $validated
            );

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully.',
                'data' => [
                    'is_store_enabled' => (bool) $settings->is_store_enabled,
                    'store_name' => $settings->store_name,
                    'store_url' => url('/' . $tenant->slug . '/store'),
                ],
            ]);
        } catch (Exception $e) {
            Log::error('E-commerce settings update API error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings.',
            ], 500);
        }
    }

    /**
     * Generate store QR code
     */
    public function generateQrCode(Request $request, Tenant $tenant)
    {
        try {
            $storeUrl = url('/' . $tenant->slug . '/store');

            $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(300)
                ->margin(2)
                ->generate($storeUrl);

            return response()->json([
                'success' => true,
                'data' => [
                    'qr_code' => (string) $qrCode,
                    'store_url' => $storeUrl,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate QR code.',
            ], 500);
        }
    }
}
