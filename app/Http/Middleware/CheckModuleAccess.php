<?php

namespace App\Http\Middleware;

use App\Services\ModuleRegistry;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware that blocks access to routes belonging to disabled modules.
 *
 * Usage in routes: ->middleware('module:inventory')
 *
 * If the module is disabled for the current tenant, returns 403.
 * Core modules (dashboard, accounting, admin, settings, support, help)
 * always pass — they cannot be disabled.
 *
 * Safe for existing tenants: tenants without enabled_modules
 * default to 'hybrid' (all modules enabled).
 */
class CheckModuleAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $tenant = tenant();

        if (!$tenant) {
            return $next($request); // No tenant context, skip check
        }

        if (!ModuleRegistry::isModuleEnabled($tenant, $module)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'This module is not enabled for your business.',
                    'module' => $module,
                ], 403);
            }

            abort(403, 'This module is not enabled for your business. You can enable it from Settings > Company > Modules.');
        }

        return $next($request);
    }
}
