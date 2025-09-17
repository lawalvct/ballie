<?php

namespace App\Http\Controllers\Tenant\Inventory;

use App\Http\Controllers\Controller;
use App\Models\PhysicalStockVoucher;
use App\Models\PhysicalStockEntry;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PhysicalStockController extends Controller
{
    /**
     * Display a listing of physical stock vouchers.
     */
    public function index(Tenant $tenant, Request $request)
    {
        $query = PhysicalStockVoucher::where('tenant_id', $tenant->id)
            ->with(['creator', 'approver', 'entries'])
            ->orderBy('voucher_date', 'desc')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->where('voucher_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('voucher_date', '<=', $request->to_date);
        }

        if ($request->filled('adjustment_type')) {
            $query->where('adjustment_type', $request->adjustment_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('voucher_number', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('remarks', 'like', "%{$search}%");
            });
        }

        $vouchers = $query->paginate(15)->withQueryString();

        // Get summary statistics
        $stats = [
            'total_vouchers' => PhysicalStockVoucher::where('tenant_id', $tenant->id)->count(),
            'pending_approval' => PhysicalStockVoucher::where('tenant_id', $tenant->id)
                ->where('status', PhysicalStockVoucher::STATUS_PENDING)->count(),
            'approved_this_month' => PhysicalStockVoucher::where('tenant_id', $tenant->id)
                ->where('status', PhysicalStockVoucher::STATUS_APPROVED)
                ->whereMonth('voucher_date', now()->month)
                ->whereYear('voucher_date', now()->year)
                ->count(),
            'total_adjustments_this_month' => PhysicalStockVoucher::where('tenant_id', $tenant->id)
                ->where('status', PhysicalStockVoucher::STATUS_APPROVED)
                ->whereMonth('voucher_date', now()->month)
                ->whereYear('voucher_date', now()->year)
                ->sum('total_adjustments'),
        ];

        return view('tenant.inventory.physical-stock.index', compact('tenant', 'vouchers', 'stats'));
    }

    /**
     * Show the form for creating a new physical stock voucher.
     */
    public function create(Tenant $tenant)
    {
        $products = Product::where('tenant_id', $tenant->id)
            ->where('maintain_stock', true)
            ->where('is_active', true)
            ->with(['category', 'primaryUnit'])
            ->orderBy('name')
            ->get();

        return view('tenant.inventory.physical-stock.create', compact('tenant', 'products'));
    }

    /**
     * Store a newly created physical stock voucher.
     */
    public function store(Tenant $tenant, Request $request)
    {
        // Debug: Log the incoming request data
        Log::info('Physical Stock Store Request Data:', [
            'request_all' => $request->all(),
            'entries' => $request->input('entries'),
            'voucher_date' => $request->input('voucher_date'),
            'tenant_id' => $tenant->id
        ]);

        $validator = Validator::make($request->all(), [
            'voucher_date' => 'required|date|before_or_equal:today',
            'reference_number' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:1000',
            'entries' => 'required|array|min:1',
            'entries.*.product_id' => 'required|exists:products,id',
            'entries.*.physical_quantity' => 'required|numeric|min:0',
            'entries.*.batch_number' => 'nullable|string|max:255',
            'entries.*.expiry_date' => 'nullable|date|after:today',
            'entries.*.location' => 'nullable|string|max:255',
            'entries.*.remarks' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            Log::error('Physical Stock Validation Failed:', [
                'errors' => $validator->errors()->toArray(),
                'input' => $request->all()
            ]);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            Log::info('Physical Stock: Starting voucher creation', [
                'tenant_id' => $tenant->id,
                'entries_count' => count($request->entries)
            ]);

            // Create voucher
            $voucher = PhysicalStockVoucher::create([
                'tenant_id' => $tenant->id,
                'voucher_number' => PhysicalStockVoucher::generateVoucherNumber($tenant->id, $request->voucher_date),
                'voucher_date' => $request->voucher_date,
                'reference_number' => $request->reference_number,
                'remarks' => $request->remarks,
                'status' => PhysicalStockVoucher::STATUS_DRAFT,
                'created_by' => auth()->id(),
            ]);

            Log::info('Physical Stock: Voucher created', [
                'voucher_id' => $voucher->id,
                'voucher_number' => $voucher->voucher_number
            ]);

            // Create entries
            foreach ($request->entries as $index => $entryData) {
                Log::info("Physical Stock: Processing entry {$index}", [
                    'product_id' => $entryData['product_id'],
                    'physical_quantity' => $entryData['physical_quantity']
                ]);

                $product = Product::find($entryData['product_id']);
                if (!$product) {
                    throw new \Exception("Product not found: " . $entryData['product_id']);
                }

                // Get book quantity as of voucher date
                $bookQuantity = $product->getStockAsOfDate($request->voucher_date);

                // Get current rate
                $stockValue = $product->getStockValueAsOfDate($request->voucher_date);
                $currentRate = $stockValue['average_rate'] ?? $product->purchase_rate ?? 0;

                $entry = PhysicalStockEntry::create([
                    'physical_stock_voucher_id' => $voucher->id,
                    'product_id' => $entryData['product_id'],
                    'book_quantity' => $bookQuantity,
                    'physical_quantity' => $entryData['physical_quantity'],
                    'current_rate' => $currentRate,
                    'batch_number' => $entryData['batch_number'] ?? null,
                    'expiry_date' => $entryData['expiry_date'] ?? null,
                    'location' => $entryData['location'] ?? null,
                    'remarks' => $entryData['remarks'] ?? null,
                    'created_by' => auth()->id(),
                ]);

                Log::info("Physical Stock: Entry created", [
                    'entry_id' => $entry->id,
                    'book_quantity' => $bookQuantity,
                    'current_rate' => $currentRate
                ]);
            }

            // Update voucher totals and type
            $voucher->calculateTotalItems();
            $voucher->calculateTotalAdjustments();
            $voucher->determineAdjustmentType();

            DB::commit();

            Log::info('Physical Stock: Transaction committed successfully', [
                'voucher_id' => $voucher->id
            ]);

            return redirect()->route('tenant.inventory.physical-stock.show', [
                'tenant' => $tenant->slug,
                'voucher' => $voucher->id
            ])->with('success', 'Physical stock voucher created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Physical Stock: Creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);
            return redirect()->back()
                ->with('error', 'Error creating voucher: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified physical stock voucher.
     */
    public function show(Tenant $tenant, PhysicalStockVoucher $voucher)
    {
        $voucher->load([
            'entries.product.category',
            'entries.product.primaryUnit',
            'creator',
            'updater',
            'approver'
        ]);

        return view('tenant.inventory.physical-stock.show', compact('tenant', 'voucher'));
    }

    /**
     * Show the form for editing the specified physical stock voucher.
     */
    public function edit(Tenant $tenant, PhysicalStockVoucher $voucher)
    {
        if (!$voucher->canEdit()) {
            return redirect()->route('tenant.inventory.physical-stock.show', [
                'tenant' => $tenant->slug,
                'voucher' => $voucher->id
            ])->with('error', 'This voucher cannot be edited.');
        }

        $voucher->load(['entries.product']);

        $products = Product::where('tenant_id', $tenant->id)
            ->where('maintain_stock', true)
            ->where('is_active', true)
            ->with(['category', 'primaryUnit'])
            ->orderBy('name')
            ->get();

        return view('tenant.inventory.physical-stock.edit', compact('tenant', 'voucher', 'products'));
    }

    /**
     * Update the specified physical stock voucher.
     */
    public function update(Tenant $tenant, PhysicalStockVoucher $voucher, Request $request)
    {
        if (!$voucher->canEdit()) {
            return redirect()->route('tenant.inventory.physical-stock.show', [
                'tenant' => $tenant->slug,
                'voucher' => $voucher->id
            ])->with('error', 'This voucher cannot be edited.');
        }

        $validator = Validator::make($request->all(), [
            'voucher_date' => 'required|date|before_or_equal:today',
            'reference_number' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:1000',
            'entries' => 'required|array|min:1',
            'entries.*.product_id' => 'required|exists:products,id',
            'entries.*.physical_quantity' => 'required|numeric|min:0',
            'entries.*.batch_number' => 'nullable|string|max:255',
            'entries.*.expiry_date' => 'nullable|date|after:today',
            'entries.*.location' => 'nullable|string|max:255',
            'entries.*.remarks' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Update voucher
            $voucher->update([
                'voucher_date' => $request->voucher_date,
                'reference_number' => $request->reference_number,
                'remarks' => $request->remarks,
                'updated_by' => auth()->id(),
            ]);

            // Delete existing entries
            $voucher->entries()->delete();

            // Create new entries
            foreach ($request->entries as $entryData) {
                $product = Product::find($entryData['product_id']);

                // Get book quantity as of voucher date
                $bookQuantity = $product->getStockAsOfDate($request->voucher_date);

                // Get current rate
                $stockValue = $product->getStockValueAsOfDate($request->voucher_date);
                $currentRate = $stockValue['average_rate'] ?? $product->purchase_rate ?? 0;

                $entry = PhysicalStockEntry::create([
                    'physical_stock_voucher_id' => $voucher->id,
                    'product_id' => $entryData['product_id'],
                    'book_quantity' => $bookQuantity,
                    'physical_quantity' => $entryData['physical_quantity'],
                    'current_rate' => $currentRate,
                    'batch_number' => $entryData['batch_number'] ?? null,
                    'expiry_date' => $entryData['expiry_date'] ?? null,
                    'location' => $entryData['location'] ?? null,
                    'remarks' => $entryData['remarks'] ?? null,
                    'created_by' => auth()->id(),
                ]);
            }

            // Update voucher totals and type
            $voucher->calculateTotalItems();
            $voucher->calculateTotalAdjustments();
            $voucher->determineAdjustmentType();

            DB::commit();

            return redirect()->route('tenant.inventory.physical-stock.show', [
                'tenant' => $tenant->slug,
                'voucher' => $voucher->id
            ])->with('success', 'Physical stock voucher updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error updating voucher: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Submit voucher for approval.
     */
    public function submit(Tenant $tenant, PhysicalStockVoucher $voucher)
    {
        if ($voucher->status !== PhysicalStockVoucher::STATUS_DRAFT) {
            return redirect()->back()->with('error', 'Only draft vouchers can be submitted for approval.');
        }

        if ($voucher->entries->count() === 0) {
            return redirect()->back()->with('error', 'Cannot submit voucher without entries.');
        }

        $voucher->update([
            'status' => PhysicalStockVoucher::STATUS_PENDING,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Voucher submitted for approval.');
    }

    /**
     * Approve the voucher and create stock movements.
     */
    public function approve(Tenant $tenant, PhysicalStockVoucher $voucher)
    {
        if (!$voucher->canApprove()) {
            return redirect()->back()->with('error', 'This voucher cannot be approved.');
        }

        try {
            DB::beginTransaction();

            $voucher->approve(auth()->id());

            DB::commit();

            return redirect()->back()->with('success', 'Voucher approved and stock movements created.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error approving voucher: ' . $e->getMessage());
        }
    }

    /**
     * Cancel the voucher.
     */
    public function cancel(Tenant $tenant, PhysicalStockVoucher $voucher)
    {
        try {
            $voucher->cancel();
            return redirect()->back()->with('success', 'Voucher cancelled successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error cancelling voucher: ' . $e->getMessage());
        }
    }

    /**
     * Delete the voucher.
     */
    public function destroy(Tenant $tenant, PhysicalStockVoucher $voucher)
    {
        if ($voucher->status === PhysicalStockVoucher::STATUS_APPROVED) {
            return redirect()->back()->with('error', 'Cannot delete an approved voucher.');
        }

        try {
            DB::beginTransaction();

            $voucher->entries()->delete();
            $voucher->delete();

            DB::commit();

            return redirect()->route('tenant.inventory.physical-stock.index', [
                'tenant' => $tenant->slug
            ])->with('success', 'Voucher deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error deleting voucher: ' . $e->getMessage());
        }
    }

    /**
     * Get stock quantity for a product as of a specific date (AJAX).
     */
    public function getProductStock(Tenant $tenant, Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'as_of_date' => 'required|date',
        ]);

        $product = Product::where('tenant_id', $tenant->id)->findOrFail($request->product_id);
        $stockQuantity = $product->getStockAsOfDate($request->as_of_date);
        $stockValue = $product->getStockValueAsOfDate($request->as_of_date);

        return response()->json([
            'stock_quantity' => $stockQuantity,
            'average_rate' => $stockValue['average_rate'] ?? 0,
            'stock_value' => $stockValue['value'] ?? 0,
        ]);
    }

    /**
     * Get products with stock for autocomplete (AJAX).
     */
    public function getProductsWithStock(Tenant $tenant, Request $request)
    {
        $search = $request->get('search', '');
        $asOfDate = $request->get('as_of_date', now()->toDateString());

        $products = Product::where('tenant_id', $tenant->id)
            ->where('maintain_stock', true)
            ->where('is_active', true)
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->with(['category', 'primaryUnit'])
            ->orderBy('name')
            ->limit(20)
            ->get()
            ->map(function ($product) use ($asOfDate) {
                $stockQuantity = $product->getStockAsOfDate($asOfDate);
                $stockValue = $product->getStockValueAsOfDate($asOfDate);

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'category' => $product->category->name ?? 'Uncategorized',
                    'unit' => $product->primaryUnit->name ?? 'Unit',
                    'current_stock' => $stockQuantity,
                    'average_rate' => $stockValue['average_rate'] ?? 0,
                ];
            });

        return response()->json($products);
    }
}
