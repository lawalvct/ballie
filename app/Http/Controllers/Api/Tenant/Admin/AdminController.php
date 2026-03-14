<?php

namespace App\Http\Controllers\Api\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Tenant\Role;
use App\Models\Tenant\Permission;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Exception;

class AdminController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    // ==================== DASHBOARD ====================

    /**
     * Admin dashboard stats
     */
    public function dashboard(Request $request, Tenant $tenant)
    {
        try {
            $tenantId = $tenant->id;

            $totalUsers = User::where('tenant_id', $tenantId)->count();
            $activeUsers = User::where('tenant_id', $tenantId)->where('is_active', true)->count();
            $totalRoles = Role::where('tenant_id', $tenantId)->count();
            $recentLogins = User::where('tenant_id', $tenantId)
                ->where('last_login_at', '>=', now()->subHours(24))
                ->count();
            $failedLogins = 0; // Placeholder until failed_login_attempts table is implemented

            $roleDistribution = Role::where('tenant_id', $tenantId)
                ->withCount('users')
                ->get()
                ->map(fn($role) => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'users_count' => $role->users_count,
                    'color' => $role->color,
                ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'total_users' => $totalUsers,
                    'active_users' => $activeUsers,
                    'inactive_users' => $totalUsers - $activeUsers,
                    'total_roles' => $totalRoles,
                    'recent_logins' => $recentLogins,
                    'failed_logins' => $failedLogins,
                    'role_distribution' => $roleDistribution,
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Admin dashboard API error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to load dashboard stats.',
            ], 500);
        }
    }

    // ==================== USERS MANAGEMENT ====================

    /**
     * List users with search, filters, pagination
     */
    public function users(Request $request, Tenant $tenant)
    {
        try {
            $query = User::with(['roles:id,name,color'])
                ->where('tenant_id', $tenant->id);

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Filter by role
            if ($request->filled('role')) {
                $query->whereHas('roles', function ($q) use ($request) {
                    $q->where('roles.id', $request->role);
                });
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('is_active', $request->status === 'active');
            }

            $perPage = min($request->integer('per_page', 15), 50);
            $users = $query->latest()->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $users->map(fn($user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'avatar_url' => $user->avatar_url,
                    'is_active' => $user->is_active,
                    'role' => $user->role,
                    'roles' => $user->roles->map(fn($r) => [
                        'id' => $r->id,
                        'name' => $r->name,
                        'color' => $r->color,
                    ]),
                    'last_login_at' => $user->last_login_at?->toIso8601String(),
                    'created_at' => $user->created_at->toIso8601String(),
                ]),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'from' => $users->firstItem(),
                    'to' => $users->lastItem(),
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Admin users list API error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to load users.',
            ], 500);
        }
    }

    /**
     * Get form data for creating a user (roles dropdown, etc.)
     */
    public function createUser(Request $request, Tenant $tenant)
    {
        try {
            $roles = Role::where('tenant_id', $tenant->id)
                ->where('is_active', true)
                ->orderBy('priority')
                ->orderBy('name')
                ->get()
                ->map(fn($role) => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'description' => $role->description,
                ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'roles' => $roles,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load form data.',
            ], 500);
        }
    }

    /**
     * Store new user
     */
    public function storeUser(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            $selectedRole = Role::where('tenant_id', $tenant->id)->findOrFail($validated['role_id']);
            $roleKey = Str::of($selectedRole->slug ?? $selectedRole->name)->lower()->replace(['-', ' '], '_')->value();
            $roleMap = [
                'super_admin' => User::ROLE_OWNER,
                'owner' => User::ROLE_OWNER,
                'admin' => User::ROLE_ADMIN,
                'manager' => User::ROLE_MANAGER,
                'accountant' => User::ROLE_ACCOUNTANT,
                'sales' => User::ROLE_SALES,
                'employee' => User::ROLE_EMPLOYEE,
            ];
            $userRole = $roleMap[$roleKey] ?? User::ROLE_EMPLOYEE;

            $user = User::create([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'tenant_id' => $tenant->id,
                'role' => $userRole,
                'is_active' => $request->boolean('is_active', true),
                'email_verified_at' => now(),
            ]);

            $tenant->users()->syncWithoutDetaching([
                $user->id => [
                    'role' => $userRole,
                    'is_active' => $request->boolean('is_active', true),
                    'joined_at' => now(),
                    'accepted_at' => now(),
                    'permissions' => null,
                ],
            ]);

            $user->roles()->attach($validated['role_id']);

            DB::commit();

            $user->load('roles:id,name,color');

            return response()->json([
                'success' => true,
                'message' => 'User created successfully.',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_active' => $user->is_active,
                    'roles' => $user->roles->map(fn($r) => [
                        'id' => $r->id,
                        'name' => $r->name,
                    ]),
                    'created_at' => $user->created_at->toIso8601String(),
                ],
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Admin store user API error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show user details
     */
    public function showUser(Request $request, Tenant $tenant, $userId)
    {
        try {
            $user = User::with(['roles.permissions:id,name,display_name,module'])
                ->where('tenant_id', $tenant->id)
                ->findOrFail($userId);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'avatar_url' => $user->avatar_url,
                    'is_active' => $user->is_active,
                    'role' => $user->role,
                    'roles' => $user->roles->map(fn($r) => [
                        'id' => $r->id,
                        'name' => $r->name,
                        'color' => $r->color,
                        'permissions' => $r->permissions->map(fn($p) => [
                            'id' => $p->id,
                            'name' => $p->name,
                            'display_name' => $p->display_name,
                            'module' => $p->module,
                        ]),
                    ]),
                    'last_login_at' => $user->last_login_at?->toIso8601String(),
                    'email_verified_at' => $user->email_verified_at?->toIso8601String(),
                    'created_at' => $user->created_at->toIso8601String(),
                    'updated_at' => $user->updated_at->toIso8601String(),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, Tenant $tenant, $userId)
    {
        $user = User::where('tenant_id', $tenant->id)->findOrFail($userId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'is_active' => $request->boolean('is_active', $user->is_active),
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            // Update roles
            if ($request->has('roles')) {
                if (!empty($validated['roles'])) {
                    $user->roles()->sync($validated['roles']);

                    $selectedRole = Role::find($validated['roles'][0]);
                    $roleKey = $selectedRole ? Str::of($selectedRole->slug ?? $selectedRole->name)->lower()->replace(['-', ' '], '_')->value() : null;
                    $roleMap = [
                        'super_admin' => User::ROLE_OWNER,
                        'owner' => User::ROLE_OWNER,
                        'admin' => User::ROLE_ADMIN,
                        'manager' => User::ROLE_MANAGER,
                        'accountant' => User::ROLE_ACCOUNTANT,
                        'sales' => User::ROLE_SALES,
                        'employee' => User::ROLE_EMPLOYEE,
                    ];
                    $user->update(['role' => $roleMap[$roleKey] ?? User::ROLE_EMPLOYEE]);
                } else {
                    $user->roles()->detach();
                }
            }

            DB::commit();

            $user->load('roles:id,name,color');

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully.',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_active' => $user->is_active,
                    'roles' => $user->roles->map(fn($r) => [
                        'id' => $r->id,
                        'name' => $r->name,
                    ]),
                ],
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Admin update user API error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete user
     */
    public function destroyUser(Request $request, Tenant $tenant, $userId)
    {
        $user = User::where('tenant_id', $tenant->id)->findOrFail($userId);

        try {
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot delete your own account.',
                ], 403);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.',
            ]);
        } catch (Exception $e) {
            Log::error('Admin delete user API error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user.',
            ], 500);
        }
    }

    /**
     * Toggle user active status
     */
    public function toggleUserStatus(Request $request, Tenant $tenant, $userId)
    {
        $user = User::where('tenant_id', $tenant->id)->findOrFail($userId);

        try {
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot deactivate your own account.',
                ], 403);
            }

            $user->update(['is_active' => !$user->is_active]);

            return response()->json([
                'success' => true,
                'message' => 'User status updated successfully.',
                'data' => [
                    'is_active' => $user->is_active,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle user status.',
            ], 500);
        }
    }

    // ==================== ROLES MANAGEMENT ====================

    /**
     * List roles with permissions and user counts
     */
    public function roles(Request $request, Tenant $tenant)
    {
        try {
            $query = Role::with(['permissions:id,name,display_name,module'])
                ->withCount('users')
                ->where('tenant_id', $tenant->id);

            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            $perPage = min($request->integer('per_page', 15), 50);
            $roles = $query->latest()->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $roles->map(fn($role) => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'slug' => $role->slug,
                    'description' => $role->description,
                    'color' => $role->color,
                    'is_active' => $role->is_active,
                    'is_default' => $role->is_default,
                    'users_count' => $role->users_count,
                    'permissions_count' => $role->permissions->count(),
                    'permissions' => $role->permissions->map(fn($p) => [
                        'id' => $p->id,
                        'name' => $p->name,
                        'display_name' => $p->display_name,
                        'module' => $p->module,
                    ]),
                    'created_at' => $role->created_at->toIso8601String(),
                ]),
                'pagination' => [
                    'current_page' => $roles->currentPage(),
                    'last_page' => $roles->lastPage(),
                    'per_page' => $roles->perPage(),
                    'total' => $roles->total(),
                    'from' => $roles->firstItem(),
                    'to' => $roles->lastItem(),
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Admin roles list API error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to load roles.',
            ], 500);
        }
    }

    /**
     * Get form data for creating a role (available permissions grouped by module)
     */
    public function createRole(Request $request, Tenant $tenant)
    {
        try {
            $permissions = Permission::where('is_active', true)
                ->orderBy('module')
                ->orderBy('name')
                ->get()
                ->groupBy('module')
                ->map(fn($group, $module) => [
                    'module' => $module,
                    'permissions' => $group->map(fn($p) => [
                        'id' => $p->id,
                        'name' => $p->name,
                        'display_name' => $p->display_name,
                    ])->values(),
                ])->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'permissions_by_module' => $permissions,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load form data.',
            ], 500);
        }
    }

    /**
     * Store new role
     */
    public function storeRole(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->where(fn($q) => $q->where('tenant_id', $tenant->id)),
            ],
            'description' => 'nullable|string|max:1000',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'is_active' => 'nullable|boolean',
            'color' => ['nullable', 'string', 'max:7', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        try {
            DB::beginTransaction();

            $role = Role::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'tenant_id' => $tenant->id,
                'is_active' => $request->boolean('is_active', true),
                'color' => $validated['color'] ?? null,
            ]);

            if (!empty($validated['permissions'])) {
                $role->permissions()->sync($validated['permissions']);
            }

            DB::commit();

            $role->load('permissions:id,name,display_name,module');
            $role->loadCount('users');

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully.',
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'slug' => $role->slug,
                    'description' => $role->description,
                    'color' => $role->color,
                    'is_active' => $role->is_active,
                    'users_count' => 0,
                    'permissions' => $role->permissions->map(fn($p) => [
                        'id' => $p->id,
                        'name' => $p->name,
                        'display_name' => $p->display_name,
                        'module' => $p->module,
                    ]),
                    'created_at' => $role->created_at->toIso8601String(),
                ],
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Admin store role API error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create role: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show role details
     */
    public function showRole(Request $request, Tenant $tenant, $roleId)
    {
        try {
            $role = Role::with(['permissions:id,name,display_name,module', 'users:id,name,email,is_active'])
                ->where('tenant_id', $tenant->id)
                ->findOrFail($roleId);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'slug' => $role->slug,
                    'description' => $role->description,
                    'color' => $role->color,
                    'is_active' => $role->is_active,
                    'is_default' => $role->is_default,
                    'users_count' => $role->users->count(),
                    'users' => $role->users->map(fn($u) => [
                        'id' => $u->id,
                        'name' => $u->name,
                        'email' => $u->email,
                        'is_active' => $u->is_active,
                    ]),
                    'permissions' => $role->permissions->map(fn($p) => [
                        'id' => $p->id,
                        'name' => $p->name,
                        'display_name' => $p->display_name,
                        'module' => $p->module,
                    ]),
                    'permissions_by_module' => $role->permissions->groupBy('module')->map(fn($group, $module) => [
                        'module' => $module,
                        'permissions' => $group->map(fn($p) => [
                            'id' => $p->id,
                            'name' => $p->name,
                            'display_name' => $p->display_name,
                        ])->values(),
                    ])->values(),
                    'created_at' => $role->created_at->toIso8601String(),
                    'updated_at' => $role->updated_at->toIso8601String(),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found.',
            ], 404);
        }
    }

    /**
     * Update role
     */
    public function updateRole(Request $request, Tenant $tenant, $roleId)
    {
        $role = Role::where('tenant_id', $tenant->id)->findOrFail($roleId);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->where(fn($q) => $q->where('tenant_id', $tenant->id))->ignore($role->id),
            ],
            'description' => 'nullable|string|max:1000',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'is_active' => 'nullable|boolean',
            'color' => ['nullable', 'string', 'max:7', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        try {
            DB::beginTransaction();

            $role->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? $role->description,
                'is_active' => $request->boolean('is_active', $role->is_active),
                'color' => $validated['color'] ?? $role->color,
            ]);

            if ($request->has('permissions')) {
                if (!empty($validated['permissions'])) {
                    $role->permissions()->sync($validated['permissions']);
                } else {
                    $role->permissions()->detach();
                }
            }

            DB::commit();

            $role->load('permissions:id,name,display_name,module');
            $role->loadCount('users');

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully.',
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'slug' => $role->slug,
                    'description' => $role->description,
                    'color' => $role->color,
                    'is_active' => $role->is_active,
                    'users_count' => $role->users_count,
                    'permissions' => $role->permissions->map(fn($p) => [
                        'id' => $p->id,
                        'name' => $p->name,
                        'display_name' => $p->display_name,
                        'module' => $p->module,
                    ]),
                ],
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Admin update role API error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update role: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete role
     */
    public function destroyRole(Request $request, Tenant $tenant, $roleId)
    {
        $role = Role::where('tenant_id', $tenant->id)->findOrFail($roleId);

        try {
            if ($role->users()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete role with assigned users. Remove users from this role first.',
                ], 422);
            }

            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully.',
            ]);
        } catch (Exception $e) {
            Log::error('Admin delete role API error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete role.',
            ], 500);
        }
    }

    /**
     * Assign permissions to a role (replaces current permissions)
     */
    public function assignPermissions(Request $request, Tenant $tenant, $roleId)
    {
        $role = Role::where('tenant_id', $tenant->id)->findOrFail($roleId);

        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            $role->permissions()->sync($validated['permissions']);

            $role->load('permissions:id,name,display_name,module');

            return response()->json([
                'success' => true,
                'message' => 'Permissions assigned successfully.',
                'data' => [
                    'role_id' => $role->id,
                    'role_name' => $role->name,
                    'permissions_count' => $role->permissions->count(),
                    'permissions' => $role->permissions->map(fn($p) => [
                        'id' => $p->id,
                        'name' => $p->name,
                        'display_name' => $p->display_name,
                        'module' => $p->module,
                    ]),
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Admin assign permissions API error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign permissions.',
            ], 500);
        }
    }

    /**
     * Permission matrix — all roles with their permissions
     */
    public function permissionMatrix(Request $request, Tenant $tenant)
    {
        try {
            $roles = Role::with('permissions:id,name,display_name,module')
                ->withCount('users')
                ->where('tenant_id', $tenant->id)
                ->orderBy('priority')
                ->orderBy('name')
                ->get();

            $allPermissions = Permission::where('is_active', true)
                ->orderBy('module')
                ->orderBy('name')
                ->get()
                ->groupBy('module');

            $matrix = [];
            foreach ($allPermissions as $module => $permissions) {
                $moduleData = [
                    'module' => $module,
                    'permissions' => [],
                ];
                foreach ($permissions as $permission) {
                    $permData = [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'display_name' => $permission->display_name,
                        'roles' => [],
                    ];
                    foreach ($roles as $role) {
                        $permData['roles'][$role->id] = $role->permissions->contains($permission->id);
                    }
                    $moduleData['permissions'][] = $permData;
                }
                $matrix[] = $moduleData;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'roles' => $roles->map(fn($r) => [
                        'id' => $r->id,
                        'name' => $r->name,
                        'color' => $r->color,
                        'users_count' => $r->users_count,
                    ]),
                    'matrix' => $matrix,
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Admin permission matrix API error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to load permission matrix.',
            ], 500);
        }
    }

    /**
     * List all available permissions grouped by module
     */
    public function permissions(Request $request, Tenant $tenant)
    {
        try {
            $permissions = Permission::where('is_active', true)
                ->orderBy('module')
                ->orderBy('name')
                ->get()
                ->groupBy('module')
                ->map(fn($group, $module) => [
                    'module' => $module,
                    'permissions' => $group->map(fn($p) => [
                        'id' => $p->id,
                        'name' => $p->name,
                        'display_name' => $p->display_name,
                        'description' => $p->description,
                    ])->values(),
                ])->values();

            return response()->json([
                'success' => true,
                'data' => $permissions,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load permissions.',
            ], 500);
        }
    }
}
