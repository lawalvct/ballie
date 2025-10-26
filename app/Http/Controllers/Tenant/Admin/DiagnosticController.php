<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class DiagnosticController extends Controller
{
    /**
     * Diagnostic endpoint to check route registration and user access
     *
     * Access via: {tenant}/admin/diagnostic/routes
     */
    public function checkRoutes()
    {
        $diagnostics = [];

        // 1. Check if routes exist
        $routeNames = [
            'tenant.admin.users.index',
            'tenant.admin.users.show',
            'tenant.admin.users.edit',
            'tenant.admin.users.create',
            'tenant.admin.users.store',
            'tenant.admin.users.update',
            'tenant.admin.users.destroy',
        ];

        $diagnostics['route_registration'] = [];
        foreach ($routeNames as $routeName) {
            $exists = Route::has($routeName);
            $diagnostics['route_registration'][$routeName] = [
                'exists' => $exists,
                'uri' => $exists ? Route::getRoutes()->getByName($routeName)?->uri() : null,
                'action' => $exists ? Route::getRoutes()->getByName($routeName)?->getActionName() : null,
            ];
        }

        // 2. Check tenant context
        $diagnostics['tenant_context'] = [
            'tenant_id' => tenant('id'),
            'tenant_slug' => tenant('slug'),
            'tenant_name' => tenant('name'),
        ];

        // 3. Check if we can query users
        try {
            $userCount = User::where('tenant_id', tenant()->id)->count();
            $firstUser = User::where('tenant_id', tenant()->id)->first();

            $diagnostics['user_query'] = [
                'success' => true,
                'total_users' => $userCount,
                'first_user_id' => $firstUser?->id,
                'first_user_name' => $firstUser?->name,
            ];
        } catch (\Exception $e) {
            $diagnostics['user_query'] = [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }

        // 4. Generate test URLs
        if (isset($firstUser)) {
            $diagnostics['generated_urls'] = [
                'show_url' => route('tenant.admin.users.show', [tenant('slug'), $firstUser->id]),
                'edit_url' => route('tenant.admin.users.edit', [tenant('slug'), $firstUser->id]),
            ];
        }

        // 5. Log results
        Log::info('Route Diagnostic Results', $diagnostics);

        // Return as JSON for easy reading
        return response()->json($diagnostics, 200, [], JSON_PRETTY_PRINT);
    }

    /**
     * Test accessing a specific user by ID
     *
     * Access via: {tenant}/admin/diagnostic/user/{userId}
     */
    public function testUserAccess($userId)
    {
        $diagnostics = [];

        try {
            // Attempt to find user with tenant scope
            $user = User::where('tenant_id', tenant()->id)
                ->findOrFail($userId);

            $diagnostics['success'] = true;
            $diagnostics['user'] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'tenant_id' => $user->tenant_id,
                'created_at' => $user->created_at,
            ];
            $diagnostics['tenant'] = [
                'current_tenant_id' => tenant('id'),
                'current_tenant_slug' => tenant('slug'),
                'match' => $user->tenant_id === tenant('id'),
            ];

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $diagnostics['success'] = false;
            $diagnostics['error'] = 'User not found in current tenant scope';
            $diagnostics['searched_user_id'] = $userId;
            $diagnostics['current_tenant_id'] = tenant('id');

        } catch (\Exception $e) {
            $diagnostics['success'] = false;
            $diagnostics['error'] = $e->getMessage();
        }

        Log::info('User Access Test', $diagnostics);

        return response()->json($diagnostics, 200, [], JSON_PRETTY_PRINT);
    }
}
