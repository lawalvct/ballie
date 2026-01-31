<?php

namespace App\Providers;

use App\Services\BrandService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class BrandServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(BrandService::class, function ($app) {
            return new BrandService();
        });

        $this->app->alias(BrandService::class, 'brand.service');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register view namespaces for each brand
        $brands = config('brands.brands', []);
        foreach ($brands as $brandKey => $brandConfig) {
            $brandViewPath = resource_path("views/brands/{$brandKey}");
            if (is_dir($brandViewPath)) {
                View::addNamespace($brandKey, $brandViewPath);
            }
        }

        // Register Blade directives for brand-specific content
        Blade::directive('brand', function ($expression) {
            return "<?php echo app(\\App\\Services\\BrandService::class)->name(); ?>";
        });

        Blade::directive('brandLogo', function ($expression) {
            return "<?php echo app(\\App\\Services\\BrandService::class)->logo(); ?>";
        });

        Blade::directive('brandCompany', function ($expression) {
            return "<?php echo app(\\App\\Services\\BrandService::class)->company(); ?>";
        });

        Blade::directive('ifBrand', function ($expression) {
            return "<?php if(app(\\App\\Services\\BrandService::class)->is({$expression})): ?>";
        });

        Blade::directive('endifBrand', function () {
            return "<?php endif; ?>";
        });

        // Blade directive to include brand-specific view with fallback
        Blade::directive('brandView', function ($expression) {
            return "<?php echo \$__env->make(app(\\App\\Services\\BrandService::class)->view({$expression}), \\Illuminate\\Support\\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>";
        });

        // Share brand helper globally in views
        view()->composer('*', function ($view) {
            $view->with('brandService', app(BrandService::class));
        });
    }
}
