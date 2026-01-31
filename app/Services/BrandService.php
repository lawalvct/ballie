<?php

namespace App\Services;

class BrandService
{
    protected ?array $brand = null;

    /**
     * Get current brand configuration
     */
    public function get(): ?array
    {
        if ($this->brand !== null) {
            return $this->brand;
        }

        if (app()->bound('brand')) {
            $this->brand = app('brand');
            return $this->brand;
        }

        return $this->getDefault();
    }

    /**
     * Get default brand
     */
    public function getDefault(): array
    {
        $default = config('brands.default', 'ballie');
        return config("brands.brands.{$default}", [
            'key' => 'ballie',
            'name' => 'Ballie',
            'company' => 'Ballie Technology Company',
            'landing_view' => 'welcome',
        ]);
    }

    /**
     * Get brand key
     */
    public function key(): string
    {
        return $this->get()['key'] ?? 'ballie';
    }

    /**
     * Get brand name
     */
    public function name(): string
    {
        return $this->get()['name'] ?? 'Ballie';
    }

    /**
     * Get brand company name
     */
    public function company(): string
    {
        return $this->get()['company'] ?? 'Ballie Technology Company';
    }

    /**
     * Get brand logo path
     */
    public function logo(): string
    {
        $brand = $this->get();
        $logoPath = $brand['logo'] ?? 'images/ballie_logo.png';

        // Check if brand-specific logo exists, fallback to default
        if (file_exists(public_path($logoPath))) {
            return asset($logoPath);
        }

        return asset('images/ballie_logo.png');
    }

    /**
     * Get brand favicon path
     */
    public function favicon(): string
    {
        $brand = $this->get();
        $faviconPath = $brand['favicon'] ?? 'favicon.ico';

        if (file_exists(public_path($faviconPath))) {
            return asset($faviconPath);
        }

        return asset('favicon.ico');
    }

    /**
     * Get primary theme color
     */
    public function primaryColor(): string
    {
        return $this->get()['theme']['primary'] ?? '#4F46E5';
    }

    /**
     * Get secondary theme color
     */
    public function secondaryColor(): string
    {
        return $this->get()['theme']['secondary'] ?? '#7C3AED';
    }

    /**
     * Check if current brand is a specific brand
     */
    public function is(string $brandKey): bool
    {
        return $this->key() === $brandKey;
    }

    /**
     * Get brand-specific tagline
     */
    public function tagline(): string
    {
        $brand = $this->get();
        return $brand['tagline'] ?? 'Business Management Software';
    }

    /**
     * Get brand support email
     */
    public function supportEmail(): string
    {
        $brand = $this->get();
        return $brand['support_email'] ?? 'support@' . strtolower($this->key()) . '.co';
    }

    /**
     * Get brand WhatsApp number
     */
    public function whatsapp(): ?string
    {
        return $this->get()['whatsapp'] ?? null;
    }

    /**
     * Resolve a view path with brand-specific override support
     * Returns brand-specific view if it exists, otherwise falls back to default
     *
     * @param string $view The base view path (e.g., 'layouts.app' or 'welcome')
     * @return string The resolved view path
     */
    public function view(string $view): string
    {
        $brandKey = $this->key();

        // Try brand-specific view first (e.g., brands.budlite.layouts.app)
        $brandView = "brands.{$brandKey}.{$view}";

        if (view()->exists($brandView)) {
            return $brandView;
        }

        // Fall back to default view
        return $view;
    }

    /**
     * Get the landing view for this brand
     */
    public function landingView(): string
    {
        return $this->get()['landing_view'] ?? 'welcome';
    }

    /**
     * Get all brand configuration as array
     */
    public function toArray(): array
    {
        return $this->get() ?? [];
    }
}
