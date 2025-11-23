<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Notifications\ResetPasswordNotification;

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
        'tour_completed',
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
        // Owner role has all permissions
        if ($this->roles()->where('name', 'Owner')->exists()) {
            return true;
        }

        // Check role-based permissions
        return $this->roles()
            ->whereHas('permissions', function($q) use ($permission) {
                $q->where('slug', $permission);
            })
            ->exists();
    }

    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->roles()->where('name', 'Owner')->exists()) {
            return true;
        }

        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    public function hasAllPermissions(array $permissions): bool
    {
        if ($this->roles()->where('name', 'Owner')->exists()) {
            return true;
        }

        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
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

    /**
     * Get the roles for the user.
     */
    public function roles()
    {
        return $this->belongsToMany(\App\Models\Tenant\Role::class, 'role_user', 'user_id', 'role_id')
                    ->withTimestamps();
    }

    // Audit Trail Relationships - Records Created
    public function createdCustomers()
    {
        return $this->hasMany(\App\Models\Customer::class, 'created_by');
    }

    public function createdVendors()
    {
        return $this->hasMany(\App\Models\Vendor::class, 'created_by');
    }

    public function createdProducts()
    {
        return $this->hasMany(\App\Models\Product::class, 'created_by');
    }

    public function createdVouchers()
    {
        return $this->hasMany(\App\Models\Voucher::class, 'created_by');
    }

    public function createdLedgerAccounts()
    {
        return $this->hasMany(\App\Models\LedgerAccount::class, 'created_by');
    }

    public function createdSales()
    {
        return $this->hasMany(\App\Models\Sale::class, 'created_by');
    }

    // Audit Trail Relationships - Records Updated
    public function updatedCustomers()
    {
        return $this->hasMany(\App\Models\Customer::class, 'updated_by');
    }

    public function updatedVendors()
    {
        return $this->hasMany(\App\Models\Vendor::class, 'updated_by');
    }

    public function updatedProducts()
    {
        return $this->hasMany(\App\Models\Product::class, 'updated_by');
    }

    public function updatedVouchers()
    {
        return $this->hasMany(\App\Models\Voucher::class, 'updated_by');
    }

    public function updatedLedgerAccounts()
    {
        return $this->hasMany(\App\Models\LedgerAccount::class, 'updated_by');
    }

    public function updatedSales()
    {
        return $this->hasMany(\App\Models\Sale::class, 'updated_by');
    }

    // Audit Trail Relationships - Records Posted
    public function postedVouchers()
    {
        return $this->hasMany(\App\Models\Voucher::class, 'posted_by');
    }

    public function postedStockJournals()
    {
        return $this->hasMany(\App\Models\StockJournalEntry::class, 'posted_by');
    }

    // Audit Trail Relationships - Records Deleted
    public function deletedCustomers()
    {
        return $this->hasMany(\App\Models\Customer::class, 'deleted_by');
    }

    public function deletedVendors()
    {
        return $this->hasMany(\App\Models\Vendor::class, 'deleted_by');
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
