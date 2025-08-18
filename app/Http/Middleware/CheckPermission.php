<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!auth()->check()) {
            return redirect()->route('tenant.login');
        }

        // For now, just allow all authenticated users
        // You can implement proper permission checking here
        // Example: if (!auth()->user()->hasPermission($permission)) {
        //     abort(403, 'Access denied.');
        // }

        return $next($request);
    }
}
