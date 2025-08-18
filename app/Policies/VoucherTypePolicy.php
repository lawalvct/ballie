<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VoucherType;
use Illuminate\Auth\Access\HandlesAuthorization;

class VoucherTypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any voucher types.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('voucher_types.view');
    }

    /**
     * Determine whether the user can view the voucher type.
     */
    public function view(User $user, VoucherType $voucherType): bool
    {
        return $user->hasPermission('voucher_types.view') &&
               $user->tenant_id === $voucherType->tenant_id;
    }

    /**
     * Determine whether the user can create voucher types.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('voucher_types.create');
    }

    /**
     * Determine whether the user can update the voucher type.
     */
    public function update(User $user, VoucherType $voucherType): bool
    {
        return $user->hasPermission('voucher_types.update') &&
               $user->tenant_id === $voucherType->tenant_id;
    }

    /**
     * Determine whether the user can delete the voucher type.
     */
    public function delete(User $user, VoucherType $voucherType): bool
    {
        return $user->hasPermission('voucher_types.delete') &&
               $user->tenant_id === $voucherType->tenant_id &&
               !$voucherType->is_system_defined;
    }
}