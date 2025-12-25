<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcommerceSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'is_store_enabled',
        'store_name',
        'store_description',
        'store_logo',
        'store_banner',
        'allow_guest_checkout',
        'allow_email_registration',
        'allow_google_login',
        'require_phone_number',
        'default_currency',
        'tax_enabled',
        'tax_percentage',
        'shipping_enabled',
        'meta_title',
        'meta_description',
        'social_facebook',
        'social_instagram',
        'social_twitter',
        'theme_primary_color',
        'theme_secondary_color',
        'payment_gateway_settings',
    ];

    protected $casts = [
        'is_store_enabled' => 'boolean',
        'allow_guest_checkout' => 'boolean',
        'allow_email_registration' => 'boolean',
        'allow_google_login' => 'boolean',
        'require_phone_number' => 'boolean',
        'tax_enabled' => 'boolean',
        'tax_percentage' => 'decimal:2',
        'shipping_enabled' => 'boolean',
        'payment_gateway_settings' => 'array',
    ];

    /**
     * Get the tenant that owns the settings
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get Paystack settings
     */
    public function getPaystackSettings()
    {
        return $this->payment_gateway_settings['paystack'] ?? [];
    }

    /**
     * Get Flutterwave settings
     */
    public function getFlutterwaveSettings()
    {
        return $this->payment_gateway_settings['flutterwave'] ?? [];
    }
}
