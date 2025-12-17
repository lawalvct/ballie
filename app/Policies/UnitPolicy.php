<?php

namespace App\Policies;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UnitPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view units
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Unit $unit): bool
    {
        // User can view units from their tenant
        return $user->tenant_id === $unit->tenant_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Owner, Admin, and Manager can create units
        return in_array($user->role, ['owner', 'admin', 'manager']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Unit $unit): bool
    {
        // User must be from the same tenant and have appropriate role
        if ($user->tenant_id !== $unit->tenant_id) {
            return false;
        }

        return in_array($user->role, ['owner', 'admin', 'manager']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Unit $unit): bool
    {
        // User must be from the same tenant and have appropriate role
        if ($user->tenant_id !== $unit->tenant_id) {
            return false;
        }

        // Cannot delete if unit is being used by products
        if ($unit->products()->count() > 0) {
            return false;
        }

        // Cannot delete if unit has derived units
        if ($unit->derivedUnits()->count() > 0) {
            return false;
        }

        return in_array($user->role, ['owner', 'admin', 'manager']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Unit $unit): bool
    {
        return $user->tenant_id === $unit->tenant_id
            && in_array($user->role, ['owner', 'admin']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Unit $unit): bool
    {
        return $user->tenant_id === $unit->tenant_id
            && $user->role === 'owner';
    }
}
