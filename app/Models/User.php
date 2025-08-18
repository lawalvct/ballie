<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'role',
        'permissions',
        'is_active',
        'last_login_at',
        'email_verified_at',
        'social_provider',
        'social_provider_id',
        'social_avatar',
        'business_name',
        'business_type',
        'onboarding_completed',
        'onboarding_step',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'onboarding_completed' => 'boolean',
        'permissions' => 'array',
        'password' => 'hashed',
    ];

    // User roles within a tenant
    const ROLE_OWNER = 'owner';
    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_ACCOUNTANT = 'accountant';
    const ROLE_SALES = 'sales';
    const ROLE_EMPLOYEE = 'employee';

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function hasRole($role): bool
    {
        return $this->role === $role;
    }

    public function hasPermission($permission): bool
    {
        return in_array($permission, $this->permissions ?? []);
    }

    public function isOwner(): bool
    {
        return $this->role === self::ROLE_OWNER;
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, [self::ROLE_OWNER, self::ROLE_ADMIN]);
    }

    public function canManage($resource): bool
    {
        return $this->isAdmin() || $this->hasPermission("manage_{$resource}");
    }

    /**
     * Scope for users by business type
     */
    public function scopeByBusinessType($query, $type)
    {
        return $query->where('business_type', $type);
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Business type constants
    const BUSINESS_TYPES = [
        'retail' => 'Retail & E-commerce',
        'service' => 'Service Business',
        'restaurant' => 'Restaurant & Food',
        'manufacturing' => 'Manufacturing',
        'wholesale' => 'Wholesale & Distribution',
        'other' => 'Other',
    ];

    /**
     * Get the business type label
     */
    public function getBusinessTypeLabelAttribute()
    {
        return self::BUSINESS_TYPES[$this->business_type] ?? 'Unknown';
    }

    /**
     * Get the user's avatar URL
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        // Generate avatar from initials
        $initials = collect(explode(' ', $this->name))
            ->map(fn($name) => strtoupper(substr($name, 0, 1)))
            ->take(2)
            ->implode('');

        return "https://ui-avatars.com/api/?name={$initials}&color=ffffff&background=2b6399&size=200";
    }


     /**
     * Check if user is active in a specific tenant.
     */
    public function isActiveInTenant(Tenant $tenant): bool
    {
        $userTenant = $this->tenants()->where('tenant_id', $tenant->id)->first();
        return $userTenant && $userTenant->pivot->is_active;
    }
}
