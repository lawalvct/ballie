<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherType extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'abbreviation',
        'description',
        'numbering_method',
        'prefix',
        'starting_number',
        'current_number',
        'has_reference',
        'affects_inventory',
        'inventory_effect',
        'affects_cashbank',
        'is_system_defined',
        'is_active',
        'default_accounts',
    ];

    protected $casts = [
        'has_reference' => 'boolean',
        'affects_inventory' => 'boolean',
        'affects_cashbank' => 'boolean',
        'is_system_defined' => 'boolean',
        'is_active' => 'boolean',
        'default_accounts' => 'array',
        'inventory_effect' => 'string',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    // Methods
    public function getNextVoucherNumber()
    {
        $this->increment('current_number');
        return $this->prefix . str_pad($this->current_number, 4, '0', STR_PAD_LEFT);
    }

    public function resetNumbering($startFrom = 1)
    {
        $this->update(['current_number' => $startFrom - 1]);
    }
}
