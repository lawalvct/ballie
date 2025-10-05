<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Helpers\TenantHelper;

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
            }
        });
    }
}
