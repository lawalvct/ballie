<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the user's profile
     */
    public function index(Request $request, Tenant $tenant)
    {
        $user = $request->user();

        return view('tenant.profile.index', [
            'tenant' => $tenant,
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information
     */
    public function update(Request $request, Tenant $tenant)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        // Update user
        $user->fill($validated);

        // If email changed, reset email verification
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()
            ->route('tenant.profile.index', ['tenant' => $tenant->slug])
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Update the user's password
     */
    public function updatePassword(Request $request, Tenant $tenant)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Update password
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('tenant.profile.index', ['tenant' => $tenant->slug])
            ->with('success', 'Password updated successfully!');
    }

    /**
     * Remove the user's avatar
     */
    public function removeAvatar(Request $request, Tenant $tenant)
    {
        $user = $request->user();

        // Delete avatar file if exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => null]);

        return redirect()
            ->route('tenant.profile.index', ['tenant' => $tenant->slug])
            ->with('success', 'Avatar removed successfully!');
    }
}
