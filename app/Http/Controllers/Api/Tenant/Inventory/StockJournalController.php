<?php

namespace App\Http\Controllers\Api\Tenant\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockJournalEntry;
use App\Models\StockJournalEntryItem;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StockJournalController extends Controller
{
    /**
     * List stock journal entries with filters and stats.
     */
    public function index(Request $request, Tenant $tenant)
    {
        $query = StockJournalEntry::forTenant($tenant->id)
            ->withCount('items')
            ->withSum('items as items_total_amount', 'amount');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('journal_number', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhere('narration', 'like', "%{$search}%");
            });
        }

        if ($request->filled('entry_type')) {
            $query->where('entry_type', $request->get('entry_type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('date_from')) {
            $query->where('journal_date', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->where('journal_date', '<=', $request->get('date_to'));
        }

        $perPage = (int) $request->get('per_page', 15);
        $entries = $query
            ->orderBy('journal_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $entries->getCollection()->transform(function (StockJournalEntry $entry) {
            return $this->formatEntry($entry);
        });

        $stats = [
            'total_entries' => StockJournalEntry::forTenant($tenant->id)->count(),
            'draft_entries' => StockJournalEntry::forTenant($tenant->id)->where('status', 'draft')->count(),
            'posted_entries' => StockJournalEntry::forTenant($tenant->id)->where('status', 'posted')->count(),
            'this_month_entries' => StockJournalEntry::forTenant($tenant->id)
                ->whereBetween('journal_date', [now()->startOfMonth(), now()->endOfMonth()])
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Stock journal entries retrieved successfully',
            'data' => $entries,
            'statistics' => $stats,
        ]);
    }

    /**
     * Get form data for creating a stock journal entry.
     */
    public function create(Request $request, Tenant $tenant)
    {
        $entryType = $request->get('type', 'consumption');
        $allowedTypes = ['consumption', 'production', 'adjustment', 'transfer'];

        if (!in_array($entryType, $allowedTypes, true)) {
            $entryType = 'consumption';
        }

        $products = Product::where('tenant_id', $tenant->id)
            ->where('maintain_stock', true)
            ->where('is_active', true)
            ->with(['category', 'primaryUnit'])
            ->orderBy('name')
            ->get()
            ->map(function (Product $product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'category' => $product->category ? [
                        'id' => $product->category->id,
                        'name' => $product->category->name,
                    ] : null,
                    'unit' => $product->primaryUnit ? [
                        'id' => $product->primaryUnit->id,
                        'name' => $product->primaryUnit->name,
                    ] : null,
                    'current_stock' => $product->getStockAsOfDate(now()),
                    'purchase_rate' => (float) ($product->purchase_rate ?? 0),
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Stock journal form data retrieved successfully',
            'data' => [
                'entry_type' => $entryType,
                'entry_types' => $this->entryTypes(),
                'products' => $products,
            ],
        ]);
    }

    /**
     * Store a new stock journal entry.
     */
    public function store(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), $this->rules());

        $validator->after(function ($validator) use ($request, $tenant) {
            $productIds = collect($request->get('items', []))->pluck('product_id')->filter()->unique()->all();
            if (!empty($productIds)) {
                $invalid = Product::whereIn('id', $productIds)
                    ->where('tenant_id', '!=', $tenant->id)
                    ->exists();
                if ($invalid) {
                    $validator->errors()->add('items', 'One or more products are invalid.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $entry = StockJournalEntry::create([
                'tenant_id' => $tenant->id,
                'journal_date' => $request->get('journal_date'),
                'entry_type' => $request->get('entry_type'),
                'reference_number' => $request->get('reference_number'),
                'narration' => $request->get('narration'),
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            foreach ($request->get('items', []) as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                $stockBefore = $product->getStockAsOfDate(now());

                StockJournalEntryItem::create([
                    'stock_journal_entry_id' => $entry->id,
                    'product_id' => $itemData['product_id'],
                    'movement_type' => $itemData['movement_type'],
                    'quantity' => $itemData['quantity'],
                    'rate' => $itemData['rate'],
                    'stock_before' => $stockBefore,
                    'batch_number' => $itemData['batch_number'] ?? null,
                    'expiry_date' => $itemData['expiry_date'] ?? null,
                    'remarks' => $itemData['remarks'] ?? null,
                ]);
            }

            if ($request->get('action') === 'save_and_post') {
                $entry->post(Auth::id());
            }

            DB::commit();

            $entry->load(['items.product.category', 'items.product.primaryUnit', 'stockMovements']);

            return response()->json([
                'success' => true,
                'message' => $request->get('action') === 'save_and_post'
                    ? 'Stock journal entry created and posted successfully'
                    : 'Stock journal entry created successfully',
                'data' => [
                    'entry' => $this->formatEntry($entry, true),
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error creating stock journal entry',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Show a stock journal entry.
     */
    public function show(Request $request, Tenant $tenant, StockJournalEntry $stockJournal)
    {
        if ($stockJournal->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Stock journal entry not found',
            ], 404);
        }

        $stockJournal->load([
            'creator',
            'poster',
            'items.product.category',
            'items.product.primaryUnit',
            'stockMovements.product.primaryUnit',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Stock journal entry retrieved successfully',
            'data' => [
                'entry' => $this->formatEntry($stockJournal, true),
            ],
        ]);
    }

    /**
     * Update a stock journal entry.
     */
    public function update(Request $request, Tenant $tenant, StockJournalEntry $stockJournal)
    {
        if ($stockJournal->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Stock journal entry not found',
            ], 404);
        }

        if ($stockJournal->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Only draft entries can be updated.',
            ], 422);
        }

        $validator = Validator::make($request->all(), $this->rules());

        $validator->after(function ($validator) use ($request, $tenant) {
            $productIds = collect($request->get('items', []))->pluck('product_id')->filter()->unique()->all();
            if (!empty($productIds)) {
                $invalid = Product::whereIn('id', $productIds)
                    ->where('tenant_id', '!=', $tenant->id)
                    ->exists();
                if ($invalid) {
                    $validator->errors()->add('items', 'One or more products are invalid.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $stockJournal->update([
                'journal_date' => $request->get('journal_date'),
                'entry_type' => $request->get('entry_type'),
                'reference_number' => $request->get('reference_number'),
                'narration' => $request->get('narration'),
                'updated_by' => Auth::id(),
            ]);

            $stockJournal->items()->delete();

            foreach ($request->get('items', []) as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                $stockBefore = $product->getStockAsOfDate(now());

                StockJournalEntryItem::create([
                    'stock_journal_entry_id' => $stockJournal->id,
                    'product_id' => $itemData['product_id'],
                    'movement_type' => $itemData['movement_type'],
                    'quantity' => $itemData['quantity'],
                    'rate' => $itemData['rate'],
                    'stock_before' => $stockBefore,
                    'batch_number' => $itemData['batch_number'] ?? null,
                    'expiry_date' => $itemData['expiry_date'] ?? null,
                    'remarks' => $itemData['remarks'] ?? null,
                ]);
            }

            DB::commit();

            $stockJournal->load(['items.product.category', 'items.product.primaryUnit', 'stockMovements']);

            return response()->json([
                'success' => true,
                'message' => 'Stock journal entry updated successfully',
                'data' => [
                    'entry' => $this->formatEntry($stockJournal, true),
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error updating stock journal entry',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Delete a stock journal entry.
     */
    public function destroy(Request $request, Tenant $tenant, StockJournalEntry $stockJournal)
    {
        if ($stockJournal->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Stock journal entry not found',
            ], 404);
        }

        if ($stockJournal->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Only draft entries can be deleted.',
            ], 422);
        }

        $stockJournal->delete();

        return response()->json([
            'success' => true,
            'message' => 'Stock journal entry deleted successfully',
        ]);
    }

    /**
     * Post a stock journal entry.
     */
    public function post(Request $request, Tenant $tenant, StockJournalEntry $stockJournal)
    {
        if ($stockJournal->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Stock journal entry not found',
            ], 404);
        }

        if (!$stockJournal->can_post) {
            return response()->json([
                'success' => false,
                'message' => 'This journal entry cannot be posted.',
            ], 422);
        }

        $stockJournal->post(Auth::id());
        $stockJournal->load(['items.product.category', 'items.product.primaryUnit', 'stockMovements']);

        return response()->json([
            'success' => true,
            'message' => 'Stock journal entry posted successfully',
            'data' => [
                'entry' => $this->formatEntry($stockJournal, true),
            ],
        ]);
    }

    /**
     * Cancel a stock journal entry.
     */
    public function cancel(Request $request, Tenant $tenant, StockJournalEntry $stockJournal)
    {
        if ($stockJournal->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Stock journal entry not found',
            ], 404);
        }

        if (!$stockJournal->can_cancel) {
            return response()->json([
                'success' => false,
                'message' => 'This journal entry cannot be cancelled.',
            ], 422);
        }

        $stockJournal->cancel();
        $stockJournal->load(['items.product.category', 'items.product.primaryUnit', 'stockMovements']);

        return response()->json([
            'success' => true,
            'message' => 'Stock journal entry cancelled successfully',
            'data' => [
                'entry' => $this->formatEntry($stockJournal, true),
            ],
        ]);
    }

    /**
     * Duplicate a stock journal entry (returns data for prefill).
     */
    public function duplicate(Request $request, Tenant $tenant, StockJournalEntry $stockJournal)
    {
        if ($stockJournal->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Stock journal entry not found',
            ], 404);
        }

        $stockJournal->load(['items.product.category', 'items.product.primaryUnit']);

        return response()->json([
            'success' => true,
            'message' => 'Stock journal entry duplicate data retrieved successfully',
            'data' => [
                'entry' => $this->formatEntry($stockJournal, true),
            ],
        ]);
    }

    /**
     * Get product stock data (AJAX helper).
     */
    public function productStock(Request $request, Tenant $tenant, Product $product)
    {
        if ($product->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product stock retrieved successfully',
            'data' => [
                'current_stock' => $product->getStockAsOfDate(now()),
                'unit' => $product->primaryUnit->name ?? '',
                'rate' => (float) ($product->purchase_rate ?? 0),
            ],
        ]);
    }

    /**
     * Calculate stock after movement (AJAX helper).
     */
    public function calculateStock(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'movement_type' => 'required|in:in,out',
            'quantity' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $product = Product::findOrFail($request->get('product_id'));

        if ($product->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $currentStock = $product->getStockAsOfDate(now());
        $quantity = (float) $request->get('quantity');

        $newStock = $request->get('movement_type') === 'in'
            ? $currentStock + $quantity
            : $currentStock - $quantity;

        return response()->json([
            'success' => true,
            'message' => 'Stock calculated successfully',
            'data' => [
                'current_stock' => $currentStock,
                'new_stock' => max(0, $newStock),
                'unit' => $product->primaryUnit->name ?? '',
            ],
        ]);
    }

    private function rules(): array
    {
        return [
            'journal_date' => 'required|date',
            'entry_type' => 'required|in:consumption,production,adjustment,transfer',
            'reference_number' => 'nullable|string|max:100',
            'narration' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.movement_type' => 'required|in:in,out',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.batch_number' => 'nullable|string|max:50',
            'items.*.expiry_date' => 'nullable|date|after:today',
            'items.*.remarks' => 'nullable|string|max:200',
            'action' => 'nullable|string|in:save,save_and_post',
        ];
    }

    private function entryTypes(): array
    {
        return [
            [
                'key' => 'consumption',
                'label' => 'Material Consumption',
                'movement' => 'out',
            ],
            [
                'key' => 'production',
                'label' => 'Production Receipt',
                'movement' => 'in',
            ],
            [
                'key' => 'adjustment',
                'label' => 'Stock Adjustment',
                'movement' => 'in|out',
            ],
            [
                'key' => 'transfer',
                'label' => 'Stock Transfer',
                'movement' => 'out + in',
            ],
        ];
    }

    private function formatEntry(StockJournalEntry $entry, bool $includeItems = false): array
    {
        $data = [
            'id' => $entry->id,
            'journal_number' => $entry->journal_number,
            'journal_date' => $entry->journal_date?->format('Y-m-d'),
            'reference_number' => $entry->reference_number,
            'narration' => $entry->narration,
            'entry_type' => $entry->entry_type,
            'entry_type_display' => $entry->entry_type_display,
            'status' => $entry->status,
            'status_display' => $entry->status_display,
            'status_color' => $entry->status_color,
            'total_items' => (int) ($entry->items_count ?? $entry->items()->count()),
            'total_amount' => (float) ($entry->items_total_amount ?? $entry->total_amount),
            'can_edit' => $entry->can_edit,
            'can_post' => $entry->can_post,
            'can_cancel' => $entry->can_cancel,
            'created_at' => $entry->created_at?->toDateTimeString(),
            'updated_at' => $entry->updated_at?->toDateTimeString(),
            'posted_at' => $entry->posted_at?->toDateTimeString(),
        ];

        if ($includeItems) {
            $items = $entry->items->map(function (StockJournalEntryItem $item) {
                return $this->formatItem($item);
            })->values();

            $data['items'] = $items;
            $data['stock_movements'] = $entry->stockMovements?->map(function ($movement) {
                return [
                    'id' => $movement->id,
                    'product' => $movement->product ? [
                        'id' => $movement->product->id,
                        'name' => $movement->product->name,
                        'sku' => $movement->product->sku,
                        'unit' => $movement->product->primaryUnit->name ?? '',
                    ] : null,
                    'quantity' => (float) $movement->quantity,
                    'reference' => $movement->reference,
                    'created_at' => $movement->created_at?->toDateTimeString(),
                ];
            })->values() ?? [];
        }

        return $data;
    }

    private function formatItem(StockJournalEntryItem $item): array
    {
        return [
            'id' => $item->id,
            'product' => $item->product ? [
                'id' => $item->product->id,
                'name' => $item->product->name,
                'sku' => $item->product->sku,
                'category' => $item->product->category ? [
                    'id' => $item->product->category->id,
                    'name' => $item->product->category->name,
                ] : null,
                'unit' => $item->product->primaryUnit ? [
                    'id' => $item->product->primaryUnit->id,
                    'name' => $item->product->primaryUnit->name,
                ] : null,
            ] : null,
            'movement_type' => $item->movement_type,
            'movement_type_display' => $item->movement_type_display,
            'movement_type_color' => $item->movement_type_color,
            'quantity' => (float) $item->quantity,
            'rate' => (float) $item->rate,
            'amount' => (float) $item->amount,
            'stock_before' => (float) $item->stock_before,
            'stock_after' => (float) $item->stock_after,
            'batch_number' => $item->batch_number,
            'expiry_date' => $item->expiry_date?->format('Y-m-d'),
            'remarks' => $item->remarks,
        ];
    }
}
