<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Tenant\Role;
use App\Models\Tenant\Permission;
use App\Models\Tenant\Team;
use App\Services\AdminService;
use App\Http\Requests\Admin\CreateUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Requests\Admin\CreateRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Models\Tenant;
use Exception;

class AdminController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
        $this->middleware('auth');
        
        // Apply permission middleware
        $this->middleware('permission:admin.users.manage')->only([
            'users', 'createUser', 'storeUser', 'showUser', 'editUser', 'updateUser', 'destroyUser', 'toggleUserStatus'
        ]);
        
        $this->middleware('permission:admin.roles.manage')->only([
            'roles', 'createRole', 'storeRole', 'showRole', 'editRole', 'updateRole', 'destroyRole', 'cloneRole', 'permissionMatrix'
        ]);
        
        $this->middleware('permission:admin.permissions.manage')->only([
            'permissions', 'createPermission', 'storePermission', 'showPermission', 'editPermission', 'updatePermission', 'destroyPermission'
        ]);
    }

    /**
     * Admin Dashboard
     */
    public function index()
    {
        $stats = $this->adminService->getDashboardStats();

        // Get recent users for the dashboard table (limit to 10)
        $users = User::with(['roles'])
            ->where('tenant_id', tenant()->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('tenant.admin.index', compact('stats', 'users'));
    }

    // ==================== USERS MANAGEMENT ====================

    /**
     * Display users list
     */
    public function users(Request $request)
    {
        $query = User::with(['roles'])
            ->where('tenant_id', tenant()->id);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->latest()->paginate(15);

        // Get tenant-specific roles for filter dropdown
        $roles = Role::where('tenant_id', tenant()->id)
            ->where('is_active', true)
            ->orderBy('priority', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        // Calculate statistics
        $stats = [
            'total_users' => User::where('tenant_id', tenant()->id)->count(),
            'active_users' => User::where('tenant_id', tenant()->id)->where('is_active', true)->count(),
            'pending_users' => User::where('tenant_id', tenant()->id)->where('is_active', false)->whereNull('email_verified_at')->count(),
            'online_users' => User::where('tenant_id', tenant()->id)->where('last_login_at', '>=', now()->subMinutes(15))->count(),
        ];

        return view('tenant.admin.users.index', compact('users', 'roles', 'stats'));
    }

    /**
     * Show create user form
     */
    public function createUser()
    {
        // Get tenant-specific roles
        $roles = Role::where('tenant_id', tenant()->id)
            ->where('is_active', true)
            ->orderBy('priority', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return view('tenant.admin.users.create', compact('roles'));
    }

    /**
     * Store new user
     */
    public function storeUser(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive',
            'send_welcome_email' => 'nullable|boolean',
            'force_password_change' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            // Get the tenant ID properly
            $tenantId = tenant() ? tenant()->id : null;

            // Create the user
            $user = User::create([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'tenant_id' => $tenantId,
                'is_active' => $validated['status'] === 'active',
                'email_verified_at' => now(), // Auto-verify for admin-created users
            ]);

            // Assign the role
            if ($request->filled('role_id')) {
                $user->roles()->attach($validated['role_id']);
            }

            // Send welcome email if requested
            if ($request->boolean('send_welcome_email')) {
                try {
                    // TODO: Implement welcome email
                    // $this->adminService->sendUserInvitation($user, $validated['password']);
                } catch (Exception $e) {
                    // Log the error but don't fail the user creation
                    Log::error('Failed to send welcome email', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('tenant.admin.users.index', tenant('slug'))
                ->with('success', 'User created successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error creating user: ' . $e->getMessage());
        }
    }

    /**
     * Show user details
     */
    public function showUser($tenant, $userId)
    {
        // Find user within the current tenant scope
        $user = User::where('tenant_id', tenant()->id)
            //->with(['roles.permissions', 'teams'])
            ->findOrFail($userId);

        // $this->authorize('view', $user);

        $activityLogs = $this->adminService->getUserActivityLogs($user->id);

        // Ensure activityLogs is always a collection
        if (!$activityLogs instanceof \Illuminate\Support\Collection) {
            $activityLogs = collect($activityLogs ?? []);
        }

        return view('tenant.admin.users.show', compact('user', 'activityLogs'));
    }

    /**
     * Show edit user form
     */
    public function editUser($tenant, $userId)
    {
        // Find user within the current tenant scope
        $user = User::where('tenant_id', tenant()->id)
            ->findOrFail($userId);

        // $this->authorize('update', $user);

        // Get tenant-specific roles
        $roles = Role::where('tenant_id', tenant()->id)
            ->where('is_active', true)
            ->orderBy('priority', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        $user->load('roles');

        return view('tenant.admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update user
     */
    public function updateUser(UpdateUserRequest $request, $tenant, $userId)
    {
        // Find user within the current tenant scope
        $user = User::where('tenant_id', tenant()->id)
            ->findOrFail($userId);

        // $this->authorize('update', $user);

        try {
            DB::beginTransaction();

            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'is_active' => $request->boolean('is_active', true),
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            // Update roles
            if ($request->filled('roles')) {
                $user->roles()->sync($request->roles);
            } else {
                $user->roles()->detach();
            }

            DB::commit();

            return redirect()->route('tenant.admin.users.index', tenant('slug'))
                ->with('success', 'User updated successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error updating user: ' . $e->getMessage());
        }
    }

    /**
     * Delete user
     */
    public function destroyUser($tenant, $userId)
    {
        // Find user within the current tenant scope
        $user = User::where('tenant_id', tenant()->id)
            ->findOrFail($userId);

        // $this->authorize('delete', $user);

        try {
            if ($user->id === auth()->id()) {
                return back()->with('error', 'You cannot delete your own account!');
            }

            $user->delete();

            return redirect()->route('tenant.admin.users.index', tenant('slug'))
                ->with('success', 'User deleted successfully!');

        } catch (Exception $e) {
            return back()->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }

    /**
     * Toggle user status
     */
    public function toggleUserStatus($tenant, $userId)
    {
        // Find user within the current tenant scope
        $user = User::where('tenant_id', tenant()->id)
            ->findOrFail($userId);

        // $this->authorize('update', $user);

        try {
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot deactivate your own account!'
                ]);
            }

            $user->update(['is_active' => !$user->is_active]);

            return response()->json([
                'success' => true,
                'message' => 'User status updated successfully!',
                'status' => $user->is_active
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating user status: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Reset user password
     */
    public function resetUserPassword(User $user)
    {
        $this->authorize('update', $user);

        try {
            $newPassword = Str::random(10);
            $user->update(['password' => Hash::make($newPassword)]);

            // Send new password via email
            $this->adminService->sendPasswordReset($user, $newPassword);

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully! New password sent via email.'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error resetting password: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Send invitation to user
     */
    public function sendInvitation(User $user)
    {
        try {
            $this->adminService->sendUserInvitation($user);

            return response()->json([
                'success' => true,
                'message' => 'Invitation sent successfully!'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending invitation: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export users
     */
    public function exportUsers(Request $request)
    {
        try {
            return $this->adminService->exportUsers($request->all());
        } catch (Exception $e) {
            return back()->with('error', 'Error exporting users: ' . $e->getMessage());
        }
    }

    /**
     * Bulk user actions
     */
    public function bulkUserAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        try {
            $result = $this->adminService->bulkUserAction($request->action, $request->user_ids);

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'processed' => $result['processed']
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing bulk action: ' . $e->getMessage()
            ]);
        }
    }

    // ==================== ROLES MANAGEMENT ====================

    /**
     * Display roles list
     */
    public function roles()
    {
        $roles = Role::with(['permissions', 'users'])
            ->where('tenant_id', tenant()->id)
            ->latest()
            ->paginate(15);

        $stats = [
            'total_roles' => Role::where('tenant_id', tenant()->id)->count(),
            'active_roles' => Role::where('tenant_id', tenant()->id)->where('is_active', true)->count(),
            'users_with_roles' => User::where('tenant_id', tenant()->id)->whereHas('roles')->count(),
            'total_permissions' => Permission::count(),
        ];

        return view('tenant.admin.roles.index', compact('roles', 'stats'));
    }

    /**
     * Show create role form
     */
    public function createRole()
    {
        $permissions = Permission::all()->groupBy('module');
        return view('tenant.admin.roles.create', compact('permissions'));
    }

    /**
     * Store new role
     */
    public function storeRole(CreateRoleRequest $request)
    {
        try {
            DB::beginTransaction();

            $role = Role::create([
                'name' => $request->name,
                'description' => $request->description,
                'tenant_id' => tenant()->id,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Assign permissions
            if ($request->filled('permissions')) {
                $role->permissions()->sync($request->permissions);
            }

            DB::commit();

            return redirect()->route('tenant.admin.roles.index', tenant('slug'))
                ->with('success', 'Role created successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error creating role: ' . $e->getMessage());
        }
    }

    /**
     * Show role details
     */
    public function showRole($tenant, $roleId)
    {
        $role = Role::where('tenant_id', tenant()->id)
            ->with(['permissions', 'users'])
            ->findOrFail($roleId);
        return view('tenant.admin.roles.show', compact('role'));
    }

    /**
     * Show edit role form
     */
    public function editRole($tenant, $roleId)
    {
        $role = Role::where('tenant_id', tenant()->id)
            ->findOrFail($roleId);
        $permissions = Permission::all()->groupBy('module');
        $role->load('permissions');

        return view('tenant.admin.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update role
     */
    public function updateRole(UpdateRoleRequest $request, $tenant, $roleId)
    {
        $role = Role::where('tenant_id', tenant()->id)
            ->findOrFail($roleId);

        try {
            DB::beginTransaction();

            $role->update([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Update permissions
            if ($request->filled('permissions')) {
                $role->permissions()->sync($request->permissions);
            } else {
                $role->permissions()->detach();
            }

            DB::commit();

            return redirect()->route('tenant.admin.roles.index', tenant('slug'))
                ->with('success', 'Role updated successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error updating role: ' . $e->getMessage());
        }
    }

    /**
     * Delete role
     */
    public function destroyRole($tenant, $roleId)
    {
        $role = Role::where('tenant_id', tenant()->id)
            ->findOrFail($roleId);

        try {
            if ($role->users()->count() > 0) {
                return back()->with('error', 'Cannot delete role with assigned users!');
            }

            $role->delete();

            return redirect()->route('tenant.admin.roles.index', tenant('slug'))
                ->with('success', 'Role deleted successfully!');

        } catch (Exception $e) {
            return back()->with('error', 'Error deleting role: ' . $e->getMessage());
        }
    }

    /**
     * Clone role
     */
    public function cloneRole($tenant, $roleId)
    {
        $role = Role::where('tenant_id', tenant()->id)
            ->with('permissions')
            ->findOrFail($roleId);

        try {
            DB::beginTransaction();

            $newRole = Role::create([
                'name' => $role->name . ' (Copy)',
                'description' => $role->description,
                'tenant_id' => tenant()->id,
                'is_active' => false,
            ]);

            $newRole->permissions()->sync($role->permissions->pluck('id')->toArray());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role cloned successfully!',
                'redirect' => route('tenant.admin.roles.edit', [tenant('slug'), $newRole->id])
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error cloning role: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Permission matrix view
     */
    public function permissionMatrix()
    {
        $roles = Role::with('permissions')->where('tenant_id', tenant()->id)->get();
        $permissions = Permission::all()->groupBy('module');

        return view('tenant.admin.roles.matrix', compact('roles', 'permissions'));
    }

    // ==================== PERMISSIONS MANAGEMENT ====================

    /**
     * Display permissions list
     */
    public function permissions()
    {
        $permissions = Permission::with('roles')
            ->latest()
            ->paginate(15);

        return view('tenant.admin.permissions.index', compact('permissions'));
    }

    /**
     * Show create permission form
     */
    public function createPermission()
    {
        $modules = Permission::distinct('module')->pluck('module');
        return view('tenant.admin.permissions.create', compact('modules'));
    }

    /**
     * Store new permission
     */
    public function storePermission(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'module' => 'required|string|max:255',
        ]);

        try {
            Permission::create($request->all());

            return redirect()->route('tenant.admin.permissions.index')
                ->with('success', 'Permission created successfully!');

        } catch (Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating permission: ' . $e->getMessage());
        }
    }

    /**
     * Show permission details
     */
    public function showPermission(Permission $permission)
    {
        $permission->load('roles');
        return view('tenant.admin.permissions.show', compact('permission'));
    }

    /**
     * Show edit permission form
     */
    public function editPermission(Permission $permission)
    {
        $modules = Permission::distinct('module')->pluck('module');
        return view('tenant.admin.permissions.edit', compact('permission', 'modules'));
    }

    /**
     * Update permission
     */
    public function updatePermission(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'module' => 'required|string|max:255',
        ]);

        try {
            $permission->update($request->all());

            return redirect()->route('tenant.admin.permissions.index')
                ->with('success', 'Permission updated successfully!');

        } catch (Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating permission: ' . $e->getMessage());
        }
    }

    /**
     * Delete permission
     */
    public function destroyPermission(Permission $permission)
    {
        try {
            if ($permission->roles()->count() > 0) {
                return back()->with('error', 'Cannot delete permission assigned to roles!');
            }

            $permission->delete();

            return redirect()->route('tenant.admin.permissions.index')
                ->with('success', 'Permission deleted successfully!');

        } catch (Exception $e) {
            return back()->with('error', 'Error deleting permission: ' . $e->getMessage());
        }
    }

    /**
     * Sync permissions (create default permissions)
     */
    public function syncPermissions()
    {
        try {
            $count = $this->adminService->syncDefaultPermissions();

            return response()->json([
                'success' => true,
                'message' => "Synchronized {$count} permissions successfully!"
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error syncing permissions: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get permissions by module
     */
    public function permissionsByModule()
    {
        $permissions = Permission::all()->groupBy('module');
        return response()->json($permissions);
    }

    /**
     * Get role permissions (API endpoint)
     */
    public function getRolePermissions($tenant, $roleId)
    {
        $role = Role::with('permissions')->findOrFail($roleId);
        
        return response()->json([
            'permissions' => $role->permissions->map(function($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'display_name' => $permission->display_name,
                    'module' => $permission->module,
                ];
            })
        ]);
    }

    // ==================== SECURITY & ACCESS MANAGEMENT ====================

    /**
     * Security dashboard
     */
    public function security()
    {
        $securityStats = $this->adminService->getSecurityStats();
        return view('tenant.admin.security.index', compact('securityStats'));
    }

    /**
     * Active sessions
     */
    public function activeSessions()
    {
        $sessions = $this->adminService->getActiveSessions();
        return view('tenant.admin.security.sessions', compact('sessions'));
    }

    /**
     * Terminate session
     */
    public function terminateSession($sessionId)
    {
        try {
            $this->adminService->terminateSession($sessionId);

            return response()->json([
                'success' => true,
                'message' => 'Session terminated successfully!'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error terminating session: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Login logs
     */
    public function loginLogs(Request $request)
    {
        $logs = $this->adminService->getLoginLogs($request->all());
        return view('tenant.admin.security.login-logs', compact('logs'));
    }

    /**
     * Failed login attempts
     */
    public function failedLogins(Request $request)
    {
        $failedLogins = $this->adminService->getFailedLogins($request->all());
        return view('tenant.admin.security.failed-logins', compact('failedLogins'));
    }

    /**
     * Unlock user account
     */
    public function unlockUser(User $user)
    {
        try {
            $this->adminService->unlockUser($user);

            return response()->json([
                'success' => true,
                'message' => 'User account unlocked successfully!'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error unlocking user: ' . $e->getMessage()
            ]);
        }
    }

    // ==================== ADDITIONAL METHODS ====================

    /**
     * Teams management
     */
    public function teams()
    {
        $teams = Team::with(['members'])
            ->where('tenant_id', tenant()->id)
            ->latest()
            ->paginate(15);

        return view('tenant.admin.teams.index', compact('teams'));
    }

    /**
     * Activity logs
     */
    public function activityLogs(Request $request)
    {
        $logs = $this->adminService->getActivityLogs($request->all());
        return view('tenant.admin.activity.index', compact('logs'));
    }

    /**
     * System information
     */
    public function systemInfo()
    {
        $systemInfo = $this->adminService->getSystemInfo();
        return view('tenant.admin.system.info', compact('systemInfo'));
    }

    /**
     * Admin reports
     */
    public function adminReports()
    {
        return view('tenant.admin.reports.index');
    }
}
