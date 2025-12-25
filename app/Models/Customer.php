<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\LedgerAccount;
use App\Models\AccountGroup;
use App\Models\Voucher;
use App\Models\VoucherEntry;
use App\Traits\HasAudit;

class Customer extends Model
{
    use HasFactory, SoftDeletes, HasAudit;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'ledger_account_id',
        'customer_type',
        'first_name',
        'last_name',
        'company_name',
        'tax_id',
        'email',
        'phone',
        'mobile',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'currency',
        'payment_terms',
        'total_spent',
        'outstanding_balance',
        'last_invoice_date',
        'last_invoice_number',
        'notes',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'last_invoice_date' => 'datetime',
        'total_spent' => 'decimal:2',
        'outstanding_balance' => 'decimal:2',
    ];

    // Boot method to auto-create ledger account
    protected static function boot()
    {
        parent::boot();

        static::created(function ($customer) {
            $customer->createLedgerAccount();
        });

        static::updated(function ($customer) {
            $customer->syncLedgerAccount();
        });

        static::deleting(function ($customer) {
            if ($customer->ledgerAccount && $customer->ledgerAccount->getCurrentBalance() == 0) {
                $customer->ledgerAccount->update(['is_active' => false]);
            }
        });
    }

    /**
     * Get the tenant that owns the customer.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function ledgerAccount()
    {
        return $this->belongsTo(LedgerAccount::class);
    }

    /**
     * Get vouchers (invoices) for this customer through their ledger account.
     */
    public function vouchers()
    {
        return $this->hasManyThrough(
            Voucher::class,
            VoucherEntry::class,
            'ledger_account_id', // Foreign key on voucher_entries table
            'id', // Foreign key on vouchers table
            'ledger_account_id', // Local key on customers table
            'voucher_id' // Local key on voucher_entries table
        )->distinct();
    }

    /**
     * Get sales vouchers (invoices) for this customer.
     */
    public function invoices()
    {
        return $this->vouchers()
            ->whereHas('voucherType', function($query) {
                $query->where('code', 'SALES')
                      ->orWhere('affects_inventory', true);
            });
    }

    /**
     * Get payment vouchers for this customer.
     */
    public function payments()
    {
        return $this->vouchers()
            ->whereHas('voucherType', function($query) {
                $query->where('code', 'RV'); // Receipt Voucher
            });
    }


    // Create ledger account for customer
    public function createLedgerAccount()
    {
        if ($this->ledgerAccount) {
            return $this->ledgerAccount;
        }

        // Find or create Accounts Receivable group
        $arGroup = AccountGroup::where('tenant_id', $this->tenant_id)
            ->where('code', 'AR')
            ->first();

        if (!$arGroup) {
            // Create AR group if it doesn't exist
            $currentAssetsGroup = AccountGroup::where('tenant_id', $this->tenant_id)
                ->where('code', 'CA')
                ->first();

            $arGroup = AccountGroup::create([
                'tenant_id' => $this->tenant_id,
                'name' => 'Accounts Receivable',
                'code' => 'AR',
                'nature' => 'assets',
                'parent_id' => $currentAssetsGroup?->id,
            ]);
        }

        // Create ledger account
        $ledgerAccount = LedgerAccount::create([
            'tenant_id' => $this->tenant_id,
            'name' => $this->getFullNameAttribute(),
            'code' => 'CUST-' . str_pad($this->id, 4, '0', STR_PAD_LEFT),
            'account_group_id' => $arGroup->id,
            'account_type' => 'asset', // Customers are assets (Accounts Receivable)
            'opening_balance' => 0,
            'balance_type' => 'dr', // Customers are assets, so debit balance
            'address' => $this->getFullAddressAttribute(),
            'phone' => $this->phone,
            'email' => $this->email,
        ]);

        $this->update(['ledger_account_id' => $ledgerAccount->id]);

        return $ledgerAccount;
    }

    // Sync ledger account when customer is updated
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

    /**
     * Get the full name of the customer.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        if ($this->customer_type === 'individual') {
            return trim($this->first_name . ' ' . $this->last_name);
        }
        return $this->company_name;
    }

    /**
     * Get the full address of the customer.
     *
     * @return string
     */
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
        $ledgerBalance = $this->getLedgerBalance();
        $this->update(['outstanding_balance' => $ledgerBalance]);
        return $ledgerBalance;
    }

    /**
     * Scope a query to only include customers for a specific tenant.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $tenantId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope a query to only include active customers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include individual customers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIndividuals($query)
    {
        return $query->where('customer_type', 'individual');
    }

    /**
     * Scope a query to only include business customers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBusinesses($query)
    {
        return $query->where('customer_type', 'business');
    }

    // E-commerce Relationships

    /**
     * Get customer's online authentication record
     */
    public function authentication()
    {
        return $this->hasOne(CustomerAuthentication::class);
    }

    /**
     * Get customer's e-commerce orders
     */
    public function orders()
    {
        return $this->hasMany(Order::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get customer's shipping addresses
     */
    public function addresses()
    {
        return $this->hasMany(ShippingAddress::class);
    }

    /**
     * Get customer's default shipping address
     */
    public function defaultAddress()
    {
        return $this->hasOne(ShippingAddress::class)->where('is_default', true);
    }

    /**
     * Get customer's shopping cart
     */
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    /**
     * Get customer's wishlist
     */
    public function wishlist()
    {
        return $this->hasOne(Wishlist::class);
    }

    /**
     * Get customer's coupon usage history
     */
    public function couponUsages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    // E-commerce Helper Methods

    /**
     * Check if customer has online account
     */
    public function hasOnlineAccount()
    {
        return (bool) ($this->attributes['has_online_account'] ?? false);
    }

    /**
     * Get total orders count
     */
    public function getTotalOrdersCount()
    {
        return $this->orders()->count();
    }

    /**
     * Get total orders amount
     */
    public function getTotalOrdersAmount()
    {
        return $this->orders()->sum('total_amount');
    }

    /**
     * Scope for customers with online accounts
     */
    public function scopeWithOnlineAccount($query)
    {
        return $query->where('has_online_account', true);
    }
}
