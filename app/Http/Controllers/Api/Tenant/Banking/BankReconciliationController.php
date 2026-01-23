<?php

namespace App\Http\Controllers\Api\Tenant\Banking;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\BankReconciliation;
use App\Models\BankReconciliationItem;
use App\Models\Tenant;
use App\Models\VoucherEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BankReconciliationController extends Controller
{
    /**
     * List reconciliations with filters and pagination.
     */
    public function index(Request $request, Tenant $tenant)
    {
        $query = BankReconciliation::where('tenant_id', $tenant->id)
            ->with(['bank', 'creator']);

        if ($request->filled('bank_id')) {
            $query->where('bank_id', $request->get('bank_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('reconciliation_date', [$request->get('from_date'), $request->get('to_date')]);
        }

        $sortBy = $request->get('sort_by', 'reconciliation_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['reconciliation_date', 'created_at', 'status'];

        if (in_array($sortBy, $allowedSorts, true)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $perPage = (int) $request->get('per_page', 20);
        $reconciliations = $query->paginate($perPage);

        $reconciliations->getCollection()->transform(function (BankReconciliation $reconciliation) {
            return $this->formatReconciliation($reconciliation);
        });

        $banks = Bank::where('tenant_id', $tenant->id)
            ->where('enable_reconciliation', true)
            ->orderBy('bank_name')
            ->get()
            ->map(function (Bank $bank) {
                return [
                    'id' => $bank->id,
                    'bank_name' => $bank->bank_name,
                    'account_number' => $bank->account_number,
                    'masked_account_number' => $bank->masked_account_number,
                    'current_balance' => (float) $bank->getCurrentBalance(),
                ];
            });

        $stats = [
            'total' => BankReconciliation::where('tenant_id', $tenant->id)->count(),
            'completed' => BankReconciliation::where('tenant_id', $tenant->id)->where('status', 'completed')->count(),
            'in_progress' => BankReconciliation::where('tenant_id', $tenant->id)->where('status', 'in_progress')->count(),
            'draft' => BankReconciliation::where('tenant_id', $tenant->id)->where('status', 'draft')->count(),
            'cancelled' => BankReconciliation::where('tenant_id', $tenant->id)->where('status', 'cancelled')->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Reconciliations retrieved successfully',
            'data' => $reconciliations,
            'banks' => $banks,
            'statistics' => $stats,
        ]);
    }

    /**
     * Get create form data.
     */
    public function create(Request $request, Tenant $tenant)
    {
        $banks = Bank::where('tenant_id', $tenant->id)
            ->where('enable_reconciliation', true)
            ->where('status', 'active')
            ->orderBy('bank_name')
            ->get()
            ->map(function (Bank $bank) {
                return [
                    'id' => $bank->id,
                    'bank_name' => $bank->bank_name,
                    'account_name' => $bank->account_name,
                    'account_number' => $bank->account_number,
                    'masked_account_number' => $bank->masked_account_number,
                    'account_type' => $bank->account_type,
                    'current_balance' => (float) $bank->getCurrentBalance(),
                    'last_reconciliation_date' => $bank->last_reconciliation_date?->format('Y-m-d'),
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Reconciliation form data retrieved successfully',
            'data' => [
                'banks' => $banks,
            ],
        ]);
    }

    /**
     * Store a new reconciliation.
     */
    public function store(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'bank_id' => 'required|exists:banks,id',
            'reconciliation_date' => 'required|date',
            'statement_number' => 'nullable|string|max:255',
            'statement_start_date' => 'required|date|before_or_equal:statement_end_date',
            'statement_end_date' => 'required|date',
            'closing_balance_per_bank' => 'required|numeric',
            'bank_charges' => 'nullable|numeric|min:0',
            'interest_earned' => 'nullable|numeric|min:0',
            'other_adjustments' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $bank = Bank::where('tenant_id', $tenant->id)
            ->where('id', $validated['bank_id'])
            ->first();

        if (!$bank) {
            return response()->json([
                'success' => false,
                'message' => 'Bank account not found',
            ], 404);
        }

        if (!$bank->enable_reconciliation) {
            return response()->json([
                'success' => false,
                'message' => 'Reconciliation is not enabled for this bank account.',
            ], 422);
        }

        $validated['tenant_id'] = $tenant->id;
        $validated['created_by'] = $request->user()?->id;
        $validated['bank_charges'] = $validated['bank_charges'] ?? 0;
        $validated['interest_earned'] = $validated['interest_earned'] ?? 0;
        $validated['other_adjustments'] = $validated['other_adjustments'] ?? 0;

        $validated['opening_balance'] = $bank->getCurrentBalance();
        $validated['closing_balance_per_books'] = $bank->getCurrentBalance();
        $validated['difference'] = $validated['closing_balance_per_bank'] - $validated['closing_balance_per_books'];
        $validated['status'] = 'in_progress';

        DB::beginTransaction();

        try {
            $reconciliation = BankReconciliation::create($validated);
            $this->loadUnreconciledTransactions($reconciliation);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reconciliation created successfully',
                'data' => [
                    'reconciliation' => $this->formatReconciliation($reconciliation->fresh(['bank', 'creator'])),
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create reconciliation. ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show reconciliation details.
     */
    public function show(Request $request, Tenant $tenant, BankReconciliation $reconciliation)
    {
        if ($reconciliation->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Reconciliation not found',
            ], 404);
        }

        $reconciliation->load(['bank', 'items', 'creator', 'completedBy']);

        $items = $reconciliation->items->map(function (BankReconciliationItem $item) {
            return $this->formatReconciliationItem($item);
        })->values();

        $clearedItems = $items->where('status', 'cleared')->values();
        $unclearedItems = $items->where('status', 'uncleared')->values();

        return response()->json([
            'success' => true,
            'message' => 'Reconciliation retrieved successfully',
            'data' => [
                'reconciliation' => $this->formatReconciliation($reconciliation),
                'items' => $items,
                'cleared_items' => $clearedItems,
                'uncleared_items' => $unclearedItems,
                'statistics' => [
                    'total' => $reconciliation->total_transactions,
                    'reconciled' => $reconciliation->reconciled_transactions,
                    'unreconciled' => $reconciliation->unreconciled_transactions,
                    'progress' => $reconciliation->getProgressPercentage(),
                ],
            ],
        ]);
    }

    /**
     * Update reconciliation item status.
     */
    public function updateItemStatus(Request $request, Tenant $tenant, BankReconciliation $reconciliation)
    {
        if ($reconciliation->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Reconciliation not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:bank_reconciliation_items,id',
            'status' => 'required|in:cleared,uncleared,excluded',
            'cleared_date' => 'nullable|date',
            'bank_reference' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!$reconciliation->canBeEdited()) {
            return response()->json([
                'success' => false,
                'message' => 'Reconciliation cannot be edited',
            ], 403);
        }

        $item = BankReconciliationItem::where('bank_reconciliation_id', $reconciliation->id)
            ->findOrFail($validator->validated()['item_id']);

        $item->update([
            'status' => $validator->validated()['status'],
            'cleared_date' => $validator->validated()['status'] === 'cleared'
                ? ($validator->validated()['cleared_date'] ?? now())
                : null,
            'bank_reference' => $validator->validated()['bank_reference'] ?? null,
        ]);

        $reconciliation->updateStatistics();

        return response()->json([
            'success' => true,
            'message' => 'Item updated successfully',
            'data' => [
                'item' => $this->formatReconciliationItem($item->fresh()),
                'statistics' => [
                    'total' => $reconciliation->total_transactions,
                    'reconciled' => $reconciliation->reconciled_transactions,
                    'unreconciled' => $reconciliation->unreconciled_transactions,
                    'progress' => $reconciliation->getProgressPercentage(),
                ],
            ],
        ]);
    }

    /**
     * Complete reconciliation.
     */
    public function complete(Request $request, Tenant $tenant, BankReconciliation $reconciliation)
    {
        if ($reconciliation->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Reconciliation not found',
            ], 404);
        }

        if (!$reconciliation->canBeCompleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Reconciliation cannot be completed. Ensure it is balanced.',
            ], 422);
        }

        $reconciliation->markAsCompleted();

        return response()->json([
            'success' => true,
            'message' => 'Reconciliation completed successfully',
            'data' => [
                'reconciliation' => $this->formatReconciliation($reconciliation->fresh(['bank', 'creator', 'completedBy'])),
            ],
        ]);
    }

    /**
     * Cancel reconciliation.
     */
    public function cancel(Request $request, Tenant $tenant, BankReconciliation $reconciliation)
    {
        if ($reconciliation->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Reconciliation not found',
            ], 404);
        }

        try {
            $reconciliation->cancel();

            return response()->json([
                'success' => true,
                'message' => 'Reconciliation cancelled successfully',
                'data' => [
                    'reconciliation' => $this->formatReconciliation($reconciliation->fresh(['bank', 'creator'])),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Delete reconciliation.
     */
    public function destroy(Request $request, Tenant $tenant, BankReconciliation $reconciliation)
    {
        if ($reconciliation->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Reconciliation not found',
            ], 404);
        }

        if (!$reconciliation->canBeDeleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete a completed reconciliation.',
            ], 422);
        }

        $reconciliation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Reconciliation deleted successfully',
        ]);
    }

    private function loadUnreconciledTransactions(BankReconciliation $reconciliation): void
    {
        $bank = $reconciliation->bank;

        if (!$bank || !$bank->ledgerAccount) {
            return;
        }

        $transactions = VoucherEntry::where('ledger_account_id', $bank->ledger_account_id)
            ->whereHas('voucher', function ($q) use ($reconciliation) {
                $q->where('status', 'posted')
                    ->whereBetween('voucher_date', [
                        $reconciliation->statement_start_date,
                        $reconciliation->statement_end_date,
                    ]);
            })
            ->with('voucher.voucherType')
            ->get();

        foreach ($transactions as $entry) {
            BankReconciliationItem::create([
                'bank_reconciliation_id' => $reconciliation->id,
                'voucher_entry_id' => $entry->id,
                'transaction_date' => $entry->voucher?->voucher_date,
                'transaction_type' => 'voucher',
                'reference_number' => $entry->voucher?->voucher_number,
                'description' => $entry->particulars ?? $entry->voucher?->narration,
                'debit_amount' => $entry->debit_amount,
                'credit_amount' => $entry->credit_amount,
                'status' => 'uncleared',
            ]);
        }

        $reconciliation->updateStatistics();
    }

    private function formatReconciliation(BankReconciliation $reconciliation): array
    {
        return [
            'id' => $reconciliation->id,
            'bank_id' => $reconciliation->bank_id,
            'bank' => $reconciliation->bank ? [
                'id' => $reconciliation->bank->id,
                'bank_name' => $reconciliation->bank->bank_name,
                'account_name' => $reconciliation->bank->account_name,
                'account_number' => $reconciliation->bank->account_number,
                'masked_account_number' => $reconciliation->bank->masked_account_number,
                'currency' => $reconciliation->bank->currency,
            ] : null,
            'reconciliation_date' => $reconciliation->reconciliation_date?->format('Y-m-d'),
            'statement_number' => $reconciliation->statement_number,
            'statement_start_date' => $reconciliation->statement_start_date?->format('Y-m-d'),
            'statement_end_date' => $reconciliation->statement_end_date?->format('Y-m-d'),
            'opening_balance' => (float) ($reconciliation->opening_balance ?? 0),
            'closing_balance_per_bank' => (float) ($reconciliation->closing_balance_per_bank ?? 0),
            'closing_balance_per_books' => (float) ($reconciliation->closing_balance_per_books ?? 0),
            'difference' => (float) ($reconciliation->difference ?? 0),
            'status' => $reconciliation->status,
            'status_color' => $reconciliation->status_color,
            'total_transactions' => (int) ($reconciliation->total_transactions ?? 0),
            'reconciled_transactions' => (int) ($reconciliation->reconciled_transactions ?? 0),
            'unreconciled_transactions' => (int) ($reconciliation->unreconciled_transactions ?? 0),
            'bank_charges' => (float) ($reconciliation->bank_charges ?? 0),
            'interest_earned' => (float) ($reconciliation->interest_earned ?? 0),
            'other_adjustments' => (float) ($reconciliation->other_adjustments ?? 0),
            'notes' => $reconciliation->notes,
            'created_by' => $reconciliation->creator ? [
                'id' => $reconciliation->creator->id,
                'name' => $reconciliation->creator->name,
            ] : null,
            'completed_by' => $reconciliation->completedBy ? [
                'id' => $reconciliation->completedBy->id,
                'name' => $reconciliation->completedBy->name,
            ] : null,
            'completed_at' => $reconciliation->completed_at?->toDateTimeString(),
            'created_at' => $reconciliation->created_at?->toDateTimeString(),
            'updated_at' => $reconciliation->updated_at?->toDateTimeString(),
        ];
    }

    private function formatReconciliationItem(BankReconciliationItem $item): array
    {
        return [
            'id' => $item->id,
            'bank_reconciliation_id' => $item->bank_reconciliation_id,
            'voucher_entry_id' => $item->voucher_entry_id,
            'transaction_date' => $item->transaction_date?->format('Y-m-d'),
            'transaction_type' => $item->transaction_type,
            'reference_number' => $item->reference_number,
            'description' => $item->description,
            'debit_amount' => (float) ($item->debit_amount ?? 0),
            'credit_amount' => (float) ($item->credit_amount ?? 0),
            'status' => $item->status,
            'cleared_date' => $item->cleared_date?->format('Y-m-d'),
            'bank_reference' => $item->bank_reference,
            'notes' => $item->notes,
        ];
    }
}
