<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
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
        'description',
        'tenant_id',
        'is_active',
        'is_default',
        'color',
        'priority',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'priority' => 'integer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($role) {
            if (empty($role->slug)) {
                $role->slug = \Illuminate\Support\Str::slug($role->name);
            }
            if (empty($role->tenant_id)) {
                $role->tenant_id = tenant('id');
            }
        });
    }

    /**
     * Get the users that belong to this role.
     */
    public function users()
    {
        return $this->belongsToMany(\App\Models\User::class, 'role_user', 'role_id', 'user_id')
                    ->withTimestamps();
    }

    /**
     * Get the permissions that belong to this role.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role', 'role_id', 'permission_id')
                    ->withTimestamps();
    }

    /**
     * Get the tenant that owns this role.
     */
    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }

    /**
     * Check if role has a specific permission.
     */
    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            return $this->permissions()->where('name', $permission)->exists();
        }

        if (is_array($permission)) {
            return $this->permissions()->whereIn('name', $permission)->exists();
        }

        return $this->permissions()->where('id', $permission->id)->exists();
    }

    /**
     * Check if role has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions)
    {
        return $this->permissions()->whereIn('name', $permissions)->exists();
    }

    /**
     * Check if role has all of the given permissions.
     */
    public function hasAllPermissions(array $permissions)
    {
        $rolePermissions = $this->permissions()->whereIn('name', $permissions)->pluck('name')->toArray();
        return count($permissions) === count($rolePermissions);
    }

    /**
     * Give permissions to the role.
     */
    public function givePermissionTo(...$permissions)
    {
        $permissions = collect($permissions)
            ->flatten()
            ->map(function ($permission) {
                if (empty($permission)) {
                    return false;
                }

                return $this->getStoredPermission($permission);
            })
            ->filter(function ($permission) {
                return $permission instanceof Permission;
            })
            ->each(function ($permission) {
                $this->ensureModelSharesTenant($permission);
            })
            ->map->id
            ->all();

        $model = $this->getModel();

        if ($model->exists) {
            $this->permissions()->sync($permissions, false);
            $model->load('permissions');
        } else {
            $class = \get_class($model);

            $class::saved(
                function ($object) use ($permissions, $model) {
                    static $modelLastFiredOn;
                    if ($modelLastFiredOn !== null && $modelLastFiredOn === $model) {
                        return;
                    }
                    $object->permissions()->sync($permissions, false);
                    $modelLastFiredOn = $object;
                }
            );
        }

        return $this;
    }

    /**
     * Revoke permissions from the role.
     */
    public function revokePermissionTo(...$permissions)
    {
        $permissions = collect($permissions)
            ->flatten()
            ->map(function ($permission) {
                if (empty($permission)) {
                    return false;
                }

                return $this->getStoredPermission($permission);
            })
            ->filter(function ($permission) {
                return $permission instanceof Permission;
            })
            ->map->id
            ->all();

        $this->permissions()->detach($permissions);

        $this->load('permissions');

        return $this;
    }

    /**
     * Sync permissions with the role.
     */
    public function syncPermissions(...$permissions)
    {
        $this->permissions()->detach();

        return $this->givePermissionTo($permissions);
    }

    /**
     * Get stored permission by name or instance.
     */
    protected function getStoredPermission($permission)
    {
        $permissionClass = Permission::class;

        if (is_numeric($permission)) {
            return $permissionClass::find($permission);
        }

        if (is_string($permission)) {
            return $permissionClass::where('name', $permission)->first();
        }

        if (is_object($permission)) {
            return $permission;
        }

        return false;
    }

    /**
     * Ensure model shares the same tenant.
     */
    protected function ensureModelSharesTenant($roleOrPermission)
    {
        if (! $this->tenant_id) {
            return;
        }

        if (isset($roleOrPermission->tenant_id) && $roleOrPermission->tenant_id !== $this->tenant_id) {
            throw new \InvalidArgumentException('Model does not belong to the same tenant.');
        }
    }

    /**
     * Scope to active roles.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to default roles.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope to tenant roles.
     */
    public function scopeForTenant($query, $tenantId = null)
    {
        $tenantId = $tenantId ?: tenant('id');
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Get formatted color.
     */
    public function getColorAttribute($value)
    {
        return $value ?: '#6366f1'; // Default purple color
    }

    /**
     * Get role display name with color badge.
     */
    public function getBadgeHtmlAttribute()
    {
        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: ' . $this->color . '20; color: ' . $this->color . ';">' .
               $this->name .
               '</span>';
    }

    /**
     * Get users count.
     */
    public function getUsersCountAttribute()
    {
        return $this->users()->count();
    }

    /**
     * Get permissions count.
     */
    public function getPermissionsCountAttribute()
    {
        return $this->permissions()->count();
    }
}
