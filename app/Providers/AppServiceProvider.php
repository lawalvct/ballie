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
        // Share tenant helper with all views
        View::composer('*', function ($view) {
            if (app()->bound('tenant')) {
                $view->with('tenantHelper', new TenantHelper());
            }
        });
    }
}
