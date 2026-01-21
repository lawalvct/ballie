<?php

namespace App\Http\Controllers\Api\Tenant\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Voucher;
use App\Models\VoucherType;
use App\Models\VoucherEntry;
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
        // Find the entry with AR (Accounts Receivable) or AP (Accounts Payable)
        $partyEntry = $invoice->entries()
            ->whereHas('ledgerAccount.accountGroup', function ($q) {
                $q->whereIn('code', ['AR', 'AP']);
            })
            ->first();

        $party = null;
        if ($partyEntry && $partyEntry->ledgerAccount) {
            // Try to find customer by ledger_account_id
            $customer = Customer::where('tenant_id', $tenant->id)
                ->where('ledger_account_id', $partyEntry->ledgerAccount->id)
                ->first();

            if ($customer) {
                $party = [
                    'id' => $customer->id,
                    'type' => 'customer',
                    'name' => $customer->getFullNameAttribute(),
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'mobile' => $customer->mobile,
                    'address' => $customer->getFullAddressAttribute(),
                    'outstanding_balance' => $customer->outstanding_balance,
                ];
            } else {
                // Try to find vendor by ledger_account_id
                $vendor = Vendor::where('tenant_id', $tenant->id)
                    ->where('ledger_account_id', $partyEntry->ledgerAccount->id)
                    ->first();

                if ($vendor) {
                    $party = [
                        'id' => $vendor->id,
                        'type' => 'vendor',
                        'name' => $vendor->getFullNameAttribute(),
                        'email' => $vendor->email,
                        'phone' => $vendor->phone,
                        'mobile' => $vendor->mobile,
                        'address' => $vendor->getFullAddressAttribute(),
                        'outstanding_balance' => $vendor->outstanding_balance,
                    ];
                }
            }
        }

        // Calculate balance due by finding related payment vouchers
        // Payment vouchers reference the invoice in their narration or reference_number
        $invoiceReference = ($invoice->voucherType->prefix ?? '') . $invoice->voucher_number;

        $payments = Voucher::where('tenant_id', $tenant->id)
            ->where('status', 'posted')
            ->whereHas('voucherType', function($q) {
                $q->where('code', 'RV'); // Receipt Voucher
            })
            ->where(function($q) use ($invoiceReference) {
                $q->where('narration', 'like', '%' . $invoiceReference . '%')
                  ->orWhere('reference_number', 'like', '%' . $invoiceReference . '%');
            })
            ->get();

        $totalPaid = $payments->sum('total_amount');
        $balanceDue = $invoice->total_amount - $totalPaid;

        // Calculate payment status
        $paymentStatus = 'Unpaid';
        $paymentPercentage = 0;

        if ($invoice->total_amount > 0) {
            $paymentPercentage = ($totalPaid / $invoice->total_amount) * 100;

            if ($totalPaid >= $invoice->total_amount) {
                $paymentStatus = 'Paid';
            } elseif ($totalPaid > 0) {
                $paymentStatus = 'Partially Paid';
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Invoice retrieved successfully',
            'data' => [
                'invoice' => $invoice,
                'party' => $party,
                'balance_due' => $balanceDue,
                'total_paid' => $totalPaid,
                'payment_status' => $paymentStatus,
                'payment_percentage' => round($paymentPercentage, 2),
                'payments' => $payments->map(function($payment) {
                    return [
                        'id' => $payment->id,
                        'voucher_number' => ($payment->voucherType->prefix ?? '') . $payment->voucher_number,
                        'date' => $payment->voucher_date->format('Y-m-d'),
                        'amount' => $payment->total_amount,
                        'reference' => $payment->reference_number,
                        'narration' => $payment->narration,
                    ];
                })
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

    /**
     * Download invoice as PDF
     */
    public function pdf(Tenant $tenant, Voucher $invoice)
    {
        // Ensure the invoice belongs to the tenant
        if ($invoice->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found'
            ], 404);
        }

        try {
            $invoice->load(['voucherType', 'entries.ledgerAccount', 'createdBy', 'postedBy', 'items']);

            // Determine customer/vendor info
            $partyLedgerEntry = $invoice->voucherType->inventory_effect === 'decrease'
                ? $invoice->entries->where('debit_amount', '>', 0)->first()
                : $invoice->entries->where('credit_amount', '>', 0)->first();

            $party = null;
            $partyNameForFile = 'party';

            if ($partyLedgerEntry && $partyLedgerEntry->ledgerAccount) {
                $ledger = $partyLedgerEntry->ledgerAccount;

                // Try to find Customer or Vendor model
                $partyModel = $invoice->voucherType->inventory_effect === 'decrease'
                    ? Customer::where('ledger_account_id', $ledger->id)->first()
                    : Vendor::where('ledger_account_id', $ledger->id)->first();

                if ($partyModel) {
                    $party = $partyModel;
                    $partyNameForFile = $partyModel->company_name
                        ?? trim(($partyModel->first_name ?? '') . ' ' . ($partyModel->last_name ?? ''))
                        ?: $ledger->name;
                } else {
                    $party = $ledger;
                    $partyNameForFile = $ledger->name ?? 'party';
                }
            }

            // Sanitize filename
            $prefix = strtolower($partyNameForFile);
            $prefix = preg_replace('/[^a-z0-9]+/i', '-', $prefix);
            $prefix = trim($prefix, '-') ?: 'party';

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('tenant.accounting.invoices.pdf', compact('tenant', 'invoice', 'party'));

            $filename = $prefix . '-invoice-' . ($invoice->voucherType->prefix ?? '') . $invoice->voucher_number . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('Error generating PDF: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Email invoice to customer/vendor
     */
    public function email(Request $request, Tenant $tenant, Voucher $invoice)
    {
        // Ensure the invoice belongs to the tenant
        if ($invoice->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'to' => 'required|email',
            'subject' => 'nullable|string|max:255',
            'message' => 'nullable|string',
            'cc' => 'nullable|array',
            'cc.*' => 'email',
            'attach_pdf' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $invoice->load(['voucherType', 'entries.ledgerAccount', 'createdBy', 'postedBy', 'items']);

            // Get party info
            $partyLedgerEntry = $invoice->voucherType->inventory_effect === 'decrease'
                ? $invoice->entries->where('debit_amount', '>', 0)->first()
                : $invoice->entries->where('credit_amount', '>', 0)->first();

            $party = $partyLedgerEntry?->ledgerAccount;

            // Default subject if not provided
            $subject = $request->subject ?? 'Invoice ' . ($invoice->voucherType->prefix ?? '') . $invoice->voucher_number . ' from ' . $tenant->name;

            // Default message if not provided
            $message = $request->message ?? "Please find attached your invoice.\n\nThank you for your business!";

            // Generate PDF if attachment requested
            $attachPdf = $request->attach_pdf ?? true;
            $pdf = null;

            if ($attachPdf) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('tenant.accounting.invoices.pdf', compact('tenant', 'invoice', 'party'));
            }

            // Send email
            \Illuminate\Support\Facades\Mail::send('emails.invoice', [
                'invoice' => $invoice,
                'tenant' => $tenant,
                'emailMessage' => $message,
            ], function ($mail) use ($request, $invoice, $pdf, $subject, $attachPdf) {
                $mail->to($request->to)
                     ->subject($subject);

                // Add CC recipients if provided
                if ($request->cc && count($request->cc) > 0) {
                    $mail->cc($request->cc);
                }

                // Attach PDF if requested
                if ($attachPdf && $pdf) {
                    $mail->attachData($pdf->output(), 'invoice-' . $invoice->voucher_number . '.pdf', [
                        'mime' => 'application/pdf',
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Invoice emailed successfully',
                'data' => [
                    'sent_to' => $request->to,
                    'sent_at' => now()->toIso8601String(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending invoice email: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Record payment against invoice
     */
    public function recordPayment(Request $request, Tenant $tenant, Voucher $invoice)
    {
        // Ensure the invoice belongs to the tenant
        if ($invoice->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found'
            ], 404);
        }

        if ($invoice->status !== 'posted') {
            return response()->json([
                'success' => false,
                'message' => 'Only posted invoices can receive payments'
            ], 422);
        }

        // Get party account from the original invoice
        $partyAccount = $invoice->voucherType->inventory_effect === 'decrease'
            ? $invoice->entries->where('debit_amount', '>', 0)->first()?->ledgerAccount
            : $invoice->entries->where('credit_amount', '>', 0)->first()?->ledgerAccount;

        if (!$partyAccount) {
            return response()->json([
                'success' => false,
                'message' => 'Party account not found in invoice entries'
            ], 422);
        }

        // Calculate balance due by finding related payment vouchers
        // Payment vouchers reference the invoice in their narration or reference_number
        $invoiceReference = ($invoice->voucherType->prefix ?? '') . $invoice->voucher_number;

        $payments = Voucher::where('tenant_id', $tenant->id)
            ->where('status', 'posted')
            ->whereHas('voucherType', function($q) {
                $q->where('code', 'RV'); // Receipt Voucher
            })
            ->where(function($q) use ($invoiceReference) {
                $q->where('narration', 'like', '%' . $invoiceReference . '%')
                  ->orWhere('reference_number', 'like', '%' . $invoiceReference . '%');
            })
            ->get();

        $totalPaid = $payments->sum('total_amount');
        $balanceDue = $invoice->total_amount - $totalPaid;

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01|max:' . max($balanceDue, $invoice->total_amount),
            'bank_account_id' => 'required|exists:ledger_accounts,id',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Get receipt voucher type
            $receiptVoucherType = VoucherType::where('tenant_id', $tenant->id)
                ->where('code', 'RV')
                ->first();

            if (!$receiptVoucherType) {
                throw new \Exception('Receipt voucher type not found. Please create it first.');
            }

            // Get bank account
            $bankAccount = LedgerAccount::findOrFail($request->bank_account_id);

            // Generate voucher number for receipt
            $lastReceipt = Voucher::where('tenant_id', $tenant->id)
                ->where('voucher_type_id', $receiptVoucherType->id)
                ->latest('id')
                ->first();

            $nextNumber = $lastReceipt ? $lastReceipt->voucher_number + 1 : 1;

            // Create receipt voucher with invoice reference in narration
            $invoiceReference = ($invoice->voucherType->prefix ?? '') . $invoice->voucher_number;
            $receiptVoucher = Voucher::create([
                'tenant_id' => $tenant->id,
                'voucher_type_id' => $receiptVoucherType->id,
                'voucher_number' => $nextNumber,
                'voucher_date' => $request->date,
                'reference_number' => $request->reference,
                'narration' => $request->notes ?? 'Payment received for Invoice ' . $invoiceReference,
                'total_amount' => $request->amount,
                'status' => 'posted',
                'created_by' => auth()->id(),
                'posted_at' => now(),
                'posted_by' => auth()->id(),
            ]);

            // Create accounting entries for receipt
            // Debit: Bank/Cash Account
            $receiptVoucher->entries()->create([
                'ledger_account_id' => $bankAccount->id,
                'debit_amount' => $request->amount,
                'credit_amount' => 0,
                'particulars' => 'Payment received from ' . $partyAccount->name,
            ]);

            // Credit: Party Account (reducing their outstanding balance)
            $receiptVoucher->entries()->create([
                'ledger_account_id' => $partyAccount->id,
                'debit_amount' => 0,
                'credit_amount' => $request->amount,
                'particulars' => 'Payment received against Invoice ' . ($invoice->voucherType->prefix ?? '') . $invoice->voucher_number,
            ]);

            DB::commit();

            // Recalculate totals by querying all related payments
            $allPayments = Voucher::where('tenant_id', $tenant->id)
                ->where('status', 'posted')
                ->whereHas('voucherType', function($q) {
                    $q->where('code', 'RV');
                })
                ->where(function($q) use ($invoiceReference) {
                    $q->where('narration', 'like', '%' . $invoiceReference . '%')
                      ->orWhere('reference_number', 'like', '%' . $invoiceReference . '%');
                })
                ->get();

            $newTotalPaid = $allPayments->sum('total_amount');
            $newBalanceDue = $invoice->total_amount - $newTotalPaid;
            $paymentStatus = $newBalanceDue <= 0 ? 'Paid' : ($newTotalPaid > 0 ? 'Partially Paid' : 'Unpaid');

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully',
                'data' => [
                    'payment_voucher' => [
                        'id' => $receiptVoucher->id,
                        'voucher_number' => ($receiptVoucherType->prefix ?? 'RV-') . $receiptVoucher->voucher_number,
                        'voucher_type' => [
                            'id' => $receiptVoucherType->id,
                            'name' => $receiptVoucherType->name,
                            'code' => $receiptVoucherType->code,
                        ],
                        'voucher_date' => $receiptVoucher->voucher_date,
                        'amount' => $receiptVoucher->total_amount,
                        'reference' => $receiptVoucher->reference_number,
                        'notes' => $receiptVoucher->narration,
                    ],
                    'invoice' => [
                        'id' => $invoice->id,
                        'voucher_number' => ($invoice->voucherType->prefix ?? '') . $invoice->voucher_number,
                        'total_amount' => $invoice->total_amount,
                        'total_paid' => $newTotalPaid,
                        'balance_due' => $newBalanceDue,
                        'payment_status' => $paymentStatus,
                    ],
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error recording payment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to record payment: ' . $e->getMessage()
            ], 500);
        }
    }
}
