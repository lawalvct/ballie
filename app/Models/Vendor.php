<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\LedgerAccount;
use App\Models\AccountGroup;
use App\Traits\HasAudit;

class Vendor extends Model
{
    use HasFactory, SoftDeletes, HasAudit;

    protected $fillable = [
        'tenant_id',
        'ledger_account_id',
        'vendor_type',
        'first_name',
        'last_name',
        'company_name',
        'tax_id',
        'registration_number',
        'email',
        'phone',
        'mobile',
        'website',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'currency',
        'payment_terms',
        'total_purchases',
        'outstanding_balance',
        'last_purchase_date',
        'last_purchase_number',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'notes',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'last_purchase_date' => 'datetime',
        'total_purchases' => 'decimal:2',
        'outstanding_balance' => 'decimal:2',
    ];

    // Boot method to auto-create ledger account
    protected static function boot()
    {
        parent::boot();

        static::created(function ($vendor) {
            $vendor->createLedgerAccount();
        });

        static::updated(function ($vendor) {
            $vendor->syncLedgerAccount();
        });

        static::deleting(function ($vendor) {
            if ($vendor->ledgerAccount && $vendor->ledgerAccount->getCurrentBalance() == 0) {
                $vendor->ledgerAccount->update(['is_active' => false]);
            }
        });
    }

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function ledgerAccount()
    {
        return $this->belongsTo(LedgerAccount::class);
    }

    // Create ledger account for vendor
    public function createLedgerAccount()
    {
        if ($this->ledgerAccount) {
            return $this->ledgerAccount;
        }

        // Find or create Accounts Payable group
        $apGroup = AccountGroup::where('tenant_id', $this->tenant_id)
            ->where('code', 'AP')
            ->first();

        if (!$apGroup) {
            // Create AP group if it doesn't exist
            $currentLiabilitiesGroup = AccountGroup::where('tenant_id', $this->tenant_id)
                ->where('code', 'CL')
                ->first();

            $apGroup = AccountGroup::create([
                'tenant_id' => $this->tenant_id,
                'name' => 'Accounts Payable',
                'code' => 'AP',
                'nature' => 'liabilities',
                'parent_id' => $currentLiabilitiesGroup?->id,
            ]);
        }

        // Create ledger account
        $ledgerAccount = LedgerAccount::create([
            'tenant_id' => $this->tenant_id,
            'name' => $this->getFullNameAttribute(),
            'code' => 'VEND-' . str_pad($this->id, 4, '0', STR_PAD_LEFT),
            'account_group_id' => $apGroup->id,
            'account_type' => 'liability', // Vendors are liabilities (Accounts Payable)
            'opening_balance' => 0,
            'balance_type' => 'cr', // Vendors are liabilities, so credit balance
            'address' => $this->getFullAddressAttribute(),
            'phone' => $this->phone,
            'email' => $this->email,
        ]);

        $this->update(['ledger_account_id' => $ledgerAccount->id]);

        return $ledgerAccount;
    }

    // Sync ledger account when vendor is updated
    public function syncLedgerAccount()
    {
        if ($this->ledgerAccount) {
            $this->ledgerAccount->update([
                'name' => $this->getFullNameAttribute(),
                'address' => $this->getFullAddressAttribute(),
                'phone' => $this->phone,
                'email' => $this->email,
            ]);
        }
    }

    // Get full name based on vendor type
    public function getFullNameAttribute()
    {
        if ($this->vendor_type === 'individual') {
            return trim($this->first_name . ' ' . $this->last_name);
        }
        return $this->company_name;
    }

    // Get formatted address
    public function getFullAddressAttribute()
    {
        $address = [];

        if ($this->address_line1) {
            $address[] = $this->address_line1;
        }

        if ($this->address_line2) {
            $address[] = $this->address_line2;
        }

        $cityStateZip = [];
        if ($this->city) {
            $cityStateZip[] = $this->city;
        }

        if ($this->state) {
            $cityStateZip[] = $this->state;
        }

        if ($this->postal_code) {
            $cityStateZip[] = $this->postal_code;
        }

        if (!empty($cityStateZip)) {
            $address[] = implode(', ', $cityStateZip);
        }

        if ($this->country) {
            $address[] = $this->country;
        }

        return implode(', ', $address);
    }

    // Get current balance from ledger
    public function getLedgerBalance()
    {
        if (!$this->ledgerAccount) {
            return 0;
        }

        return $this->ledgerAccount->getCurrentBalance();
    }

    // Update outstanding balance from ledger
    public function updateOutstandingBalance()
    {
        $ledgerBalance = abs($this->getLedgerBalance()); // Always positive for display
        $this->update(['outstanding_balance' => $ledgerBalance]);
        return $ledgerBalance;
    }

    // Scopes
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeIndividuals($query)
    {
        return $query->where('vendor_type', 'individual');
    }

    public function scopeBusinesses($query)
    {
        return $query->where('vendor_type', 'business');
    }
}
