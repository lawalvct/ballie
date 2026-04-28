<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAnyPermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (! auth()->check()) {
            return redirect()->route('tenant.login', tenant('slug'));
        }

        foreach ($permissions as $permission) {
            if (auth()->user()->hasPermission($permission)) {
                return $next($request);
            }
        }

        abort(403, 'You do not have permission to access this resource.');
    }
}
