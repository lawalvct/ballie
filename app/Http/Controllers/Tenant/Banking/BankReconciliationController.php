<?php

namespace App\Http\Controllers\Tenant\Banking;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\BankReconciliation;
use App\Models\BankReconciliationItem;
use App\Models\Tenant;
use App\Models\VoucherEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BankReconciliationController extends Controller
{
    /**
     * Display a listing of reconciliations
     */
    public function index(Request $request, Tenant $tenant)
    {
        $query = BankReconciliation::where('tenant_id', $tenant->id)
            ->with(['bank', 'creator']);

        // Filter by bank
        if ($request->filled('bank_id')) {
            $query->where('bank_id', $request->bank_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('reconciliation_date', [$request->from_date, $request->to_date]);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'reconciliation_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $reconciliations = $query->paginate(20)->withQueryString();

        // Get banks for filter
        $banks = Bank::where('tenant_id', $tenant->id)
            ->where('enable_reconciliation', true)
            ->orderBy('bank_name')
            ->get();

        // Statistics
        $stats = [
            'total' => BankReconciliation::where('tenant_id', $tenant->id)->count(),
            'completed' => BankReconciliation::where('tenant_id', $tenant->id)->where('status', 'completed')->count(),
            'in_progress' => BankReconciliation::where('tenant_id', $tenant->id)->where('status', 'in_progress')->count(),
            'draft' => BankReconciliation::where('tenant_id', $tenant->id)->where('status', 'draft')->count(),
        ];

        return view('tenant.banking.reconciliations.index', compact('tenant', 'reconciliations', 'banks', 'stats'));
    }

    /**
     * Show the form for creating a new reconciliation
     */
    public function create(Request $request, Tenant $tenant)
    {
        $bankId = $request->get('bank_id');
        $bank = null;

        if ($bankId) {
            $bank = Bank::where('tenant_id', $tenant->id)
                ->where('id', $bankId)
                ->firstOrFail();
        }

        // Get banks that have reconciliation enabled
        $banks = Bank::where('tenant_id', $tenant->id)
            ->where('enable_reconciliation', true)
            ->where('status', 'active')
            ->orderBy('bank_name')
            ->get();

        return view('tenant.banking.reconciliations.create', compact('tenant', 'banks', 'bank'));
    }

    /**
     * Store a newly created reconciliation
     */
    public function store(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
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

        $validated['tenant_id'] = $tenant->id;
        $validated['created_by'] = auth()->id();
        $validated['bank_charges'] = $validated['bank_charges'] ?? 0;
        $validated['interest_earned'] = $validated['interest_earned'] ?? 0;
        $validated['other_adjustments'] = $validated['other_adjustments'] ?? 0;

        // Get bank opening balance
        $bank = Bank::findOrFail($validated['bank_id']);
        $validated['opening_balance'] = $bank->getCurrentBalance();

        // Get closing balance per books (from ledger account)
        $validated['closing_balance_per_books'] = $bank->getCurrentBalance();

        // Calculate difference
        $validated['difference'] = $validated['closing_balance_per_bank'] - $validated['closing_balance_per_books'];

        try {
            DB::beginTransaction();

            $reconciliation = BankReconciliation::create($validated);

            // Load unreconciled transactions
            $this->loadUnreconciledTransactions($reconciliation);

            DB::commit();

            Log::info('Bank reconciliation created', [
                'tenant_id' => $tenant->id,
                'reconciliation_id' => $reconciliation->id,
                'bank_id' => $bank->id,
            ]);

            return redirect()
                ->route('tenant.banking.reconciliations.show', ['tenant' => $tenant->slug, 'reconciliation' => $reconciliation->id])
                ->with('success', 'Bank reconciliation created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create bank reconciliation', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to create reconciliation. ' . $e->getMessage());
        }
    }

    /**
     * Display the specified reconciliation
     */
    public function show(Tenant $tenant, $reconciliationId)
    {
        $reconciliation = BankReconciliation::with(['bank', 'items.voucherEntry.voucher', 'creator', 'completedBy'])
            ->where('tenant_id', $tenant->id)
            ->findOrFail($reconciliationId);

        // Group items by status
        $clearedItems = $reconciliation->clearedItems()->with('voucherEntry.voucher.voucherType')->get();
        $unclearedItems = $reconciliation->unclearedItems()->with('voucherEntry.voucher.voucherType')->get();

        return view('tenant.banking.reconciliations.show', compact('tenant', 'reconciliation', 'clearedItems', 'unclearedItems'));
    }

    /**
     * Update reconciliation item status
     */
    public function updateItemStatus(Request $request, Tenant $tenant, $reconciliationId)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:bank_reconciliation_items,id',
            'status' => 'required|in:cleared,uncleared,excluded',
            'cleared_date' => 'nullable|date',
            'bank_reference' => 'nullable|string',
        ]);

        $reconciliation = BankReconciliation::where('tenant_id', $tenant->id)
            ->findOrFail($reconciliationId);

        if (!$reconciliation->canBeEdited()) {
            return response()->json(['error' => 'Reconciliation cannot be edited'], 403);
        }

        $item = BankReconciliationItem::where('bank_reconciliation_id', $reconciliation->id)
            ->findOrFail($validated['item_id']);

        $item->update([
            'status' => $validated['status'],
            'cleared_date' => $validated['status'] === 'cleared' ? ($validated['cleared_date'] ?? now()) : null,
            'bank_reference' => $validated['bank_reference'] ?? null,
        ]);

        $reconciliation->updateStatistics();

        return response()->json([
            'success' => true,
            'item' => $item,
            'statistics' => [
                'total' => $reconciliation->total_transactions,
                'reconciled' => $reconciliation->reconciled_transactions,
                'unreconciled' => $reconciliation->unreconciled_transactions,
                'progress' => $reconciliation->getProgressPercentage(),
            ],
        ]);
    }

    /**
     * Complete reconciliation
     */
    public function complete(Tenant $tenant, $reconciliationId)
    {
        $reconciliation = BankReconciliation::where('tenant_id', $tenant->id)
            ->findOrFail($reconciliationId);

        if (!$reconciliation->canBeCompleted()) {
            return back()->with('error', 'Reconciliation cannot be completed. Ensure all items are reconciled and balances match.');
        }

        try {
            $reconciliation->markAsCompleted();

            Log::info('Bank reconciliation completed', [
                'tenant_id' => $tenant->id,
                'reconciliation_id' => $reconciliation->id,
            ]);

            return redirect()
                ->route('tenant.banking.reconciliations.show', ['tenant' => $tenant->slug, 'reconciliation' => $reconciliation->id])
                ->with('success', 'Reconciliation completed successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to complete reconciliation', [
                'tenant_id' => $tenant->id,
                'reconciliation_id' => $reconciliation->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cancel reconciliation
     */
    public function cancel(Tenant $tenant, $reconciliationId)
    {
        $reconciliation = BankReconciliation::where('tenant_id', $tenant->id)
            ->findOrFail($reconciliationId);

        try {
            $reconciliation->cancel();

            return redirect()
                ->route('tenant.banking.reconciliations.index', ['tenant' => $tenant->slug])
                ->with('success', 'Reconciliation cancelled successfully!');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete reconciliation
     */
    public function destroy(Tenant $tenant, $reconciliationId)
    {
        $reconciliation = BankReconciliation::where('tenant_id', $tenant->id)
            ->findOrFail($reconciliationId);

        if (!$reconciliation->canBeDeleted()) {
            return back()->with('error', 'Cannot delete a completed reconciliation.');
        }

        try {
            $reconciliation->delete();

            Log::info('Bank reconciliation deleted', [
                'tenant_id' => $tenant->id,
                'reconciliation_id' => $reconciliation->id,
            ]);

            return redirect()
                ->route('tenant.banking.reconciliations.index', ['tenant' => $tenant->slug])
                ->with('success', 'Reconciliation deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to delete reconciliation', [
                'tenant_id' => $tenant->id,
                'reconciliation_id' => $reconciliation->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to delete reconciliation. ' . $e->getMessage());
        }
    }

    /**
     * Load unreconciled transactions for the reconciliation period
     */
    private function loadUnreconciledTransactions($reconciliation)
    {
        $bank = $reconciliation->bank;

        if (!$bank->ledgerAccount) {
            return;
        }

        // Get all transactions in the period
        $transactions = VoucherEntry::where('ledger_account_id', $bank->ledger_account_id)
            ->whereHas('voucher', function($q) use ($reconciliation) {
                $q->where('status', 'posted')
                  ->whereBetween('voucher_date', [
                      $reconciliation->statement_start_date,
                      $reconciliation->statement_end_date
                  ]);
            })
            ->with('voucher.voucherType')
            ->get();

        foreach ($transactions as $entry) {
            BankReconciliationItem::create([
                'bank_reconciliation_id' => $reconciliation->id,
                'voucher_entry_id' => $entry->id,
                'transaction_date' => $entry->voucher->voucher_date,
                'transaction_type' => 'voucher',
                'reference_number' => $entry->voucher->voucher_number,
                'description' => $entry->particulars ?? $entry->voucher->narration,
                'debit_amount' => $entry->debit_amount,
                'credit_amount' => $entry->credit_amount,
                'status' => 'uncleared',
            ]);
        }

        $reconciliation->updateStatistics();
    }
}
