<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'display_name',
        'description',
        'module',
        'guard_name',
        'is_active',
        'priority',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($permission) {
            if (empty($permission->slug)) {
                $permission->slug = \Illuminate\Support\Str::slug($permission->name);
            }
            if (empty($permission->guard_name)) {
                $permission->guard_name = 'web';
            }
        });
    }

    /**
     * Get the roles that belong to this permission.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_role', 'permission_id', 'role_id')
                    ->withTimestamps();
    }

    /**
     * Get the users that have this permission directly.
     */
    public function users()
    {
        return $this->belongsToMany(\App\Models\User::class, 'permission_user', 'permission_id', 'user_id')
                    ->withTimestamps();
    }

    /**
     * Scope to active permissions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to permissions by module.
     */
    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope to permissions by guard.
     */
    public function scopeByGuard($query, $guard = 'web')
    {
        return $query->where('guard_name', $guard);
    }

    /**
     * Get formatted module name.
     */
    public function getModuleDisplayAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->module));
    }

    /**
     * Get permission display name with module.
     */
    public function getFullDisplayNameAttribute()
    {
        return $this->module_display . ' - ' . $this->display_name;
    }

    /**
     * Get roles count.
     */
    public function getRolesCountAttribute()
    {
        return $this->roles()->count();
    }

    /**
     * Get users count (including through roles).
     */
    public function getUsersCountAttribute()
    {
        $directUsers = $this->users()->count();
        $roleUsers = \App\Models\User::whereHas('roles.permissions', function ($query) {
            $query->where('permissions.id', $this->id);
        })->count();

        return $directUsers + $roleUsers;
    }

    /**
     * Check if permission belongs to module.
     */
    public function belongsToModule($module)
    {
        return $this->module === $module;
    }

    /**
     * Get icon for permission module.
     */
    public function getModuleIconAttribute()
    {
        $icons = [
            'dashboard' => 'fas fa-tachometer-alt',
            'users' => 'fas fa-users',
            'roles' => 'fas fa-user-shield',
            'permissions' => 'fas fa-key',
            'inventory' => 'fas fa-boxes',
            'products' => 'fas fa-box',
            'customers' => 'fas fa-user-friends',
            'vendors' => 'fas fa-truck',
            'invoices' => 'fas fa-file-invoice',
            'payments' => 'fas fa-credit-card',
            'accounting' => 'fas fa-calculator',
            'reports' => 'fas fa-chart-bar',
            'pos' => 'fas fa-cash-register',
            'payroll' => 'fas fa-money-bill-wave',
            'crm' => 'fas fa-handshake',
            'settings' => 'fas fa-cog',
            'admin' => 'fas fa-crown',
            'system' => 'fas fa-server',
            'security' => 'fas fa-shield-alt',
            'audit' => 'fas fa-history',
            'backup' => 'fas fa-download',
            'maintenance' => 'fas fa-tools',
        ];

        return $icons[$this->module] ?? 'fas fa-circle';
    }

    /**
     * Get color for permission module.
     */
    public function getModuleColorAttribute()
    {
        $colors = [
            'dashboard' => '#3b82f6',    // Blue
            'users' => '#10b981',        // Green
            'roles' => '#8b5cf6',        // Purple
            'permissions' => '#f59e0b',  // Amber
            'inventory' => '#ef4444',    // Red
            'products' => '#f97316',     // Orange
            'customers' => '#06b6d4',    // Cyan
            'vendors' => '#84cc16',      // Lime
            'invoices' => '#6366f1',     // Indigo
            'payments' => '#14b8a6',     // Teal
            'accounting' => '#8b5cf6',   // Purple
            'reports' => '#f59e0b',      // Amber
            'pos' => '#10b981',          // Green
            'payroll' => '#3b82f6',      // Blue
            'crm' => '#ec4899',          // Pink
            'settings' => '#6b7280',     // Gray
            'admin' => '#dc2626',        // Red
            'system' => '#374151',       // Gray-dark
            'security' => '#7c2d12',     // Red-dark
            'audit' => '#1f2937',        // Gray-darker
            'backup' => '#059669',       // Green-dark
            'maintenance' => '#d97706',  // Orange-dark
        ];

        return $colors[$this->module] ?? '#6b7280';
    }

    /**
     * Get permission badge HTML.
     */
    public function getBadgeHtmlAttribute()
    {
        return '<span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium" style="background-color: ' . $this->module_color . '20; color: ' . $this->module_color . ';">' .
               '<i class="' . $this->module_icon . ' mr-1"></i>' .
               $this->display_name .
               '</span>';
    }

    /**
     * Check if this permission can be deleted.
     */
    public function canBeDeleted()
    {
        // System permissions cannot be deleted
        $systemPermissions = [
            'view_dashboard',
            'manage_own_profile',
            'view_notifications',
        ];

        return !in_array($this->name, $systemPermissions) && $this->roles()->count() === 0;
    }

    /**
     * Get permission hierarchy level.
     */
    public function getHierarchyLevelAttribute()
    {
        $levels = [
            'view' => 1,
            'create' => 2,
            'edit' => 3,
            'update' => 3,
            'delete' => 4,
            'manage' => 5,
            'admin' => 6,
            'system' => 7,
        ];

        foreach ($levels as $action => $level) {
            if (str_contains($this->name, $action)) {
                return $level;
            }
        }

        return 1; // Default to view level
    }
}
