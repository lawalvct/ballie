<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug', // For path-based routing (tenant1, tenant2)
        'domain',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'business_structure',
        'business_type_id',
        'business_registration_number',
        'tax_identification_number',
        'logo',
        'website',
        'plan_id', // References plans table
        'subscription_status',
        'subscription_starts_at',
        'subscription_ends_at',
        'trial_ends_at',
        'billing_cycle', // monthly, yearly
        'created_by', // Super admin who created this tenant
        'is_active',
        'settings',
        'onboarding_completed_at',
        'onboarding_progress',
        'status',
        'payment_terms',
        'fiscal_year_start',
        'employee_number_format'
    ];

    protected $casts = [
        'subscription_starts_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'onboarding_completed_at' => 'datetime',
        'is_active' => 'boolean',
        'settings' => 'array',
        'onboarding_progress' => 'array',
    ];

    // Subscription plans (for plan table reference)
    const PLAN_STARTER = 'starter';
    const PLAN_PROFESSIONAL = 'professional';
    const PLAN_ENTERPRISE = 'enterprise';

    // Subscription status
    const STATUS_TRIAL = 'trial';
    const STATUS_ACTIVE = 'active';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EXPIRED = 'expired';

    // Billing cycles
    const BILLING_MONTHLY = 'monthly';
    const BILLING_YEARLY = 'yearly';

    // Tenant status
    const TENANT_STATUS_ACTIVE = 'active';
    const TENANT_STATUS_INACTIVE = 'inactive';
    const TENANT_STATUS_SUSPENDED = 'suspended';

    /**
     * Get users that belong to this tenant
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tenant_users')
                    ->withPivot(['role', 'is_active', 'joined_at', 'accepted_at', 'permissions'])
                    ->withTimestamps();
    }

    public function superAdmin(): BelongsTo
    {
        return $this->belongsTo(SuperAdmin::class, 'created_by');
    }

    /**
     * Get the current plan
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the business type
     */
    public function businessType(): BelongsTo
    {
        return $this->belongsTo(BusinessType::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get subscription payments
     */
    public function subscriptionPayments(): HasMany
    {
        return $this->hasMany(SubscriptionPayment::class);
    }

    /**
     * Get the current active subscription
     */
    public function subscription()
    {
        return $this->hasOne(Subscription::class)->latest();
    }

    // Pricing methods
    public function getPlanPrice(): int
    {
        if (!$this->plan) {
            return 0;
        }

        return $this->billing_cycle === self::BILLING_YEARLY
            ? $this->plan->yearly_price
            : $this->plan->monthly_price;
    }

    public function isOnTrial(): bool
    {
        return $this->subscription_status === self::STATUS_TRIAL &&
               $this->trial_ends_at &&
               $this->trial_ends_at->isFuture();
    }

    public function hasActiveSubscription(): bool
    {
        return $this->subscription_status === self::STATUS_ACTIVE &&
               $this->subscription_ends_at &&
               $this->subscription_ends_at->isFuture();
    }

    public function canAccess(): bool
    {
        return $this->is_active && ($this->isOnTrial() || $this->hasActiveSubscription());
    }

    /**
     * Check if subscription has expired
     */
    public function hasExpiredSubscription(): bool
    {
        // If already marked as expired
        if ($this->subscription_status === self::STATUS_EXPIRED) {
            return true;
        }

        // If subscription is active but past end date
        return $this->subscription_status === self::STATUS_ACTIVE &&
               $this->subscription_ends_at &&
               $this->subscription_ends_at->isPast();
    }

    /**
     * Get subscription status for display
     */
    public function getSubscriptionDisplayStatus(): string
    {
        if ($this->isOnTrial()) {
            return 'trial';
        } elseif ($this->hasActiveSubscription()) {
            return 'active';
        } elseif ($this->hasExpiredSubscription()) {
            return 'expired';
        } else {
            return $this->subscription_status ?? 'inactive';
        }
    }

    /**
     * Handle expired subscription
     */
    public function handleExpiredSubscription(): void
    {
        if ($this->hasExpiredSubscription()) {
            $this->update([
                'subscription_status' => self::STATUS_EXPIRED,
            ]);

            // Log the expiration
            Log::info('Tenant subscription expired', [
                'tenant_id' => $this->id,
                'tenant_name' => $this->name,
                'expired_at' => $this->subscription_ends_at
            ]);
        }
    }

    /**
     * Get days until subscription expires (or days overdue if expired)
     */
    public function subscriptionDaysRemaining(): int
    {
        if (!$this->subscription_ends_at) {
            return 0;
        }

        return now()->diffInDays($this->subscription_ends_at, false);
    }

    // Additional Subscription Helper Methods

    /**
     * Get days remaining in trial
     */
    public function trialDaysRemaining(): int
    {
        if (!$this->trial_ends_at || $this->trial_ends_at < now()) {
            return 0;
        }
        return now()->diffInDays($this->trial_ends_at);
    }

    /**
     * Start trial for new tenant with plan
     */
    public function startTrial(Plan $plan): void
    {
        $this->update([
            'plan_id' => $plan->id,
            'subscription_status' => self::STATUS_TRIAL,
            'trial_ends_at' => now()->addDays(30),
            'subscription_starts_at' => now(),
            'subscription_ends_at' => now()->addDays(30),
        ]);
    }

    /**
     * Upgrade to paid subscription
     */
    public function upgradeToPaid(Plan $plan, string $billingCycle = 'monthly'): Subscription
    {
        // Create subscription record for history
        $subscription = $this->subscriptions()->create([
            'plan_id' => $plan->id,
            'billing_cycle' => $billingCycle,
            'amount' => $billingCycle === 'yearly' ? $plan->yearly_price : $plan->monthly_price,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => $billingCycle === 'yearly' ? now()->addYear() : now()->addMonth(),
        ]);

        // Update tenant current subscription status
        $this->update([
            'plan_id' => $plan->id,
            'subscription_status' => self::STATUS_ACTIVE,
            'billing_cycle' => $billingCycle,
            'subscription_starts_at' => now(),
            'subscription_ends_at' => $subscription->ends_at,
            'trial_ends_at' => null, // Clear trial
        ]);

        return $subscription;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Make this tenant the current tenant for the request (Single Database)
     */
    public function makeCurrent(): void
    {
        // Store current tenant in app container
        app()->instance('current_tenant', $this);

        // Set tenant context in config
        Config::set('app.current_tenant', $this);

        // Set tenant ID in session for query scoping
        session(['current_tenant_id' => $this->id]);
    }

    /**
     * Get the current tenant
     */
    public static function current(): ?self
    {
        return app('current_tenant');
    }

    /**
     * Check if this tenant is the current tenant
     */
    public function isCurrent(): bool
    {
        $current = static::current();
        return $current && $current->id === $this->id;
    }

    /**
     * Execute a callback with this tenant as current
     */
    public function execute(callable $callback)
    {
        $previousTenant = static::current();

        $this->makeCurrent();

        try {
            return $callback($this);
        } finally {
            if ($previousTenant) {
                $previousTenant->makeCurrent();
            }
        }
    }

    /**
     * Check if onboarding is completed
     */
    public function hasCompletedOnboarding(): bool
    {
        return !is_null($this->onboarding_completed_at);
    }

    /**
     * Mark onboarding as completed
     */
    public function completeOnboarding(): void
    {
        $this->update(['onboarding_completed_at' => now()]);
    }

    /**
     * Get onboarding progress percentage
     */
    public function getOnboardingProgress(): int
    {
        if ($this->hasCompletedOnboarding()) {
            return 100;
        }

        $progress = $this->onboarding_progress ?? [];
        $totalSteps = 4; // company, preferences, team, complete
        $completedSteps = count(array_filter($progress));

        return (int) (($completedSteps / $totalSteps) * 100);
    }

    public function accountGroups()
    {
        return $this->hasMany(AccountGroup::class);
    }

    public function ledgerAccounts()
    {
        return $this->hasMany(LedgerAccount::class);
    }

    public function voucherTypes()
    {
        return $this->hasMany(VoucherType::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    public function banks()
    {
        return $this->hasMany(Bank::class);
    }

    // E-commerce Relationships

    /**
     * Get tenant's e-commerce settings
     */
    public function ecommerceSettings()
    {
        return $this->hasOne(EcommerceSetting::class);
    }

    /**
     * Get tenant's e-commerce orders
     */
    public function orders()
    {
        return $this->hasMany(Order::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get tenant's shipping methods
     */
    public function shippingMethods()
    {
        return $this->hasMany(ShippingMethod::class);
    }

    /**
     * Get tenant's coupons
     */
    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    /**
     * Get tenant's carts
     */
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Get tenant's wishlists
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    // E-commerce Helper Methods

    /**
     * Check if tenant has e-commerce enabled
     */
    public function hasEcommerceEnabled()
    {
        return $this->ecommerceSettings && $this->ecommerceSettings->is_enabled;
    }

    /**
     * Get store URL
     */
    public function getStoreUrl()
    {
        return url('/' . $this->slug . '/store');
    }

    /**
     * Get active shipping methods
     */
    public function getActiveShippingMethods()
    {
        return $this->shippingMethods()->active()->get();
    }

    /**
     * Get valid coupons
     */
    public function getValidCoupons()
    {
        return $this->coupons()->valid()->get();
    }

    /**
     * Check if tenant owner has verified their email
     *
     * @return bool
     */
    public function getIsVerifiedAttribute(): bool
    {
        // Get the owner user (first user with owner role)
        $owner = User::where('tenant_id', $this->id)
            ->where('role', User::ROLE_OWNER)
            ->first();

        return $owner && !is_null($owner->email_verified_at);
    }
}
