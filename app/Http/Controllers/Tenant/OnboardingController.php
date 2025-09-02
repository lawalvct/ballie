<?php

namespace App\Http\Controllers\Tenant;
use App\Models\ProductCategory;
use App\Models\Unit;
use App\Models\LedgerAccount;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Database\Seeders\AccountGroupSeeder;
use Database\Seeders\VoucherTypeSeeder;
use Database\Seeders\DefaultLedgerAccountsSeeder;
use Database\Seeders\DefaultProductCategoriesSeeder;
use Database\Seeders\DefaultUnitsSeeder;

class OnboardingController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Clear any existing prepared statements and reconnect
            $this->refreshDatabaseConnection();

            $result = DB::transaction(function () use ($request) {
                $tenant = $this->createTenant($request);
                $this->seedDefaultData($tenant);

                return [
                    'success' => true,
                    'message' => 'Tenant onboarded successfully with default data',
                    'tenant' => $tenant
                ];
            }, 3); // Retry up to 3 times

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Onboarding failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['password', 'password_confirmation'])
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Onboarding failed: ' . $e->getMessage()
            ], 500);
        }
    }

    private function refreshDatabaseConnection()
    {
        // Disconnect and reconnect to refresh prepared statements
        DB::disconnect();
        DB::reconnect();

        // Clear query cache
        DB::getQueryLog();
    }

   private function seedDefaultData($tenant)
    {
        try {
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

            // Seed Default Ledger Accounts with retry mechanism
            $this->retryOperation(function() use ($tenant) {
                DefaultLedgerAccountsSeeder::seedForTenant($tenant->id);
            }, "Ledger accounts seeding for tenant: {$tenant->id}");

            // Seed Product Categories with retry mechanism
            $this->retryOperation(function() use ($tenant) {
                DefaultProductCategoriesSeeder::seedForTenant($tenant->id);
            }, "Product categories seeding for tenant: {$tenant->id}");

            // Seed Units with retry mechanism
            $this->retryOperation(function() use ($tenant) {
                DefaultUnitsSeeder::seedForTenant($tenant->id);
            }, "Units seeding for tenant: {$tenant->id}");

            Log::info("All default data seeded successfully for tenant: {$tenant->name} (ID: {$tenant->id})");

        } catch (\Exception $e) {
            Log::error("Error seeding default data for tenant {$tenant->id}: " . $e->getMessage());
            throw $e;
        }
    }

    private function retryOperation($callback, $logMessage, $maxAttempts = 3)
    {
        $attempts = 0;

        while ($attempts < $maxAttempts) {
            try {
                if ($attempts > 0) {
                    // Wait a bit before retry and refresh connection
                    sleep(1);
                    $this->refreshDatabaseConnection();
                }

                $callback();
                Log::info($logMessage . " - Success on attempt " . ($attempts + 1));
                return;

            } catch (\Exception $e) {
                $attempts++;
                Log::warning($logMessage . " - Failed on attempt {$attempts}: " . $e->getMessage());

                if ($attempts >= $maxAttempts) {
                    throw $e;
                }

                // If it's the prepared statement error, definitely refresh connection
                if (strpos($e->getMessage(), '1615') !== false ||
                    strpos($e->getMessage(), 'Prepared statement') !== false) {
                    $this->refreshDatabaseConnection();
                }
            }
        }
    }

    private function createTenant($request)
    {
        // Refresh connection before creating tenant
        $this->refreshDatabaseConnection();

        return Tenant::create([
            // Add any default tenant data here if needed
        ]);
    }

   public function checkOnboardingStatus($tenantId)
    {
        try {
            // Refresh connection before status check
            $this->refreshDatabaseConnection();

            $accountGroupsCount = \App\Models\AccountGroup::where('tenant_id', $tenantId)->count();
            $voucherTypesCount = \App\Models\VoucherType::where('tenant_id', $tenantId)->count();
            $ledgerAccountsCount = \App\Models\LedgerAccount::where('tenant_id', $tenantId)->count();
            $categoriesCount = \App\Models\ProductCategory::where('tenant_id', $tenantId)->count();
            $unitsCount = \App\Models\Unit::where('tenant_id', $tenantId)->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'account_groups' => $accountGroupsCount,
                    'voucher_types' => $voucherTypesCount,
                    'ledger_accounts' => $ledgerAccountsCount,
                    'product_categories' => $categoriesCount,
                    'units' => $unitsCount,
                    'total_seeded_items' => $accountGroupsCount + $voucherTypesCount + $ledgerAccountsCount + $categoriesCount + $unitsCount
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking onboarding status', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error checking onboarding status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reseedDefaultData($tenantId)
    {
        try {
            $tenant = \App\Models\Tenant::findOrFail($tenantId);

            // Refresh connection before reseeding
            $this->refreshDatabaseConnection();

            DB::transaction(function () use ($tenant) {
                $this->seedDefaultData($tenant);
            }, 3); // Retry transaction up to 3 times

            return response()->json([
                'success' => true,
                'message' => 'Default data re-seeded successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Re-seeding failed', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Re-seeding failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index(Tenant $tenant)
    {
        if ($tenant->onboarding_completed_at) {
            return redirect()->route('tenant.dashboard', ['tenant' => $tenant->slug]);
        }

        return view('tenant.onboarding.index', compact('tenant'));
    }

    public function showStep(Tenant $tenant, $step)
    {
        if ($tenant->onboarding_completed_at) {
            return redirect()->route('tenant.dashboard', ['tenant' => $tenant->slug]);
        }

        $validSteps = ['company', 'preferences', 'team', 'complete'];

        if (!in_array($step, $validSteps)) {
            return redirect()->route('tenant.onboarding.index', ['tenant' => $tenant->slug]);
        }

        return view("tenant.onboarding.steps.{$step}", compact('tenant'));
    }

    public function saveStep(Request $request, Tenant $tenant, $step)
    {
        switch ($step) {
            case 'company':
                return $this->saveCompanyStep($request, $tenant);
            case 'preferences':
                return $this->savePreferencesStep($request, $tenant);
            case 'team':
                return $this->saveTeamStep($request, $tenant);
            default:
                return redirect()->route('tenant.onboarding.index', ['tenant' => $tenant->slug]);
        }
    }

    private function saveCompanyStep(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'business_type' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:50',
            'rc_number' => 'nullable|string|max:50',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['logo']);

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('tenant-logos', 'public');
            $data['logo'] = $logoPath;
        }

        // Refresh connection before update
        $this->refreshDatabaseConnection();
        $tenant->update($data);

        $progress = $tenant->onboarding_progress ?? [];
        $progress['company'] = true;
        $tenant->update(['onboarding_progress' => $progress]);

        return redirect()->route('tenant.onboarding.step', [
            'tenant' => $tenant->slug,
            'step' => 'preferences'
        ])->with('success', 'Company information saved successfully!');
    }

    private function savePreferencesStep(Request $request, Tenant $tenant)
    {
        $request->validate([
            'currency' => 'required|string|size:3',
            'timezone' => 'required|string|max:50',
            'date_format' => 'required|string|max:20',
            'time_format' => 'required|string|max:10',
            'fiscal_year_start' => 'required|string|max:10',
            'invoice_prefix' => 'nullable|string|max:10',
            'quote_prefix' => 'nullable|string|max:10',
            'payment_terms' => 'nullable|integer|min:0|max:365',
            'default_tax_rate' => 'required|numeric|min:0|max:100',
            'tax_inclusive' => 'required|boolean',
            'enable_withholding_tax' => 'nullable|boolean',
            'features' => 'nullable|array',
            'features.*' => 'string|in:inventory,invoicing,customers,payroll,pos,reports',
        ]);

        $data = $request->all();
        $data['enable_withholding_tax'] = $request->boolean('enable_withholding_tax');
        $data['features'] = $request->input('features', []);

        $settings = $tenant->settings ?? [];
        $settings = array_merge($settings, $data);

        // Refresh connection before update
        $this->refreshDatabaseConnection();
        $tenant->update(['settings' => $settings]);

        $progress = $tenant->onboarding_progress ?? [];
        $progress['preferences'] = true;
        $tenant->update(['onboarding_progress' => $progress]);

        return redirect()->route('tenant.onboarding.step', [
            'tenant' => $tenant->slug,
            'step' => 'team'
        ])->with('success', 'Preferences saved successfully!');
    }

    public function saveTeamStep(Request $request, Tenant $tenant)
    {
        if ($request->has('skip_team') && $request->skip_team == '1') {
            return redirect()->route('tenant.onboarding.step', [
                'tenant' => $tenant->slug,
                'step' => 'complete'
            ])->with('success', 'Team setup skipped. You can add team members later from your dashboard.');
        }

        $teamMembers = $request->input('team_members', []);

        $validTeamMembers = array_filter($teamMembers, function($member) {
            return !empty($member['name']) || !empty($member['email']) || !empty($member['role']);
        });

        if (!empty($validTeamMembers)) {
            $rules = [];
            $messages = [];

            foreach ($validTeamMembers as $index => $member) {
                $rules["team_members.{$index}.name"] = 'required|string|max:255';
                $rules["team_members.{$index}.email"] = 'required|email|max:255';
                $rules["team_members.{$index}.role"] = 'required|string|in:admin,manager,accountant,sales,employee';
                $rules["team_members.{$index}.department"] = 'nullable|string|max:255';

                $messages["team_members.{$index}.name.required"] = "Team Member " . ($index + 1) . ": Name is required";
                $messages["team_members.{$index}.email.required"] = "Team Member " . ($index + 1) . ": Email is required";
                $messages["team_members.{$index}.email.email"] = "Team Member " . ($index + 1) . ": Please enter a valid email address";
                $messages["team_members.{$index}.role.required"] = "Team Member " . ($index + 1) . ": Role is required";
            }

            $request->validate($rules, $messages);

            foreach ($validTeamMembers as $memberData) {
                $this->createTeamMemberInvitation($tenant, $memberData);
            }

            $memberCount = count($validTeamMembers);
            $successMessage = "Great! {$memberCount} team member" . ($memberCount > 1 ? 's' : '') . " invited successfully.";
        } else {
            $successMessage = "Team setup completed. You can add team members later from your dashboard.";
        }

        $progress = $tenant->onboarding_progress ?? [];
        $progress['team'] = true;

        // Refresh connection before update
        $this->refreshDatabaseConnection();
        $tenant->update(['onboarding_progress' => $progress]);

        return redirect()->route('tenant.onboarding.step', [
            'tenant' => $tenant->slug,
            'step' => 'complete'
        ])->with('success', $successMessage);
    }

    private function createTeamMemberInvitation($tenant, $memberData)
    {
        Log::info('Team member invitation created', [
            'tenant_id' => $tenant->id,
            'member_data' => $memberData
        ]);
    }

    private function getCurrentTenant()
    {
        $routeParameters = request()->route()->parameters();
        if (isset($routeParameters['tenant'])) {
            if ($routeParameters['tenant'] instanceof Tenant) {
                return $routeParameters['tenant'];
            } else {
                return Tenant::where('slug', $routeParameters['tenant'])->firstOrFail();
            }
        }

        if (function_exists('tenant') && tenant()) {
            return tenant();
        }

        if (auth()->check() && auth()->user()->tenant_id) {
            return Tenant::find(auth()->user()->tenant_id);
        }

        throw new \Exception('Could not determine the current tenant.');
    }

    public function complete(Request $request, Tenant $tenant)
    {
        try {
            $tenant = $request->route('tenant');

            // Refresh connection before completion
            $this->refreshDatabaseConnection();

            // Use transaction with retry for the completion process
            DB::transaction(function () use ($tenant) {
                // Seed default data for the tenant
                $this->seedDefaultData($tenant);

                $tenant->update([
                    'onboarding_completed_at' => now(),
                    'onboarding_progress' => [
                        'company' => true,
                        'preferences' => true,
                        'team' => true,
                        'complete' => true
                    ]
                ]);
            }, 3);

            return redirect()->route('tenant.dashboard', ['tenant' => $tenant->slug])
                ->with('success', 'Welcome to Ballie! Your account is now fully set up and ready to use.');

        } catch (\Exception $e) {
            Log::error('Onboarding completion failed', [
                'tenant_id' => $tenant->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'There was an error completing your onboarding. Please try again or contact support.');
        }
    }
}
