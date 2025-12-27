<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Helpers\TenantHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $query = Tenant::with(['superAdmin', 'users', 'plan']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('subscription_status', $request->status);
        }

        // Filter by plan
        if ($request->filled('plan')) {
            $query->whereHas('plan', function ($q) use ($request) {
                $q->where('slug', $request->plan);
            });
        }

        $tenants = $query->latest()->paginate(20);

        // Get available plans for filtering
        $availablePlans = \App\Models\Plan::where('is_active', true)->orderBy('sort_order')->get();

        return view('super-admin.tenants.index', compact('tenants', 'availablePlans'));
    }

    public function create()
    {
        // Get available plans for selection
        $plans = \App\Models\Plan::where('is_active', true)->orderBy('sort_order')->get();

        return view('super-admin.tenants.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email',
            'phone' => 'nullable|string|max:20',
            'business_type' => 'required|string',
            'plan_id' => 'required|integer|exists:plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',

            // Owner details
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|unique:users,email',
            'owner_password' => 'required|string|min:8|confirmed',
        ]);

        try {
            DB::beginTransaction();

            // Get the selected plan
            $selectedPlan = \App\Models\Plan::findOrFail($validated['plan_id']);

            // Create tenant
            $tenant = Tenant::create([
                'name' => $validated['name'],
                'slug' => TenantHelper::generateUniqueSlug($validated['name']),
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'business_type' => $validated['business_type'],
                'plan_id' => $selectedPlan->id,
                'billing_cycle' => $validated['billing_cycle'],
                'subscription_status' => 'trial',
                'trial_ends_at' => now()->addDays(30),
                'created_by' => Auth::guard('super_admin')->id(),
                'is_active' => true,
            ]);

            // Start trial for the selected plan
            $tenant->startTrial($selectedPlan);

            // Create owner user
            User::create([
                'tenant_id' => $tenant->id,
                'name' => $validated['owner_name'],
                'email' => $validated['owner_email'],
                'password' => Hash::make($validated['owner_password']),
                'role' => User::ROLE_OWNER,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            DB::commit();

            return redirect()
                ->route('super-admin.tenants.show', $tenant)
                ->with('success', 'Tenant created successfully! A 30-day trial for the ' . $selectedPlan->name . ' plan has been started.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create tenant: ' . $e->getMessage()]);
        }
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['users', 'subscriptions', 'superAdmin', 'plan', 'businessType']);

        // Get real stats for this tenant
        $stats = [
            'customers_count' => \App\Models\Customer::where('tenant_id', $tenant->id)->count(),
            'products_count' => \App\Models\Product::where('tenant_id', $tenant->id)->count(),
            'vouchers_count' => \App\Models\Voucher::where('tenant_id', $tenant->id)->count(),
            'ledger_accounts_count' => \App\Models\LedgerAccount::where('tenant_id', $tenant->id)->count(),
            'invoices_count' => \App\Models\Voucher::where('tenant_id', $tenant->id)
                ->whereHas('voucherType', fn($q) => $q->whereIn('code', ['SV', 'SALES', 'INV']))
                ->count(),
            'total_revenue' => \App\Models\Voucher::where('tenant_id', $tenant->id)
                ->whereHas('voucherType', fn($q) => $q->whereIn('code', ['SV', 'SALES', 'INV']))
                ->where('status', 'posted')
                ->sum('total_amount'),
        ];

        return view('super-admin.tenants.show', compact('tenant', 'stats'));
    }

    public function edit(Tenant $tenant)
    {
        return view('super-admin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email,' . $tenant->id,
            'phone' => 'nullable|string|max:20',
            'business_type' => 'required|string',
            'plan_id' => 'nullable|integer|exists:plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
            'is_active' => 'boolean',
        ]);

        $tenant->update($validated);

        return redirect()
            ->route('super-admin.tenants.show', $tenant)
            ->with('success', 'Tenant updated successfully!');
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();

        return redirect()
            ->route('super-admin.tenants.index')
            ->with('success', 'Tenant deleted successfully!');
    }

    public function suspend(Tenant $tenant)
    {
        $tenant->update([
            'is_active' => false,
            'subscription_status' => 'suspended'
        ]);

        return back()->with('success', 'Tenant suspended successfully!');
    }

    public function activate(Tenant $tenant)
    {
        $tenant->update([
            'is_active' => true,
            'subscription_status' => $tenant->trial_ends_at && $tenant->trial_ends_at->isFuture() ? 'trial' : 'active'
        ]);

        return back()->with('success', 'Tenant activated successfully!');
    }

    public function impersonate(Tenant $tenant, User $user)
    {
        if (!Auth::guard('super_admin')->user()->canImpersonate()) {
            abort(403, 'Unauthorized');
        }

        session([
            'impersonating_user_id' => $user->id,
            'super_admin_id' => Auth::guard('super_admin')->id()
        ]);

        return redirect()->route('tenant.dashboard', ['tenant' => $tenant->slug]);
    }

    public function stopImpersonation()
    {
        session()->forget(['impersonating_user_id', 'super_admin_id']);

        return redirect()->route('super-admin.dashboard');
    }

    /**
     * Show invitation form
     */
    public function invite()
    {
        // Get available plans for selection
        $plans = \App\Models\Plan::where('is_active', true)->orderBy('sort_order')->get();

        return view('super-admin.tenants.invite', compact('plans'));
    }

    /**
     * Send invitation email
     */
    public function sendInvitation(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email|unique:tenants,email',
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'business_type' => 'required|string',
            'plan_id' => 'required|integer|exists:plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
            'message' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Generate unique invitation token
            $token = bin2hex(random_bytes(32));

            // Get the selected plan
            $selectedPlan = \App\Models\Plan::findOrFail($validated['plan_id']);

            // Create pending tenant invitation record
            DB::table('tenant_invitations')->insert([
                'token' => $token,
                'company_name' => $validated['company_name'],
                'company_email' => $validated['company_email'],
                'owner_name' => $validated['owner_name'],
                'owner_email' => $validated['owner_email'],
                'phone' => $validated['phone'],
                'business_type' => $validated['business_type'],
                'plan_id' => $validated['plan_id'],
                'billing_cycle' => $validated['billing_cycle'],
                'message' => $validated['message'],
                'expires_at' => now()->addDays(7), // 7 days to accept
                'created_by' => Auth::guard('super_admin')->id(),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Try to send invitation email
            try {
                \Illuminate\Support\Facades\Mail::to($validated['owner_email'])
                    ->send(new \App\Mail\TenantInvitation($token, $validated, $selectedPlan));

                $emailSent = true;
                $message = 'Invitation sent successfully to ' . $validated['owner_email'] . '! They have 7 days to accept.';
            } catch (\Exception $mailException) {
                // Log mail error but don't fail the entire process
                \Illuminate\Support\Facades\Log::error('Failed to send invitation email', [
                    'error' => $mailException->getMessage(),
                    'email' => $validated['owner_email'],
                    'token' => $token
                ]);

                $emailSent = false;
                $message = 'Invitation created but email failed to send due to mail server issues. Please share the invitation link manually.';
            }

            DB::commit();

            if (!$emailSent) {
                return redirect()
                    ->route('super-admin.tenants.invite')
                    ->with('error', 'Mail server connection failed. Please check your MAIL configuration in .env file.');
            }

            return redirect()
                ->route('super-admin.tenants.invite')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            \Illuminate\Support\Facades\Log::error('Failed to create tenant invitation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $validated ?? $request->all()
            ]);

            return redirect()
                ->route('super-admin.tenants.invite')
                ->withInput()
                ->with('error', 'Failed to create invitation. Please try again.');
        }
    }

    /**
     * Export tenants to CSV
     */
    public function export(Request $request)
    {
        $query = Tenant::with(['users', 'plan', 'superAdmin']);

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('subscription_status', $request->status);
        }

        if ($request->filled('plan')) {
            $query->whereHas('plan', function ($q) use ($request) {
                $q->where('slug', $request->plan);
            });
        }

        // Export selected IDs if provided
        if ($request->filled('ids')) {
            $ids = explode(',', $request->ids);
            $query->whereIn('id', $ids);
        }

        $tenants = $query->latest()->get();

        $filename = 'companies-' . now()->format('Y-m-d-H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($tenants) {
            $file = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($file, [
                'ID', 'Company Name', 'Email', 'Slug', 'Phone', 'Business Type',
                'Status', 'Plan', 'Billing Cycle', 'Users Count', 'Active Users',
                'Trial Ends', 'Created By', 'Created At', 'Updated At', 'Is Active'
            ]);

            foreach ($tenants as $tenant) {
                fputcsv($file, [
                    $tenant->id,
                    $tenant->name,
                    $tenant->email,
                    $tenant->slug,
                    $tenant->phone ?? 'N/A',
                    $tenant->business_type ?? 'N/A',
                    ucfirst($tenant->subscription_status),
                    $tenant->plan->name ?? 'N/A',
                    ucfirst($tenant->billing_cycle ?? 'N/A'),
                    $tenant->users->count(),
                    $tenant->users->where('is_active', true)->count(),
                    $tenant->trial_ends_at ? $tenant->trial_ends_at->format('Y-m-d H:i:s') : 'N/A',
                    $tenant->superAdmin->name ?? 'System',
                    $tenant->created_at->format('Y-m-d H:i:s'),
                    $tenant->updated_at->format('Y-m-d H:i:s'),
                    $tenant->is_active ? 'Yes' : 'No'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
