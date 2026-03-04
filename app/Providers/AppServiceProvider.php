<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use App\Helpers\TenantHelper;
use App\Services\ModuleRegistry;
use App\Services\TerminologyService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share tenant with all views
        View::composer('*', function ($view) {
            $tenant = tenant(); // Use the tenant() helper function
            if ($tenant) {
                $view->with('tenantHelper', new TenantHelper());
                $view->with('tenant', $tenant);
                $view->with('term', new TerminologyService($tenant));
                $view->with('enabledModules', ModuleRegistry::getEnabledModules($tenant));
            }
        });

        // Register @module / @endmodule Blade directive for module visibility
        Blade::if('module', function (string $module) {
            $tenant = tenant();
            if (!$tenant) {
                return true; // No tenant context = show everything
            }
            return ModuleRegistry::isModuleEnabled($tenant, $module);
        });

        // Register @term('key') Blade directive for category-aware terminology
        Blade::directive('term', function ($expression) {
            return "<?php echo \App\Services\TerminologyService::resolve({$expression}, tenant()); ?>";
        });
    }
}
