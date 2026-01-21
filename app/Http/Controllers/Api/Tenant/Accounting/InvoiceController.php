<?php

namespace App\Http\Controllers\Api\Tenant\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Voucher;
use App\Models\VoucherType;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\LedgerAccount;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices.
     */
    public function index(Request $request, Tenant $tenant)
    {
        $query = Voucher::where('tenant_id', $tenant->id)
            ->with(['voucherType', 'entries.ledgerAccount']);

        // Filter by voucher type (sales or purchase)
        if ($request->filled('type')) {
            $type = $request->get('type');
            if ($type === 'sales') {
                $query->whereHas('voucherType', function ($q) {
                    $q->where('inventory_effect', 'decrease');
                });
            } elseif ($type === 'purchase') {
                $query->whereHas('voucherType', function ($q) {
                    $q->where('inventory_effect', 'increase');
                });
            }
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('voucher_date', '>=', $request->get('from_date'));
        }
        if ($request->filled('to_date')) {
            $query->whereDate('voucher_date', '<=', $request->get('to_date'));
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('voucher_number', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('narration', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort', 'voucher_date');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $invoices = $query->paginate($perPage);

        // Calculate statistics
        $statistics = [
            'total_invoices' => Voucher::where('tenant_id', $tenant->id)->count(),
            'draft_invoices' => Voucher::where('tenant_id', $tenant->id)->where('status', 'draft')->count(),
            'posted_invoices' => Voucher::where('tenant_id', $tenant->id)->where('status', 'posted')->count(),
            'total_sales_amount' => Voucher::where('tenant_id', $tenant->id)
                ->whereHas('voucherType', fn($q) => $q->where('inventory_effect', 'decrease'))
                ->where('status', 'posted')
                ->sum('total_amount'),
            'total_purchase_amount' => Voucher::where('tenant_id', $tenant->id)
                ->whereHas('voucherType', fn($q) => $q->where('inventory_effect', 'increase'))
                ->where('status', 'posted')
                ->sum('total_amount'),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Invoices retrieved successfully',
            'data' => $invoices,
            'statistics' => $statistics
        ]);
    }

    /**
     * Get data for creating a new invoice.
     */
    public function create(Request $request, Tenant $tenant)
    {
        $type = $request->get('type', 'sales'); // 'sales' or 'purchase'

        // Get appropriate voucher types based on inventory effect
        $voucherTypes = VoucherType::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->where('affects_inventory', true)
            ->where('inventory_effect', $type === 'sales' ? 'decrease' : 'increase')
            ->orderBy('name')
            ->get();

        // Get customers or vendors based on type
        $parties = $type === 'sales'
            ? Customer::where('tenant_id', $tenant->id)->where('status', 'active')->get()
            : Vendor::where('tenant_id', $tenant->id)->where('status', 'active')->get();

        // Get products
        $products = Product::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->with(['salesAccount', 'purchaseAccount', 'unit'])
            ->get();

        // Get ledger accounts for additional charges
        $ledgerAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->whereNotNull('account_group_id')
            ->with('accountGroup')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Create form data retrieved successfully',
            'data' => [
                'voucher_types' => $voucherTypes,
                'parties' => $parties,
                'products' => $products->map(function ($product) use ($type) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'code' => $product->code,
                        'type' => $product->type,
                        'unit' => $product->unit ? $product->unit->name : null,
                        'unit_id' => $product->unit_id,
                        'sales_price' => $product->sales_price,
                        'purchase_price' => $product->purchase_price,
                        'current_stock' => $product->current_stock,
                        'sales_account_id' => $product->sales_account_id,
                        'purchase_account_id' => $product->purchase_account_id,
                        'account_id' => $type === 'sales' ? $product->sales_account_id : $product->purchase_account_id,
                    ];
                }),
                'ledger_accounts' => $ledgerAccounts,
                'type' => $type
            ]
        ]);
    }

    /**
     * Store a newly created invoice.
     */
    public function store(Request $request, Tenant $tenant)
    {
        // Normalize payload - accept both old and new field names
        $partyId = $request->input('party_id') ?? $request->input('customer_id');
        $items = $request->input('items') ?? $request->input('inventory_items');
        $status = $request->input('status') ?? $request->input('action');

        // Prepare normalized data for validation
        $validationData = array_merge($request->all(), [
            'party_id' => $partyId,
            'items' => $items,
            'status' => $status,
        ]);

        $validator = Validator::make($validationData, [
            'voucher_type_id' => 'required|exists:voucher_types,id',
            'voucher_date' => 'required|date',
            'party_id' => 'required|integer',
            'reference_number' => 'nullable|string|max:255',
            'narration' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.vat_rate' => 'nullable|numeric|min:0',
            'items.*.description' => 'nullable|string',
            'additional_ledger_accounts' => 'nullable|array',
            'additional_ledger_accounts.*.ledger_account_id' => 'required|exists:ledger_accounts,id',
            'additional_ledger_accounts.*.amount' => 'required|numeric|min:0',
            'vat_enabled' => 'nullable|boolean',
            'vat_amount' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:draft,posted,save,save_and_post,save_and_post_new_sales'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $voucherType = VoucherType::findOrFail($request->voucher_type_id);

            // Get party ledger account
            $partyLedgerAccount = null;
            if ($voucherType->inventory_effect === 'decrease') {
                // Sales - party is customer
                $customer = Customer::find($partyId);
                if (!$customer) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Customer not found',
                        'errors' => ['party_id' => ['Customer not found']]
                    ], 422);
                }
                $partyLedgerAccount = $customer->ledger_account_id;
            } else {
                // Purchase - party is vendor
                $vendor = Vendor::find($partyId);
                if (!$vendor) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Vendor not found',
                        'errors' => ['party_id' => ['Vendor not found']]
                    ], 422);
                }
                $partyLedgerAccount = $vendor->ledger_account_id;
            }

            if (!$partyLedgerAccount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Party does not have a ledger account',
                    'errors' => ['party_id' => ['Party ledger account not found']]
                ], 422);
            }

            // Normalize items - calculate amounts and fetch product details
            $inventoryItems = collect($items)->map(function ($item) {
                $product = Product::find($item['product_id']);

                $quantity = $item['quantity'];
                $rate = $item['rate'];
                $discount = $item['discount'] ?? 0;
                $amount = ($quantity * $rate) - $discount;

                return [
                    'product_id' => $item['product_id'],
                    'product_name' => $product ? $product->name : null,
                    'description' => $item['description'] ?? ($product ? $product->name : null),
                    'quantity' => $quantity,
                    'unit_id' => $item['unit_id'] ?? ($product ? $product->primary_unit_id : null),
                    'rate' => $rate,
                    'amount' => $amount,
                    'discount_percentage' => 0,
                    'discount_amount' => $discount,
                    'tax_percentage' => $item['vat_rate'] ?? 0,
                    'tax_amount' => 0,
                    'total' => $amount,
                    'purchase_rate' => $product ? $product->purchase_rate : 0,
                ];
            })->toArray();

            $additionalLedgerAccounts = $request->additional_ledger_accounts ?? [];

            // Calculate total
            $totalAmount = collect($inventoryItems)->sum('amount')
                + collect($additionalLedgerAccounts)->sum('amount')
                + ($request->vat_amount ?? 0);

            // Generate voucher number
            $voucherNumber = $voucherType->getNextVoucherNumber();

            // Determine if should post
            $shouldPost = in_array($status, ['posted', 'save_and_post', 'save_and_post_new_sales']);

            // Create voucher
            $voucher = Voucher::create([
                'tenant_id' => $tenant->id,
                'voucher_type_id' => $request->voucher_type_id,
                'voucher_number' => $voucherNumber,
                'voucher_date' => $request->voucher_date,
                'reference_number' => $request->reference_number,
                'narration' => $request->narration,
                'total_amount' => $totalAmount,
                'status' => $shouldPost ? 'posted' : 'draft',
                'created_by' => auth()->id(),
                'posted_at' => $shouldPost ? now() : null,
                'posted_by' => $shouldPost ? auth()->id() : null,
            ]);

            // Create invoice items
            foreach ($inventoryItems as $item) {
                $voucher->items()->create([
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_id' => $item['unit_id'],
                    'rate' => $item['rate'],
                    'amount' => $item['amount'],
                    'discount_percentage' => $item['discount_percentage'] ?? 0,
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'tax_percentage' => $item['tax_percentage'] ?? 0,
                    'tax_amount' => $item['tax_amount'] ?? 0,
                    'total' => $item['total'] ?? $item['amount'],
                    'purchase_rate' => $item['purchase_rate'] ?? 0,
                ]);
            }

            // Create accounting entries
            $this->createAccountingEntries(
                $voucher,
                $inventoryItems,
                $tenant,
                $partyLedgerAccount,
                $additionalLedgerAccounts
            );

            // Update stock if posted
            if ($shouldPost) {
                $this->updateProductStock($voucher, $inventoryItems, $voucherType->inventory_effect);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $shouldPost ? 'Invoice created and posted successfully' : 'Invoice created successfully',
                'data' => $voucher->load(['voucherType', 'items.product', 'entries.ledgerAccount'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Invoice creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified invoice.
     */
    public function show(Request $request, Tenant $tenant, Voucher $invoice)
    {
        $invoice->load([
            'voucherType',
            'items.product.unit',
            'entries.ledgerAccount.accountGroup',
            'createdBy',
            'postedBy'
        ]);

        // Get customer/vendor info
        $partyEntry = $invoice->entries()
            ->whereHas('ledgerAccount', function ($q) {
                $q->whereHas('customers')->orWhereHas('vendors');
            })
            ->first();

        $party = null;
        if ($partyEntry) {
            $party = $partyEntry->ledgerAccount->customers()->first()
                ?? $partyEntry->ledgerAccount->vendors()->first();
        }

        // Calculate balance due
        $totalPaid = 0; // Implement payment tracking if needed
        $balanceDue = $invoice->total_amount - $totalPaid;

        return response()->json([
            'success' => true,
            'message' => 'Invoice retrieved successfully',
            'data' => [
                'invoice' => $invoice,
                'party' => $party,
                'balance_due' => $balanceDue,
                'total_paid' => $totalPaid,
            ]
        ]);
    }

    /**
     * Update the specified invoice.
     */
    public function update(Request $request, Tenant $tenant, Voucher $invoice)
    {
        if ($invoice->status === 'posted') {
            return response()->json([
                'success' => false,
                'message' => 'Posted invoices cannot be edited. Unpost first to make changes.',
                'errors' => ['status' => ['Posted invoices are locked']]
            ], 422);
        }

        // Normalize payload - accept both old and new field names
        $partyId = $request->input('party_id') ?? $request->input('customer_id');
        $items = $request->input('items') ?? $request->input('inventory_items');

        // Prepare normalized data for validation
        $validationData = array_merge($request->all(), [
            'party_id' => $partyId,
            'items' => $items,
        ]);

        $validator = Validator::make($validationData, [
            'voucher_date' => 'required|date',
            'party_id' => 'required|integer',
            'reference_number' => 'nullable|string|max:255',
            'narration' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.vat_rate' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Get party ledger account
            $partyLedgerAccount = null;
            if ($invoice->voucherType->inventory_effect === 'decrease') {
                // Sales - party is customer
                $customer = Customer::find($partyId);
                if (!$customer) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Customer not found',
                        'errors' => ['party_id' => ['Customer not found']]
                    ], 422);
                }
                $partyLedgerAccount = $customer->ledger_account_id;
            } else {
                // Purchase - party is vendor
                $vendor = Vendor::find($partyId);
                if (!$vendor) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Vendor not found',
                        'errors' => ['party_id' => ['Vendor not found']]
                    ], 422);
                }
                $partyLedgerAccount = $vendor->ledger_account_id;
            }

// Normalize items - calculate amounts and fetch product details
            $inventoryItems = collect($items)->map(function ($item) {
                $product = Product::find($item['product_id']);

                $quantity = $item['quantity'];
                $rate = $item['rate'];
                $discount = $item['discount'] ?? 0;
                $amount = ($quantity * $rate) - $discount;

                return [
                    'product_id' => $item['product_id'],
                    'product_name' => $product ? $product->name : null,
                    'description' => $item['description'] ?? ($product ? $product->name : null),
                    'quantity' => $quantity,
                    'unit_id' => $item['unit_id'] ?? ($product ? $product->primary_unit_id : null),
                    'rate' => $rate,
                    'amount' => $amount,
                    'discount_percentage' => 0,
                    'discount_amount' => $discount,
                    'tax_percentage' => $item['vat_rate'] ?? 0,
                    'tax_amount' => 0,
                    'total' => $amount,
                    'purchase_rate' => $product ? $product->purchase_rate : 0,
                ];
            })->toArray();

            $additionalLedgerAccounts = $request->additional_ledger_accounts ?? [];

            $totalAmount = collect($inventoryItems)->sum('amount')
                + collect($additionalLedgerAccounts)->sum('amount')
                + ($request->vat_amount ?? 0);

            // Update voucher
            $invoice->update([
                'voucher_date' => $request->voucher_date,
                'reference_number' => $request->reference_number,
                'narration' => $request->narration,
                'total_amount' => $totalAmount,
                'updated_by' => auth()->id(),
            ]);

            // Delete old items and entries
            $invoice->items()->delete();
            $invoice->entries()->delete();

            // Recreate items
            foreach ($inventoryItems as $item) {
                $invoice->items()->create([
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_id' => $item['unit_id'],
                    'rate' => $item['rate'],
                    'amount' => $item['amount'],
                    'discount_percentage' => $item['discount_percentage'] ?? 0,
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'tax_percentage' => $item['tax_percentage'] ?? 0,
                    'tax_amount' => $item['tax_amount'] ?? 0,
                    'total' => $item['total'] ?? $item['amount'],
                    'purchase_rate' => $item['purchase_rate'] ?? 0,
                ]);
            }

            // Recreate accounting entries
            $this->createAccountingEntries(
                $invoice,
                $inventoryItems,
                $tenant,
                $partyLedgerAccount,
                $additionalLedgerAccounts
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Invoice updated successfully',
                'data' => $invoice->fresh()->load(['voucherType', 'items.product', 'entries.ledgerAccount'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified invoice.
     */
    public function destroy(Request $request, Tenant $tenant, Voucher $invoice)
    {
        if ($invoice->status === 'posted') {
            return response()->json([
                'success' => false,
                'message' => 'Posted invoices cannot be deleted. Unpost first.',
                'errors' => ['status' => ['Posted invoices cannot be deleted']]
            ], 422);
        }

        DB::beginTransaction();
        try {
            $invoice->items()->delete();
            $invoice->entries()->delete();
            $invoice->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Invoice deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Post an invoice.
     */
    public function post(Request $request, Tenant $tenant, Voucher $invoice)
    {
        if ($invoice->status === 'posted') {
            return response()->json([
                'success' => false,
                'message' => 'Invoice is already posted',
                'errors' => ['status' => ['Already posted']]
            ], 422);
        }

        DB::beginTransaction();
        try {
            $invoice->update([
                'status' => 'posted',
                'posted_at' => now(),
                'posted_by' => auth()->id(),
            ]);

            // Update stock
            $this->updateProductStock(
                $invoice,
                $invoice->items->toArray(),
                $invoice->voucherType->inventory_effect
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Invoice posted successfully',
                'data' => $invoice->fresh()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to post invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unpost an invoice.
     */
    public function unpost(Request $request, Tenant $tenant, Voucher $invoice)
    {
        if ($invoice->status !== 'posted') {
            return response()->json([
                'success' => false,
                'message' => 'Only posted invoices can be unposted',
                'errors' => ['status' => ['Not posted']]
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Reverse stock movements
            $this->reverseStockMovements($invoice);

            $invoice->update([
                'status' => 'draft',
                'posted_at' => null,
                'posted_by' => null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Invoice unposted successfully',
                'data' => $invoice->fresh()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to unpost invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search customers for invoice.
     */
    public function searchCustomers(Request $request, Tenant $tenant)
    {
        $search = $request->get('search', '');
        $type = $request->get('type', 'customer'); // 'customer' or 'vendor'

        if ($type === 'customer') {
            $customers = Customer::where('tenant_id', $tenant->id)
                ->where('status', 'active')
                ->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('company_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('mobile', 'like', "%{$search}%");
                })
                ->with('ledgerAccount')
                ->limit(20)
                ->get()
                ->map(function ($customer) {
                    return [
                        'id' => $customer->id,
                        'ledger_account_id' => $customer->ledger_account_id,
                        'name' => $customer->getFullNameAttribute(),
                        'customer_type' => $customer->customer_type,
                        'email' => $customer->email,
                        'phone' => $customer->phone,
                        'mobile' => $customer->mobile,
                        'outstanding_balance' => $customer->outstanding_balance,
                        'currency' => $customer->currency,
                        'payment_terms' => $customer->payment_terms,
                        'address' => $customer->getFullAddressAttribute(),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $customers
            ]);
        } else {
            $vendors = Vendor::where('tenant_id', $tenant->id)
                ->where('status', 'active')
                ->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('company_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('mobile', 'like', "%{$search}%");
                })
                ->with('ledgerAccount')
                ->limit(20)
                ->get()
                ->map(function ($vendor) {
                    return [
                        'id' => $vendor->id,
                        'ledger_account_id' => $vendor->ledger_account_id,
                        'name' => $vendor->getFullNameAttribute(),
                        'vendor_type' => $vendor->vendor_type,
                        'email' => $vendor->email,
                        'phone' => $vendor->phone,
                        'mobile' => $vendor->mobile,
                        'outstanding_balance' => $vendor->outstanding_balance,
                        'currency' => $vendor->currency,
                        'payment_terms' => $vendor->payment_terms,
                        'address' => $vendor->getFullAddressAttribute(),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $vendors
            ]);
        }
    }

    /**
     * Search products for invoice.
     */
    public function searchProducts(Request $request, Tenant $tenant)
    {
        $search = $request->get('search', '');
        $type = $request->get('type', 'sales'); // 'sales' or 'purchase'

        $products = Product::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            })
            ->with(['salesAccount', 'purchaseAccount', 'unit'])
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products->map(function ($product) use ($type) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'code' => $product->code,
                    'type' => $product->type,
                    'unit' => $product->unit ? $product->unit->name : null,
                    'unit_id' => $product->unit_id,
                    'sales_price' => $product->sales_price,
                    'purchase_price' => $product->purchase_price,
                    'current_stock' => $product->current_stock,
                    'sales_account_id' => $product->sales_account_id,
                    'purchase_account_id' => $product->purchase_account_id,
                    'default_price' => $type === 'sales' ? $product->sales_price : $product->purchase_price,
                    'account_id' => $type === 'sales' ? $product->sales_account_id : $product->purchase_account_id,
                ];
            })
        ]);
    }

    /**
     * Search ledger accounts for additional charges.
     */
    public function searchLedgerAccounts(Request $request, Tenant $tenant)
    {
        $search = $request->get('search', '');

        $accounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->whereNotNull('account_group_id')
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            })
            ->with('accountGroup')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $accounts
        ]);
    }

    /**
     * Helper: Create accounting entries
     */
    private function createAccountingEntries($voucher, $inventoryItems, $tenant, $customerLedgerId, $additionalLedgerAccounts = [])
    {
        $partyAccount = LedgerAccount::find($customerLedgerId);
        if (!$partyAccount) {
            throw new \Exception('Party account not found');
        }

        $totalAmount = collect($inventoryItems)->sum('amount');
        $additionalTotal = collect($additionalLedgerAccounts)->sum('amount');
        $totalAmount += $additionalTotal;

        // Group items by ledger account
        $groupedItems = [];
        foreach ($inventoryItems as $item) {
            $product = Product::find($item['product_id']);
            $accountId = $voucher->voucherType->inventory_effect === 'decrease'
                ? $product->sales_account_id
                : $product->purchase_account_id;

            if (!isset($groupedItems[$accountId])) {
                $groupedItems[$accountId] = 0;
            }
            $groupedItems[$accountId] += $item['amount'];
        }

        // Create entries based on inventory effect
        $isSales = $voucher->voucherType->inventory_effect === 'decrease';

        if ($isSales) {
            // Debit: Customer Account
            $voucher->entries()->create([
                'ledger_account_id' => $partyAccount->id,
                'debit_amount' => $totalAmount,
                'credit_amount' => 0,
                'particulars' => 'Sales to ' . $partyAccount->name,
            ]);

            // Credit: Sales Accounts
            foreach ($groupedItems as $accountId => $amount) {
                if ($accountId) {
                    $voucher->entries()->create([
                        'ledger_account_id' => $accountId,
                        'debit_amount' => 0,
                        'credit_amount' => $amount,
                        'particulars' => 'Sales',
                    ]);
                }
            }
        } else {
            // Credit: Vendor Account
            $voucher->entries()->create([
                'ledger_account_id' => $partyAccount->id,
                'debit_amount' => 0,
                'credit_amount' => $totalAmount,
                'particulars' => 'Purchase from ' . $partyAccount->name,
            ]);

            // Debit: Purchase Accounts
            foreach ($groupedItems as $accountId => $amount) {
                if ($accountId) {
                    $voucher->entries()->create([
                        'ledger_account_id' => $accountId,
                        'debit_amount' => $amount,
                        'credit_amount' => 0,
                        'particulars' => 'Purchase',
                    ]);
                }
            }
        }

        // Additional charges
        foreach ($additionalLedgerAccounts as $ledger) {
            if ($isSales) {
                $voucher->entries()->create([
                    'ledger_account_id' => $ledger['ledger_account_id'],
                    'debit_amount' => 0,
                    'credit_amount' => $ledger['amount'],
                    'particulars' => $ledger['narration'] ?? 'Additional charge',
                ]);
            } else {
                $voucher->entries()->create([
                    'ledger_account_id' => $ledger['ledger_account_id'],
                    'debit_amount' => $ledger['amount'],
                    'credit_amount' => 0,
                    'particulars' => $ledger['narration'] ?? 'Additional charge',
                ]);
            }
        }
    }

    /**
     * Helper: Update product stock
     */
    private function updateProductStock($voucher, $items, $inventoryEffect)
    {
        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            if (!$product) continue;

            if ($inventoryEffect === 'decrease') {
                // Sales - decrease stock
                $movementType = 'out';
                StockMovement::createFromVoucher($voucher, $item, $movementType);
            } elseif ($inventoryEffect === 'increase') {
                // Purchase - increase stock
                $movementType = 'in';
                StockMovement::createFromVoucher($voucher, $item, $movementType);
            }
        }
    }

    /**
     * Helper: Reverse stock movements
     */
    private function reverseStockMovements($voucher)
    {
        $movements = StockMovement::where('source_transaction_type', get_class($voucher))
            ->where('source_transaction_id', $voucher->id)
            ->get();

        foreach ($movements as $movement) {
            $movement->delete();
        }
    }
}
