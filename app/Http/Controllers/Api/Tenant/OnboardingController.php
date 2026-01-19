<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Database\Seeders\AccountGroupSeeder;
use Database\Seeders\VoucherTypeSeeder;
use Database\Seeders\DefaultLedgerAccountsSeeder;
use Database\Seeders\DefaultBanksSeeder;
use Database\Seeders\DefaultProductCategoriesSeeder;
use Database\Seeders\DefaultUnitsSeeder;
use Database\Seeders\DefaultShiftsSeeder;
use Database\Seeders\DefaultPfasSeeder;
use Database\Seeders\PermissionsSeeder;

class OnboardingController extends BaseApiController
{
    /**
     * Get onboarding status and configuration
     *
     * Returns whether onboarding is completed, current step, and available options
     */
    public function status(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;

        return $this->success([
            'onboarding_completed' => (bool) $tenant->onboarding_completed,
            'onboarding_completed_at' => $tenant->onboarding_completed_at,
            'current_step' => $this->determineCurrentStep($tenant),
            'steps' => [
                ['name' => 'company', 'label' => 'Company Info', 'completed' => !empty($tenant->business_structure)],
                ['name' => 'preferences', 'label' => 'Preferences', 'completed' => !empty($tenant->fiscal_year_start)],
            ],
            'can_skip' => true,
        ]);
    }

    /**
     * Save company information (Step 1)
     *
     * Matches web form: company.blade.php
     */
    public function saveCompany(Request $request): JsonResponse
    {
        $validated = $request->validate([
            // Logo
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'logo_base64' => 'nullable|string', // Alternative: base64 encoded image

            // Basic Information
            'company_name' => 'required|string|max:255',
            'business_structure' => 'required|string|in:Sole Proprietorship,Limited Liability Company (LLC),Partnership,Corporation,Other',
            'other_structure' => 'nullable|required_if:business_structure,Other|string|max:255',

            // Contact Information
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'website' => 'nullable|url|max:255',

            // Address Information
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',

            // Registration Information (Optional)
            'registration_number' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:100',
        ]);

        $tenant = $request->user()->tenant;

        try {
            // Handle logo upload
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($tenant->logo) {
                    Storage::disk('public')->delete($tenant->logo);
                }

                // Store new logo
                $logoPath = $request->file('logo')->store('logos', 'public');
                $validated['logo'] = $logoPath;
            } elseif ($request->has('logo_base64')) {
                // Handle base64 image (common in mobile apps)
                $image = $request->logo_base64;
                $image = str_replace('data:image/png;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = 'logo_' . time() . '.png';

                Storage::disk('public')->put('logos/' . $imageName, base64_decode($image));
                $validated['logo'] = 'logos/' . $imageName;
            }

            // Update tenant with company info
            $tenant->update([
                'logo' => $validated['logo'] ?? $tenant->logo,
                'name' => $validated['company_name'], // Update tenant name
                'business_structure' => $validated['business_structure'],
                'other_structure' => $validated['other_structure'] ?? null,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'website' => $validated['website'] ?? null,
                'address' => $validated['address'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'country' => $validated['country'],
                'postal_code' => $validated['postal_code'] ?? null,
                'registration_number' => $validated['registration_number'] ?? null,
                'tax_id' => $validated['tax_id'] ?? null,
            ]);

            return $this->success([
                'tenant' => $tenant->fresh(),
                'next_step' => 'preferences',
            ], 'Company information saved successfully');

        } catch (\Exception $e) {
            Log::error('Failed to save company info', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenant->id,
            ]);

            return $this->error('Failed to save company information', 500);
        }
    }

    /**
     * Save business preferences (Step 2)
     *
     * Matches web form: preferences.blade.php
     */
    public function savePreferences(Request $request): JsonResponse
    {
        $validated = $request->validate([
            // Currency & Localization
            'default_currency' => 'required|string|max:3',
            'timezone' => 'required|timezone',
            'date_format' => 'required|string|in:d/m/Y,m/d/Y,Y-m-d,d-m-Y',
            'time_format' => 'required|string|in:12,24',

            // Business Settings
            'fiscal_year_start' => 'nullable|date_format:m-d',
            'default_tax_rate' => 'nullable|numeric|min:0|max:100',

            // Payment Methods (array of enabled methods)
            'payment_methods' => 'nullable|array',
            'payment_methods.*' => 'string|in:cash,bank_transfer,card,mobile_money,cheque',
        ]);

        $tenant = $request->user()->tenant;

        try {
            // Convert fiscal_year_start from m-d to full date (use current year)
            $fiscalYearDate = date('Y') . '-' . $validated['fiscal_year_start'];

            // Get existing settings or initialize empty array
            $settings = $tenant->settings ?? [];

            // Merge preferences into settings JSON
            $settings['currency'] = $validated['default_currency'];
            $settings['timezone'] = $validated['timezone'];
            $settings['date_format'] = $validated['date_format'];
            $settings['time_format'] = $validated['time_format'];
            $settings['default_tax_rate'] = $validated['default_tax_rate'] ?? 0;
            $settings['payment_methods'] = $validated['payment_methods'] ?? ['cash', 'bank_transfer'];

            // Update tenant with fiscal_year_start as DATE and other settings in JSON
            $tenant->update([
                'fiscal_year_start' => $fiscalYearDate,
                'settings' => $settings,
            ]);

            return $this->success([
                'tenant' => $tenant->fresh(),
                'next_step' => 'complete',
            ], 'Preferences saved successfully');

        } catch (\Exception $e) {
            Log::error('Failed to save preferences', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenant->id,
            ]);

            return $this->error('Failed to save preferences', 500);
        }
    }

    /**
     * Complete onboarding (with or without filling forms)
     *
     * This marks onboarding as complete and seeds default data if needed.
     * Called when:
     * - User clicks "Quick Start" (skip onboarding)
     * - User completes all steps
     */
    public function complete(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;

        try {
            DB::beginTransaction();

            // Seed default data FIRST if not already seeded
            $this->seedDefaultData($tenant);

            // Create default roles if they don't exist
            $this->createDefaultRoles($tenant);

            // Assign owner role to the current user (CRITICAL for web access)
            $this->assignOwnerRoleToUser($request->user(), $tenant);

            // Mark onboarding as completed AFTER seeding succeeds
            // Use retry operation with connection refresh for reliability
            $this->retryOperation(function() use ($tenant) {
                $this->refreshDatabaseConnection();

                // Use DB::table() instead of Eloquent to bypass model cache
                DB::table('tenants')
                    ->where('id', $tenant->id)
                    ->update([
                        'onboarding_completed' => true,
                        'onboarding_completed_at' => now(),
                        'updated_at' => now(),
                    ]);

                Log::info('Onboarding marked as completed', [
                    'tenant_id' => $tenant->id,
                    'completed_at' => now()
                ]);
            }, "Marking onboarding complete for tenant: {$tenant->id}");

            DB::commit();

            return $this->success([
                'tenant' => $tenant->fresh(),
                'message' => 'Welcome to your dashboard! Your business is now set up and ready to go.',
            ], 'Onboarding completed successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to complete onboarding', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenant->id,
                'trace' => $e->getTraceAsString()
            ]);

            return $this->error('Failed to complete onboarding: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Skip onboarding and go straight to dashboard
     *
     * Uses default values for everything
     */
    public function skip(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;

        try {
            DB::beginTransaction();

            // Set default values if not already set
            $settings = $tenant->settings ?? [];
            if (empty($settings['currency'])) {
                $settings['currency'] = 'NGN';
                $settings['timezone'] = 'Africa/Lagos';
                $settings['date_format'] = 'd/m/Y';
                $settings['time_format'] = '12';
                $settings['default_tax_rate'] = 0;
                $settings['payment_methods'] = ['cash', 'bank_transfer'];

                // Use retry operation for settings update
                $this->retryOperation(function() use ($tenant, $settings) {
                    $this->refreshDatabaseConnection();

                    $tenant->update([
                        'fiscal_year_start' => date('Y') . '-01-01', // January 1st of current year
                        'settings' => $settings
                    ]);
                }, "Updating default settings for tenant: {$tenant->id}");
            }

            // Seed defaults FIRST
            $this->seedDefaultData($tenant);
            $this->createDefaultRoles($tenant);

            // Assign owner role to the current user (CRITICAL for web access)
            $this->assignOwnerRoleToUser($request->user(), $tenant);

            // Mark as completed AFTER seeding succeeds
            $this->retryOperation(function() use ($tenant) {
                $this->refreshDatabaseConnection();

                // Use DB::table() instead of Eloquent to bypass model cache
                DB::table('tenants')
                    ->where('id', $tenant->id)
                    ->update([
                        'onboarding_completed' => true,
                        'onboarding_completed_at' => now(),
                        'updated_at' => now(),
                    ]);

                Log::info('Onboarding skipped and marked as completed', [
                    'tenant_id' => $tenant->id,
                    'completed_at' => now()
                ]);
            }, "Marking onboarding complete (skipped) for tenant: {$tenant->id}");

            DB::commit();

            return $this->success([
                'tenant' => $tenant->fresh(),
                'message' => 'Setup complete! Sensible defaults have been applied. You can customize settings anytime.',
            ], 'Onboarding skipped successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to skip onboarding', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenant->id,
                'trace' => $e->getTraceAsString()
            ]);

            return $this->error('Failed to skip onboarding: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Determine the current step based on tenant data
     */
    private function determineCurrentStep(Tenant $tenant): string
    {
        if ($tenant->onboarding_completed) {
            return 'completed';
        }

        if (empty($tenant->business_structure)) {
            return 'company';
        }

        if (empty($tenant->fiscal_year_start)) {
            return 'preferences';
        }

        return 'complete';
    }

    /**
     * Seed default data for the tenant
     */
    private function seedDefaultData(Tenant $tenant)
    {
        try {
            // Only seed if not already seeded
            if (!$tenant->default_data_seeded) {
                // Extend execution time for seeding operations
                $originalTimeout = ini_get('max_execution_time');
                set_time_limit(300); // 5 minutes for seeding

                Log::info("Starting seeding for tenant", [
                    'tenant_id' => $tenant->id,
                    'tenant_name' => $tenant->name,
                    'original_timeout' => $originalTimeout
                ]);

                // Refresh connection before each seeding operation
                $this->refreshDatabaseConnection();

                // Seed Account Groups with retry mechanism
                $this->retryOperation(function() use ($tenant) {
                    AccountGroupSeeder::seedForTenant($tenant->id);
                }, "Account groups seeding for tenant: {$tenant->id}");

                // Seed Voucher Types with retry mechanism
                $this->retryOperation(function() use ($tenant) {
                    VoucherTypeSeeder::seedForTenant($tenant->id);
                }, "Voucher types seeding for tenant: {$tenant->id}");

                // Seed Default Ledger Accounts with retry mechanism and extended timeout
                Log::info("Starting ledger accounts seeding (largest dataset)", [
                    'tenant_id' => $tenant->id
                ]);

                $this->retryOperation(function() use ($tenant) {
                    $this->refreshDatabaseConnection();
                    DefaultLedgerAccountsSeeder::seedForTenant($tenant->id);
                }, "Ledger accounts seeding for tenant: {$tenant->id}", 5);

                // Verify ledger accounts were seeded
                $ledgerCount = \App\Models\LedgerAccount::where('tenant_id', $tenant->id)->count();
                Log::info("Ledger accounts seeded", [
                    'tenant_id' => $tenant->id,
                    'count' => $ledgerCount
                ]);

                if ($ledgerCount === 0) {
                    throw new \Exception("Ledger accounts seeding failed - no accounts created");
                }

                // Seed Default Banks with retry mechanism
                // This must happen AFTER ledger accounts are seeded
                Log::info("Starting default banks seeding", [
                    'tenant_id' => $tenant->id
                ]);

                $this->retryOperation(function() use ($tenant) {
                    DefaultBanksSeeder::seedForTenant($tenant->id);
                }, "Default banks seeding for tenant: {$tenant->id}");

                // Verify default bank was seeded
                $banksCount = \App\Models\Bank::where('tenant_id', $tenant->id)->count();
                Log::info("Default banks seeded", [
                    'tenant_id' => $tenant->id,
                    'count' => $banksCount
                ]);

                // Seed Product Categories with retry mechanism
                $this->retryOperation(function() use ($tenant) {
                    DefaultProductCategoriesSeeder::seedForTenant($tenant->id);
                }, "Product categories seeding for tenant: {$tenant->id}");

                // Seed Units with retry mechanism
                $this->retryOperation(function() use ($tenant) {
                    DefaultUnitsSeeder::seedForTenant($tenant->id);
                }, "Units seeding for tenant: {$tenant->id}");

                // Seed Default Shifts with retry mechanism
                $this->retryOperation(function() use ($tenant) {
                    DefaultShiftsSeeder::seedForTenant($tenant->id);
                }, "Default shifts seeding for tenant: {$tenant->id}");

                // Seed Default PFAs with retry mechanism
                $this->retryOperation(function() use ($tenant) {
                    DefaultPfasSeeder::seedForTenant($tenant->id);
                }, "Default PFAs seeding for tenant: {$tenant->id}");

                // Mark as seeded using DB::table() for reliability
                $this->refreshDatabaseConnection();
                DB::table('tenants')
                    ->where('id', $tenant->id)
                    ->update([
                        'default_data_seeded' => true,
                        'updated_at' => now(),
                    ]);

                // Final verification
                $accountGroupsCount = \App\Models\AccountGroup::where('tenant_id', $tenant->id)->count();
                $voucherTypesCount = \App\Models\VoucherType::where('tenant_id', $tenant->id)->count();
                $categoriesCount = \App\Models\ProductCategory::where('tenant_id', $tenant->id)->count();
                $unitsCount = \App\Models\Unit::where('tenant_id', $tenant->id)->count();
                $shiftsCount = \App\Models\ShiftSchedule::where('tenant_id', $tenant->id)->count();
                $pfasCount = \App\Models\Pfa::where('tenant_id', $tenant->id)->count();

                Log::info("All default data seeded successfully", [
                    'tenant_id' => $tenant->id,
                    'account_groups' => $accountGroupsCount,
                    'voucher_types' => $voucherTypesCount,
                    'ledger_accounts' => $ledgerCount,
                    'banks' => $banksCount,
                    'product_categories' => $categoriesCount,
                    'units' => $unitsCount,
                    'shifts' => $shiftsCount,
                    'pfas' => $pfasCount,
                    'total' => $accountGroupsCount + $voucherTypesCount + $ledgerCount + $banksCount + $categoriesCount + $unitsCount + $shiftsCount + $pfasCount
                ]);

                // Restore original timeout
                set_time_limit((int)$originalTimeout);
            }

        } catch (\Exception $e) {
            Log::error('Failed to seed default data', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenant->id,
                'trace' => $e->getTraceAsString()
            ]);
            // Don't throw - allow onboarding to complete even if seeding fails
        }
    }

    /**
     * Refresh database connection to avoid prepared statement issues
     */
    private function refreshDatabaseConnection()
    {
        try {
            DB::disconnect();
            DB::reconnect();
            DB::connection()->getPdo();
            usleep(100000); // 100ms delay
        } catch (\Exception $e) {
            Log::warning('Database connection refresh failed, retrying: ' . $e->getMessage());
            sleep(1);
            DB::disconnect();
            DB::reconnect();
        }
    }

    /**
     * Retry an operation multiple times with connection refresh
     */
    private function retryOperation($callback, $logMessage, $maxAttempts = 3)
    {
        $attempts = 0;

        while ($attempts < $maxAttempts) {
            try {
                if ($attempts > 0) {
                    sleep(1); // Wait before retry
                    $this->refreshDatabaseConnection();
                }

                Log::info("Executing: {$logMessage}" . ($attempts > 0 ? " (Attempt " . ($attempts + 1) . ")" : ""));
                $callback();
                Log::info("Success: {$logMessage}");
                return;

            } catch (\Exception $e) {
                $attempts++;
                Log::warning("Failed: {$logMessage} (Attempt {$attempts}/{$maxAttempts})", [
                    'error' => $e->getMessage()
                ]);

                if ($attempts >= $maxAttempts) {
                    Log::error("Final failure after {$maxAttempts} attempts: {$logMessage}", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            }
        }
    }

    /**
     * Create default roles for the tenant
     */
    private function createDefaultRoles(Tenant $tenant)
    {
        try {
            $rolesExist = \App\Models\Tenant\Role::where('tenant_id', $tenant->id)->exists();

            if (!$rolesExist) {
                Log::info("Starting permissions seeding", [
                    'tenant_id' => $tenant->id
                ]);

                // First, ensure permissions exist
                $this->retryOperation(function() {
                    $permissionsSeeder = new PermissionsSeeder();
                    $permissionsSeeder->run();
                }, "Permissions seeding for tenant: {$tenant->id}");

                // Now create default roles with their permissions
                $defaultRoles = [
                    [
                        'name' => 'Owner',
                        'description' => 'Full system access with all permissions',
                        'color' => '#dc2626',
                        'priority' => 100,
                        'permissions' => 'all',
                    ],
                    [
                        'name' => 'Admin',
                        'description' => 'Administrative access to most system features',
                        'color' => '#7c3aed',
                        'priority' => 90,
                        'permissions' => ['dashboard.view', 'admin.users.manage', 'admin.roles.manage', 'admin.teams.manage', 'settings.view', 'settings.company.manage', 'reports.view', 'reports.export', 'audit.view'],
                    ],
                    [
                        'name' => 'Manager',
                        'description' => 'Management access to business operations',
                        'color' => '#059669',
                        'priority' => 80,
                        'permissions' => ['dashboard.view', 'accounting.view', 'accounting.invoices.manage', 'inventory.view', 'inventory.products.manage', 'crm.view', 'crm.customers.manage', 'crm.vendors.manage', 'reports.view'],
                    ],
                    [
                        'name' => 'Accountant',
                        'description' => 'Access to financial and accounting features',
                        'color' => '#2563eb',
                        'priority' => 70,
                        'permissions' => ['dashboard.view', 'accounting.view', 'accounting.invoices.manage', 'accounting.vouchers.manage', 'accounting.reports.view', 'payroll.view', 'payroll.process', 'banking.view', 'reports.view'],
                    ],
                    [
                        'name' => 'Sales Representative',
                        'description' => 'Access to sales and customer management features',
                        'color' => '#ea580c',
                        'priority' => 60,
                        'permissions' => ['dashboard.view', 'crm.view', 'crm.customers.manage', 'accounting.invoices.manage', 'pos.access', 'pos.sales.process', 'inventory.view'],
                    ],
                    [
                        'name' => 'Employee',
                        'description' => 'Basic access for regular employees',
                        'color' => '#64748b',
                        'priority' => 30,
                        'permissions' => ['dashboard.view'],
                    ],
                ];

                foreach ($defaultRoles as $roleData) {
                    $permissions = $roleData['permissions'];
                    unset($roleData['permissions']);

                    $role = \App\Models\Tenant\Role::firstOrCreate(
                        [
                            'name' => $roleData['name'],
                            'tenant_id' => $tenant->id
                        ],
                        array_merge($roleData, [
                            'slug' => \Illuminate\Support\Str::slug($roleData['name']) . '-' . $tenant->id,
                            'tenant_id' => $tenant->id,
                            'is_active' => true,
                            'is_default' => true,
                        ])
                    );

                    // Assign permissions to the role
                    if ($permissions === 'all') {
                        $allPermissions = \App\Models\Tenant\Permission::all();
                        $role->permissions()->sync($allPermissions->pluck('id')->toArray());
                    } else {
                        $permissionIds = \App\Models\Tenant\Permission::whereIn('slug', $permissions)->pluck('id')->toArray();
                        $role->permissions()->sync($permissionIds);
                    }

                    Log::info("Role created", [
                        'tenant_id' => $tenant->id,
                        'role_name' => $role->name,
                        'role_id' => $role->id,
                        'permissions_count' => $permissions === 'all' ? 'all' : count($permissions)
                    ]);
                }

                Log::info('Default roles created successfully', [
                    'tenant_id' => $tenant->id,
                    'roles_count' => count($defaultRoles)
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to create default roles', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenant->id,
                'trace' => $e->getTraceAsString()
            ]);
            // Don't throw - allow onboarding to complete
        }
    }

    /**
     * Assign owner role to the user completing onboarding
     * This ensures users who register via mobile can access web features
     */
    private function assignOwnerRoleToUser($user, Tenant $tenant)
    {
        try {
            if (!$user) {
                Log::warning("Cannot assign owner role - user is null", [
                    'tenant_id' => $tenant->id
                ]);
                return;
            }

            $ownerRole = \App\Models\Tenant\Role::where('name', 'Owner')
                ->where('tenant_id', $tenant->id)
                ->first();

            if ($ownerRole) {
                // Use syncWithoutDetaching to avoid removing other roles
                $user->roles()->syncWithoutDetaching([$ownerRole->id]);

                Log::info("Owner role assigned to user during mobile onboarding", [
                    'tenant_id' => $tenant->id,
                    'user_id' => $user->id,
                    'role_id' => $ownerRole->id,
                    'role_name' => $ownerRole->name
                ]);
            } else {
                Log::warning("Could not assign owner role - Owner role not found", [
                    'tenant_id' => $tenant->id,
                    'user_id' => $user->id
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to assign owner role to user', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenant->id,
                'user_id' => $user ? $user->id : null,
                'trace' => $e->getTraceAsString()
            ]);
            // Don't throw - allow onboarding to complete even if role assignment fails
        }
    }
}
