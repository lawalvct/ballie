<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'ledger_account_id',
        'bank_name',
        'account_name',
        'account_number',
        'account_type',
        'branch_name',
        'branch_code',
        'swift_code',
        'iban',
        'routing_number',
        'sort_code',
        'branch_address',
        'branch_city',
        'branch_state',
        'branch_phone',
        'branch_email',
        'relationship_manager',
        'manager_phone',
        'manager_email',
        'currency',
        'opening_balance',
        'current_balance',
        'minimum_balance',
        'overdraft_limit',
        'account_opening_date',
        'last_reconciliation_date',
        'last_reconciled_balance',
        'online_banking_url',
        'online_banking_username',
        'online_banking_notes',
        'monthly_maintenance_fee',
        'transaction_limit_daily',
        'transaction_limit_monthly',
        'free_transactions_per_month',
        'excess_transaction_fee',
        'description',
        'notes',
        'custom_fields',
        'status',
        'is_primary',
        'is_payroll_account',
        'enable_reconciliation',
        'enable_auto_import',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'minimum_balance' => 'decimal:2',
        'overdraft_limit' => 'decimal:2',
        'last_reconciled_balance' => 'decimal:2',
        'monthly_maintenance_fee' => 'decimal:2',
        'transaction_limit_daily' => 'decimal:2',
        'transaction_limit_monthly' => 'decimal:2',
        'excess_transaction_fee' => 'decimal:2',
        'account_opening_date' => 'date',
        'last_reconciliation_date' => 'date',
        'is_primary' => 'boolean',
        'is_payroll_account' => 'boolean',
        'enable_reconciliation' => 'boolean',
        'enable_auto_import' => 'boolean',
        'custom_fields' => 'array',
    ];

    // Boot method to auto-create ledger account
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bank) {
            // Ensure only one primary bank per tenant
            if ($bank->is_primary) {
                static::where('tenant_id', $bank->tenant_id)
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }
        });

        static::created(function ($bank) {
            $bank->createLedgerAccount();
        });

        static::updating(function ($bank) {
            // Ensure only one primary bank per tenant
            if ($bank->is_primary && $bank->isDirty('is_primary')) {
                static::where('tenant_id', $bank->tenant_id)
                    ->where('id', '!=', $bank->id)
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }
        });

        static::updated(function ($bank) {
            $bank->syncLedgerAccount();
        });

        static::deleting(function ($bank) {
            // Mark ledger account as inactive if balance is zero
            if ($bank->ledgerAccount && $bank->ledgerAccount->getCurrentBalance() == 0) {
                $bank->ledgerAccount->update(['is_active' => false]);
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

    public function voucherEntries()
    {
        return $this->hasMany(VoucherEntry::class, 'ledger_account_id', 'ledger_account_id');
    }

    public function reconciliations()
    {
        return $this->hasMany(BankReconciliation::class);
    }

    // public function reconciliations()
    // {
    //     return $this->hasMany(BankReconciliation::class);
    // }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeForPayroll($query)
    {
        return $query->where('is_payroll_account', true);
    }

    public function scopeByBank($query, $bankName)
    {
        return $query->where('bank_name', $bankName);
    }

    // Methods

    /**
     * Create ledger account for bank
     */
    public function createLedgerAccount()
    {
        if ($this->ledgerAccount) {
            return $this->ledgerAccount;
        }

        // Find "Current Assets" account group
        $accountGroup = AccountGroup::where('tenant_id', $this->tenant_id)
            ->where('name', 'Current Assets')
            ->first();

        // Fallback to any assets group if Current Assets not found
        if (!$accountGroup) {
            $accountGroup = AccountGroup::where('tenant_id', $this->tenant_id)
                ->where('nature', 'assets')
                ->first();
        }

        // Create ledger account
        $ledgerAccount = LedgerAccount::create([
            'tenant_id' => $this->tenant_id,
            'name' => $this->bank_name . ' - ' . $this->account_number,
            'code' => 'BANK-' . strtoupper(substr($this->bank_name, 0, 3)) . '-' . substr($this->account_number, -4),
            'account_group_id' => $accountGroup?->id,
            'account_type' => 'asset',
            'opening_balance' => $this->opening_balance ?? 0,
            'current_balance' => $this->opening_balance ?? 0,
            'description' => 'Bank account: ' . $this->account_name,
            'is_active' => 1,
        ]);

        $this->update(['ledger_account_id' => $ledgerAccount->id]);

        return $ledgerAccount;
    }

    /**
     * Sync ledger account details when bank is updated
     */
    public function syncLedgerAccount()
    {
        if (!$this->ledgerAccount) {
            return;
        }

        $this->ledgerAccount->update([
            'name' => $this->bank_name . ' - ' . $this->account_number,
            'description' => 'Bank account: ' . $this->account_name,
            'is_active' => $this->status === 'active',
        ]);
    }

    /**
     * Get current balance from ledger account
     */
    public function getCurrentBalance()
    {
        if ($this->ledgerAccount) {
            return $this->ledgerAccount->getCurrentBalance();
        }

        return $this->current_balance;
    }

    /**
     * Get available balance (considering overdraft)
     */
    public function getAvailableBalance()
    {
        $currentBalance = $this->getCurrentBalance();
        return $currentBalance + $this->overdraft_limit;
    }

    /**
     * Check if account has sufficient funds
     */
    public function hasSufficientFunds($amount)
    {
        return $this->getAvailableBalance() >= $amount;
    }

    /**
     * Check if account needs reconciliation
     */
    public function needsReconciliation($daysThreshold = 30)
    {
        if (!$this->enable_reconciliation) {
            return false;
        }

        if (!$this->last_reconciliation_date) {
            return true;
        }

        return $this->last_reconciliation_date->diffInDays(now()) > $daysThreshold;
    }

    /**
     * Get reconciliation status
     */
    public function getReconciliationStatus()
    {
        if (!$this->enable_reconciliation) {
            return 'disabled';
        }

        if (!$this->last_reconciliation_date) {
            return 'never';
        }

        $daysSince = $this->last_reconciliation_date->diffInDays(now());

        if ($daysSince <= 7) {
            return 'current';
        } elseif ($daysSince <= 30) {
            return 'due';
        } else {
            return 'overdue';
        }
    }

    /**
     * Get formatted account display name
     */
    public function getDisplayNameAttribute()
    {
        return $this->bank_name . ' - ' . $this->account_number;
    }

    /**
     * Get masked account number
     */
    public function getMaskedAccountNumberAttribute()
    {
        $length = strlen($this->account_number);
        if ($length <= 4) {
            return $this->account_number;
        }

        return str_repeat('*', $length - 4) . substr($this->account_number, -4);
    }

    /**
     * Get full branch address
     */
    public function getFullBranchAddressAttribute()
    {
        $parts = array_filter([
            $this->branch_address,
            $this->branch_city,
            $this->branch_state,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Check if account is approaching minimum balance
     */
    public function isApproachingMinimumBalance($threshold = 0.1)
    {
        if ($this->minimum_balance <= 0) {
            return false;
        }

        $currentBalance = $this->getCurrentBalance();
        $warningLevel = $this->minimum_balance * (1 + $threshold);

        return $currentBalance <= $warningLevel && $currentBalance > $this->minimum_balance;
    }

    /**
     * Check if account is below minimum balance
     */
    public function isBelowMinimumBalance()
    {
        if ($this->minimum_balance <= 0) {
            return false;
        }

        return $this->getCurrentBalance() < $this->minimum_balance;
    }

    /**
     * Get account age in days
     */
    public function getAccountAge()
    {
        if (!$this->account_opening_date) {
            return null;
        }

        return $this->account_opening_date->diffInDays(now());
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'green',
            'inactive' => 'gray',
            'closed' => 'red',
            'suspended' => 'yellow',
            default => 'gray'
        };
    }

    /**
     * Get account type display name
     */
    public function getAccountTypeDisplayAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->account_type ?? 'current'));
    }

    /**
     * Get last transaction date from ledger entries
     */
    public function getLastTransactionDate()
    {
        if (!$this->ledgerAccount) {
            return null;
        }

        return $this->ledgerAccount->getLastTransactionDate();
    }

    /**
     * Get total transactions count
     */
    public function getTotalTransactionsCount()
    {
        if (!$this->ledgerAccount) {
            return 0;
        }

        return $this->ledgerAccount->voucherEntries()->count();
    }

    /**
     * Get monthly transactions count
     */
    public function getMonthlyTransactionsCount()
    {
        if (!$this->ledgerAccount) {
            return 0;
        }

        return $this->ledgerAccount->voucherEntries()
            ->whereHas('voucher', function($q) {
                $q->whereMonth('voucher_date', now()->month)
                  ->whereYear('voucher_date', now()->year);
            })
            ->count();
    }

    /**
     * Check if bank can be deleted
     */
    public function canBeDeleted()
    {
        // Cannot delete if has transactions
        if ($this->getTotalTransactionsCount() > 0) {
            return false;
        }

        // Cannot delete if has non-zero balance
        if ($this->getCurrentBalance() != 0) {
            return false;
        }

        return true;
    }
}
