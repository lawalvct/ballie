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

        // Provide $authUrls to the shared auth views so they work in both
        // central and tenant routing contexts.
        View::composer([
            'auth.login',
            'auth.register',
            'auth.forgot-password',
            'auth.reset-password',
        ], function ($view) {
            $request = request();
            $routeTenant = $request ? $request->route('tenant') : null;
            $boundTenant = function_exists('tenant') ? tenant() : null;
            $tenantRef = $routeTenant ?: $boundTenant;
            $tenantSlug = is_object($tenantRef) ? ($tenantRef->slug ?? null) : $tenantRef;
            $isTenant = ! empty($tenantSlug) && \Illuminate\Support\Facades\Route::has('tenant.login');

            $resolve = function (string $tenantName, string $centralName, string $fallbackPath) use ($isTenant, $tenantSlug) {
                if ($isTenant) {
                    return \Illuminate\Support\Facades\Route::has($tenantName)
                        ? route($tenantName, ['tenant' => $tenantSlug])
                        : url($tenantSlug . $fallbackPath);
                }

                return \Illuminate\Support\Facades\Route::has($centralName)
                    ? route($centralName)
                    : url($fallbackPath);
            };

            $view->with('authUrls', [
                'login'            => $resolve('tenant.login', 'login', '/login'),
                'login_post'       => $resolve('tenant.login', 'login', '/login'),
                'register'         => $resolve('tenant.register', 'register', '/register'),
                'register_post'    => $resolve('tenant.register', 'register', '/register'),
                'password_request' => $resolve('tenant.password.request', 'password.request', '/forgot-password'),
                'password_email'   => $resolve('tenant.password.email', 'password.email', '/forgot-password'),
                'password_update'  => $isTenant
                    ? (\Illuminate\Support\Facades\Route::has('tenant.password.update')
                        ? route('tenant.password.update', ['tenant' => $tenantSlug])
                        : url($tenantSlug . '/reset-password'))
                    : (\Illuminate\Support\Facades\Route::has('password.store')
                        ? route('password.store')
                        : (\Illuminate\Support\Facades\Route::has('password.update')
                            ? route('password.update')
                            : url('/reset-password'))),
                'auth_google'      => $isTenant
                    ? (\Illuminate\Support\Facades\Route::has('tenant.auth.google')
                        ? route('tenant.auth.google', ['tenant' => $tenantSlug])
                        : '#')
                    : (\Illuminate\Support\Facades\Route::has('auth.google') ? route('auth.google') : '#'),
                'auth_facebook'    => $isTenant
                    ? (\Illuminate\Support\Facades\Route::has('tenant.auth.facebook')
                        ? route('tenant.auth.facebook', ['tenant' => $tenantSlug])
                        : '#')
                    : (\Illuminate\Support\Facades\Route::has('auth.facebook') ? route('auth.facebook') : '#'),
            ]);
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
