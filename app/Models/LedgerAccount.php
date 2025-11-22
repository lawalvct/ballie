<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAudit;

class LedgerAccount extends Model
{
    use HasFactory, HasAudit;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'account_group_id',
        'account_type',
        'parent_id',
        'opening_balance',
        'opening_balance_voucher_id',
        'current_balance',
        'last_transaction_date',
        'balance_type',
        'address',
        'phone',
        'email',
        'description',
        'is_active',
        'is_system_account',
        'is_opening_balance_account',
        'tags',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'last_transaction_date' => 'date',
        'is_active' => 'boolean',
        'is_system_account' => 'boolean',
        'is_opening_balance_account' => 'boolean',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function accountGroup()
    {
        return $this->belongsTo(AccountGroup::class);
    }

    public function voucherEntries()
    {
        return $this->hasMany(VoucherEntry::class);
    }

    public function openingBalanceVoucher()
    {
        return $this->belongsTo(Voucher::class, 'opening_balance_voucher_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByGroup($query, $groupId)
    {
        return $query->where('account_group_id', $groupId);
    }

    // Methods
    public function parent()
    {
        return $this->belongsTo(LedgerAccount::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(LedgerAccount::class, 'parent_id');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('account_type', $type);
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeChildren($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function getFullCodeAttribute()
    {
        if ($this->parent) {
            return $this->parent->code . '.' . $this->code;
        }
        return $this->code;
    }

    public function getFullNameAttribute()
    {
        if ($this->parent) {
            return $this->parent->name . ' → ' . $this->name;
        }
        return $this->name;
    }

    public function hasChildren()
    {
        return $this->children()->count() > 0;
    }

    public function getHierarchyLevel()
    {
        $level = 0;
        $parent = $this->parent;
        while ($parent) {
            $level++;
            $parent = $parent->parent;
        }
        return $level;
    }

    public function updateCurrentBalance()
    {
        $currentBalance = $this->getCurrentBalance();
        $lastTransactionDate = $this->getLastTransactionDate();

        $this->update([
            'current_balance' => $currentBalance,
            'last_transaction_date' => $lastTransactionDate
        ]);

        return $currentBalance;
    }

    public function getCurrentBalance($asOfDate = null, $useCache = true)
    {
        // Skip cache if explicitly requested or if calculating for a specific date
        if (!$useCache || $asOfDate || $this->current_balance === null) {
            $query = $this->voucherEntries()
                ->whereHas('voucher', function ($q) use ($asOfDate) {
                    $q->where('status', 'posted');
                    if ($asOfDate) {
                        $q->where('voucher_date', '<=', $asOfDate);
                    }
                });

            $totalDebits = $query->sum('debit_amount');
            $totalCredits = $query->sum('credit_amount');

            // Calculate based on account type and nature
            // For asset and expense accounts: Debit increases balance, Credit decreases balance
            // For liability, equity, and income accounts: Credit increases balance, Debit decreases balance

            // If account_type is not set, determine it from the account group or name
            $accountType = $this->account_type;
            if (!$accountType) {
                // Fallback logic based on account name patterns
                $name = strtolower($this->name);
                if (strpos($name, 'sales') !== false || strpos($name, 'income') !== false || strpos($name, 'revenue') !== false) {
                    $accountType = 'income';
                } elseif (strpos($name, 'receivable') !== false || strpos($name, 'cash') !== false || strpos($name, 'bank') !== false) {
                    $accountType = 'asset';
                } elseif (strpos($name, 'payable') !== false || strpos($name, 'liability') !== false) {
                    $accountType = 'liability';
                } else {
                    $accountType = 'asset'; // Default fallback
                }
            }

            if (in_array($accountType, ['asset', 'expense'])) {
                $currentBalance = $totalDebits - $totalCredits;
            } else {
                // liability, equity, income
                $currentBalance = $totalCredits - $totalDebits;
            }

            // Update current_balance if no date specified and not using cache
            if (!$asOfDate && !$useCache) {
                $this->updateQuietly(['current_balance' => $currentBalance]);
            }

            return $currentBalance;
        }

        // Return cached value
        return $this->current_balance;
    }

    public function getBalanceType($balance = null)
    {
        $balance = $balance ?? $this->getCurrentBalance();

        // Determine account type if not set
        $accountType = $this->account_type;
        if (!$accountType) {
            $name = strtolower($this->name);
            if (strpos($name, 'sales') !== false || strpos($name, 'income') !== false || strpos($name, 'revenue') !== false) {
                $accountType = 'income';
            } elseif (strpos($name, 'receivable') !== false || strpos($name, 'cash') !== false || strpos($name, 'bank') !== false) {
                $accountType = 'asset';
            } elseif (strpos($name, 'payable') !== false || strpos($name, 'liability') !== false) {
                $accountType = 'liability';
            } else {
                $accountType = 'asset';
            }
        }

        // For asset and expense accounts, positive balance is debit
        if (in_array($accountType, ['asset', 'expense'])) {
            return $balance >= 0 ? 'dr' : 'cr';
        }

        // For liability, equity, and income accounts, positive balance is credit
        return $balance >= 0 ? 'cr' : 'dr';
    }

    public function getFormattedBalance($asOfDate = null)
    {
        $balance = abs($this->getCurrentBalance($asOfDate));
        $type = $this->getBalanceType();

        return [
            'amount' => $balance,
            'type' => $type,
            'formatted' => number_format($balance, 2) . ' ' . strtoupper($type)
        ];
    }

    public function getLedgerEntries($fromDate = null, $toDate = null)
    {
        $query = $this->voucherEntries()
            ->with(['voucher'])
            ->whereHas('voucher', function ($q) use ($fromDate, $toDate) {
                $q->where('status', 'posted');
                if ($fromDate) {
                    $q->where('voucher_date', '>=', $fromDate);
                }
                if ($toDate) {
                    $q->where('voucher_date', '<=', $toDate);
                }
            })
            ->orderBy('created_at');

        return $query->get();
    }

    /**
     * Get total debits for this account
     */
    public function getTotalDebits()
    {
        return $this->voucherEntries()->sum('debit_amount');
    }

    /**
     * Get total credits for this account
     */
    public function getTotalCredits()
    {
        return $this->voucherEntries()->sum('credit_amount');
    }



    /**
     * Get balance as of specific date
     */
    public function getBalanceAsOf($date)
    {
        $totalDebits = $this->voucherEntries()
            ->whereHas('voucher', function ($query) use ($date) {
                $query->where('voucher_date', '<=', $date);
            })
            ->sum('debit_amount');

        $totalCredits = $this->voucherEntries()
            ->whereHas('voucher', function ($query) use ($date) {
                $query->where('voucher_date', '<=', $date);
            })
            ->sum('credit_amount');

        return $this->opening_balance + $totalDebits - $totalCredits;
    }

    /**
     * Check if account can be deleted
     */
    public function canBeDeleted()
    {
        // Cannot delete if has transactions
        if ($this->voucherEntries()->count() > 0) {
            return false;
        }

        // Cannot delete if has children
        if ($this->children()->count() > 0) {
            return false;
        }

        // Cannot delete system-defined accounts
        if ($this->is_system_account) {
            return false;
        }

        return true;
    }

    /**
     * Get account hierarchy path
     */
    public function getHierarchyPath()
    {
        $path = [$this->name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }

        return implode(' > ', $path);
    }

    /**
     * Get all descendants (children and their children)
     */
    public function getAllDescendants()
    {
        $descendants = collect();

        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->getAllDescendants());
        }

        return $descendants;
    }



    /**
     * Scope for specific account type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('account_type', $type);
    }

    /**
     * Scope for accounts with balance
     */
    public function scopeWithBalance($query)
    {
        return $query->whereHas('voucherEntries')
            ->orWhere('opening_balance', '!=', 0);
    }



    /**
     * Get account type badge color
     */
    public function getTypeColor()
    {
        return match($this->account_type) {
            'asset' => 'success',
            'liability' => 'danger',
            'equity' => 'warning',
            'income' => 'info',
            'expense' => 'secondary',
            default => 'primary'
        };
    }

    /**
     * Check if account has recent activity
     */
    public function hasRecentActivity($days = 30)
    {
        return $this->voucherEntries()
            ->where('created_at', '>=', now()->subDays($days))
            ->exists();
    }

    /**
     * Get last transaction date
     */
    public function getLastTransactionDate()
    {
        $lastEntry = $this->voucherEntries()
            ->latest()
            ->first();

        return $lastEntry ? $lastEntry->voucher->voucher_date : null;
    }
}
