<?php

namespace App\Services;

use App\Models\VoucherType;
use App\Models\Voucher;
use Database\Seeders\VoucherTypeSeeder;

class VoucherTypeService
{
    public function initializeSystemVoucherTypes($tenantId)
    {
        // Check if system voucher types already exist
        $existingCount = VoucherType::where('tenant_id', $tenantId)
            ->where('is_system_defined', true)
            ->count();

        if ($existingCount === 0) {
            VoucherTypeSeeder::seedForTenant($tenantId);
            return true;
        }

        return false;
    }

    public function getVoucherTypeStats($tenantId)
    {
        $total = VoucherType::where('tenant_id', $tenantId)->count();
        $active = VoucherType::where('tenant_id', $tenantId)->where('is_active', true)->count();
        $system = VoucherType::where('tenant_id', $tenantId)->where('is_system_defined', true)->count();
        $custom = VoucherType::where('tenant_id', $tenantId)->where('is_system_defined', false)->count();

        return compact('total', 'active', 'system', 'custom');
    }

    public function validateVoucherTypeCode($code, $tenantId, $excludeId = null)
    {
        $query = VoucherType::where('tenant_id', $tenantId)
            ->where('code', strtoupper($code));

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return !$query->exists();
    }

    public function generateSuggestedCode($name)
    {
        // Generate a suggested code based on the name
        $words = explode(' ', strtoupper($name));

        if (count($words) === 1) {
            return substr($words[0], 0, 3);
        }

        $code = '';
        foreach ($words as $word) {
            if (strlen($word) > 0) {
                $code .= $word[0];
            }
        }

        return substr($code, 0, 5);
    }

    public function getNextAvailableNumber($voucherTypeId)
    {
        $voucherType = VoucherType::find($voucherTypeId);

        if (!$voucherType) {
            return 1;
        }

        return $voucherType->current_number + 1;
    }

    public function getPrimaryVoucherTypeOptions()
    {
        // Fetch all system-defined voucher types from the DB, keyed by code
        $voucherTypes = VoucherType::where('is_system_defined', true)
            ->orderBy('name')
            ->get();

        // Key by code (uppercase)
        $result = [];
        foreach ($voucherTypes as $type) {
            $result[strtolower($type->code)] = [
                'name' => $type->name,
                'code' => $type->code,
                'abbreviation' => $type->abbreviation,
                'description' => $type->description,
                'affects_inventory' => $type->affects_inventory,
                'affects_cashbank' => $type->affects_cashbank,
                'has_reference' => $type->has_reference,
                'prefix' => $type->prefix,
            ];
        }
        return $result;
    }

    public function canDeleteVoucherType($voucherTypeId)
    {
        $voucherType = VoucherType::find($voucherTypeId);

        if (!$voucherType) {
            return false;
        }

        // Cannot delete system-defined voucher types
        if ($voucherType->is_system_defined) {
            return false;
        }

        // Cannot delete if there are associated vouchers
        if ($voucherType->vouchers()->count() > 0) {
            return false;
        }

        return true;
    }

    public function duplicateVoucherType($voucherTypeId, $newName)
    {
        $originalVoucherType = VoucherType::find($voucherTypeId);

        if (!$originalVoucherType) {
            return null;
        }

        $newVoucherType = $originalVoucherType->replicate();
        $newVoucherType->name = $newName;
        $newVoucherType->code = $this->generateSuggestedCode($newName);
        $newVoucherType->is_system_defined = false;
        $newVoucherType->current_number = 0;
        $newVoucherType->save();

        return $newVoucherType;
    }
}
