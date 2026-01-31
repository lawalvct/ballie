# Multi-Brand White-Label System

This document describes the multi-brand architecture implemented for supporting multiple company brands (Ballie, Budlite, etc.) from a single codebase.

## Quick Start: Creating a New Brand

Follow these steps to add a new brand (e.g., "Acme Corp"):

### Step 1: Add Brand Configuration

Edit `config/brands.php` and add your brand:

```php
'acme' => [
    'key' => 'acme',
    'name' => 'Acme',
    'company' => 'Acme Corporation',
    'tagline' => 'Business Management Suite',
    'description' => 'Acme helps businesses manage operations efficiently.',
    'support_email' => 'support@acme.com',
    'whatsapp' => '2341234567890',  // or null to hide WhatsApp button

    // Feature Flags - Control what features are visible
    'show_pricing' => true,           // Show pricing page link
    'allow_registration' => true,     // Allow public registration (Get Started button)
    'show_demo' => true,              // Show demo link
    'show_affiliate' => true,         // Show affiliate program link

    // Domain Detection
    'domains' => [
        'acme.com',
        'www.acme.com',
        'acme.test',              // Local testing
    ],

    // Theme Colors
    'theme' => [
        'primary' => '#FF6B00',       // Primary brand color
        'secondary' => '#FF9500',     // Secondary/accent color
        'gradient_from' => '#FF6B00', // Gradient start
        'gradient_via' => '#FF8000',  // Gradient middle
        'gradient_to' => '#FF9500',   // Gradient end
    ],

    // Assets
    'logo' => 'brands/acme/logo.png',
    'logo_fallback' => 'images/ballie_logo.png',
    'favicon' => 'brands/acme/favicon.ico',

    // Views
    'landing_view' => 'brands.acme.welcome',  // or just 'welcome' to use default

    // SEO
    'meta' => [
        'keywords' => 'business management, accounting, inventory, ERP',
    ],
],
```

### Step 2: Create Brand Assets Directory

```bash
mkdir -p public/brands/acme
```

Add your brand assets:

- `public/brands/acme/logo.png` - Main logo
- `public/brands/acme/favicon.ico` - Favicon

### Step 3: Create Brand-Specific Views (Optional)

For a fully branded experience, create brand-specific layouts:

```
resources/views/brands/acme/
├── layouts/
│   ├── app.blade.php              ← Main layout (includes header/footer)
│   └── website/
│       ├── header.blade.php       ← Brand-specific header
│       └── footer.blade.php       ← Brand-specific footer
└── welcome.blade.php              ← Landing page
```

**Minimal `layouts/app.blade.php`:**

```blade
{{-- Acme Brand Layout --}}
@include('brands.acme.layouts.website.header')

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

@include('brands.acme.layouts.website.footer')
```

**`welcome.blade.php` should extend the brand layout:**

```blade
@extends('brands.acme.layouts.app')

@section('title', 'Acme - Your Business Solution')
@section('description', 'Acme helps businesses succeed.')

@section('content')
    {{-- Your landing page content --}}
@endsection
```

### Step 4: Test Your Brand

**Option A: Use .env override (recommended for development)**

```env
BRAND=acme
```

**Option B: Add local domain to hosts file**

```
# Windows: C:\Windows\System32\drivers\etc\hosts
# Linux/Mac: /etc/hosts
127.0.0.1 acme.test
```

### Step 5: Clear Cache

```bash
php artisan optimize:clear
php artisan view:clear
```

### Step 6: Verify

Visit your application and confirm:

- [ ] Logo displays correctly
- [ ] Brand name appears in title and header
- [ ] Theme colors are applied
- [ ] Feature flags work (Pricing hidden if `show_pricing: false`)
- [ ] Landing page loads the correct view

---

## Overview

The system uses domain-based detection with `.env` override support to serve different brand experiences from the same application.

## Architecture

### Brand Detection Flow

```
Request → DetectBrand Middleware → Check .env BRAND → Check Domain → Use Default
```

1. **Environment Override**: If `BRAND=budlite` is set in `.env`, that brand is always used
2. **Domain Detection**: Otherwise, the request domain is matched against configured brand domains
3. **Default Fallback**: Falls back to the default brand (ballie) if no match

### Key Components

| Component  | Path                                     | Purpose                                 |
| ---------- | ---------------------------------------- | --------------------------------------- |
| Config     | `config/brands.php`                      | Central brand configuration             |
| Middleware | `app/Http/Middleware/DetectBrand.php`    | Brand detection and caching             |
| Service    | `app/Services/BrandService.php`          | Brand helper methods                    |
| Provider   | `app/Providers/BrandServiceProvider.php` | Service registration & Blade directives |
| Facade     | `app/Facades/Brand.php`                  | Static access to BrandService           |

## Configuration

### Brand Configuration (`config/brands.php`)

```php
return [
    'default' => env('BRAND', 'ballie'),

    'brands' => [
        'ballie' => [
            'key' => 'ballie',
            'name' => 'Ballie',
            'company' => 'Ballie Technology Company',
            'tagline' => 'Nigerian Business Management Software',
            'description' => 'Comprehensive business management software...',
            'support_email' => 'support@ballie.co',
            'whatsapp' => '2348132712715',

            // Feature Flags
            'show_pricing' => true,           // Show pricing page link
            'allow_registration' => true,     // Allow public registration
            'show_demo' => true,              // Show demo link
            'show_affiliate' => true,         // Show affiliate program

            'domains' => ['ballie.co', 'www.ballie.co', 'localhost'],
            'theme' => [
                'primary' => '#4F46E5',
                'secondary' => '#7C3AED',
                'gradient_from' => '#3c2c64',
                'gradient_via' => '#4a3570',
                'gradient_to' => '#614c80',
            ],
            'logo' => 'brands/ballie/logo.png',
            'landing_view' => 'welcome',
        ],
        'budlite' => [
            'key' => 'budlite',
            'name' => 'Budlite',
            'company' => 'Budlite Group of Companies',

            // Feature Flags - Enterprise brand (no public registration)
            'show_pricing' => false,          // No public pricing page
            'allow_registration' => false,    // Companies created via super admin
            'show_demo' => false,             // No demo link
            'show_affiliate' => false,        // No affiliate program

            'domains' => ['budlite.ng', 'www.budlite.ng'],
            'landing_view' => 'brands.budlite.welcome',
            // ... similar structure
        ],
    ],
];
```

### Feature Flags

| Flag                 | Purpose                          | When `true`                      | When `false`          |
| -------------------- | -------------------------------- | -------------------------------- | --------------------- |
| `show_pricing`       | Controls pricing page visibility | Pricing link shown in nav/footer | Pricing link hidden   |
| `allow_registration` | Controls public signup           | "Get Started" button shown       | Only "Login" shown    |
| `show_demo`          | Controls demo link visibility    | Demo link in footer              | Demo link hidden      |
| `show_affiliate`     | Controls affiliate program       | Affiliate link in footer         | Affiliate link hidden |

### Environment Override

Set in `.env` to force a specific brand:

```env
BRAND=budlite
```

## Usage

### In Controllers

```php
use App\Facades\Brand;

// Get brand info
$brandName = Brand::name();         // "Ballie" or "Budlite"
$company = Brand::company();        // Full company name
$logo = Brand::logo();              // URL to logo
$primaryColor = Brand::primaryColor();

// Check current brand
if (Brand::is('budlite')) {
    // Budlite-specific logic
}
```

### In Blade Templates

```blade
{{-- Using the $brand variable (auto-shared) --}}
<h1>{{ $brand['name'] }}</h1>
<p>{{ $brand['description'] }}</p>

{{-- Using the BrandService --}}
<img src="{{ $brandService->logo() }}" alt="{{ $brandService->name() }}">

{{-- Using Blade directives --}}
<h1>@brand</h1>
<img src="@brandLogo" alt="Logo">
<p>@brandCompany</p>

{{-- Conditional content --}}
@ifBrand('budlite')
    <p>Welcome to Budlite!</p>
@endifBrand
```

### In CSS (Theme Colors)

```blade
<style>
    :root {
        --brand-primary: {{ $brand['theme']['primary'] ?? '#4F46E5' }};
        --brand-secondary: {{ $brand['theme']['secondary'] ?? '#7C3AED' }};
    }
</style>
```

## Brand-Specific Views

### Directory Structure

```
resources/views/brands/{brand-key}/
├── layouts/
│   ├── app.blade.php              ← Main layout for the brand
│   └── website/
│       ├── header.blade.php       ← Brand header with navigation
│       └── footer.blade.php       ← Brand footer with links
├── welcome.blade.php              ← Landing page
└── {other-views}.blade.php        ← Any other brand-specific views
```

### How View Resolution Works

The `BrandService::view()` method resolves views with automatic fallback:

```php
// In BrandService.php
public function view(string $view): string
{
    $brandView = "brands.{$this->key()}.{$view}";

    if (view()->exists($brandView)) {
        return $brandView;  // Use brand-specific view
    }

    return $view;  // Fall back to default view
}
```

### Using Brand-Aware Layouts

In your Blade templates, extend the brand-aware layout:

```blade
{{-- This automatically uses brand-specific layout if it exists --}}
@extends($brandService->view('layouts.app'))

@section('content')
    {{-- Your content --}}
@endsection
```

### Landing Pages

Each brand can have its own landing page. The `HomeController::welcome()` method loads the correct view:

```php
public function welcome()
{
    $brand = app()->bound('brand') ? app('brand') : null;
    $view = $brand['landing_view'] ?? 'welcome';

    if (!view()->exists($view)) {
        $view = 'welcome';
    }

    return view($view, compact('brand'));
}
```

### View Override Pattern

For any view, create a brand-specific version:

```
resources/views/brands/{brand-key}/path/to/view.blade.php
```

Example: To override the login page for Budlite:

```
resources/views/brands/budlite/auth/login.blade.php
```

## Brand Assets

Store brand-specific assets in:

```
public/brands/
├── ballie/
│   ├── logo.png
│   ├── favicon.ico
│   └── README.md
└── budlite/
    ├── logo.png
    ├── favicon.ico
    └── README.md
```

The `BrandService::logo()` method automatically falls back to the default logo if the brand-specific logo doesn't exist.

## Adding a New Brand

> **Quick Reference**: See the "Quick Start: Creating a New Brand" section at the top for a step-by-step guide.

### Checklist

- [ ] Add brand config to `config/brands.php`
- [ ] Create assets directory `public/brands/{brand}/`
- [ ] Add logo and favicon
- [ ] Create brand-specific views (optional)
- [ ] Add domains to brand config
- [ ] Clear cache: `php artisan optimize:clear`
- [ ] Test with `.env` override or local domain

### Required Files

| File                                                              | Required | Purpose             |
| ----------------------------------------------------------------- | -------- | ------------------- |
| `config/brands.php` entry                                         | ✅ Yes   | Brand configuration |
| `public/brands/{brand}/logo.png`                                  | ✅ Yes   | Brand logo          |
| `resources/views/brands/{brand}/welcome.blade.php`                | Optional | Custom landing page |
| `resources/views/brands/{brand}/layouts/app.blade.php`            | Optional | Custom layout       |
| `resources/views/brands/{brand}/layouts/website/header.blade.php` | Optional | Custom header       |
| `resources/views/brands/{brand}/layouts/website/footer.blade.php` | Optional | Custom footer       |

### Brand Types

**SaaS Brand (Public Registration)**

```php
'show_pricing' => true,
'allow_registration' => true,
'show_demo' => true,
'show_affiliate' => true,
```

**Enterprise Brand (Admin-Provisioned)**

```php
'show_pricing' => false,
'allow_registration' => false,
'show_demo' => false,
'show_affiliate' => false,
```

## Deployment Options

### Option A: Single Server, Multiple Domains

Point all brand domains to the same server. Brand detection happens automatically via domain matching.

```nginx
server {
    server_name ballie.co www.ballie.co budlite.ng www.budlite.ng;
    # ... standard Laravel config
}
```

### Option B: Separate Servers, Same Codebase

Deploy the same codebase to different servers. Set `BRAND=brandname` in each server's `.env`.

```
# Server 1 (Ballie)
BRAND=ballie

# Server 2 (Budlite)
BRAND=budlite
```

## Caching

The middleware caches brand detection per-request using:

```php
$request->attributes->set('brand', $brand);
app()->instance('brand', $brand);
view()->share('brand', $brand);
```

This ensures brand detection only happens once per request.

## Testing

To test different brands locally:

1. **Add local domains to hosts file**:

```
127.0.0.1 ballie.test
127.0.0.1 budlite.test
```

2. **Configure domains in config/brands.php**:

```php
'domains' => ['ballie.test', 'localhost'],
```

3. **Or use .env override**:

```env
BRAND=budlite
```

## Available Brands

| Brand   | Key       | Domains              | Theme Color      | Registration  |
| ------- | --------- | -------------------- | ---------------- | ------------- |
| Ballie  | `ballie`  | ballie.co, localhost | Indigo (#4F46E5) | ✅ Public     |
| Budlite | `budlite` | budlite.ng           | Teal (#0F766E)   | ❌ Admin Only |

## Files Modified/Created

### Core Files

| File                                     | Purpose                                                   |
| ---------------------------------------- | --------------------------------------------------------- |
| `config/brands.php`                      | Central brand configuration with feature flags            |
| `app/Http/Middleware/DetectBrand.php`    | Domain detection and brand resolution                     |
| `app/Services/BrandService.php`          | Brand helper methods (`name()`, `logo()`, `view()`, etc.) |
| `app/Providers/BrandServiceProvider.php` | Service registration, view namespaces, Blade directives   |
| `app/Facades/Brand.php`                  | Static facade for easy access                             |

### View Files

| File                                                              | Purpose              |
| ----------------------------------------------------------------- | -------------------- |
| `resources/views/brands/budlite/welcome.blade.php`                | Budlite landing page |
| `resources/views/brands/budlite/layouts/app.blade.php`            | Budlite main layout  |
| `resources/views/brands/budlite/layouts/website/header.blade.php` | Budlite header       |
| `resources/views/brands/budlite/layouts/website/footer.blade.php` | Budlite footer       |

### Modified Files

| File                                               | Changes                                             |
| -------------------------------------------------- | --------------------------------------------------- |
| `config/app.php`                                   | Added BrandServiceProvider and Brand facade         |
| `app/Http/Kernel.php`                              | Added DetectBrand middleware                        |
| `app/Http/Controllers/HomeController.php`          | Brand-aware landing view                            |
| `resources/views/layouts/website/header.blade.php` | Dynamic brand meta/logo, feature flags              |
| `resources/views/layouts/website/footer.blade.php` | Dynamic brand info, conditional links               |
| `resources/views/auth/login.blade.php`             | Brand-aware login (logo, colors, registration link) |

## Troubleshooting

### Brand Not Detected

1. Check domain is listed in brand config: `config/brands.php`
2. Clear config cache: `php artisan config:clear`
3. Try `.env` override: `BRAND=brandname`

### Views Not Loading

1. Clear view cache: `php artisan view:clear`
2. Check view path matches: `resources/views/brands/{key}/...`
3. Verify `landing_view` in config points to correct view

### Assets Not Found

1. Check asset exists: `public/brands/{key}/logo.png`
2. Verify path in config matches actual file location
3. Check file permissions

### Feature Flags Not Working

1. Ensure flags are set in brand config
2. Use correct variable in views: `$brand['show_pricing']` or `$brandService->toArray()['show_pricing']`
3. Clear all caches: `php artisan optimize:clear`
