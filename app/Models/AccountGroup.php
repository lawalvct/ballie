<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'nature',
        'parent_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function parent()
    {
        return $this->belongsTo(AccountGroup::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(AccountGroup::class, 'parent_id');
    }

    public function ledgerAccounts()
    {
        return $this->hasMany(LedgerAccount::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByNature($query, $nature)
    {
        return $query->where('nature', $nature);
    }

    // Methods
    public function isParent()
    {
        return $this->children()->count() > 0;
    }

    public function getFullNameAttribute()
    {
        if ($this->parent) {
            return $this->parent->full_name . ' → ' . $this->name;
        }
        return $this->name;
    }

    public function scopeByLevel($query, $level = 0)
    {
        if ($level === 0) {
            return $query->whereNull('parent_id');
        }

        return $query->whereNotNull('parent_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function getLevel()
    {
        $level = 0;
        $parent = $this->parent;
        while ($parent) {
            $level++;
            $parent = $parent->parent;
        }
        return $level;
    }

    public function getAllChildren()
    {
        $children = collect();

        foreach ($this->children as $child) {
            $children->push($child);
            $children = $children->merge($child->getAllChildren());
        }

        return $children;
    }

    /**
     * Get the hierarchy path for this account group
     * Returns a string like "Assets > Current Assets > Cash & Bank"
     */
    public function getHierarchyPath($separator = ' > ')
    {
        $path = [$this->name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }

        return implode($separator, $path);
    }

    /**
     * Get the hierarchy path with codes
     * Returns a string like "CA > CASH > BANK (Current Assets > Cash & Bank > Bank Accounts)"
     */
    public function getHierarchyPathWithCodes($separator = ' > ')
    {
        $pathNames = [$this->name];
        $pathCodes = [$this->code];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($pathNames, $parent->name);
            array_unshift($pathCodes, $parent->code);
            $parent = $parent->parent;
        }

        $codePath = implode($separator, $pathCodes);
        $namePath = implode($separator, $pathNames);

        return "{$codePath} ({$namePath})";
    }

    /**
     * Get breadcrumb array for navigation
     */
    public function getBreadcrumbs()
    {
        $breadcrumbs = [];
        $current = $this;

        while ($current) {
            array_unshift($breadcrumbs, [
                'id' => $current->id,
                'name' => $current->name,
                'code' => $current->code,
                'url' => route('tenant.accounting.account-groups.show', [
                    'tenant' => $current->tenant->slug,
                    'account_group' => $current->id
                ])
            ]);
            $current = $current->parent;
        }

        return $breadcrumbs;
    }

    /**
     * Get the root parent of this account group
     */
    public function getRootParent()
    {
        $current = $this;
        while ($current->parent) {
            $current = $current->parent;
        }
        return $current;
    }

    /**
     * Check if this group is a descendant of another group
     */
    public function isDescendantOf(AccountGroup $group)
    {
        $parent = $this->parent;
        while ($parent) {
            if ($parent->id === $group->id) {
                return true;
            }
            $parent = $parent->parent;
        }
        return false;
    }

    /**
     * Check if this group is an ancestor of another group
     */
    public function isAncestorOf(AccountGroup $group)
    {
        return $group->isDescendantOf($this);
    }

    /**
     * Get all ancestors (parent, grandparent, etc.)
     */
    public function getAncestors()
    {
        $ancestors = collect();
        $parent = $this->parent;

        while ($parent) {
            $ancestors->push($parent);
            $parent = $parent->parent;
        }

        return $ancestors;
    }

    /**
     * Get all descendants (children, grandchildren, etc.)
     */
    public function getDescendants()
    {
        return $this->getAllChildren();
    }

    public function getDefaultBalanceType()
    {
        if ($this->balance_type) {
            return $this->balance_type;
        }

        // Default balance types based on nature
        $defaults = [
            'assets' => 'dr',
            'expenses' => 'dr',
            'liabilities' => 'cr',
            'income' => 'cr',
            'equity' => 'cr'
        ];

        return $defaults[$this->nature] ?? 'dr';
    }

    public function getTotalBalance($asOfDate = null)
    {
        $balance = 0;

        // Get balance from direct ledger accounts
        foreach ($this->ledgerAccounts()->active()->get() as $ledger) {
            $accountBalance = $ledger->getCurrentBalance($asOfDate);

            // Normalize balance based on account type
            if (in_array($this->nature, ['liabilities', 'income', 'equity'])) {
                $balance += $accountBalance;
            } else {
                $balance += $accountBalance;
            }
        }

        // Get balance from child groups
        foreach ($this->children()->active()->get() as $child) {
            $balance += $child->getTotalBalance($asOfDate);
        }

        return $balance;
    }

    /**
     * Get formatted balance with proper sign based on nature
     */
    public function getFormattedBalance($asOfDate = null)
    {
        $balance = $this->getTotalBalance($asOfDate);
        $absBalance = abs($balance);

        // Determine if balance is normal or abnormal for this nature
        $isNormalBalance = match($this->nature) {
            'assets', 'expenses' => $balance >= 0,
            'liabilities', 'income', 'equity' => $balance <= 0,
            default => $balance >= 0
        };

        $type = match($this->nature) {
            'assets', 'expenses' => $balance >= 0 ? 'Dr' : 'Cr',
            'liabilities', 'income', 'equity' => $balance >= 0 ? 'Cr' : 'Dr',
            default => $balance >= 0 ? 'Dr' : 'Cr'
        };

        return [
            'amount' => $absBalance,
            'type' => $type,
            'is_normal' => $isNormalBalance,
            'formatted' => '₦' . number_format($absBalance, 2) . ' ' . $type,
            'raw_balance' => $balance
        ];
    }

    /**
     * Get count of direct ledger accounts
     */
    public function getDirectAccountsCount()
    {
        return $this->ledgerAccounts()->count();
    }

    /**
     * Get count of all ledger accounts (including from child groups)
     */
    public function getTotalAccountsCount()
    {
        $count = $this->getDirectAccountsCount();

        foreach ($this->children as $child) {
            $count += $child->getTotalAccountsCount();
        }

        return $count;
    }

    /**
     * Check if this group can be deleted
     */
    public function canBeDeleted()
    {
        // Cannot delete if has ledger accounts
        if ($this->ledgerAccounts()->count() > 0) {
            return false;
        }

        // Cannot delete if has children
        if ($this->children()->count() > 0) {
            return false;
        }

        // Cannot delete system-defined groups
        if ($this->is_system_defined ?? false) {
            return false;
        }

        return true;
    }

    /**
     * Get the deletion constraints
     */
    public function getDeletionConstraints()
    {
        $constraints = [];

        if ($this->ledgerAccounts()->count() > 0) {
            $constraints[] = 'Has ' . $this->ledgerAccounts()->count() . ' ledger account(s)';
        }

        if ($this->children()->count() > 0) {
            $constraints[] = 'Has ' . $this->children()->count() . ' child group(s)';
        }

        if ($this->is_system_defined ?? false) {
            $constraints[] = 'System-defined group';
        }

        return $constraints;
    }

    /**
     * Get nature badge color for UI
     */
    public function getNatureBadgeColor()
    {
        return match($this->nature) {
            'assets' => 'green',
            'liabilities' => 'red',
            'equity' => 'purple',
            'income' => 'blue',
            'expenses' => 'orange',
            default => 'gray'
        };
    }

    /**
     * Get nature icon for UI
     */
    public function getNatureIcon()
    {
        return match($this->nature) {
            'assets' => 'building',
            'liabilities' => 'credit-card',
            'equity' => 'users',
            'income' => 'trending-up',
            'expenses' => 'trending-down',
            default => 'folder'
        };
    }
}
