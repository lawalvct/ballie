<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Plan;
use Carbon\Carbon;

class InvitationController extends Controller
{
    /**
     * Show the invitation acceptance page
     */
    public function show($token)
    {
        // Find the invitation
        $invitation = DB::table('tenant_invitations')
            ->where('token', $token)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if (!$invitation) {
            return view('invitation.expired-or-invalid');
        }

        // Get the plan details
        $plan = Plan::find($invitation->plan_id);

        return view('invitation.accept', [
            'invitation' => $invitation,
            'plan' => $plan,
            'token' => $token
        ]);
    }

    /**
     * Accept the invitation and create the tenant account
     */
    public function accept(Request $request, $token)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'required|accepted',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Find the invitation
        $invitation = DB::table('tenant_invitations')
            ->where('token', $token)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if (!$invitation) {
            return view('invitation.expired-or-invalid');
        }

        try {
            DB::beginTransaction();

            // Create the tenant
            $tenant = Tenant::create([
                'name' => $invitation->company_name,
                'email' => $invitation->company_email,
                'phone' => $invitation->phone,
                'business_type' => $invitation->business_type,
                'slug' => $this->generateUniqueSlug($invitation->company_name),
                'plan_id' => $invitation->plan_id,
                'billing_cycle' => $invitation->billing_cycle,
                'subscription_status' => 'trial',
                'trial_ends_at' => now()->addDays(30), // 30-day trial
                'is_active' => true,
            ]);

            // Create the owner user
            $owner = User::create([
                'name' => $invitation->owner_name,
                'email' => $invitation->owner_email,
                'password' => Hash::make($request->password),
                'role' => 'owner',
                'tenant_id' => $tenant->id,
                'is_active' => true,
                'email_verified_at' => now(), // Auto-verify since they accepted invitation
            ]);

            // Update the invitation status
            DB::table('tenant_invitations')
                ->where('id', $invitation->id)
                ->update([
                    'status' => 'accepted',
                    'accepted_at' => now(),
                    'tenant_id' => $tenant->id,
                    'updated_at' => now(),
                ]);

            DB::commit();

            // Log the user in
            auth()->login($owner);

            // Redirect to tenant setup or dashboard
            return redirect()
                ->intended('/dashboard') // or wherever new tenants should go
                ->with('success', 'Welcome to Ballie! Your account has been created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'There was an error creating your account. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Generate a unique slug for the tenant
     */
    private function generateUniqueSlug($companyName)
    {
        $baseSlug = Str::slug($companyName);
        $slug = $baseSlug;
        $counter = 1;

        while (Tenant::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
