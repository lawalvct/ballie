<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!SystemSetting::getValue('maintenance_mode', false)) {
            return $next($request);
        }

        // Always allow super-admin routes
        if ($request->is('super-admin/*') || $request->is('super-admin')) {
            return $next($request);
        }

        // Allow whitelisted IPs
        $allowedIps = SystemSetting::getValue('maintenance_allowed_ips', '');
        if ($allowedIps) {
            $ips = array_filter(array_map('trim', explode("\n", $allowedIps)));
            if (in_array($request->ip(), $ips)) {
                return $next($request);
            }
        }

        $message = SystemSetting::getValue('maintenance_message', 'We are currently performing scheduled maintenance. Please check back soon.');

        return response()->view('errors.maintenance', [
            'message' => $message,
        ], 503);
    }
}
