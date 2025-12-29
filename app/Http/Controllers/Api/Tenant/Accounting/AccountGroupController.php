<?php

namespace App\Http\Controllers\Api\Tenant\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\AccountGroup;
use App\Models\LedgerAccount;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AccountGroupController extends Controller
{
    /**
     * Get form data for creating a new account group.
     *
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        try {
            $tenant = tenant();

            // Get parent groups for hierarchy
            $parentGroups = AccountGroup::where('tenant_id', $tenant->id)
                ->where('is_active', true)
                ->whereNull('parent_id')
                ->orderBy('name')
                ->get()
                ->map(function ($group) {
                    return [
                        'id' => $group->id,
                        'name' => $group->name,
                        'code' => $group->code,
                        'nature' => $group->nature,
                        'display_name' => "{$group->name} ({$group->code})",
                    ];
                });

            // Account nature options
            $natures = [
                [
                    'value' => 'assets',
                    'label' => 'Assets',
                    'description' => 'Resources owned by the business (Cash, Inventory, Equipment, etc.)',
                    'icon' => '💰'
                ],
                [
                    'value' => 'liabilities',
                    'label' => 'Liabilities',
                    'description' => 'Debts and obligations (Accounts Payable, Loans, etc.)',
                    'icon' => '📋'
                ],
                [
                    'value' => 'equity',
                    'label' => 'Equity',
                    'description' => 'Owner\'s stake in the business (Capital, Retained Earnings, etc.)',
                    'icon' => '🏦'
                ],
                [
                    'value' => 'income',
                    'label' => 'Income',
                    'description' => 'Revenue and earnings (Sales, Service Income, etc.)',
                    'icon' => '📈'
                ],
                [
                    'value' => 'expenses',
                    'label' => 'Expenses',
                    'description' => 'Costs and expenditures (Office Expenses, Travel, etc.)',
                    'icon' => '💸'
                ]
            ];

            return response()->json([
                'success' => true,
                'message' => 'Account group creation form data retrieved successfully',
                'data' => [
                    'parent_groups' => $parentGroups,
                    'natures' => $natures,
                    'defaults' => [
                        'is_active' => true
                    ],
                    'validation_rules' => [
                        'name' => 'Required, max 255 characters',
                        'code' => 'Required, uppercase letters, numbers, hyphens, underscores only, max 10 characters',
                        'nature' => 'Required, one of: assets, liabilities, equity, income, expenses',
                        'parent_id' => 'Optional, must exist and have same nature',
                        'is_active' => 'Optional, boolean'
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load account group creation form',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created account group.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $tenant = tenant();

            // Validate request
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'code' => [
                    'required',
                    'string',
                    'max:10',
                    'regex:/^[A-Z0-9_-]+$/',
                    Rule::unique('account_groups')->where('tenant_id', $tenant->id)
                ],
                'nature' => ['required', 'in:assets,liabilities,equity,income,expenses'],
                'parent_id' => ['nullable', 'exists:account_groups,id'],
                'is_active' => ['nullable', 'boolean'],
            ], [
                'code.regex' => 'Code can only contain uppercase letters, numbers, hyphens, and underscores.',
                'code.unique' => 'This code is already used by another account group.',
            ]);

            // If parent is selected, validate it belongs to tenant and nature matches
            if ($request->filled('parent_id')) {
                $parent = AccountGroup::where('tenant_id', $tenant->id)
                    ->where('id', $request->parent_id)
                    ->first();

                if (!$parent) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid parent group selected',
                        'errors' => [
                            'parent_id' => ['The selected parent group is invalid']
                        ]
                    ], 422);
                }

                if ($parent->nature !== $request->nature) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Parent group must have the same nature',
                        'errors' => [
                            'parent_id' => ['Child groups must have the same nature as their parent']
                        ]
                    ], 422);
                }
            }

            // Create account group
            $accountGroup = AccountGroup::create([
                'tenant_id' => $tenant->id,
                'name' => $validated['name'],
                'code' => strtoupper($validated['code']),
                'nature' => $validated['nature'],
                'parent_id' => $validated['parent_id'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // Load relationships for response
            $accountGroup->load(['parent', 'children']);

            return response()->json([
                'success' => true,
                'message' => 'Account group created successfully',
                'data' => [
                    'account_group' => [
                        'id' => $accountGroup->id,
                        'name' => $accountGroup->name,
                        'code' => $accountGroup->code,
                        'nature' => $accountGroup->nature,
                        'nature_label' => ucfirst($accountGroup->nature),
                        'parent_id' => $accountGroup->parent_id,
                        'parent' => $accountGroup->parent ? [
                            'id' => $accountGroup->parent->id,
                            'name' => $accountGroup->parent->name,
                            'code' => $accountGroup->parent->code,
                        ] : null,
                        'is_active' => $accountGroup->is_active,
                        'is_system' => $accountGroup->is_system ?? false,
                        'children_count' => $accountGroup->children()->count(),
                        'ledger_accounts_count' => $accountGroup->ledgerAccounts()->count(),
                        'created_at' => $accountGroup->created_at->toIso8601String(),
                        'updated_at' => $accountGroup->updated_at->toIso8601String(),
                    ]
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create account group',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of account groups.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $tenant = tenant();

            $query = AccountGroup::where('tenant_id', $tenant->id)
                ->with(['parent', 'children']);

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            }

            // Filter by status
            if ($request->filled('status')) {
                $isActive = $request->get('status') === 'active';
                $query->where('is_active', $isActive);
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

            // Pagination
            $perPage = $request->get('per_page', 15);
            $accountGroups = $query->paginate($perPage);

            // Get account counts for each group
            $accountCounts = LedgerAccount::where('tenant_id', $tenant->id)
                ->select('account_group_id', DB::raw('count(*) as count'))
                ->groupBy('account_group_id')
                ->pluck('count', 'account_group_id');

            // Format response data
            $formattedGroups = $accountGroups->map(function ($group) use ($accountCounts) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'code' => $group->code,
                    'nature' => $group->nature,
                    'nature_label' => ucfirst($group->nature),
                    'parent_id' => $group->parent_id,
                    'parent' => $group->parent ? [
                        'id' => $group->parent->id,
                        'name' => $group->parent->name,
                        'code' => $group->parent->code,
                    ] : null,
                    'is_active' => $group->is_active,
                    'is_system' => $group->is_system ?? false,
                    'children_count' => $group->children()->count(),
                    'ledger_accounts_count' => $accountCounts[$group->id] ?? 0,
                    'created_at' => $group->created_at->toIso8601String(),
                ];
            });

            // Get statistics
            $stats = [
                'total_groups' => AccountGroup::where('tenant_id', $tenant->id)->count(),
                'active_groups' => AccountGroup::where('tenant_id', $tenant->id)->where('is_active', true)->count(),
                'parent_groups' => AccountGroup::where('tenant_id', $tenant->id)->whereNull('parent_id')->count(),
                'child_groups' => AccountGroup::where('tenant_id', $tenant->id)->whereNotNull('parent_id')->count(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Account groups retrieved successfully',
                'data' => [
                    'account_groups' => $formattedGroups,
                    'pagination' => [
                        'current_page' => $accountGroups->currentPage(),
                        'per_page' => $accountGroups->perPage(),
                        'total' => $accountGroups->total(),
                        'last_page' => $accountGroups->lastPage(),
                        'from' => $accountGroups->firstItem(),
                        'to' => $accountGroups->lastItem(),
                    ],
                    'statistics' => $stats,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve account groups',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified account group.
     *
     * @param Tenant $tenant
     * @param AccountGroup $accountGroup
     * @return JsonResponse
     */
    public function show(Tenant $tenant, AccountGroup $accountGroup): JsonResponse
    {
        try {

            // Ensure account group belongs to tenant
            if ($accountGroup->tenant_id !== $tenant->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account group not found'
                ], 404);
            }

            // Load relationships
            $accountGroup->load(['parent', 'children', 'ledgerAccounts']);

            // Get statistics
            $directAccountsCount = $accountGroup->ledgerAccounts()->count();
            $totalAccountsCount = $accountGroup->getTotalAccountsCount();
            $childGroupsCount = $accountGroup->children()->count();

            return response()->json([
                'success' => true,
                'message' => 'Account group retrieved successfully',
                'data' => [
                    'account_group' => [
                        'id' => $accountGroup->id,
                        'name' => $accountGroup->name,
                        'code' => $accountGroup->code,
                        'nature' => $accountGroup->nature,
                        'nature_label' => ucfirst($accountGroup->nature),
                        'parent_id' => $accountGroup->parent_id,
                        'parent' => $accountGroup->parent ? [
                            'id' => $accountGroup->parent->id,
                            'name' => $accountGroup->parent->name,
                            'code' => $accountGroup->parent->code,
                        ] : null,
                        'is_active' => $accountGroup->is_active,
                        'is_system' => $accountGroup->is_system ?? false,
                        'children_count' => $childGroupsCount,
                        'direct_accounts_count' => $directAccountsCount,
                        'total_accounts_count' => $totalAccountsCount,
                        'can_be_deleted' => $accountGroup->canBeDeleted(),
                        'created_at' => $accountGroup->created_at->toIso8601String(),
                        'updated_at' => $accountGroup->updated_at->toIso8601String(),
                    ],
                    'children' => $accountGroup->children->map(function ($child) {
                        return [
                            'id' => $child->id,
                            'name' => $child->name,
                            'code' => $child->code,
                            'nature' => $child->nature,
                            'is_active' => $child->is_active,
                            'ledger_accounts_count' => $child->ledgerAccounts()->count(),
                        ];
                    }),
                    'ledger_accounts' => $accountGroup->ledgerAccounts->map(function ($account) {
                        return [
                            'id' => $account->id,
                            'name' => $account->name,
                            'code' => $account->code,
                            'is_active' => $account->is_active,
                        ];
                    }),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve account group',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified account group.
     *
     * @param Request $request
     * @param Tenant $tenant
     * @param AccountGroup $accountGroup
     * @return JsonResponse
     */
    public function update(Request $request, Tenant $tenant, AccountGroup $accountGroup): JsonResponse
    {
        try {

            // Ensure account group belongs to tenant
            if ($accountGroup->tenant_id !== $tenant->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account group not found'
                ], 404);
            }

            // Validate request
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'code' => [
                    'required',
                    'string',
                    'max:10',
                    'regex:/^[A-Z0-9_-]+$/',
                    Rule::unique('account_groups')->where('tenant_id', $tenant->id)->ignore($accountGroup->id)
                ],
                'nature' => ['nullable', 'in:assets,liabilities,equity,income,expenses'],
                'parent_id' => ['nullable', 'exists:account_groups,id'],
                'is_active' => ['nullable', 'boolean'],
            ], [
                'code.regex' => 'Code can only contain uppercase letters, numbers, hyphens, and underscores.',
            ]);

            // Validate parent selection
            if ($request->filled('parent_id')) {
                $parent = AccountGroup::where('tenant_id', $tenant->id)
                    ->where('id', $request->parent_id)
                    ->first();

                if (!$parent) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid parent group',
                        'errors' => ['parent_id' => ['The selected parent group is invalid']]
                    ], 422);
                }

                // Prevent circular reference
                if ($parent->id === $accountGroup->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot set group as its own parent',
                        'errors' => ['parent_id' => ['A group cannot be its own parent']]
                    ], 422);
                }

                // Check if parent is a descendant
                $descendants = $accountGroup->getAllChildren()->pluck('id');
                if ($descendants->contains($parent->id)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid parent selection',
                        'errors' => ['parent_id' => ['Cannot set a child group as parent']]
                    ], 422);
                }

                // Nature must match
                if ($parent->nature !== $request->nature) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Parent nature mismatch',
                        'errors' => ['parent_id' => ['Parent group must have the same nature']]
                    ], 422);
                }
            }

            // Update account group
            $accountGroup->update([
                'name' => $validated['name'],
                'code' => strtoupper($validated['code']),
                'nature' => $validated['nature'],
                'parent_id' => $validated['parent_id'] ?? null,
                'is_active' => $validated['is_active'] ?? $accountGroup->is_active,
            ]);

            // Reload relationships
            $accountGroup->load(['parent', 'children']);

            return response()->json([
                'success' => true,
                'message' => 'Account group updated successfully',
                'data' => [
                    'account_group' => [
                        'id' => $accountGroup->id,
                        'name' => $accountGroup->name,
                        'code' => $accountGroup->code,
                        'nature' => $accountGroup->nature,
                        'nature_label' => ucfirst($accountGroup->nature),
                        'parent_id' => $accountGroup->parent_id,
                        'parent' => $accountGroup->parent ? [
                            'id' => $accountGroup->parent->id,
                            'name' => $accountGroup->parent->name,
                            'code' => $accountGroup->parent->code,
                        ] : null,
                        'is_active' => $accountGroup->is_active,
                        'updated_at' => $accountGroup->updated_at->toIso8601String(),
                    ]
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update account group',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified account group.
     *
     * @param Tenant $tenant
     * @param AccountGroup $accountGroup
     * @return JsonResponse
     */
    public function destroy(Tenant $tenant, AccountGroup $accountGroup): JsonResponse
    {
        try {

            // Ensure account group belongs to tenant
            if ($accountGroup->tenant_id !== $tenant->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account group not found'
                ], 404);
            }

            // Check if account group has ledger accounts
            $ledgerAccountsCount = LedgerAccount::where('tenant_id', $tenant->id)
                ->where('account_group_id', $accountGroup->id)
                ->count();

            if ($ledgerAccountsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete account group with ledger accounts',
                    'error' => "This group has {$ledgerAccountsCount} ledger account(s). Please move or delete them first."
                ], 422);
            }

            // Check if account group has children
            if ($accountGroup->children()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete account group with child groups',
                    'error' => 'This group has child groups. Please delete or reassign them first.'
                ], 422);
            }

            $accountGroup->delete();

            return response()->json([
                'success' => true,
                'message' => 'Account group deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete account group',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle the active status of an account group.
     *
     * @param Tenant $tenant
     * @param AccountGroup $accountGroup
     * @return JsonResponse
     */
    public function toggle(Tenant $tenant, AccountGroup $accountGroup): JsonResponse
    {
        try {

            // Ensure account group belongs to tenant
            if ($accountGroup->tenant_id !== $tenant->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account group not found'
                ], 404);
            }

            $accountGroup->update([
                'is_active' => !$accountGroup->is_active
            ]);

            $status = $accountGroup->is_active ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "Account group {$status} successfully",
                'data' => [
                    'account_group' => [
                        'id' => $accountGroup->id,
                        'name' => $accountGroup->name,
                        'is_active' => $accountGroup->is_active,
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle account group status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
