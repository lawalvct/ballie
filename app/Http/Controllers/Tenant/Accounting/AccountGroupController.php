<?php

namespace App\Http\Controllers\Tenant\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\AccountGroup;
use App\Models\LedgerAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AccountGroupController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'tenant']);
    }

    /**
     * Display a listing of account groups.
     */
    public function index(Request $request, Tenant $tenant)
    {
        $query = AccountGroup::where('tenant_id', $tenant->id)
            ->with(['parent', 'children']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('nature', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->get('status') === 'active');
        }

        // Filter by nature
        if ($request->filled('nature')) {
            $query->where('nature', $request->get('nature'));
        }

        // Filter by level (parent/child)
        if ($request->filled('level')) {
            if ($request->get('level') === 'parent') {
                $query->whereNull('parent_id');
            } else {
                $query->whereNotNull('parent_id');
            }
        }

        // Sort
        $sortBy = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');

        $allowedSorts = ['name', 'code', 'nature', 'created_at', 'is_active'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $accountGroups = $query->paginate(15)->withQueryString();

        // Get statistics
        $totalGroups = AccountGroup::where('tenant_id', $tenant->id)->count();
        $activeGroups = AccountGroup::where('tenant_id', $tenant->id)->where('is_active', true)->count();
        $parentGroups = AccountGroup::where('tenant_id', $tenant->id)->whereNull('parent_id')->count();
        $childGroups = AccountGroup::where('tenant_id', $tenant->id)->whereNotNull('parent_id')->count();

        // Get account counts for each group
        $accountCounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->select('account_group_id', DB::raw('count(*) as count'))
            ->groupBy('account_group_id')
            ->pluck('count', 'account_group_id');

        return view('tenant.accounting.account-groups.index', compact(
            'tenant',
            'accountGroups',
            'totalGroups',
            'activeGroups',
            'parentGroups',
            'childGroups',
            'accountCounts'
        ));
    }

    /**
     * Show the form for creating a new account group.
     */
    public function create(Tenant $tenant)
    {
        // Get parent groups for hierarchy
        $parentGroups = AccountGroup::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        // Account nature options
        $natures = [
            'assets' => 'Assets',
            'liabilities' => 'Liabilities',
            'equity' => 'Equity',
            'income' => 'Income',
            'expenses' => 'Expenses'
        ];

        return view('tenant.accounting.account-groups.create', compact(
            'tenant',
            'parentGroups',
            'natures'
        ));
    }

    /**
     * Store a newly created account group.
     */
    public function store(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Z0-9_-]+$/',
                Rule::unique('account_groups')->where('tenant_id', $tenant->id)
            ],
            'nature' => ['required', 'in:assets,liabilities,equity,income,expenses'],
            'parent_id' => ['nullable', 'exists:account_groups,id'],
            'is_active' => ['boolean'],
        ], [
            'code.regex' => 'Code can only contain uppercase letters, numbers, hyphens, and underscores.',
        ]);

        // If parent is selected, validate it belongs to tenant and nature matches
        if ($request->filled('parent_id')) {
            $parent = AccountGroup::where('tenant_id', $tenant->id)
                ->where('id', $request->parent_id)
                ->first();

            if (!$parent) {
                return redirect()->back()
                    ->withErrors(['parent_id' => 'Invalid parent account group selected.'])
                    ->withInput();
            }

            if ($parent->nature !== $request->nature) {
                return redirect()->back()
                    ->withErrors(['nature' => 'Child group nature must match parent group nature.'])
                    ->withInput();
            }
        }

        $accountGroup = AccountGroup::create([
            'tenant_id' => $tenant->id,
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'nature' => $request->nature,
            'parent_id' => $request->parent_id,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('tenant.accounting.account-groups.show', [
                'tenant' => $tenant->slug,
                'account_group' => $accountGroup->id
            ])
            ->with('success', 'Account group created successfully.');
    }

    /**
     * Display the specified account group.
     */
    public function show(Tenant $tenant, AccountGroup $account_group)
{
    // Ensure account group belongs to tenant
    if ($account_group->tenant_id !== $tenant->id) {
        abort(404);
    }

    // Load relationships
    $account_group->load(['parent', 'children.ledgerAccounts', 'ledgerAccounts']);

    // Get direct ledger accounts count
    $directAccountsCount = $account_group->ledgerAccounts()->count();
    $ledgerAccountsCount = $directAccountsCount; // For backward compatibility

    // Get total accounts count (including from child groups)
    $totalAccountsCount = $account_group->getTotalAccountsCount();

    // Get recent ledger accounts
    $recentAccounts = $account_group->ledgerAccounts()
        ->latest()
        ->take(5)
        ->get();

    // Get child groups count
    $childGroupsCount = $account_group->children()->count();

    // Get balance information
    $totalBalance = $account_group->getTotalBalance();
    $balanceInfo = $account_group->getFormattedBalance();

    // Get hierarchy information
    $hierarchyLevel = $account_group->getLevel();
    $ancestors = $account_group->getAncestors();
    $breadcrumbs = $account_group->getBreadcrumbs();

    // Get statistics for child groups
    $childGroupsStats = $account_group->children->map(function ($child) {
        return [
            'group' => $child,
            'accounts_count' => $child->ledgerAccounts()->count(),
            'balance' => $child->getTotalBalance(),
            'formatted_balance' => $child->getFormattedBalance()
        ];
    });
    $ledgerAccounts = $account_group->ledgerAccounts()
        ->with(['accountGroup'])
        ->orderBy('name')
        ->get();

          $breadcrumbs = [
        [
            'title' => 'Dashboard',
            'url' => route('tenant.dashboard', $tenant->slug)
        ],
        [
            'title' => 'Accounting',
            'url' => route('tenant.accounting.index', $tenant->slug)
        ],
        [
            'title' => 'Account Groups',
            'url' => route('tenant.accounting.account-groups.index', $tenant->slug)
        ],
        [
            'title' => $account_group->name,
            'url' => null // Current page
        ]
    ];

    
    // Check if can be deleted
    $canBeDeleted = $account_group->canBeDeleted();
    $deletionConstraints = $account_group->getDeletionConstraints();

    return view('tenant.accounting.account-groups.show', compact(
        'tenant',
        'account_group',
        'directAccountsCount',
        'ledgerAccountsCount',
         'ledgerAccounts',
        'totalAccountsCount',
        'recentAccounts',
        'childGroupsCount',
        'totalBalance',
        'balanceInfo',
        'hierarchyLevel',
        'ancestors',
        'breadcrumbs',
        'childGroupsStats',
        'canBeDeleted',
        'deletionConstraints'
    ));
}

    /**
     * Show the form for editing the specified account group.
     */
    public function edit(Tenant $tenant, AccountGroup $accountGroup)
    {
        // Ensure account group belongs to tenant
        if ($accountGroup->tenant_id !== $tenant->id) {
            abort(404);
        }

        // Get potential parent groups (excluding self and its descendants)
        $parentGroups = AccountGroup::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->where('id', '!=', $accountGroup->id)
            ->where('nature', $accountGroup->nature)
            ->orderBy('name')
            ->get();

        // Remove descendants from potential parents
        $descendants = $accountGroup->getAllChildren()->pluck('id');
        $parentGroups = $parentGroups->whereNotIn('id', $descendants);

        // Account nature options
        $natures = [
            'assets' => 'Assets',
            'liabilities' => 'Liabilities',
            'equity' => 'Equity',
            'income' => 'Income',
            'expenses' => 'Expenses'
        ];

        return view('tenant.accounting.account-groups.edit', compact(
            'tenant',
            'accountGroup',
            'parentGroups',
            'natures'
        ));
    }

    /**
     * Update the specified account group.
     */
    public function update(Request $request, Tenant $tenant, AccountGroup $accountGroup)
    {
        // Ensure account group belongs to tenant
        if ($accountGroup->tenant_id !== $tenant->id) {
            abort(404);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Z0-9_-]+$/',
                Rule::unique('account_groups')
                    ->where('tenant_id', $tenant->id)
                    ->ignore($accountGroup->id)
            ],
            'nature' => ['required', 'in:assets,liabilities,equity,income,expenses'],
            'parent_id' => ['nullable', 'exists:account_groups,id'],
            'is_active' => ['boolean'],
        ], [
            'code.regex' => 'Code can only contain uppercase letters, numbers, hyphens, and underscores.',
        ]);

        // Validate parent selection
        if ($request->filled('parent_id')) {
            $parent = AccountGroup::where('tenant_id', $tenant->id)
                ->where('id', $request->parent_id)
                ->first();

            if (!$parent) {
                return redirect()->back()
                    ->withErrors(['parent_id' => 'Invalid parent account group selected.'])
                    ->withInput();
            }

            // Prevent circular reference
            if ($parent->id === $accountGroup->id) {
                return redirect()->back()
                    ->withErrors(['parent_id' => 'Account group cannot be its own parent.'])
                    ->withInput();
            }

            // Check if parent is a descendant
            $descendants = $accountGroup->getAllChildren()->pluck('id');
            if ($descendants->contains($parent->id)) {
                return redirect()->back()
                    ->withErrors(['parent_id' => 'Cannot select a child group as parent.'])
                    ->withInput();
            }

            // Nature must match
            if ($parent->nature !== $request->nature) {
                return redirect()->back()
                    ->withErrors(['nature' => 'Child group nature must match parent group nature.'])
                    ->withInput();
            }
        }

        $accountGroup->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'nature' => $request->nature,
            'parent_id' => $request->parent_id,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('tenant.accounting.account-groups.show', [
                'tenant' => $tenant->slug,
                'account_group' => $accountGroup->id
            ])
            ->with('success', 'Account group updated successfully.');
    }

    /**
     * Remove the specified account group.
     */
    public function destroy(Tenant $tenant, AccountGroup $accountGroup)
    {
        // Ensure account group belongs to tenant
        if ($accountGroup->tenant_id !== $tenant->id) {
            abort(404);
        }

        // Check if account group has ledger accounts
        $ledgerAccountsCount = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('account_group_id', $accountGroup->id)
            ->count();

        if ($ledgerAccountsCount > 0) {
            return redirect()
                ->route('tenant.accounting.account-groups.show', [
                    'tenant' => $tenant->slug,
                    'account_group' => $accountGroup->id
                ])
                ->with('error', 'Cannot delete account group that has ledger accounts.');
        }

        // Check if account group has children
        if ($accountGroup->children()->count() > 0) {
            return redirect()
                ->route('tenant.accounting.account-groups.show', [
                    'tenant' => $tenant->slug,
                    'account_group' => $accountGroup->id
                ])
                ->with('error', 'Cannot delete account group that has child groups.');
        }

        $accountGroup->delete();

        return redirect()
            ->route('tenant.accounting.account-groups.index', ['tenant' => $tenant->slug])
            ->with('success', 'Account group deleted successfully.');
    }

    /**
     * Toggle the active status of an account group.
     */
    public function toggle(Tenant $tenant, AccountGroup $accountGroup)
    {
        // Ensure account group belongs to tenant
        if ($accountGroup->tenant_id !== $tenant->id) {
            abort(404);
        }

        $accountGroup->update([
            'is_active' => !$accountGroup->is_active
        ]);

        $status = $accountGroup->is_active ? 'activated' : 'deactivated';

        return redirect()
            ->route('tenant.accounting.account-groups.show', [
                'tenant' => $tenant->slug,
                'account_group' => $accountGroup->id
            ])
            ->with('success', "Account group {$status} successfully.");
    }

    /**
     * Get account group hierarchy for API/AJAX requests.
     */
    public function apiIndex(Request $request, Tenant $tenant)
    {
        $query = AccountGroup::where('tenant_id', $tenant->id)
            ->where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('nature')) {
            $query->where('nature', $request->get('nature'));
        }

        $accountGroups = $query->with(['parent', 'children'])->orderBy('name')->get();

        return response()->json([
            'data' => $accountGroups->map(function ($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'code' => $group->code,
                    'nature' => $group->nature,
                    'full_name' => $group->getFullNameAttribute(),
                    'level' => $group->getLevel(),
                    'parent_id' => $group->parent_id,
                    'has_children' => $group->children()->count() > 0,
                ];
            })
        ]);
    }

    /**
     * Bulk actions for account groups.
     */
    public function bulkAction(Request $request, Tenant $tenant)
    {
        $request->validate([
            'action' => ['required', 'in:activate,deactivate,delete'],
            'account_groups' => ['required', 'array', 'min:1'],
            'account_groups.*' => ['exists:account_groups,id']
        ]);

        $accountGroupIds = $request->account_groups;
        $action = $request->action;

        // Get account groups belonging to this tenant
        $accountGroups = AccountGroup::where('tenant_id', $tenant->id)
            ->whereIn('id', $accountGroupIds)
            ->get();

        if ($accountGroups->isEmpty()) {
            return redirect()
                ->route('tenant.accounting.account-groups.index', ['tenant' => $tenant->slug])
                ->with('error', 'No valid account groups selected.');
        }

        $successCount = 0;
        $errors = [];

        foreach ($accountGroups as $accountGroup) {
            try {
                switch ($action) {
                    case 'activate':
                        if (!$accountGroup->is_active) {
                            $accountGroup->update(['is_active' => true]);
                            $successCount++;
                        }
                        break;

                    case 'deactivate':
                        if ($accountGroup->is_active) {
                            $accountGroup->update(['is_active' => false]);
                            $successCount++;
                        }
                        break;

                    case 'delete':
                        // Check constraints
                        $ledgerAccountsCount = LedgerAccount::where('tenant_id', $tenant->id)
                            ->where('account_group_id', $accountGroup->id)
                            ->count();

                        if ($ledgerAccountsCount > 0) {
                            $errors[] = "Cannot delete account group with ledger accounts: {$accountGroup->name}";
                            continue 2;
                        }

                        if ($accountGroup->children()->count() > 0) {
                            $errors[] = "Cannot delete account group with child groups: {$accountGroup->name}";
                            continue 2;
                        }

                        $accountGroup->delete();
                        $successCount++;
                        break;
                }
            } catch (\Exception $e) {
                $errors[] = "Error processing {$accountGroup->name}: " . $e->getMessage();
            }
        }

        $message = '';
        if ($successCount > 0) {
            $actionText = $action === 'activate' ? 'activated' : ($action === 'deactivate' ? 'deactivated' : 'deleted');
            $message = "{$successCount} account group(s) {$actionText} successfully.";
        }

        if (!empty($errors)) {
            $message .= ' ' . implode(' ', $errors);
        }

        return redirect()
            ->route('tenant.accounting.account-groups.index', ['tenant' => $tenant->slug])
            ->with($successCount > 0 ? 'success' : 'error', $message);
    }
}