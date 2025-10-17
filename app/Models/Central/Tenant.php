<?php

namespace App\Models\Central;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $fillable = [
        'id',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'business_type',
        'plan_id',
        'subscription_status',
        'trial_ends_at',
        'subscription_ends_at',
        'created_by',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'email',
            'phone',
            'address',
            'city',
            'state',
            'country',
            'business_type',
            'plan_id',
            'subscription_status',
            'trial_ends_at',
            'subscription_ends_at',
            'created_by',
            'is_active',
            'settings',
        ];
    }

    public function superAdmin()
    {
        return $this->belongsTo(SuperAdmin::class, 'created_by');
    }

    public function isOnTrial()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function hasActiveSubscription()
    {
        return $this->subscription_status === 'active' &&
               $this->subscription_ends_at &&
               $this->subscription_ends_at->isFuture();
    }

    public function canAccess()
    {
        return $this->is_active && ($this->isOnTrial() || $this->hasActiveSubscription());
    }
}
