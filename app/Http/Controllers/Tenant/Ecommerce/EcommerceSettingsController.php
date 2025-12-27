<?php

namespace App\Http\Controllers\Tenant\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\EcommerceSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EcommerceSettingsController extends Controller
{
    public function index(Request $request)
    {
        $tenant = tenant();
        $settings = $tenant->ecommerceSettings ?? new EcommerceSetting(['tenant_id' => $tenant->id]);

        return view('tenant.ecommerce.settings.index', compact('tenant', 'settings'));
    }

    public function update(Request $request)
    {
        $tenant = tenant();

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

        // Convert null to false for boolean fields
        $validated['is_store_enabled'] = $request->has('is_store_enabled');
        $validated['allow_guest_checkout'] = $request->has('allow_guest_checkout');
        $validated['allow_email_registration'] = $request->has('allow_email_registration');
        $validated['allow_google_login'] = $request->has('allow_google_login');
        $validated['require_phone_number'] = $request->has('require_phone_number');
        $validated['tax_enabled'] = $request->has('tax_enabled');
        $validated['shipping_enabled'] = $request->has('shipping_enabled');

        $settings = $tenant->ecommerceSettings ?? new EcommerceSetting(['tenant_id' => $tenant->id]);

        // Handle logo upload
        if ($request->hasFile('store_logo')) {
            // Delete old logo if exists
            if ($settings->store_logo) {
                Storage::disk('public')->delete($settings->store_logo);
            }
            $validated['store_logo'] = $request->file('store_logo')->store('ecommerce/logos', 'public');
        }

        // Handle banner upload
        if ($request->hasFile('store_banner')) {
            // Delete old banner if exists
            if ($settings->store_banner) {
                Storage::disk('public')->delete($settings->store_banner);
            }
            $validated['store_banner'] = $request->file('store_banner')->store('ecommerce/banners', 'public');
        }

        $tenant->ecommerceSettings()->updateOrCreate(
            ['tenant_id' => $tenant->id],
            $validated
        );

        return redirect()->back()->with('success', 'E-commerce settings updated successfully!');
    }

    public function generateQrCode(Request $request)
    {
        $tenant = tenant();
        $storeUrl = url('/' . $tenant->slug . '/store');

        $qrCode = QrCode::size(300)
            ->margin(2)
            ->generate($storeUrl);

        return response()->json([
            'success' => true,
            'qr_code' => (string) $qrCode,
            'store_url' => $storeUrl
        ]);
    }
}
