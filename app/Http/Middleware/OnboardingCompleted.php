<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant;

class OnboardingCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $request->route('tenant');

        // If tenant is a string (slug), fetch the Tenant model
        if (is_string($tenant)) {
            $tenant = Tenant::where('slug', $tenant)->firstOrFail();
        }

        // Check if tenant is a Tenant model instance
        if (!$tenant instanceof Tenant) {
            abort(404, 'Tenant not found');
        }

        if (!$tenant->onboarding_completed_at) {
            return redirect()->route('tenant.onboarding.index', ['tenant' => $tenant->slug])
                ->with('warning', 'Please complete the onboarding process first.');
        }

        return $next($request);
    }
}
