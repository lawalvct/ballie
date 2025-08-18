<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Config;

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
        'business_type',
        'business_registration_number',
        'tax_identification_number',
        'logo',
        'website',
        'subscription_plan',
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
        'fiscal_year_start'
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

    // Subscription plans
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

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    // Pricing methods
    public function getPlanPrice(): int
    {
        $prices = [
            self::PLAN_STARTER => [
                self::BILLING_MONTHLY => 7500,
                self::BILLING_YEARLY => 76500,
            ],
            self::PLAN_PROFESSIONAL => [
                self::BILLING_MONTHLY => 10000,
                self::BILLING_YEARLY => 102000,
            ],
            self::PLAN_ENTERPRISE => [
                self::BILLING_MONTHLY => 15000,
                self::BILLING_YEARLY => 153000,
            ],
        ];

        return $prices[$this->subscription_plan][$this->billing_cycle] ?? 0;
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


      /**
     * Get default units data.
     */
    private function getDefaultUnitsData(): array
    {
        return [
            // Basic counting units
            ['name' => 'Piece', 'symbol' => 'pcs', 'description' => 'Individual pieces or items', 'is_base_unit' => true],
            ['name' => 'Dozen', 'symbol' => 'doz', 'description' => 'Dozen - 12 pieces', 'is_base_unit' => true],
            ['name' => 'Box', 'symbol' => 'box', 'description' => 'Box or carton', 'is_base_unit' => true],
            ['name' => 'Pack', 'symbol' => 'pack', 'description' => 'Pack or package', 'is_base_unit' => true],
            ['name' => 'Set', 'symbol' => 'set', 'description' => 'Set of items', 'is_base_unit' => true],

            // Weight units
            ['name' => 'Kilogram', 'symbol' => 'kg', 'description' => 'Base unit for weight measurement', 'is_base_unit' => true],
            ['name' => 'Gram', 'symbol' => 'g', 'description' => 'Gram - 1/1000 of a kilogram', 'is_base_unit' => true],

            // Volume units
            ['name' => 'Liter', 'symbol' => 'L', 'description' => 'Base unit for volume measurement', 'is_base_unit' => true],
            ['name' => 'Milliliter', 'symbol' => 'mL', 'description' => 'Milliliter - 1/1000 of a liter', 'is_base_unit' => true],

            // Length units
            ['name' => 'Meter', 'symbol' => 'm', 'description' => 'Base unit for length measurement', 'is_base_unit' => true],
            ['name' => 'Centimeter', 'symbol' => 'cm', 'description' => 'Centimeter - 1/100 of a meter', 'is_base_unit' => true],

            // Time units
            ['name' => 'Hour', 'symbol' => 'hr', 'description' => 'Hour - unit of time', 'is_base_unit' => true],
            ['name' => 'Day', 'symbol' => 'day', 'description' => 'Day - unit of time', 'is_base_unit' => true],
        ];
    }

    /**
     * Get default categories data.
     */
    private function getDefaultCategoriesData(): array
    {
        return [
            [
                'name' => 'General',
                'slug' => 'general',
                'description' => 'General products and miscellaneous items',
                'sort_order' => 1,
                'meta_title' => 'General Products',
                'meta_description' => 'General products and miscellaneous items',
            ],
            [
                'name' => 'Electronics',
                'slug' => 'electronics',
                'description' => 'Electronic devices and accessories',
                'sort_order' => 2,
                'meta_title' => 'Electronics - Electronic Devices and Accessories',
                'meta_description' => 'Browse our wide range of electronic devices and accessories',
                'subcategories' => [
                    [
                        'name' => 'Computers & Laptops',
                        'slug' => 'computers-laptops',
                        'description' => 'Desktop computers, laptops, and accessories',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Mobile Phones',
                        'slug' => 'mobile-phones',
                        'description' => 'Smartphones and mobile accessories',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Audio & Video',
                        'slug' => 'audio-video',
                        'description' => 'Speakers, headphones, cameras, and video equipment',
                        'sort_order' => 3,
                    ],
                ]
            ],
            [
                'name' => 'Clothing & Fashion',
                'slug' => 'clothing-fashion',
                'description' => 'Clothing, shoes, and fashion accessories',
                'sort_order' => 3,
                'meta_title' => 'Clothing & Fashion - Apparel and Accessories',
                'meta_description' => 'Discover the latest fashion trends and clothing styles',
                'subcategories' => [
                    [
                        'name' => 'Men\'s Clothing',
                        'slug' => 'mens-clothing',
                        'description' => 'Men\'s shirts, pants, suits, and casual wear',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Women\'s Clothing',
                        'slug' => 'womens-clothing',
                        'description' => 'Women\'s dresses, tops, pants, and formal wear',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Shoes & Footwear',
                        'slug' => 'shoes-footwear',
                        'description' => 'Shoes, sandals, boots, and footwear accessories',
                        'sort_order' => 3,
                    ],
                ]
            ],
            [
                'name' => 'Home & Garden',
                'slug' => 'home-garden',
                'description' => 'Home improvement, furniture, and garden supplies',
                'sort_order' => 4,
                'meta_title' => 'Home & Garden - Furniture and Home Improvement',
                'meta_description' => 'Everything you need for your home and garden',
                'subcategories' => [
                    [
                        'name' => 'Furniture',
                        'slug' => 'furniture',
                        'description' => 'Living room, bedroom, office, and outdoor furniture',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Home Decor',
                        'slug' => 'home-decor',
                        'description' => 'Decorative items, artwork, and home accessories',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Kitchen & Dining',
                        'slug' => 'kitchen-dining',
                        'description' => 'Cookware, dinnerware, and kitchen accessories',
                        'sort_order' => 3,
                    ],
                ]
            ],
            [
                'name' => 'Office & Stationery',
                'slug' => 'office-stationery',
                'description' => 'Office supplies, stationery, and business equipment',
                'sort_order' => 5,
                'meta_title' => 'Office & Stationery - Business Supplies and Equipment',
                'meta_description' => 'Professional office supplies and stationery items',
                'subcategories' => [
                    [
                        'name' => 'Writing Instruments',
                        'slug' => 'writing-instruments',
                        'description' => 'Pens, pencils, markers, and writing accessories',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Paper Products',
                        'slug' => 'paper-products',
                        'description' => 'Notebooks, printing paper, and paper supplies',
                        'sort_order' => 2,
                    ],
                ]
            ],
        ];
    }
}