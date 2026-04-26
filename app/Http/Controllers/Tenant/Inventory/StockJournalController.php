<?php

namespace App\Http\Controllers\Tenant\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockJournalEntry;
use App\Models\StockJournalEntryItem;
use App\Models\Product;
use App\Models\Employee;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class StockJournalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Tenant $tenant)
    {
        $query = StockJournalEntry::where('tenant_id', $tenant->id)
            ->with(['creator', 'operator', 'assistantOperator', 'items.product.primaryUnit']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('journal_number', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('narration', 'like', "%{$search}%")
                  ->orWhere('production_batch_number', 'like', "%{$search}%")
                  ->orWhere('work_order_number', 'like', "%{$search}%")
                  ->orWhereHas('operator', function ($employeeQuery) use ($search) {
                      $employeeQuery->where('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%")
                          ->orWhere('employee_number', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by entry type
        if ($request->filled('entry_type')) {
            $query->where('entry_type', $request->entry_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('journal_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('journal_date', '<=', $request->date_to);
        }

        // Sort by journal date (latest first)
        $journalEntries = $query->orderBy('journal_date', 'desc')
                               ->orderBy('created_at', 'desc')
                               ->paginate(15)
                               ->withQueryString();

        // Calculate statistics
        $stats = [
            'total_entries' => StockJournalEntry::where('tenant_id', $tenant->id)->count(),
            'draft_entries' => StockJournalEntry::where('tenant_id', $tenant->id)->where('status', 'draft')->count(),
            'posted_entries' => StockJournalEntry::where('tenant_id', $tenant->id)->where('status', 'posted')->count(),
            'this_month_entries' => StockJournalEntry::where('tenant_id', $tenant->id)
                ->whereBetween('journal_date', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            'production_runs_this_month' => StockJournalEntry::where('tenant_id', $tenant->id)
                ->where('entry_type', 'production')
                ->whereBetween('journal_date', [now()->startOfMonth(), now()->endOfMonth()])->count(),
        ];

        return view('tenant.inventory.stock-journal.index', compact(
            'journalEntries',
            'tenant',
            'stats'
        ));
    }

    /**
     * Display a dedicated production history report with filters.
     */
    public function productionHistory(Request $request, Tenant $tenant)
    {
        $fromDate = $request->date_from ?: now()->startOfMonth()->toDateString();
        $toDate = $request->date_to ?: now()->endOfMonth()->toDateString();

        $query = StockJournalEntry::where('tenant_id', $tenant->id)
            ->where('entry_type', 'production')
            ->whereBetween('journal_date', [$fromDate, $toDate])
            ->with(['operator', 'assistantOperator', 'items.product.primaryUnit']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('operator_id')) {
            $query->where('operator_id', $request->operator_id);
        }

        if ($request->filled('product_id')) {
            $productId = $request->product_id;
            $query->whereHas('items', function ($q) use ($productId) {
                $q->where('product_id', $productId)->where('movement_type', 'in');
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('journal_number', 'like', "%{$search}%")
                  ->orWhere('production_batch_number', 'like', "%{$search}%")
                  ->orWhere('work_order_number', 'like', "%{$search}%");
            });
        }

        $productionHistory = $query->orderBy('journal_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Aggregates only against posted entries within the same filters (excluding pagination).
        $aggregateQuery = StockJournalEntry::where('tenant_id', $tenant->id)
            ->where('entry_type', 'production')
            ->where('status', 'posted')
            ->whereBetween('journal_date', [$fromDate, $toDate]);

        if ($request->filled('operator_id')) {
            $aggregateQuery->where('operator_id', $request->operator_id);
        }

        if ($request->filled('product_id')) {
            $productId = $request->product_id;
            $aggregateQuery->whereHas('items', function ($q) use ($productId) {
                $q->where('product_id', $productId)->where('movement_type', 'in');
            });
        }

        $postedProductionIds = $aggregateQuery->pluck('id');

        $productionOutputItems = StockJournalEntryItem::with(['product.primaryUnit'])
            ->whereIn('stock_journal_entry_id', $postedProductionIds)
            ->where('movement_type', 'in')
            ->get();

        // Wastage is captured on the OUT (consumption) side of production entries.
        $productionConsumptionItems = StockJournalEntryItem::with(['product.primaryUnit'])
            ->whereIn('stock_journal_entry_id', $postedProductionIds)
            ->where('movement_type', 'out')
            ->get();

        $consumptionWasteByUnit = $productionConsumptionItems
            ->groupBy(function ($item) {
                return $item->unit_snapshot ?: ($item->product->primaryUnit->symbol ?? $item->product->primaryUnit->name ?? 'Unit');
            })
            ->map(fn ($items) => $items->sum('waste_quantity'));

        $productionUnitTotals = $productionOutputItems
            ->groupBy(function ($item) {
                return $item->unit_snapshot ?: ($item->product->primaryUnit->symbol ?? $item->product->primaryUnit->name ?? 'Unit');
            })
            ->map(function ($items, $unit) use ($consumptionWasteByUnit) {
                return [
                    'unit' => $unit,
                    'quantity' => $items->sum('quantity'),
                    'rejected_quantity' => $items->sum('rejected_quantity'),
                    'waste_quantity' => (float) ($consumptionWasteByUnit[$unit] ?? 0),
                    'amount' => $items->sum('amount'),
                ];
            })
            ->values();

        // Include any wastage units that have no matching production output (still useful to display).
        foreach ($consumptionWasteByUnit as $unit => $waste) {
            if ($waste <= 0) {
                continue;
            }
            $exists = $productionUnitTotals->firstWhere('unit', $unit);
            if (!$exists) {
                $productionUnitTotals->push([
                    'unit' => $unit,
                    'quantity' => 0,
                    'rejected_quantity' => 0,
                    'waste_quantity' => (float) $waste,
                    'amount' => 0,
                ]);
            }
        }

        $productionProductTotals = $productionOutputItems
            ->groupBy('product_id')
            ->map(function ($items) {
                $first = $items->first();
                return [
                    'product' => $first?->product,
                    'quantity' => $items->sum('quantity'),
                    'unit' => $first?->unit_snapshot ?: ($first?->product?->primaryUnit?->symbol ?? $first?->product?->primaryUnit?->name ?? ''),
                    'rejected_quantity' => $items->sum('rejected_quantity'),
                    'waste_quantity' => $items->sum('waste_quantity'),
                    'amount' => $items->sum('amount'),
                ];
            })
            ->sortByDesc('quantity')
            ->values();

        $operators = $this->productionEmployees($tenant);

        $products = Product::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'sku']);

        return view('tenant.inventory.stock-journal.production-history', [
            'tenant' => $tenant,
            'productionHistory' => $productionHistory,
            'productionUnitTotals' => $productionUnitTotals,
            'productionProductTotals' => $productionProductTotals,
            'operators' => $operators,
            'products' => $products,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'filters' => $request->only(['date_from', 'date_to', 'status', 'operator_id', 'product_id', 'search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, Tenant $tenant, $type = null)
    {
        $entryType = $type ?? $request->get('type', 'consumption');

        // Validate entry type
        if (!in_array($entryType, ['consumption', 'production', 'adjustment', 'transfer'])) {
            $entryType = 'consumption';
        }

        // Get products that maintain stock
        $products = Product::where('tenant_id', $tenant->id)
            ->where('maintain_stock', true)
            ->where('is_active', true)
            ->with(['category', 'primaryUnit'])
            ->orderBy('name')
            ->get();

        $employees = $this->productionEmployees($tenant);

        return view('tenant.inventory.stock-journal.create', compact(
            'tenant',
            'entryType',
            'products',
            'employees'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Tenant $tenant)
    {
        $request->validate([
            'journal_date' => 'required|date',
            'entry_type' => 'required|in:consumption,production,adjustment,transfer',
            'reference_number' => 'nullable|string|max:100',
            'narration' => 'nullable|string|max:500',
            'operator_id' => ['nullable', 'required_if:entry_type,production', Rule::exists('employees', 'id')->where('tenant_id', $tenant->id)],
            'assistant_operator_id' => ['nullable', 'different:operator_id', Rule::exists('employees', 'id')->where('tenant_id', $tenant->id)],
            'production_batch_number' => 'nullable|string|max:100',
            'work_order_number' => 'nullable|string|max:100',
            'production_shift' => 'nullable|string|max:50',
            'machine_name' => 'nullable|string|max:150',
            'production_started_at' => 'nullable|date_format:H:i',
            'production_ended_at' => 'nullable|date_format:H:i',
            'production_notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => ['required', Rule::exists('products', 'id')->where('tenant_id', $tenant->id)],
            'items.*.movement_type' => 'required|in:in,out',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.rejected_quantity' => 'nullable|numeric|min:0',
            'items.*.waste_quantity' => 'nullable|numeric|min:0',
            'items.*.batch_number' => 'nullable|string|max:50',
            'items.*.expiry_date' => 'nullable|date',
            'items.*.remarks' => 'nullable|string|max:200',
        ]);

        DB::beginTransaction();
        try {
            $journalEntry = StockJournalEntry::create([
                'tenant_id' => $tenant->id,
                'journal_date' => $request->journal_date,
                'entry_type' => $request->entry_type,
                'reference_number' => $request->reference_number,
                'narration' => $request->narration,
                ...$this->productionHeaderData($request),
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            foreach ($request->items as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                $stockBefore = $product->getStockAsOfDate($request->journal_date);

                StockJournalEntryItem::create([
                    'stock_journal_entry_id' => $journalEntry->id,
                    'product_id' => $itemData['product_id'],
                    'movement_type' => $itemData['movement_type'],
                    'quantity' => $itemData['quantity'],
                    'unit_snapshot' => $product->primaryUnit->symbol ?? $product->primaryUnit->name ?? null,
                    'rejected_quantity' => $itemData['rejected_quantity'] ?? 0,
                    'waste_quantity' => $itemData['waste_quantity'] ?? 0,
                    'rate' => $itemData['rate'],
                    'stock_before' => $stockBefore,
                    'batch_number' => $itemData['batch_number'] ?? null,
                    'expiry_date' => $itemData['expiry_date'] ?? null,
                    'remarks' => $itemData['remarks'] ?? null,
                ]);
            }

            if ($request->action === 'save_and_post') {
                $journalEntry->post(Auth::id());
                $message = 'Stock journal entry created and posted successfully.';
            } else {
                $message = 'Stock journal entry created successfully.';
            }

            DB::commit();

            return redirect()
                ->route('tenant.inventory.stock-journal.show', ['tenant' => $tenant->slug, 'stockJournal' => $journalEntry->id])
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Error creating stock journal entry: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Tenant $tenant, StockJournalEntry $stockJournal)
    {
        $stockJournal->load(['creator', 'poster', 'operator', 'assistantOperator', 'items.product.category', 'items.product.primaryUnit', 'stockMovements.product.primaryUnit']);

        return view('tenant.inventory.stock-journal.show', compact('tenant', 'stockJournal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tenant $tenant, StockJournalEntry $stockJournal)
    {
        // Only allow editing of draft entries
        if ($stockJournal->status !== 'draft') {
            return redirect()
                ->route('tenant.inventory.stock-journal.show', ['tenant' => $tenant->slug, 'stockJournal' => $stockJournal->id])
                ->with('error', 'Only draft entries can be edited.');
        }

        $stockJournal->load(['items.product.category', 'items.product.primaryUnit']);

        // Get products that maintain stock
        $products = Product::where('tenant_id', $tenant->id)
            ->where('maintain_stock', true)
            ->where('is_active', true)
            ->with(['category', 'primaryUnit'])
            ->orderBy('name')
            ->get();

        $employees = $this->productionEmployees($tenant);
        $entryType = $stockJournal->entry_type;

        return view('tenant.inventory.stock-journal.create', compact(
            'tenant',
            'stockJournal',
            'products',
            'employees',
            'entryType'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tenant $tenant, StockJournalEntry $stockJournal)
    {
        // Only allow updating of draft entries
        if ($stockJournal->status !== 'draft') {
            return redirect()
                ->route('tenant.inventory.stock-journal.show', ['tenant' => $tenant->slug, 'stockJournal' => $stockJournal->id])
                ->with('error', 'Only draft entries can be updated.');
        }

        $request->validate([
            'journal_date' => 'required|date',
            'entry_type' => 'required|in:consumption,production,adjustment,transfer',
            'reference_number' => 'nullable|string|max:100',
            'narration' => 'nullable|string|max:500',
            'operator_id' => ['nullable', 'required_if:entry_type,production', Rule::exists('employees', 'id')->where('tenant_id', $tenant->id)],
            'assistant_operator_id' => ['nullable', 'different:operator_id', Rule::exists('employees', 'id')->where('tenant_id', $tenant->id)],
            'production_batch_number' => 'nullable|string|max:100',
            'work_order_number' => 'nullable|string|max:100',
            'production_shift' => 'nullable|string|max:50',
            'machine_name' => 'nullable|string|max:150',
            'production_started_at' => 'nullable|date_format:H:i',
            'production_ended_at' => 'nullable|date_format:H:i',
            'production_notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => ['required', Rule::exists('products', 'id')->where('tenant_id', $tenant->id)],
            'items.*.movement_type' => 'required|in:in,out',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.rejected_quantity' => 'nullable|numeric|min:0',
            'items.*.waste_quantity' => 'nullable|numeric|min:0',
            'items.*.batch_number' => 'nullable|string|max:50',
            'items.*.expiry_date' => 'nullable|date',
            'items.*.remarks' => 'nullable|string|max:200',
        ]);

        DB::beginTransaction();
        try {
            // Update the journal entry
            $stockJournal->update([
                'journal_date' => $request->journal_date,
                'entry_type' => $request->entry_type,
                'reference_number' => $request->reference_number,
                'narration' => $request->narration,
                ...$this->productionHeaderData($request),
                'updated_by' => Auth::id(),
            ]);

            // Delete existing items
            $stockJournal->items()->delete();

            // Create new journal entry items
            foreach ($request->items as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);

                // Get current stock using date-based calculation
                $stockBefore = $product->getStockAsOfDate($request->journal_date);

                StockJournalEntryItem::create([
                    'stock_journal_entry_id' => $stockJournal->id,
                    'product_id' => $itemData['product_id'],
                    'movement_type' => $itemData['movement_type'],
                    'quantity' => $itemData['quantity'],
                    'unit_snapshot' => $product->primaryUnit->symbol ?? $product->primaryUnit->name ?? null,
                    'rejected_quantity' => $itemData['rejected_quantity'] ?? 0,
                    'waste_quantity' => $itemData['waste_quantity'] ?? 0,
                    'rate' => $itemData['rate'],
                    'stock_before' => $stockBefore,
                    'batch_number' => $itemData['batch_number'] ?? null,
                    'expiry_date' => $itemData['expiry_date'] ?? null,
                    'remarks' => $itemData['remarks'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('tenant.inventory.stock-journal.show', ['tenant' => $tenant->slug, 'stockJournal' => $stockJournal->id])
                ->with('success', 'Stock journal entry updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Error updating stock journal entry: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tenant $tenant, StockJournalEntry $stockJournal)
    {
        // Only allow deletion of draft entries
        if ($stockJournal->status !== 'draft') {
            return back()->with('error', 'Only draft entries can be deleted.');
        }

        try {
            $stockJournal->delete();
            return redirect()
                ->route('tenant.inventory.stock-journal.index', ['tenant' => $tenant->slug])
                ->with('success', 'Stock journal entry deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting stock journal entry: ' . $e->getMessage());
        }
    }

    /**
     * Post a journal entry
     */
    public function post(Tenant $tenant, StockJournalEntry $stockJournal)
    {
        if (!$stockJournal->can_post) {
            return back()->with('error', 'This journal entry cannot be posted.');
        }

        try {
            $stockJournal->post(Auth::id());
            return back()->with('success', 'Stock journal entry posted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error posting journal entry: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a journal entry
     */
    public function cancel(Tenant $tenant, StockJournalEntry $stockJournal)
    {
        if (!$stockJournal->can_cancel) {
            return back()->with('error', 'This journal entry cannot be cancelled.');
        }

        try {
            $stockJournal->cancel();
            return back()->with('success', 'Stock journal entry cancelled successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error cancelling journal entry: ' . $e->getMessage());
        }
    }

    /**
     * Duplicate a journal entry
     */
    public function duplicate(Tenant $tenant, StockJournalEntry $stockJournal)
    {
        $stockJournal->load(['items.product.primaryUnit']);

        return view('tenant.inventory.stock-journal.create', [
            'tenant' => $tenant,
            'entryType' => $stockJournal->entry_type,
            'products' => Product::where('tenant_id', $tenant->id)
                ->where('maintain_stock', true)
                ->where('is_active', true)
                ->with(['category', 'primaryUnit'])
                ->orderBy('name')
                ->get(),
            'employees' => $this->productionEmployees($tenant),
            'duplicateFrom' => $stockJournal,
        ]);
    }

    /**
     * Get product stock via AJAX
     */
    public function getProductStock(Tenant $tenant, Product $product)
    {
        return response()->json([
            'current_stock' => $product->getStockAsOfDate(now()),
            'unit' => $product->primaryUnit->symbol ?? $product->primaryUnit->name ?? '',
            'rate' => $product->purchase_rate ?? 0,
        ]);
    }

    /**
     * Calculate stock after movement via AJAX
     */
    public function calculateStock(Request $request, Tenant $tenant)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'movement_type' => 'required|in:in,out',
            'quantity' => 'required|numeric|min:0',
        ]);

        $product = Product::findOrFail($request->product_id);
        $currentStock = $product->getStockAsOfDate(now());

        if ($request->movement_type === 'in') {
            $newStock = $currentStock + $request->quantity;
        } else {
            $newStock = $currentStock - $request->quantity;
        }

        return response()->json([
            'current_stock' => $currentStock,
            'new_stock' => max(0, $newStock), // Don't allow negative stock in display
            'unit' => $product->primaryUnit->symbol ?? $product->primaryUnit->name ?? '',
        ]);
    }

    /**
     * Print journal entry
     */
    public function print(Tenant $tenant, StockJournalEntry $stockJournal)
    {
        $stockJournal->load(['creator', 'poster', 'operator', 'assistantOperator', 'items.product.category', 'items.product.primaryUnit']);

        return view('tenant.inventory.stock-journal.print', compact('tenant', 'stockJournal'));
    }

    /**
     * Download journal entry as PDF (presentation-ready, with company info).
     */
    public function pdf(Tenant $tenant, StockJournalEntry $stockJournal)
    {
        $stockJournal->load(['creator', 'poster', 'operator', 'assistantOperator', 'items.product.category', 'items.product.primaryUnit']);

        $isProduction = $stockJournal->entry_type === 'production';
        $documentLabel = $isProduction ? 'Production Report' : 'Stock Journal';
        $filename = strtoupper(str_replace(' ', '-', $documentLabel)) . '-' . $stockJournal->journal_number . '.pdf';

        $pdf = Pdf::loadView('tenant.inventory.stock-journal.pdf', [
            'tenant' => $tenant,
            'stockJournal' => $stockJournal,
            'documentLabel' => $documentLabel,
        ])->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }

    /**
     * Export journal entries
     */
    public function export(Request $request, Tenant $tenant)
    {
        // Implementation for export functionality
        return response()->json(['message' => 'Export functionality to be implemented']);
    }

    /**
     * Bulk operations
     */
    public function bulkPost(Request $request, Tenant $tenant)
    {
        $request->validate([
            'selected_entries' => 'required|array',
            'selected_entries.*' => 'exists:stock_journal_entries,id',
        ]);

        $posted = 0;
        $errors = [];

        foreach ($request->selected_entries as $entryId) {
            $entry = StockJournalEntry::find($entryId);
            if ($entry && $entry->can_post) {
                try {
                    $entry->post(Auth::id());
                    $posted++;
                } catch (\Exception $e) {
                    $errors[] = "Failed to post {$entry->journal_number}: {$e->getMessage()}";
                }
            }
        }

        $message = "Posted {$posted} entries successfully.";
        if (!empty($errors)) {
            $message .= " Errors: " . implode(', ', $errors);
        }

        return back()->with($posted > 0 ? 'success' : 'error', $message);
    }

    public function bulkCancel(Request $request, Tenant $tenant)
    {
        $request->validate([
            'selected_entries' => 'required|array',
            'selected_entries.*' => 'exists:stock_journal_entries,id',
        ]);

        $cancelled = 0;
        $errors = [];

        foreach ($request->selected_entries as $entryId) {
            $entry = StockJournalEntry::find($entryId);
            if ($entry && $entry->can_cancel) {
                try {
                    $entry->cancel();
                    $cancelled++;
                } catch (\Exception $e) {
                    $errors[] = "Failed to cancel {$entry->journal_number}: {$e->getMessage()}";
                }
            }
        }

        $message = "Cancelled {$cancelled} entries successfully.";
        if (!empty($errors)) {
            $message .= " Errors: " . implode(', ', $errors);
        }

        return back()->with($cancelled > 0 ? 'success' : 'error', $message);
    }

    public function bulkDelete(Request $request, Tenant $tenant)
    {
        $request->validate([
            'selected_entries' => 'required|array',
            'selected_entries.*' => 'exists:stock_journal_entries,id',
        ]);

        $deleted = 0;
        $errors = [];

        foreach ($request->selected_entries as $entryId) {
            $entry = StockJournalEntry::find($entryId);
            if ($entry && $entry->status === 'draft') {
                try {
                    $entry->delete();
                    $deleted++;
                } catch (\Exception $e) {
                    $errors[] = "Failed to delete {$entry->journal_number}: {$e->getMessage()}";
                }
            }
        }

        $message = "Deleted {$deleted} entries successfully.";
        if (!empty($errors)) {
            $message .= " Errors: " . implode(', ', $errors);
        }

        return back()->with($deleted > 0 ? 'success' : 'error', $message);
    }

    private function productionEmployees(Tenant $tenant)
    {
        return Employee::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
    }

    private function productionHeaderData(Request $request): array
    {
        if ($request->entry_type !== 'production') {
            return [
                'operator_id' => null,
                'assistant_operator_id' => null,
                'production_batch_number' => null,
                'work_order_number' => null,
                'production_shift' => null,
                'machine_name' => null,
                'production_started_at' => null,
                'production_ended_at' => null,
                'production_notes' => null,
            ];
        }

        return [
            'operator_id' => $request->operator_id,
            'assistant_operator_id' => $request->assistant_operator_id,
            'production_batch_number' => $request->production_batch_number,
            'work_order_number' => $request->work_order_number,
            'production_shift' => $request->production_shift,
            'machine_name' => $request->machine_name,
            'production_started_at' => $request->production_started_at,
            'production_ended_at' => $request->production_ended_at,
            'production_notes' => $request->production_notes,
        ];
    }
}
