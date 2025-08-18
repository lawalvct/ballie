<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Helpers\TenantHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $query = Tenant::with(['superAdmin', 'users']);

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
            $query->where('subscription_plan', $request->plan);
        }

        $tenants = $query->latest()->paginate(20);

        return view('super-admin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('super-admin.tenants.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email',
            'phone' => 'nullable|string|max:20',
            'business_type' => 'required|string',
            'subscription_plan' => 'required|in:starter,professional,enterprise',
            'billing_cycle' => 'required|in:monthly,yearly',

            // Owner details
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email',
            'owner_password' => 'required|string|min:8|confirmed',
        ]);

        // Create tenant
        $tenant = Tenant::create([
            'name' => $validated['name'],
            'slug' => TenantHelper::generateUniqueSlug($validated['name']),
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'business_type' => $validated['business_type'],
            'subscription_plan' => $validated['subscription_plan'],
            'billing_cycle' => $validated['billing_cycle'],
            'subscription_status' => 'trial',
            'trial_ends_at' => now()->addDays(30),
            'created_by' => Auth::guard('super_admin')->id(),
            'is_active' => true,
        ]);

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

        return redirect()
            ->route('super-admin.tenants.show', $tenant)
            ->with('success', 'Tenant created successfully!');
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['users', 'subscriptions', 'superAdmin']);

        return view('super-admin.tenants.show', compact('tenant'));
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
            'subscription_plan' => 'required|in:starter,professional,enterprise',
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
}
