<?php

namespace App\Http\Controllers\Api\Tenant\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\LedgerAccount;
use App\Models\AccountGroup;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LedgerAccountController extends Controller
{
    /**
     * Get form data for creating a new ledger account.
     *
     * @param Tenant $tenant
     * @return JsonResponse
     */
    public function create(Tenant $tenant): JsonResponse
    {
        try {
            // Get active account groups organized by nature
            $accountGroups = AccountGroup::where('tenant_id', $tenant->id)
                ->where('is_active', true)
                ->orderBy('nature')
                ->orderBy('name')
                ->get()
                ->groupBy('nature')
                ->map(function ($groups, $nature) {
                    return [
                        'nature' => $nature,
                        'nature_label' => ucfirst($nature),
                        'groups' => $groups->map(function ($group) {
                            return [
                                'id' => $group->id,
                                'name' => $group->name,
                                'code' => $group->code,
                                'display_name' => "{$group->name} ({$group->code})",
                            ];
                        })->values()
                    ];
                })->values();

            // Get parent accounts for hierarchy
            $parentAccounts = LedgerAccount::where('tenant_id', $tenant->id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
                ->map(function ($account) {
                    return [
                        'id' => $account->id,
                        'name' => $account->name,
                        'code' => $account->code,
                        'account_type' => $account->account_type,
                        'display_name' => "{$account->code} - {$account->name}",
                    ];
                });

            // Account types
            $accountTypes = [
                [
                    'value' => 'asset',
                    'label' => 'Asset',
                    'description' => 'Resources owned (Cash, Inventory, Equipment)',
                    'icon' => '💰',
                    'balance_type' => 'debit'
                ],
                [
                    'value' => 'liability',
                    'label' => 'Liability',
                    'description' => 'Debts and obligations (Loans, Payables)',
                    'icon' => '📋',
                    'balance_type' => 'credit'
                ],
                [
                    'value' => 'equity',
                    'label' => 'Equity',
                    'description' => 'Owner\'s stake (Capital, Retained Earnings)',
                    'icon' => '🏦',
                    'balance_type' => 'credit'
                ],
                [
                    'value' => 'income',
                    'label' => 'Income',
                    'description' => 'Revenue and earnings (Sales, Services)',
                    'icon' => '📈',
                    'balance_type' => 'credit'
                ],
                [
                    'value' => 'expense',
                    'label' => 'Expense',
                    'description' => 'Costs and expenditures (Rent, Utilities)',
                    'icon' => '💸',
                    'balance_type' => 'debit'
                ]
            ];

            return response()->json([
                'success' => true,
                'message' => 'Ledger account creation form data retrieved successfully',
                'data' => [
                    'account_groups' => $accountGroups,
                    'parent_accounts' => $parentAccounts,
                    'account_types' => $accountTypes,
                    'defaults' => [
                        'is_active' => true,
                        'opening_balance' => 0,
                        'opening_balance_date' => now()->startOfMonth()->toDateString()
                    ],
                    'validation_rules' => [
                        'code' => 'Required, unique, max 20 characters',
                        'name' => 'Required, max 255 characters',
                        'account_type' => 'Required, one of: asset, liability, equity, income, expense',
                        'account_group_id' => 'Required, must exist',
                        'parent_id' => 'Optional, must exist and match account type',
                        'opening_balance' => 'Optional, numeric',
                        'opening_balance_date' => 'Optional, date (YYYY-MM-DD)',
                        'description' => 'Optional, max 500 characters',
                        'is_active' => 'Optional, boolean'
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load ledger account creation form',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created ledger account.
     *
     * @param Request $request
     * @param Tenant $tenant
     * @return JsonResponse
     */
    public function store(Request $request, Tenant $tenant): JsonResponse
    {
        try {
            // Validate request
            $validated = $request->validate([
                'code' => [
                    'required',
                    'string',
                    'max:20',
                    Rule::unique('ledger_accounts')->where('tenant_id', $tenant->id)
                ],
                'name' => ['required', 'string', 'max:255'],
                'account_type' => ['required', 'in:asset,liability,equity,income,expense'],
                'account_group_id' => ['required', 'exists:account_groups,id'],
                'parent_id' => ['nullable', 'exists:ledger_accounts,id'],
                'opening_balance' => ['nullable', 'numeric'],
                'opening_balance_date' => ['nullable', 'date'],
                'description' => ['nullable', 'string', 'max:500'],
                'address' => ['nullable', 'string', 'max:500'],
                'phone' => ['nullable', 'string', 'max:20'],
                'email' => ['nullable', 'email', 'max:100'],
                'is_active' => ['nullable', 'boolean'],
            ], [
                'code.unique' => 'This code is already used by another account.',
                'account_group_id.exists' => 'The selected account group is invalid.',
            ]);

            // Validate account group belongs to tenant
            $accountGroup = AccountGroup::where('tenant_id', $tenant->id)
                ->where('id', $request->account_group_id)
                ->first();

            if (!$accountGroup) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid account group selected',
                    'errors' => [
                        'account_group_id' => ['The selected account group is invalid']
                    ]
                ], 422);
            }

            // Validate parent account if provided
            if ($request->filled('parent_id')) {
                $parent = LedgerAccount::where('tenant_id', $tenant->id)
                    ->where('id', $request->parent_id)
                    ->first();

                if (!$parent) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid parent account',
                        'errors' => [
                            'parent_id' => ['The selected parent account is invalid']
                        ]
                    ], 422);
                }

                // Validate parent has same account type
                if ($parent->account_type !== $request->account_type) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Account type mismatch',
                        'errors' => [
                            'parent_id' => ['Parent account must have the same account type']
                        ]
                    ], 422);
                }
            }

            DB::beginTransaction();

            // Create ledger account
            $ledgerAccount = LedgerAccount::create([
                'tenant_id' => $tenant->id,
                'code' => strtoupper($validated['code']),
                'name' => $validated['name'],
                'account_type' => $validated['account_type'],
                'account_group_id' => $validated['account_group_id'],
                'parent_id' => $validated['parent_id'] ?? null,
                'opening_balance' => $validated['opening_balance'] ?? 0,
                'opening_balance_date' => $validated['opening_balance_date'] ?? now()->startOfMonth(),
                'current_balance' => $validated['opening_balance'] ?? 0,
                'description' => $validated['description'] ?? null,
                'address' => $validated['address'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
                'is_system_account' => false,
            ]);

            DB::commit();

            // Load relationships for response
            $ledgerAccount->load(['accountGroup', 'parent']);

            return response()->json([
                'success' => true,
                'message' => 'Ledger account created successfully',
                'data' => [
                    'ledger_account' => $this->formatAccountResponse($ledgerAccount)
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ledger account',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of ledger accounts.
     *
     * @param Request $request
     * @param Tenant $tenant
     * @return JsonResponse
     */
    public function index(Request $request, Tenant $tenant): JsonResponse
    {
        try {
            $query = LedgerAccount::where('tenant_id', $tenant->id)
                ->with(['accountGroup', 'parent']);

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Filter by account type
            if ($request->filled('account_type')) {
                $query->where('account_type', $request->get('account_type'));
            }

            // Filter by account group
            if ($request->filled('account_group_id')) {
                $query->where('account_group_id', $request->get('account_group_id'));
            }

            // Filter by status
            if ($request->filled('status')) {
                $isActive = $request->get('status') === 'active';
                $query->where('is_active', $isActive);
            }

            // Filter by parent/child
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
            $allowedSorts = ['name', 'code', 'account_type', 'current_balance', 'created_at', 'is_active'];

            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortDirection);
            }

            // Pagination
            $perPage = $request->get('per_page', 20);
            $ledgerAccounts = $query->paginate($perPage);

            // Format response data
            $formattedAccounts = $ledgerAccounts->map(function ($account) {
                return $this->formatAccountResponse($account);
            });

            // Get statistics
            $stats = [
                'total_accounts' => LedgerAccount::where('tenant_id', $tenant->id)->count(),
                'active_accounts' => LedgerAccount::where('tenant_id', $tenant->id)->where('is_active', true)->count(),
                'by_type' => [
                    'assets' => LedgerAccount::where('tenant_id', $tenant->id)->where('account_type', 'asset')->count(),
                    'liabilities' => LedgerAccount::where('tenant_id', $tenant->id)->where('account_type', 'liability')->count(),
                    'equity' => LedgerAccount::where('tenant_id', $tenant->id)->where('account_type', 'equity')->count(),
                    'income' => LedgerAccount::where('tenant_id', $tenant->id)->where('account_type', 'income')->count(),
                    'expenses' => LedgerAccount::where('tenant_id', $tenant->id)->where('account_type', 'expense')->count(),
                ],
            ];

            return response()->json([
                'success' => true,
                'message' => 'Ledger accounts retrieved successfully',
                'data' => [
                    'ledger_accounts' => $formattedAccounts,
                    'pagination' => [
                        'current_page' => $ledgerAccounts->currentPage(),
                        'per_page' => $ledgerAccounts->perPage(),
                        'total' => $ledgerAccounts->total(),
                        'last_page' => $ledgerAccounts->lastPage(),
                        'from' => $ledgerAccounts->firstItem(),
                        'to' => $ledgerAccounts->lastItem(),
                    ],
                    'statistics' => $stats,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve ledger accounts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified ledger account.
     *
     * @param Tenant $tenant
     * @param LedgerAccount $ledgerAccount
     * @return JsonResponse
     */
    public function show(Tenant $tenant, LedgerAccount $ledgerAccount): JsonResponse
    {
        try {
            // Ensure ledger account belongs to tenant
            if ($ledgerAccount->tenant_id !== $tenant->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ledger account not found'
                ], 404);
            }

            // Load relationships
            $ledgerAccount->load(['accountGroup', 'parent', 'children']);

            // Get recent transactions (last 10)
            $recentTransactions = $ledgerAccount->voucherEntries()
                ->with(['voucher.voucherType'])
                ->whereHas('voucher', function ($query) {
                    $query->where('status', 'posted');
                })
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($entry) {
                    return [
                        'id' => $entry->id,
                        'date' => $entry->voucher->date ?? $entry->created_at->toDateString(),
                        'voucher_number' => $entry->voucher->voucher_number ?? 'N/A',
                        'voucher_type' => $entry->voucher->voucherType->name ?? 'N/A',
                        'description' => $entry->description ?? $entry->voucher->description ?? '',
                        'debit' => $entry->debit_amount,
                        'credit' => $entry->credit_amount,
                        'created_at' => $entry->created_at->toIso8601String(),
                    ];
                });

            // Get statistics
            $totalTransactions = $ledgerAccount->voucherEntries()->count();
            $childrenCount = $ledgerAccount->children()->count();
            $canBeDeleted = $totalTransactions === 0 && $childrenCount === 0 && !$ledgerAccount->is_system_account;

            return response()->json([
                'success' => true,
                'message' => 'Ledger account retrieved successfully',
                'data' => [
                    'ledger_account' => $this->formatAccountResponse($ledgerAccount),
                    'children' => $ledgerAccount->children->map(function ($child) {
                        return [
                            'id' => $child->id,
                            'code' => $child->code,
                            'name' => $child->name,
                            'account_type' => $child->account_type,
                            'current_balance' => $child->current_balance,
                            'is_active' => $child->is_active,
                        ];
                    }),
                    'recent_transactions' => $recentTransactions,
                    'statistics' => [
                        'total_transactions' => $totalTransactions,
                        'children_count' => $childrenCount,
                        'can_be_deleted' => $canBeDeleted,
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve ledger account',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified ledger account.
     *
     * @param Request $request
     * @param Tenant $tenant
     * @param LedgerAccount $ledgerAccount
     * @return JsonResponse
     */
    public function update(Request $request, Tenant $tenant, LedgerAccount $ledgerAccount): JsonResponse
    {
        try {
            // Ensure ledger account belongs to tenant
            if ($ledgerAccount->tenant_id !== $tenant->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ledger account not found'
                ], 404);
            }

            // Validate request
            $validated = $request->validate([
                'code' => [
                    'required',
                    'string',
                    'max:20',
                    Rule::unique('ledger_accounts')->where('tenant_id', $tenant->id)->ignore($ledgerAccount->id)
                ],
                'name' => ['required', 'string', 'max:255'],
                'account_type' => ['required', 'in:asset,liability,equity,income,expense'],
                'account_group_id' => ['required', 'exists:account_groups,id'],
                'parent_id' => ['nullable', 'exists:ledger_accounts,id'],
                'description' => ['nullable', 'string', 'max:500'],
                'address' => ['nullable', 'string', 'max:500'],
                'phone' => ['nullable', 'string', 'max:20'],
                'email' => ['nullable', 'email', 'max:100'],
                'is_active' => ['nullable', 'boolean'],
            ], [
                'code.unique' => 'This code is already used by another account.',
            ]);

            // Validate account group belongs to tenant
            $accountGroup = AccountGroup::where('tenant_id', $tenant->id)
                ->where('id', $request->account_group_id)
                ->first();

            if (!$accountGroup) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid account group',
                    'errors' => ['account_group_id' => ['The selected account group is invalid']]
                ], 422);
            }

            // Validate parent account if provided
            if ($request->filled('parent_id')) {
                $parent = LedgerAccount::where('tenant_id', $tenant->id)
                    ->where('id', $request->parent_id)
                    ->first();

                if (!$parent) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid parent account',
                        'errors' => ['parent_id' => ['The selected parent account is invalid']]
                    ], 422);
                }

                // Prevent circular reference
                if ($parent->id === $ledgerAccount->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot set account as its own parent',
                        'errors' => ['parent_id' => ['An account cannot be its own parent']]
                    ], 422);
                }

                // Check if parent is a descendant
                $descendants = $ledgerAccount->getAllChildren()->pluck('id');
                if ($descendants->contains($parent->id)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid parent selection',
                        'errors' => ['parent_id' => ['Cannot set a child account as parent']]
                    ], 422);
                }

                // Account type must match
                if ($parent->account_type !== $request->account_type) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Account type mismatch',
                        'errors' => ['parent_id' => ['Parent account must have the same account type']]
                    ], 422);
                }
            }

            DB::beginTransaction();

            // Update ledger account
            $ledgerAccount->update([
                'code' => strtoupper($validated['code']),
                'name' => $validated['name'],
                'account_type' => $validated['account_type'],
                'account_group_id' => $validated['account_group_id'],
                'parent_id' => $validated['parent_id'] ?? null,
                'description' => $validated['description'] ?? $ledgerAccount->description,
                'address' => $validated['address'] ?? $ledgerAccount->address,
                'phone' => $validated['phone'] ?? $ledgerAccount->phone,
                'email' => $validated['email'] ?? $ledgerAccount->email,
                'is_active' => $validated['is_active'] ?? $ledgerAccount->is_active,
            ]);

            DB::commit();

            // Reload relationships
            $ledgerAccount->load(['accountGroup', 'parent']);

            return response()->json([
                'success' => true,
                'message' => 'Ledger account updated successfully',
                'data' => [
                    'ledger_account' => $this->formatAccountResponse($ledgerAccount)
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update ledger account',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified ledger account.
     *
     * @param Tenant $tenant
     * @param LedgerAccount $ledgerAccount
     * @return JsonResponse
     */
    public function destroy(Tenant $tenant, LedgerAccount $ledgerAccount): JsonResponse
    {
        try {
            // Ensure ledger account belongs to tenant
            if ($ledgerAccount->tenant_id !== $tenant->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ledger account not found'
                ], 404);
            }

            // Check if system account
            if ($ledgerAccount->is_system_account) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete system account',
                    'error' => 'System accounts are protected and cannot be deleted.'
                ], 422);
            }

            // Check if account has transactions
            $transactionsCount = $ledgerAccount->voucherEntries()->count();
            if ($transactionsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete account with transactions',
                    'error' => "This account has {$transactionsCount} transaction(s). Please archive it instead."
                ], 422);
            }

            // Check if account has children
            if ($ledgerAccount->children()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete account with child accounts',
                    'error' => 'This account has child accounts. Please delete or reassign them first.'
                ], 422);
            }

            $ledgerAccount->delete();

            return response()->json([
                'success' => true,
                'message' => 'Ledger account deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete ledger account',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle the active status of a ledger account.
     *
     * @param Tenant $tenant
     * @param LedgerAccount $ledgerAccount
     * @return JsonResponse
     */
    public function toggle(Tenant $tenant, LedgerAccount $ledgerAccount): JsonResponse
    {
        try {
            // Ensure ledger account belongs to tenant
            if ($ledgerAccount->tenant_id !== $tenant->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ledger account not found'
                ], 404);
            }

            $ledgerAccount->update([
                'is_active' => !$ledgerAccount->is_active
            ]);

            $status = $ledgerAccount->is_active ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "Ledger account {$status} successfully",
                'data' => [
                    'ledger_account' => [
                        'id' => $ledgerAccount->id,
                        'code' => $ledgerAccount->code,
                        'name' => $ledgerAccount->name,
                        'is_active' => $ledgerAccount->is_active,
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle ledger account status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search ledger accounts for autocomplete.
     *
     * @param Request $request
     * @param Tenant $tenant
     * @return JsonResponse
     */
    public function search(Request $request, Tenant $tenant): JsonResponse
    {
        try {
            $query = $request->get('search', '');

            if (strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'message' => 'Search query too short',
                    'data' => []
                ], 200);
            }

            $accounts = LedgerAccount::where('tenant_id', $tenant->id)
                ->where('is_active', true)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('code', 'like', "%{$query}%");
                })
                ->with(['accountGroup'])
                ->orderBy('name')
                ->limit(20)
                ->get();

            $results = $accounts->map(function ($account) {
                return [
                    'id' => $account->id,
                    'code' => $account->code,
                    'name' => $account->name,
                    'display_name' => "{$account->code} - {$account->name}",
                    'account_type' => $account->account_type,
                    'account_type_label' => ucfirst($account->account_type),
                    'current_balance' => $account->current_balance,
                    'account_group' => $account->accountGroup ? [
                        'id' => $account->accountGroup->id,
                        'name' => $account->accountGroup->name,
                    ] : null,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Search results retrieved successfully',
                'data' => [
                    'accounts' => $results,
                    'count' => $results->count()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get account balance information.
     *
     * @param Tenant $tenant
     * @param LedgerAccount $ledgerAccount
     * @return JsonResponse
     */
    public function balance(Tenant $tenant, LedgerAccount $ledgerAccount): JsonResponse
    {
        try {
            // Ensure ledger account belongs to tenant
            if ($ledgerAccount->tenant_id !== $tenant->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ledger account not found'
                ], 404);
            }

            $currentBalance = $ledgerAccount->current_balance ?? 0;

            return response()->json([
                'success' => true,
                'message' => 'Account balance retrieved successfully',
                'data' => [
                    'account_id' => $ledgerAccount->id,
                    'account_code' => $ledgerAccount->code,
                    'account_name' => $ledgerAccount->name,
                    'opening_balance' => $ledgerAccount->opening_balance,
                    'current_balance' => $currentBalance,
                    'balance_formatted' => number_format(abs($currentBalance), 2),
                    'balance_type' => $currentBalance >= 0 ? 'debit' : 'credit',
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve balance',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get child accounts.
     *
     * @param Tenant $tenant
     * @param LedgerAccount $ledgerAccount
     * @return JsonResponse
     */
    public function children(Tenant $tenant, LedgerAccount $ledgerAccount): JsonResponse
    {
        try {
            // Ensure ledger account belongs to tenant
            if ($ledgerAccount->tenant_id !== $tenant->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ledger account not found'
                ], 404);
            }

            $children = $ledgerAccount->children()
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
                ->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'code' => $child->code,
                        'name' => $child->name,
                        'display_name' => "{$child->code} - {$child->name}",
                        'account_type' => $child->account_type,
                        'current_balance' => $child->current_balance,
                        'is_active' => $child->is_active,
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Child accounts retrieved successfully',
                'data' => [
                    'parent' => [
                        'id' => $ledgerAccount->id,
                        'code' => $ledgerAccount->code,
                        'name' => $ledgerAccount->name,
                    ],
                    'children' => $children,
                    'count' => $children->count()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve child accounts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk operations on ledger accounts.
     *
     * @param Request $request
     * @param Tenant $tenant
     * @return JsonResponse
     */
    public function bulkAction(Request $request, Tenant $tenant): JsonResponse
    {
        try {
            $validated = $request->validate([
                'action' => ['required', 'in:activate,deactivate,delete'],
                'account_ids' => ['required', 'array', 'min:1'],
                'account_ids.*' => ['exists:ledger_accounts,id'],
            ]);

            $accountIds = $validated['account_ids'];
            $action = $validated['action'];

            // Verify all accounts belong to tenant
            $accounts = LedgerAccount::where('tenant_id', $tenant->id)
                ->whereIn('id', $accountIds)
                ->get();

            if ($accounts->count() !== count($accountIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some accounts do not belong to this tenant'
                ], 422);
            }

            DB::beginTransaction();

            $results = [
                'success_count' => 0,
                'failed_count' => 0,
                'errors' => []
            ];

            foreach ($accounts as $account) {
                try {
                    switch ($action) {
                        case 'activate':
                            $account->update(['is_active' => true]);
                            $results['success_count']++;
                            break;

                        case 'deactivate':
                            $account->update(['is_active' => false]);
                            $results['success_count']++;
                            break;

                        case 'delete':
                            // Check if system account
                            if ($account->is_system_account) {
                                $results['failed_count']++;
                                $results['errors'][] = "{$account->name}: Cannot delete system account";
                                continue 2;
                            }

                            // Check if has transactions
                            if ($account->voucherEntries()->count() > 0) {
                                $results['failed_count']++;
                                $results['errors'][] = "{$account->name}: Has transactions";
                                continue 2;
                            }

                            // Check if has children
                            if ($account->children()->count() > 0) {
                                $results['failed_count']++;
                                $results['errors'][] = "{$account->name}: Has child accounts";
                                continue 2;
                            }

                            $account->delete();
                            $results['success_count']++;
                            break;
                    }
                } catch (\Exception $e) {
                    $results['failed_count']++;
                    $results['errors'][] = "{$account->name}: {$e->getMessage()}";
                }
            }

            DB::commit();

            $message = match($action) {
                'activate' => "{$results['success_count']} account(s) activated",
                'deactivate' => "{$results['success_count']} account(s) deactivated",
                'delete' => "{$results['success_count']} account(s) deleted",
            };

            if ($results['failed_count'] > 0) {
                $message .= ", {$results['failed_count']} failed";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $results
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Bulk action failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format ledger account for API response.
     *
     * @param LedgerAccount $account
     * @return array
     */
    private function formatAccountResponse(LedgerAccount $account): array
    {
        return [
            'id' => $account->id,
            'code' => $account->code,
            'name' => $account->name,
            'account_type' => $account->account_type,
            'account_type_label' => ucfirst($account->account_type),
            'account_group_id' => $account->account_group_id,
            'account_group' => $account->accountGroup ? [
                'id' => $account->accountGroup->id,
                'name' => $account->accountGroup->name,
                'code' => $account->accountGroup->code,
                'nature' => $account->accountGroup->nature,
            ] : null,
            'parent_id' => $account->parent_id,
            'parent' => $account->parent ? [
                'id' => $account->parent->id,
                'code' => $account->parent->code,
                'name' => $account->parent->name,
            ] : null,
            'opening_balance' => $account->opening_balance,
            'opening_balance_date' => $account->opening_balance_date ? $account->opening_balance_date->toDateString() : null,
            'current_balance' => $account->current_balance,
            'description' => $account->description,
            'address' => $account->address,
            'phone' => $account->phone,
            'email' => $account->email,
            'is_active' => $account->is_active,
            'is_system_account' => $account->is_system_account ?? false,
            'children_count' => $account->children ? $account->children->count() : 0,
            'created_at' => $account->created_at->toIso8601String(),
            'updated_at' => $account->updated_at->toIso8601String(),
        ];
    }
}
