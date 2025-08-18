<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SuperAdmin;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminImpersonation
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if super admin is impersonating a user
        if ($request->session()->has('impersonating_user_id')) {
            $userId = $request->session()->get('impersonating_user_id');
            $superAdminId = $request->session()->get('super_admin_id');

            $user = User::find($userId);
            $superAdmin = SuperAdmin::find($superAdminId);

            if ($user && $superAdmin && $superAdmin->canImpersonate()) {
                Auth::guard('web')->login($user);

                // Add impersonation indicator to views
                view()->share('impersonating', [
                    'user' => $user,
                    'super_admin' => $superAdmin
                ]);
            }
        }

        return $next($request);
    }
}
