<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class InvitationController extends Controller
{
    public function accept(Tenant $tenant, $token)
    {
        try {
            $decrypted = decrypt($token);
            [$userId, $tenantId] = explode('|', $decrypted);

            if ($tenantId != $tenant->id) {
                abort(404);
            }

            $user = User::findOrFail($userId);
            $invitation = $user->tenants()->where('tenant_id', $tenant->id)->first();

            if (!$invitation) {
                abort(404, 'Invitation not found');
            }

            if ($invitation->pivot->accepted_at) {
                return redirect()->route('tenant.dashboard', ['tenant' => $tenant->slug])
                    ->with('info', 'You have already accepted this invitation.');
            }

            return view('tenant.invitation.accept', compact('tenant', 'user', 'token'));

        } catch (\Exception $e) {
            abort(404, 'Invalid invitation token');
        }
    }

    public function processAcceptance(Request $request, Tenant $tenant, $token)
    {
        try {
            $decrypted = decrypt($token);
            [$userId, $tenantId] = explode('|', $decrypted);

            if ($tenantId != $tenant->id) {
                abort(404);
            }

            $user = User::findOrFail($userId);
            $invitation = $user->tenants()->where('tenant_id', $tenant->id)->first();

            if (!$invitation || $invitation->pivot->accepted_at) {
                abort(404);
            }

            // If user doesn't have a verified email, they need to set a password
            if (!$user->email_verified_at) {
                $request->validate([
                    'password' => ['required', 'confirmed', Rules\Password::defaults()],
                ]);

                $user->update([
                    'password' => Hash::make($request->password),
                    'email_verified_at' => now(),
                ]);
            }

            // Update invitation status
            $user->tenants()->updateExistingPivot($tenant->id, [
                'accepted_at' => now(),
                'is_active' => true,
            ]);

            // Log the user in
            Auth::login($user);

            return redirect()->route('tenant.dashboard', ['tenant' => $tenant->slug])
                ->with('success', 'Welcome to ' . $tenant->name . '! Your invitation has been accepted.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to process invitation. Please try again.']);
        }
    }
}