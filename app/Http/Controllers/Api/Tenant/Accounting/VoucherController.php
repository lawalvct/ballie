<?php

namespace App\Http\Controllers\Api\Tenant\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Voucher;
use App\Models\VoucherType;
use App\Models\VoucherEntry;
use App\Models\LedgerAccount;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Voucher Management API Controller
 *
 * Handles all voucher-related operations for mobile app:
 * - Journal Vouchers (JV)
 * - Payment Vouchers (PV)
 * - Receipt Vouchers (RV)
 * - Contra Vouchers (CV)
 * - Credit Notes (CN)
 * - Debit Notes (DN)
 */
class VoucherController extends Controller
{
    /**
     * Get form data for creating a voucher
     *
     * @param Request $request
     * @param Tenant $tenant
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request, Tenant $tenant)
    {
        try {
            // Get voucher types
            $voucherTypes = VoucherType::where('tenant_id', $tenant->id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
                ->map(function ($type) {
                    return [
                        'id' => $type->id,
                        'name' => $type->name,
                        'code' => $type->code,
                        'description' => $type->description,
                        'has_numbering' => $type->has_numbering,
                        'number_prefix' => $type->number_prefix,
                        'number_suffix' => $type->number_suffix,
                        'next_number' => $type->next_number,
                    ];
                });

            // Get ledger accounts organized by account group
            $ledgerAccounts = LedgerAccount::with('accountGroup')
                ->where('tenant_id', $tenant->id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
                ->map(function ($account) {
                    return [
                        'id' => $account->id,
                        'name' => $account->name,
                        'code' => $account->code,
                        'display_name' => $account->display_name,
                        'account_type' => $account->account_type,
                        'account_group_id' => $account->account_group_id,
                        'account_group_name' => $account->accountGroup ? $account->accountGroup->name : null,
                        'parent_id' => $account->parent_id,
                        'level' => $account->level,
                        'current_balance' => $account->getCurrentBalance(),
                    ];
                });

            // Get products (for inventory-related entries)
            $products = Product::where('tenant_id', $tenant->id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'price' => $product->price,
                        'cost' => $product->cost,
                        'stock_quantity' => $product->stock_quantity,
                    ];
                });

            // Get selected voucher type if provided
            $selectedType = null;
            if ($request->has('type')) {
                $selectedType = VoucherType::where('tenant_id', $tenant->id)
                    ->where('code', strtoupper($request->type))
                    ->first();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'voucher_types' => $voucherTypes,
                    'ledger_accounts' => $ledgerAccounts,
                    'products' => $products,
                    'selected_type' => $selectedType ? [
                        'id' => $selectedType->id,
                        'name' => $selectedType->name,
                        'code' => $selectedType->code,
                        'description' => $selectedType->description,
                    ] : null,
                    'defaults' => [
                        'voucher_date' => now()->format('Y-m-d'),
                        'status' => 'draft',
                    ],
                    'validation_rules' => [
                        'voucher_type_id' => 'required|exists:voucher_types,id',
                        'voucher_date' => 'required|date',
                        'voucher_number' => 'nullable|string|max:50',
                        'narration' => 'nullable|string|max:1000',
                        'entries' => 'required|array|min:2',
                        'entries.*.ledger_account_id' => 'required|exists:ledger_accounts,id',
                        'entries.*.debit_amount' => 'nullable|numeric|min:0',
                        'entries.*.credit_amount' => 'nullable|numeric|min:0',
                        'entries.*.description' => 'nullable|string|max:500',
                    ],
                ],
                'message' => 'Form data retrieved successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Voucher create API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve form data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created voucher
     *
     * @param Request $request
     * @param Tenant $tenant
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Tenant $tenant)
    {
        try {
            // Transform contra voucher data to standard journal entry format
            if ($request->has('cv_from_account_id') && $request->has('cv_to_account_id')) {
                $request->merge([
                    'entries' => [
                        [
                            'ledger_account_id' => $request->cv_to_account_id,
                            'debit_amount' => $request->amount,
                            'credit_amount' => 0,
                            'description' => $request->narration ?? 'Contra transfer',
                        ],
                        [
                            'ledger_account_id' => $request->cv_from_account_id,
                            'debit_amount' => 0,
                            'credit_amount' => $request->amount,
                            'description' => $request->narration ?? 'Contra transfer',
                        ],
                    ],
                ]);
            }

            // Transform credit note data to standard journal entry format
            if ($request->has('cn_customer_account_id') && $request->has('credit_entries')) {
                $entries = [];
                $totalCredit = 0;

                foreach ($request->credit_entries as $creditEntry) {
                    $entries[] = [
                        'ledger_account_id' => $creditEntry['ledger_account_id'],
                        'debit_amount' => $creditEntry['amount'],
                        'credit_amount' => 0,
                        'description' => $creditEntry['description'] ?? '',
                    ];
                    $totalCredit += $creditEntry['amount'];
                }

                $entries[] = [
                    'ledger_account_id' => $request->cn_customer_account_id,
                    'debit_amount' => 0,
                    'credit_amount' => $totalCredit,
                    'description' => $request->narration ?? 'Credit Note',
                ];

                $request->merge(['entries' => $entries]);
            }

            // Transform debit note data to standard journal entry format
            if ($request->has('dn_customer_account_id') && $request->has('debit_entries')) {
                $entries = [];
                $totalDebit = 0;

                foreach ($request->debit_entries as $debitEntry) {
                    $entries[] = [
                        'ledger_account_id' => $debitEntry['ledger_account_id'],
                        'debit_amount' => 0,
                        'credit_amount' => $debitEntry['amount'],
                        'description' => $debitEntry['description'] ?? '',
                    ];
                    $totalDebit += $debitEntry['amount'];
                }

                $entries[] = [
                    'ledger_account_id' => $request->dn_customer_account_id,
                    'debit_amount' => $totalDebit,
                    'credit_amount' => 0,
                    'description' => $request->narration ?? 'Debit Note',
                ];

                $request->merge(['entries' => $entries]);
            }

            // Validate request
            $validated = $request->validate([
                'voucher_type_id' => 'required|exists:voucher_types,id',
                'voucher_date' => 'required|date',
                'voucher_number' => 'nullable|string|max:50',
                'narration' => 'nullable|string|max:1000',
                'reference_number' => 'nullable|string|max:100',
                'entries' => 'required|array|min:2',
                'entries.*.ledger_account_id' => 'required|exists:ledger_accounts,id',
                'entries.*.debit_amount' => 'nullable|numeric|min:0',
                'entries.*.credit_amount' => 'nullable|numeric|min:0',
                'entries.*.description' => 'nullable|string|max:500',
            ]);

            // Validate that entries are balanced
            $totalDebits = collect($request->entries)->sum('debit_amount');
            $totalCredits = collect($request->entries)->sum('credit_amount');

            if (abs($totalDebits - $totalCredits) > 0.01) {
                throw ValidationException::withMessages([
                    'entries' => ['Voucher entries must be balanced. Total debits must equal total credits.'],
                ]);
            }

            if ($totalDebits == 0) {
                throw ValidationException::withMessages([
                    'entries' => ['Voucher must have at least one debit or credit entry with an amount.'],
                ]);
            }

            // Validate each entry has either debit or credit (not both)
            foreach ($request->entries as $index => $entry) {
                $debit = $entry['debit_amount'] ?? 0;
                $credit = $entry['credit_amount'] ?? 0;

                if ($debit > 0 && $credit > 0) {
                    throw ValidationException::withMessages([
                        "entries.{$index}" => ['An entry cannot have both debit and credit amounts.'],
                    ]);
                }

                if ($debit == 0 && $credit == 0) {
                    throw ValidationException::withMessages([
                        "entries.{$index}" => ['An entry must have either a debit or credit amount.'],
                    ]);
                }
            }

            // Create voucher
            $voucher = DB::transaction(function () use ($request, $tenant, $validated) {
                $voucherType = VoucherType::findOrFail($validated['voucher_type_id']);

                // Generate voucher number if not provided
                $voucherNumber = $validated['voucher_number'] ?? null;

            if (!$voucherNumber) {
                if ($voucherType->has_numbering) {
                    // Use voucher type's auto-numbering
                    $voucherNumber = $voucherType->generateNextNumber();
                } else {
                    // Generate fallback number using timestamp
                    $prefix = $voucherType->code ?? 'VC';
                    $timestamp = now()->format('YmdHis');
                    $voucherNumber = $prefix . '-' . $timestamp;
                }
            }

            // Create voucher
            $voucher = Voucher::create([
                'tenant_id' => $tenant->id,
                'voucher_type_id' => $validated['voucher_type_id'],
                'voucher_number' => $voucherNumber,
                'voucher_date' => $validated['voucher_date'],
                'narration' => $validated['narration'] ?? null,
                'reference_number' => $validated['reference_number'] ?? null,
                'total_amount' => collect($request->entries)->sum('debit_amount'),
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

                // Create entries
                foreach ($validated['entries'] as $entryData) {
                    VoucherEntry::create([
                        'voucher_id' => $voucher->id,
                        'ledger_account_id' => $entryData['ledger_account_id'],
                        'debit_amount' => $entryData['debit_amount'] ?? 0,
                        'credit_amount' => $entryData['credit_amount'] ?? 0,
                        'description' => $entryData['description'] ?? null,
                    ]);
                }

                return $voucher->fresh(['voucherType', 'entries.ledgerAccount']);
            });

            // Handle post action if requested
            if ($request->input('action') === 'save_and_post') {
                $this->postVoucher($voucher);
                $voucher->refresh();
            }

            return response()->json([
                'success' => true,
                'message' => 'Voucher created successfully',
                'data' => $this->formatVoucherResponse($voucher),
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Voucher store API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create voucher',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * List vouchers with filters and pagination
     *
     * @param Request $request
     * @param Tenant $tenant
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, Tenant $tenant)
    {
        try {
            $query = Voucher::with(['voucherType', 'createdBy'])
                ->where('tenant_id', $tenant->id);

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('voucher_number', 'like', "%{$search}%")
                      ->orWhere('narration', 'like', "%{$search}%")
                      ->orWhere('reference_number', 'like', "%{$search}%");
                });
            }

            if ($request->filled('voucher_type_id')) {
                $query->where('voucher_type_id', $request->voucher_type_id);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('voucher_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('voucher_date', '<=', $request->date_to);
            }

            // Sorting
            $sortBy = $request->input('sort_by', 'voucher_date');
            $sortDirection = $request->input('sort_direction', 'desc');
            $query->orderBy($sortBy, $sortDirection);

            // Pagination
            $perPage = $request->input('per_page', 20);
            $vouchers = $query->paginate($perPage);

            // Calculate statistics
            $baseQuery = Voucher::where('tenant_id', $tenant->id);
            $stats = [
                'total_vouchers' => $baseQuery->count(),
                'draft_vouchers' => (clone $baseQuery)->where('status', 'draft')->count(),
                'posted_vouchers' => (clone $baseQuery)->where('status', 'posted')->count(),
                'total_amount' => $baseQuery->sum('total_amount'),
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'vouchers' => $vouchers->map(function ($voucher) {
                        return $this->formatVoucherResponse($voucher, false);
                    }),
                    'pagination' => [
                        'current_page' => $vouchers->currentPage(),
                        'last_page' => $vouchers->lastPage(),
                        'per_page' => $vouchers->perPage(),
                        'total' => $vouchers->total(),
                        'from' => $vouchers->firstItem(),
                        'to' => $vouchers->lastItem(),
                    ],
                    'statistics' => $stats,
                ],
                'message' => 'Vouchers retrieved successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Voucher index API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve vouchers',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show voucher details
     *
     * @param Tenant $tenant
     * @param Voucher $voucher
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Tenant $tenant, Voucher $voucher)
    {
        try {
            // Ensure voucher belongs to tenant
            if ($voucher->tenant_id !== $tenant->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Voucher not found',
                ], 404);
            }

            $voucher->load([
                'voucherType',
                'entries.ledgerAccount.accountGroup',
                'createdBy',
                'updatedBy',
                'postedBy',
            ]);

            return response()->json([
                'success' => true,
                'data' => $this->formatVoucherResponse($voucher, true),
                'message' => 'Voucher retrieved successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Voucher show API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve voucher',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update voucher
     *
     * @param Request $request
     * @param Tenant $tenant
     * @param Voucher $voucher
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Tenant $tenant, Voucher $voucher)
    {
        try {
            // Ensure voucher belongs to tenant
            if ($voucher->tenant_id !== $tenant->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Voucher not found',
                ], 404);
            }

            // Check if voucher is posted
            if ($voucher->status === 'posted') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot update a posted voucher. Please unpost it first.',
                ], 422);
            }

            // Validate request
            $validated = $request->validate([
                'voucher_type_id' => 'required|exists:voucher_types,id',
                'voucher_date' => 'required|date',
                'voucher_number' => 'nullable|string|max:50',
                'narration' => 'nullable|string|max:1000',
                'reference_number' => 'nullable|string|max:100',
                'entries' => 'required|array|min:2',
                'entries.*.ledger_account_id' => 'required|exists:ledger_accounts,id',
                'entries.*.debit_amount' => 'nullable|numeric|min:0',
                'entries.*.credit_amount' => 'nullable|numeric|min:0',
                'entries.*.description' => 'nullable|string|max:500',
            ]);

            // Validate that entries are balanced
            $totalDebits = collect($request->entries)->sum('debit_amount');
            $totalCredits = collect($request->entries)->sum('credit_amount');

            if (abs($totalDebits - $totalCredits) > 0.01) {
                throw ValidationException::withMessages([
                    'entries' => ['Voucher entries must be balanced. Total debits must equal total credits.'],
                ]);
            }

            // Update voucher
            DB::transaction(function () use ($request, $voucher, $validated, $totalDebits) {
                // Update voucher details
                $voucher->update([
                    'voucher_type_id' => $validated['voucher_type_id'],
                    'voucher_date' => $validated['voucher_date'],
                    'voucher_number' => $validated['voucher_number'] ?? $voucher->voucher_number,
                    'narration' => $validated['narration'] ?? null,
                    'reference_number' => $validated['reference_number'] ?? null,
                    'total_amount' => $totalDebits,
                    'updated_by' => Auth::id(),
                ]);

                // Delete old entries
                $voucher->entries()->delete();

                // Create new entries
                foreach ($validated['entries'] as $entryData) {
                    VoucherEntry::create([
                        'voucher_id' => $voucher->id,
                        'ledger_account_id' => $entryData['ledger_account_id'],
                        'debit_amount' => $entryData['debit_amount'] ?? 0,
                        'credit_amount' => $entryData['credit_amount'] ?? 0,
                        'description' => $entryData['description'] ?? null,
                    ]);
                }
            });

            $voucher->refresh(['voucherType', 'entries.ledgerAccount']);

            return response()->json([
                'success' => true,
                'message' => 'Voucher updated successfully',
                'data' => $this->formatVoucherResponse($voucher),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Voucher update API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update voucher',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete voucher
     *
     * @param Tenant $tenant
     * @param Voucher $voucher
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Tenant $tenant, Voucher $voucher)
    {
        try {
            // Ensure voucher belongs to tenant
            if ($voucher->tenant_id !== $tenant->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Voucher not found',
                ], 404);
            }

            // Check if voucher is posted
            if ($voucher->status === 'posted') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete a posted voucher. Please unpost it first.',
                ], 422);
            }

            DB::transaction(function () use ($voucher) {
                $voucher->entries()->delete();
                $voucher->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Voucher deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Voucher destroy API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete voucher',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Post a voucher (finalize it)
     *
     * @param Tenant $tenant
     * @param Voucher $voucher
     * @return \Illuminate\Http\JsonResponse
     */
    public function post(Tenant $tenant, Voucher $voucher)
    {
        try {
            // Ensure voucher belongs to tenant
            if ($voucher->tenant_id !== $tenant->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Voucher not found',
                ], 404);
            }

            // Check if already posted
            if ($voucher->status === 'posted') {
                return response()->json([
                    'success' => false,
                    'message' => 'Voucher is already posted',
                ], 422);
            }

            // Validate voucher is balanced
            $totalDebits = $voucher->entries->sum('debit_amount');
            $totalCredits = $voucher->entries->sum('credit_amount');

            if (abs($totalDebits - $totalCredits) > 0.01) {
                return response()->json([
                    'success' => false,
                    'message' => 'Voucher is not balanced. Cannot post unbalanced vouchers.',
                ], 422);
            }

            if ($voucher->entries->count() < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Voucher must have at least 2 entries',
                ], 422);
            }

            $this->postVoucher($voucher);
            $voucher->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Voucher posted successfully',
                'data' => $this->formatVoucherResponse($voucher),
            ]);
        } catch (\Exception $e) {
            Log::error('Voucher post API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to post voucher',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Unpost a voucher
     *
     * @param Tenant $tenant
     * @param Voucher $voucher
     * @return \Illuminate\Http\JsonResponse
     */
    public function unpost(Tenant $tenant, Voucher $voucher)
    {
        try {
            // Ensure voucher belongs to tenant
            if ($voucher->tenant_id !== $tenant->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Voucher not found',
                ], 404);
            }

            // Check if not posted
            if ($voucher->status !== 'posted') {
                return response()->json([
                    'success' => false,
                    'message' => 'Voucher is not posted',
                ], 422);
            }

            DB::transaction(function () use ($voucher) {
                $voucher->update([
                    'status' => 'draft',
                    'posted_at' => null,
                    'posted_by' => null,
                    'updated_by' => Auth::id(),
                ]);
            });

            $voucher->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Voucher unposted successfully',
                'data' => $this->formatVoucherResponse($voucher),
            ]);
        } catch (\Exception $e) {
            Log::error('Voucher unpost API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to unpost voucher',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get duplicate voucher data
     *
     * @param Tenant $tenant
     * @param Voucher $voucher
     * @return \Illuminate\Http\JsonResponse
     */
    public function duplicate(Tenant $tenant, Voucher $voucher)
    {
        try {
            // Ensure voucher belongs to tenant
            if ($voucher->tenant_id !== $tenant->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Voucher not found',
                ], 404);
            }

            $voucher->load('entries.ledgerAccount');

            $duplicateData = [
                'voucher_type_id' => $voucher->voucher_type_id,
                'voucher_date' => now()->format('Y-m-d'),
                'narration' => $voucher->narration,
                'reference_number' => null, // Clear reference for duplicate
                'entries' => $voucher->entries->map(function ($entry) {
                    return [
                        'ledger_account_id' => $entry->ledger_account_id,
                        'ledger_account_name' => $entry->ledgerAccount->display_name,
                        'debit_amount' => $entry->debit_amount,
                        'credit_amount' => $entry->credit_amount,
                        'description' => $entry->description,
                    ];
                })->toArray(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Duplicate data retrieved successfully',
                'data' => $duplicateData,
            ]);
        } catch (\Exception $e) {
            Log::error('Voucher duplicate API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve duplicate data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk actions on vouchers
     *
     * @param Request $request
     * @param Tenant $tenant
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkAction(Request $request, Tenant $tenant)
    {
        try {
            $validated = $request->validate([
                'action' => 'required|in:post,unpost,delete',
                'voucher_ids' => 'required|array|min:1',
                'voucher_ids.*' => 'exists:vouchers,id',
            ]);

            $vouchers = Voucher::where('tenant_id', $tenant->id)
                ->whereIn('id', $validated['voucher_ids'])
                ->get();

            $successCount = 0;
            $errors = [];

            DB::beginTransaction();
            try {
                foreach ($vouchers as $voucher) {
                    try {
                        switch ($validated['action']) {
                            case 'post':
                                if ($voucher->status !== 'posted') {
                                    $this->postVoucher($voucher);
                                    $successCount++;
                                }
                                break;

                            case 'unpost':
                                if ($voucher->status === 'posted') {
                                    $voucher->update([
                                        'status' => 'draft',
                                        'posted_at' => null,
                                        'posted_by' => null,
                                        'updated_by' => Auth::id(),
                                    ]);
                                    $successCount++;
                                }
                                break;

                            case 'delete':
                                if ($voucher->status !== 'posted') {
                                    $voucher->entries()->delete();
                                    $voucher->delete();
                                    $successCount++;
                                } else {
                                    $errors[] = "Voucher {$voucher->voucher_number} is posted and cannot be deleted";
                                }
                                break;
                        }
                    } catch (\Exception $e) {
                        $errors[] = "Voucher {$voucher->voucher_number}: {$e->getMessage()}";
                    }
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => "{$successCount} vouchers processed successfully",
                    'data' => [
                        'success_count' => $successCount,
                        'failed_count' => count($errors),
                        'errors' => $errors,
                    ],
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Voucher bulk action API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform bulk action',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search vouchers for autocomplete
     *
     * @param Request $request
     * @param Tenant $tenant
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request, Tenant $tenant)
    {
        try {
            $query = Voucher::with('voucherType')
                ->where('tenant_id', $tenant->id);

            if ($request->filled('q')) {
                $search = $request->q;
                $query->where(function ($q) use ($search) {
                    $q->where('voucher_number', 'like', "%{$search}%")
                      ->orWhere('narration', 'like', "%{$search}%")
                      ->orWhere('reference_number', 'like', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('voucher_type_id')) {
                $query->where('voucher_type_id', $request->voucher_type_id);
            }

            $vouchers = $query->orderBy('voucher_date', 'desc')
                ->limit(20)
                ->get()
                ->map(function ($voucher) {
                    return [
                        'id' => $voucher->id,
                        'voucher_number' => $voucher->voucher_number,
                        'voucher_type' => $voucher->voucherType->name ?? '',
                        'voucher_date' => $voucher->voucher_date,
                        'total_amount' => $voucher->total_amount,
                        'status' => $voucher->status,
                        'display_name' => $voucher->voucher_number . ' - ' . $voucher->voucherType->name . ' (' . $voucher->voucher_date . ')',
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $vouchers,
            ]);
        } catch (\Exception $e) {
            Log::error('Voucher search API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Search failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Format voucher response for API
     *
     * @param Voucher $voucher
     * @param bool $includeDetails
     * @return array
     */
    private function formatVoucherResponse(Voucher $voucher, bool $includeDetails = true)
    {
        $response = [
            'id' => $voucher->id,
            'voucher_type_id' => $voucher->voucher_type_id,
            'voucher_type_name' => $voucher->voucherType->name ?? '',
            'voucher_type_code' => $voucher->voucherType->code ?? '',
            'voucher_number' => $voucher->voucher_number,
            'voucher_date' => $voucher->voucher_date,
            'narration' => $voucher->narration,
            'reference_number' => $voucher->reference_number,
            'total_amount' => $voucher->total_amount,
            'status' => $voucher->status,
            'created_at' => $voucher->created_at?->toDateTimeString(),
            'updated_at' => $voucher->updated_at?->toDateTimeString(),
            'posted_at' => $voucher->posted_at?->toDateTimeString(),
        ];

        if ($includeDetails) {
            $response['entries'] = $voucher->entries->map(function ($entry) {
                return [
                    'id' => $entry->id,
                    'ledger_account_id' => $entry->ledger_account_id,
                    'ledger_account_name' => $entry->ledgerAccount->name ?? '',
                    'ledger_account_code' => $entry->ledgerAccount->code ?? '',
                    'ledger_account_display_name' => $entry->ledgerAccount->display_name ?? '',
                    'account_group_name' => $entry->ledgerAccount->accountGroup->name ?? '',
                    'debit_amount' => $entry->debit_amount,
                    'credit_amount' => $entry->credit_amount,
                    'description' => $entry->description,
                ];
            })->toArray();

            $response['created_by'] = [
                'id' => $voucher->createdBy->id ?? null,
                'name' => $voucher->createdBy->name ?? 'N/A',
            ];

            $response['updated_by'] = [
                'id' => $voucher->updatedBy->id ?? null,
                'name' => $voucher->updatedBy->name ?? 'N/A',
            ];

            $response['posted_by'] = [
                'id' => $voucher->postedBy->id ?? null,
                'name' => $voucher->postedBy->name ?? 'N/A',
            ];

            $response['can_be_edited'] = $voucher->status === 'draft';
            $response['can_be_deleted'] = $voucher->status === 'draft';
            $response['can_be_posted'] = $voucher->status === 'draft';
            $response['can_be_unposted'] = $voucher->status === 'posted';
        }

        return $response;
    }

    /**
     * Helper to post a voucher
     *
     * @param Voucher $voucher
     * @return void
     */
    private function postVoucher(Voucher $voucher)
    {
        DB::transaction(function () use ($voucher) {
            $voucher->update([
                'status' => 'posted',
                'posted_at' => now(),
                'posted_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        });
    }
}
