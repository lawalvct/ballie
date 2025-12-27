<?php

namespace App\Http\Controllers\Tenant\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\Voucher;
use App\Models\VoucherType;
use App\Models\VoucherEntry;
use App\Models\Product;
use App\Models\LedgerAccount;
use App\Models\Tenant;
use App\Models\AccountGroup;
use App\Models\StockMovement;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\PaymentHelper;
use App\Helpers\PaystackPaymentHelper;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:accounting.invoices.manage')
            ->except(['index', 'show']);
        $this->middleware('permission:accounting.view')
            ->only(['index', 'show']);
    }

    public function index(Request $request, Tenant $tenant)
    {
        $query = Voucher::where('tenant_id', $tenant->id)
            ->whereHas('voucherType', function($q) use ($request) {
                $q->where('affects_inventory', true);

                // Filter by type (sales or purchase)
                $type = $request->input('type', 'sales'); // Default to sales
                if ($type === 'sales') {
                    $q->where('inventory_effect', 'decrease');
                } elseif ($type === 'purchase') {
                    $q->where('inventory_effect', 'increase');
                }
            })
            ->with(['voucherType', 'createdBy', 'entries.ledgerAccount']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('voucher_number', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('narration', 'like', "%{$search}%")
                  ->orWhereHas('entries.ledgerAccount', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('voucher_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('voucher_date', '<=', $request->date_to);
        }

        $invoices = $query->latest('voucher_date')->paginate(15);

        return view('tenant.accounting.invoices.index', compact('invoices', 'tenant'));
    }

    public function create(Tenant $tenant)
    {
        // Get sales voucher types that affect inventory, with 'SV' first
        $voucherTypes = VoucherType::where('tenant_id', $tenant->id)
            ->where('affects_inventory', true)
            ->orderByRaw("FIELD(code, 'SV') DESC")
            ->orderBy('name', 'desc')
            ->get();

        // Get products
        $products = Product::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->where('is_saleable', true)
            ->with(['primaryUnit'])
            ->orderBy('name')
            ->get();

        // Get customers and vendors, and prepend type to display_name
        $buyers = Customer::with('ledgerAccount')->where('tenant_id', $tenant->id)->get();
        $vendors = Vendor::with('ledgerAccount')->where('tenant_id', $tenant->id)->get();

        // Helper to get name for customer or vendor
        $getName = function($item, $type) {
            if (!empty($item->company_name)) {
                return $item->company_name;
            }
            $first = $item->first_name ?? '';
            $last = $item->last_name ?? '';
            $full = trim($first . ' ' . $last);
            return $full ?: ($type === 'customer' ? 'Unnamed Customer' : 'Unnamed Vendor');
        };

        $buyers->each(function($item) use ($getName) {
            $item->display_name = $getName($item, 'customer');
            $item->type = 'customer';
        });

        $vendors->each(function($item) use ($getName) {
            $item->display_name = $getName($item, 'vendor');
            $item->type = 'vendor';
        });

        // Keep customers and vendors separate for the form
        $customers = $buyers;
        // Also merge for backward compatibility if needed
        $allPartners = $buyers->concat($vendors);

        // Get ledger accounts for additional charges (like VAT, Transport, etc.)
        // Exclude customer and vendor accounts, get expense/income/liability accounts
        $ledgerAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->whereHas('accountGroup', function($query) {
                $query->whereIn('nature', ['expenses', 'income', 'liabilities', 'assets'])
                    ->whereNotIn('code', ['AR', 'AP']); // Exclude Accounts Receivable/Payable
            })
            ->where('is_active', true)
            ->with('accountGroup')
            ->orderBy('name')
            ->get();

        // Get default sales voucher type
        $selectedType = $voucherTypes->where('code', 'SALES')->first() ?? $voucherTypes->first();

        // Get units for quick add product
        $units = Unit::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('tenant.accounting.invoices.create', compact(
            'tenant',
            'voucherTypes',
            'products',
            'customers',
            'vendors',
            'ledgerAccounts',
            'selectedType',
            'units'
        ));
    }

    public function store(Request $request, Tenant $tenant)
    {
        Log::info('=== INVOICE STORE STARTED ===', [
            'tenant_id' => $tenant->id,
            'tenant_slug' => $tenant->slug,
            'user_id' => auth()->id(),
            'timestamp' => now()->toDateTimeString()
        ]);

        Log::info('Request Data Received', [
            'all_request_data' => $request->all(),
            'action' => $request->input('action'),
            'voucher_type_id' => $request->input('voucher_type_id'),
            'customer_id' => $request->input('customer_id'),
            'customer_id_is_null' => is_null($request->input('customer_id')),
            'customer_id_is_empty' => empty($request->input('customer_id')),
            'inventory_items_count' => count($request->input('inventory_items', []))
        ]);

        // Check if customer_id is missing and log the form state
        if (is_null($request->input('customer_id')) || empty($request->input('customer_id'))) {
            Log::warning('Customer ID is missing from request', [
                'has_customer_id_key' => $request->has('customer_id'),
                'customer_id_value' => $request->input('customer_id'),
                'all_form_keys' => array_keys($request->except(['_token', 'current_tenant'])),
                'voucher_type_id' => $request->input('voucher_type_id')
            ]);
        }

        $validator = Validator::make($request->all(), [
            'voucher_type_id' => 'required|exists:voucher_types,id',
            'voucher_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'narration' => 'nullable|string',
            'customer_id' => 'required|exists:ledger_accounts,id',
            'inventory_items' => 'required|array|min:1',
            'inventory_items.*.product_id' => 'required|exists:products,id',
            'inventory_items.*.quantity' => 'required|numeric|min:0.01',
            'inventory_items.*.rate' => 'required|numeric|min:0',
            'inventory_items.*.purchase_rate' => 'nullable|numeric|min:0',
            'inventory_items.*.description' => 'nullable|string',
            'ledger_accounts' => 'nullable|array',
            'ledger_accounts.*.ledger_account_id' => 'required_with:ledger_accounts|exists:ledger_accounts,id',
            'ledger_accounts.*.amount' => 'required_with:ledger_accounts|numeric|min:0',
            'ledger_accounts.*.narration' => 'nullable|string',
        ], [
            'customer_id.required' => 'Please select a customer or vendor before saving the invoice.',
            'customer_id.exists' => 'The selected customer or vendor is invalid. Please select a valid customer/vendor.',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation Failed', [
                'errors' => $validator->errors()->toArray(),
                'failed_rules' => $validator->failed(),
                'customer_id_submitted' => $request->input('customer_id'),
                'voucher_type_id' => $request->input('voucher_type_id')
            ]);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Log::info('Validation Passed Successfully');

        try {
            $shouldPost = in_array($request->action, ['save_and_post', 'save_and_post_new_sales']);

            DB::beginTransaction();
            Log::info('Database Transaction Started');

            // Get voucher type
            $voucherType = VoucherType::findOrFail($request->voucher_type_id);
            Log::info('Voucher Type Retrieved', [
                'voucher_type_id' => $voucherType->id,
                'voucher_type_name' => $voucherType->name,
                'voucher_type_code' => $voucherType->code,
                'affects_inventory' => $voucherType->affects_inventory,
                'inventory_effect' => $voucherType->inventory_effect
            ]);

            // Calculate total amount from inventory items
            $totalAmount = 0;
            $inventoryItems = [];

            Log::info('Processing Inventory Items', [
                'items_count' => count($request->inventory_items)
            ]);

            foreach ($request->inventory_items as $index => $item) {
                Log::info("Processing Item {$index}", [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'rate' => $item['rate']
                ]);

                $product = Product::findOrFail($item['product_id']);
                Log::info("Product Retrieved", [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'current_stock' => $product->stock_quantity
                ]);

                $amount = $item['quantity'] * $item['rate'];
                $totalAmount += $amount;

                $inventoryItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'description' => $item['description'] ?? $product->name,
                    'quantity' => $item['quantity'],
                    'rate' => $item['rate'],
                    'amount' => $amount,
                    'purchase_rate' => $item['purchase_rate'] ?? $product->purchase_rate,
                ];

                Log::info("Item Processed", [
                    'amount' => $amount,
                    'running_total' => $totalAmount
                ]);
            }

            // Process additional ledger accounts (like VAT, Transport, etc.)
            $additionalLedgerAccounts = [];
            if ($request->has('ledger_accounts') && is_array($request->ledger_accounts)) {
                Log::info('Processing Additional Ledger Accounts', [
                    'count' => count($request->ledger_accounts)
                ]);

                foreach ($request->ledger_accounts as $index => $ledgerItem) {
                    if (!empty($ledgerItem['ledger_account_id']) && !empty($ledgerItem['amount'])) {
                        $amount = (float) $ledgerItem['amount'];
                        $totalAmount += $amount;

                        $additionalLedgerAccounts[] = [
                            'ledger_account_id' => $ledgerItem['ledger_account_id'],
                            'amount' => $amount,
                            'narration' => $ledgerItem['narration'] ?? ''
                        ];

                        Log::info("Additional Ledger Account {$index}", [
                            'ledger_account_id' => $ledgerItem['ledger_account_id'],
                            'amount' => $amount,
                            'running_total' => $totalAmount
                        ]);
                    }
                }
            }

            // Process VAT if enabled
            Log::info('VAT Processing Check', [
                'has_vat_enabled' => $request->has('vat_enabled'),
                'vat_enabled_value' => $request->vat_enabled,
                'vat_amount_value' => $request->vat_amount,
                'vat_applies_to' => $request->vat_applies_to,
            ]);

            if ($request->has('vat_enabled') && $request->vat_enabled == '1') {
                $vatAmount = (float) $request->vat_amount;
                $vatAppliesTo = $request->input('vat_applies_to', 'items_only');

                Log::info('VAT Enabled - Processing', [
                    'vat_amount' => $vatAmount,
                    'vat_applies_to' => $vatAppliesTo,
                    'voucher_type_code' => $voucherType->code,
                    'inventory_effect' => $voucherType->inventory_effect,
                ]);

                if ($vatAmount > 0) {
                    // Determine VAT account based on invoice type
                    $isSales = $voucherType->inventory_effect === 'decrease';
                    $vatAccountCode = $isSales ? 'VAT-OUT-001' : 'VAT-IN-001';

                    Log::info('VAT Account Selection', [
                        'is_sales' => $isSales,
                        'vat_account_code' => $vatAccountCode,
                    ]);

                    // Get VAT ledger account
                    $vatAccount = LedgerAccount::where('tenant_id', $tenant->id)
                        ->where('code', $vatAccountCode)
                        ->first();

                    if ($vatAccount) {
                        $totalAmount += $vatAmount;

                        // Create descriptive narration based on what VAT applies to
                        $narration = $vatAppliesTo === 'items_only'
                            ? 'VAT @ 7.5% (on items)'
                            : 'VAT @ 7.5% (on items + charges)';

                        $additionalLedgerAccounts[] = [
                            'ledger_account_id' => $vatAccount->id,
                            'amount' => $vatAmount,
                            'narration' => $narration
                        ];

                        Log::info('VAT Added Successfully', [
                            'vat_account_id' => $vatAccount->id,
                            'vat_account' => $vatAccount->name,
                            'vat_account_code' => $vatAccountCode,
                            'vat_amount' => $vatAmount,
                            'vat_applies_to' => $vatAppliesTo,
                            'is_sales' => $isSales,
                            'running_total' => $totalAmount,
                            'additional_ledger_accounts_count' => count($additionalLedgerAccounts),
                        ]);
                    } else {
                        Log::warning('VAT Account Not Found', [
                            'vat_account_code' => $vatAccountCode,
                            'tenant_id' => $tenant->id,
                            'searched_for' => "LedgerAccount with code '{$vatAccountCode}' in tenant {$tenant->id}"
                        ]);
                    }
                } else {
                    Log::warning('VAT Amount is Zero or Negative', [
                        'vat_amount' => $vatAmount,
                    ]);
                }
            } else {
                Log::info('VAT Not Enabled', [
                    'has_vat_enabled' => $request->has('vat_enabled'),
                    'vat_enabled_value' => $request->vat_enabled,
                ]);
            }

            Log::info('All Items Processed', [
                'products_total' => array_sum(array_column($inventoryItems, 'amount')),
                'ledger_accounts_total' => array_sum(array_column($additionalLedgerAccounts, 'amount')),
                'grand_total' => $totalAmount,
                'items_count' => count($inventoryItems),
                'ledger_accounts_count' => count($additionalLedgerAccounts)
            ]);

            // Generate voucher number
            $lastVoucher = Voucher::where('tenant_id', $tenant->id)
                ->where('voucher_type_id', $voucherType->id)
                ->latest('id')
                ->first();

            $nextNumber = 1;
            if ($lastVoucher) {
                $rawNumber = $lastVoucher->voucher_number;

                if (is_numeric($rawNumber)) {
                    $nextNumber = (int) $rawNumber + 1;
                } elseif (preg_match('/(\d+)(?!.*\d)/', (string) $rawNumber, $matches)) {
                    // Handles values such as "SV-2024-0012" by taking the last numeric block
                    $nextNumber = (int) $matches[1] + 1;
                } else {
                    Log::warning('Unable to parse numeric portion of voucher number', [
                        'last_voucher_id' => $lastVoucher->id,
                        'last_voucher_number' => $rawNumber,
                    ]);
                    $nextNumber = $lastVoucher->id + 1; // Fall back to monotonic id-based value
                }
            }

            Log::info('Voucher Number Generated', [
                'last_voucher_id' => $lastVoucher?->id,
                'last_voucher_number' => $lastVoucher?->voucher_number,
                'next_number' => $nextNumber
            ]);

            // Create voucher
            $voucherData = [
                'tenant_id' => $tenant->id,
                'voucher_type_id' => $voucherType->id,
                'voucher_number' => $nextNumber,
                'voucher_date' => $request->voucher_date,
                'reference_number' => $request->reference_number,
                'narration' => $request->narration,
                'total_amount' => $totalAmount,
                'status' => $shouldPost ? 'posted' : 'draft',
                'created_by' => auth()->id(),
                'posted_at' => $shouldPost ? now() : null,
                'posted_by' => $shouldPost ? auth()->id() : null,
                'meta_data' => json_encode(['inventory_items' => $inventoryItems]),
            ];

            Log::info('Creating Voucher with Data', $voucherData);

            $voucher = Voucher::create($voucherData);

            Log::info('Voucher Created Successfully', [
                'voucher_id' => $voucher->id,
                'voucher_number' => $voucher->voucher_number,
                'status' => $voucher->status
            ]);

          foreach ($inventoryItems as $item) {
    Log::info('Creating Voucher Item', [
        'product_id' => $item['product_id'],
        'quantity' => $item['quantity'],
        'amount' => $item['amount']
    ]);

    $voucher->items()->create([
        'product_id' => $item['product_id'],
        'product_name' => $item['product_name'],
        'description' => $item['description'],
        'quantity' => $item['quantity'],
        'rate' => $item['rate'],
        'amount' => $item['amount'],
        'voucher_id' => $voucher->id,

        'purchase_rate' => $item['purchase_rate'] ?? 0,
        'discount' => $item['discount'] ?? 0,
        'tax' => $item['tax'] ?? 0,
        'is_tax_inclusive' => $item['is_tax_inclusive'] ?? false,
        'total' => $item['total'] ?? null,
    ]);
}

            Log::info('All Voucher Items Created', [
                'items_count' => count($inventoryItems)
            ]);

            // Create accounting entries
            Log::info('Creating Accounting Entries', [
                'customer_ledger_id' => $request->customer_id,
                'additional_ledger_accounts' => count($additionalLedgerAccounts)
            ]);

            $this->createAccountingEntries($voucher, $inventoryItems, $tenant, $request->customer_id, $additionalLedgerAccounts);

            Log::info('Accounting Entries Created Successfully');

            // Update product stock if posted, using voucher type's inventory_effect
            if ($shouldPost) {
                Log::info('Updating Product Stock', [
                    'effect' => $voucherType->inventory_effect ?? 'decrease'
                ]);

                $effect = $voucherType->inventory_effect ?? 'decrease';
                if ($effect === 'increase' || $effect === 'decrease') {
                    $this->updateProductStock($inventoryItems, $effect, $voucher);
                    Log::info('Product Stock Updated Successfully');
                } else {
                    Log::info('Stock Update Skipped', [
                        'effect' => $effect
                    ]);
                }
                // If 'none', do not update stock
            } else {
                Log::info('Stock Update Skipped - Draft Invoice');
            }

            DB::commit();
            Log::info('Database Transaction Committed Successfully');

            // Generate payment links for customer convenience
            $this->generatePaymentLinks($voucher, $tenant, $request->customer_id);

            $message = $shouldPost
                ? 'Invoice created and posted successfully!'
                : 'Invoice saved as draft successfully!';

            Log::info('=== INVOICE STORE COMPLETED SUCCESSFULLY ===', [
                'voucher_id' => $voucher->id,
                'voucher_number' => $voucher->voucher_number,
                'status' => $voucher->status,
                'total_amount' => $voucher->total_amount,
                'message' => $message
            ]);

            // If user chose to Save, Post and open a new Sales invoice, redirect to create page with type=sv
            if ($request->action === 'save_and_post_new_sales') {
                return redirect()
                    ->route('tenant.accounting.invoices.create', ['tenant' => $tenant->slug, 'type' => 'sv'])
                    ->with('success', $message);
            }

            return redirect()
                ->route('tenant.accounting.invoices.show', ['tenant' => $tenant->slug, 'invoice' => $voucher->id])
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('=== INVOICE STORE FAILED ===', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'tenant_id' => $tenant->id,
                'user_id' => auth()->id(),
                'request_data' => $request->except(['_token'])
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while creating the invoice: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Tenant $tenant, Voucher $invoice)
    {
        // Ensure the invoice belongs to the tenant
        if ($invoice->tenant_id !== $tenant->id) {
            abort(404);
        }

        $invoice->load(['voucherType', 'entries.ledgerAccount.accountGroup', 'createdBy', 'postedBy', 'items']);

        // Get bank accounts for receipt voucher posting
        $bankAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where(function($q) {
                $q->where('code', 'LIKE', '%CASH%')
                  ->orWhere('code', 'LIKE', '%BANK%')
                  ->orWhere('name', 'LIKE', '%Cash%')
                  ->orWhere('name', 'LIKE', '%Bank%');
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get payment vouchers related to this invoice
        $invoiceReference = $invoice->voucherType->prefix . $invoice->voucher_number;
        $payments = Voucher::where('tenant_id', $tenant->id)
            ->whereHas('voucherType', function($q) {
                $q->where('code', 'RV'); // Receipt Voucher
            })
            ->where(function($q) use ($invoiceReference) {
                $q->where('narration', 'LIKE', '%' . $invoiceReference . '%')
                  ->orWhereHas('entries', function($entryQuery) use ($invoiceReference) {
                      $entryQuery->where('particulars', 'LIKE', '%' . $invoiceReference . '%');
                  });
            })
            ->with(['voucherType', 'entries.ledgerAccount', 'createdBy'])
            ->orderBy('voucher_date', 'desc')
            ->get();

        // Calculate total paid amount
        $totalPaid = $payments->sum('total_amount');
        $balanceDue = $invoice->total_amount - $totalPaid;

        // Determine Payment Status
        $paymentStatus = '';
        $paymentPercentage = 0;

        if ($invoice->total_amount > 0) {
            if ($balanceDue <= 0) {
                $paymentStatus = 'Paid';
                $paymentPercentage = 100;
            } elseif ($totalPaid > 0) {
                $paymentStatus = 'Partially Paid';
                $paymentPercentage = ($totalPaid / $invoice->total_amount) * 100;
            } else {
                $paymentStatus = 'Unpaid';
            }
        } else {
            $paymentStatus = 'Paid';
            $paymentPercentage = 100;
        }

        // Get customer info
        $customer = null;
        $customerLedger = null;

        // Find the ledger account associated with Accounts Receivable or Payable for this voucher
        $partyEntry = $invoice->entries->first(function ($entry) {
            return in_array($entry->ledgerAccount->accountGroup->code, ['AR', 'AP']);
        });

        if ($partyEntry) {
            $customerLedger = $partyEntry->ledgerAccount;
            // Determine if it's a customer or vendor and fetch the model
            if ($partyEntry->ledgerAccount->accountGroup->code === 'AR') {
                $customer = Customer::where('ledger_account_id', $customerLedger->id)->first();
            } elseif ($partyEntry->ledgerAccount->accountGroup->code === 'AP') {
                $customer = Vendor::where('ledger_account_id', $customerLedger->id)->first();
            }

            // Fallback if no model is found
            if (!$customer) {
                $customer = (object) [
                    'display_name' => $customerLedger->name,
                    'email' => $customerLedger->email,
                    'phone' => $customerLedger->phone,
                    'address_line1' => $customerLedger->address, // Assuming address is in a single field
                ];
            }
        }

        Log::info('Found bank accounts and payments for invoice', [
            'tenant_id' => $tenant->id,
            'invoice_id' => $invoice->id,
            'invoice_reference' => $invoiceReference,
            'bank_accounts_count' => $bankAccounts->count(),
            'payments_count' => $payments->count(),
            'total_paid' => $totalPaid,
            'balance_due' => $balanceDue
        ]);

        return view('tenant.accounting.invoices.show', compact(
            'tenant',
            'invoice',
            'bankAccounts',
            'payments',
            'totalPaid',
            'balanceDue',
            'paymentStatus',
            'paymentPercentage',
            'customer',
            'customerLedger'
        ));
    }

    public function edit(Tenant $tenant, Voucher $invoice)
    {
        // Ensure the invoice belongs to the tenant and is editable
        if ($invoice->tenant_id !== $tenant->id) {
            abort(404);
        }

        if ($invoice->status !== 'draft') {
            return redirect()->route('tenant.accounting.invoices.show', ['tenant' => $tenant->slug, 'invoice' => $invoice->id])
                ->with('error', 'Only draft invoices can be edited.');
        }

        $invoice->load(['items', 'entries.ledgerAccount.accountGroup']);

        // Most of this is copied from the 'create' method to ensure the view has all necessary data
        $voucherTypes = VoucherType::where('tenant_id', $tenant->id)
            ->where('affects_inventory', true)
            ->orderBy('name', 'desc')
            ->get();

        $products = Product::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->with(['primaryUnit'])
            ->orderBy('name')
            ->get();

        $customers = Customer::with('ledgerAccount')->where('tenant_id', $tenant->id)->get();
        $vendors = Vendor::with('ledgerAccount')->where('tenant_id', $tenant->id)->get();

        $ledgerAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->whereHas('accountGroup', function($query) {
                $query->whereIn('nature', ['expenses', 'income', 'liabilities', 'assets'])
                    ->whereNotIn('code', ['AR', 'AP']);
            })
            ->where('is_active', true)
            ->with('accountGroup')
            ->orderBy('name')
            ->get();

        $units = Unit::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get the party (customer/vendor) associated with the invoice
        $partyEntry = $invoice->entries->first(function ($entry) {
            return in_array($entry->ledgerAccount->accountGroup->code, ['AR', 'AP']);
        });
        $partyLedger = $partyEntry ? $partyEntry->ledgerAccount : null;

        // Extract inventory items from meta_data
        $inventoryItems = collect();
        if ($invoice->meta_data) {
            $metaData = json_decode($invoice->meta_data, true);
            if (isset($metaData['inventory_items'])) {
                $inventoryItems = collect($metaData['inventory_items']);
            }
        }

        return view('tenant.accounting.invoices.edit', compact(
            'tenant',
            'invoice',
            'voucherTypes',
            'products',
            'customers',
            'vendors',
            'ledgerAccounts',
            'units',
            'partyLedger',
            'inventoryItems'
        ));
    }

    public function update(Request $request, Tenant $tenant, Voucher $invoice)
    {
        Log::info('=== INVOICE UPDATE STARTED ===', [
            'invoice_id' => $invoice->id,
            'tenant_id' => $tenant->id,
            'user_id' => auth()->id(),
        ]);

        // Ensure the invoice belongs to the tenant and is editable
        if ($invoice->tenant_id !== $tenant->id) {
            abort(404);
        }

        if ($invoice->status !== 'draft') {
            return redirect()->back()
                ->with('error', 'Only draft invoices can be edited.');
        }

        $validator = Validator::make($request->all(), [
            'voucher_type_id' => 'required|exists:voucher_types,id',
            'voucher_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'narration' => 'nullable|string',
            'customer_id' => 'required|exists:ledger_accounts,id',
            'inventory_items' => 'required|array|min:1',
            'inventory_items.*.product_id' => 'required|exists:products,id',
            'inventory_items.*.quantity' => 'required|numeric|min:0.01',
            'inventory_items.*.rate' => 'required|numeric|min:0',
            'inventory_items.*.purchase_rate' => 'nullable|numeric|min:0',
            'inventory_items.*.description' => 'nullable|string',
            'ledger_accounts' => 'nullable|array',
            'ledger_accounts.*.ledger_account_id' => 'required_with:ledger_accounts|exists:ledger_accounts,id',
            'ledger_accounts.*.amount' => 'required_with:ledger_accounts|numeric|min:0',
            'ledger_accounts.*.narration' => 'nullable|string',
        ], [
            'customer_id.required' => 'Please select a customer or vendor.',
            'customer_id.exists' => 'The selected customer or vendor is invalid.',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation Failed', [
                'errors' => $validator->errors()->toArray()
            ]);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $shouldPost = in_array($request->action, ['save_and_post', 'save_and_post_new_sales']);

            DB::beginTransaction();
            Log::info('Database Transaction Started');

            // Get voucher type
            $voucherType = VoucherType::findOrFail($request->voucher_type_id);

            // Calculate total amount from inventory items
            $totalAmount = 0;
            $inventoryItems = [];

            foreach ($request->inventory_items as $index => $item) {
                $product = Product::findOrFail($item['product_id']);
                $amount = $item['quantity'] * $item['rate'];
                $totalAmount += $amount;

                $inventoryItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'description' => $item['description'] ?? $product->name,
                    'quantity' => $item['quantity'],
                    'rate' => $item['rate'],
                    'purchase_rate' => $item['purchase_rate'] ?? 0,
                    'amount' => $amount,
                    'discount' => 0,
                    'tax' => 0,
                    'is_tax_inclusive' => false,
                    'total' => $amount,
                ];
            }

            // Process additional ledger accounts
            $additionalLedgerAccounts = [];
            if ($request->has('ledger_accounts') && is_array($request->ledger_accounts)) {
                foreach ($request->ledger_accounts as $ledgerAccount) {
                    if (!empty($ledgerAccount['ledger_account_id']) && !empty($ledgerAccount['amount'])) {
                        $amount = floatval($ledgerAccount['amount']);
                        $totalAmount += $amount;

                        $additionalLedgerAccounts[] = [
                            'ledger_account_id' => $ledgerAccount['ledger_account_id'],
                            'amount' => $amount,
                            'narration' => $ledgerAccount['narration'] ?? '',
                        ];
                    }
                }
            }

            Log::info('Calculated Totals', [
                'total_amount' => $totalAmount,
                'inventory_items_count' => count($inventoryItems),
                'additional_ledger_accounts_count' => count($additionalLedgerAccounts)
            ]);

            // Update voucher
            $invoice->update([
                'voucher_type_id' => $request->voucher_type_id,
                'voucher_date' => $request->voucher_date,
                'reference_number' => $request->reference_number,
                'narration' => $request->narration,
                'total_amount' => $totalAmount,
                'status' => $shouldPost ? 'posted' : 'draft',
                'posted_at' => $shouldPost ? now() : null,
                'posted_by' => $shouldPost ? auth()->id() : null,
                'meta_data' => json_encode([
                    'inventory_items' => $inventoryItems,
                    'additional_ledger_accounts' => $additionalLedgerAccounts
                ])
            ]);

            Log::info('Voucher Updated', [
                'voucher_id' => $invoice->id,
                'status' => $invoice->status
            ]);

            // Delete old voucher items and create new ones
            $invoice->items()->delete();

            foreach ($inventoryItems as $item) {
                $invoice->items()->create([
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'rate' => $item['rate'],
                    'amount' => $item['amount'],
                    'voucher_id' => $invoice->id,
                    'purchase_rate' => $item['purchase_rate'] ?? 0,
                    'discount' => $item['discount'] ?? 0,
                    'tax' => $item['tax'] ?? 0,
                    'is_tax_inclusive' => $item['is_tax_inclusive'] ?? false,
                    'total' => $item['total'] ?? null,
                ]);
            }

            Log::info('Voucher Items Recreated', ['items_count' => count($inventoryItems)]);

            // Delete old accounting entries and create new ones
            $invoice->entries()->delete();
            $this->createAccountingEntries($invoice, $inventoryItems, $tenant, $request->customer_id, $additionalLedgerAccounts);

            Log::info('Accounting Entries Recreated');

            // Update product stock if posted
            if ($shouldPost) {
                $effect = $voucherType->inventory_effect ?? 'decrease';
                if ($effect === 'increase' || $effect === 'decrease') {
                    $this->updateProductStock($inventoryItems, $effect, $invoice);
                    Log::info('Product Stock Updated');
                }
            }

            DB::commit();
            Log::info('Database Transaction Committed');

            $message = $shouldPost
                ? 'Invoice updated and posted successfully!'
                : 'Invoice updated successfully!';

            Log::info('=== INVOICE UPDATE COMPLETED ===', [
                'voucher_id' => $invoice->id,
                'status' => $invoice->status,
                'message' => $message
            ]);

            // If user chose to Save, Post and open a new Sales invoice, redirect to create page with type=sv
            if ($request->action === 'save_and_post_new_sales') {
                return redirect()
                    ->route('tenant.accounting.invoices.create', ['tenant' => $tenant->slug, 'type' => 'sv'])
                    ->with('success', $message);
            }

            return redirect()
                ->route('tenant.accounting.invoices.show', ['tenant' => $tenant->slug, 'invoice' => $invoice->id])
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('=== INVOICE UPDATE FAILED ===', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'invoice_id' => $invoice->id,
                'tenant_id' => $tenant->id,
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while updating the invoice: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Tenant $tenant, Voucher $invoice)
    {
        // Ensure the invoice belongs to the tenant
        if ($invoice->tenant_id !== $tenant->id) {
            abort(404);
        }

        if ($invoice->status !== 'draft') {
            return redirect()->back()
                ->with('error', 'Only draft invoices can be deleted.');
        }

        try {
            DB::beginTransaction();

            // Delete entries
            $invoice->entries()->delete();

            // Delete invoice
            $invoice->delete();

            DB::commit();

            return redirect()
                ->route('tenant.accounting.invoices.index', ['tenant' => $tenant->slug])
                ->with('success', 'Invoice deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting invoice: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while deleting the invoice. Please try again.');
        }
    }

    public function post(Tenant $tenant, Voucher $invoice)
    {
        // Ensure the invoice belongs to the tenant
        if ($invoice->tenant_id !== $tenant->id) {
            abort(404);
        }

        if ($invoice->status !== 'draft') {
            return redirect()->back()
                ->with('error', 'Only draft invoices can be posted.');
        }

        try {
            DB::beginTransaction();

            // Post the invoice
            $invoice->update([
                'status' => 'posted',
                'posted_at' => now(),
                'posted_by' => auth()->id(),
            ]);

            // Update product stock using voucher type's inventory_effect
            if ($invoice->meta_data) {
                $metaData = json_decode($invoice->meta_data, true);
                if (isset($metaData['inventory_items'])) {
                    $effect = $invoice->voucherType->inventory_effect ?? 'decrease';
                    if ($effect === 'increase' || $effect === 'decrease') {
                        $this->updateProductStock($metaData['inventory_items'], $effect, $invoice);
                    }
                    // If 'none', do not update stock
                }
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Invoice posted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error posting invoice: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while posting the invoice. Please try again.');
        }
    }

    public function unpost(Tenant $tenant, Voucher $invoice)
    {
        // Ensure the invoice belongs to the tenant
        if ($invoice->tenant_id !== $tenant->id) {
            abort(404);
        }

        if ($invoice->status !== 'posted') {
            return redirect()->back()
                ->with('error', 'Only posted invoices can be unposted.');
        }

        try {
            DB::beginTransaction();

            // Unpost the invoice
            $invoice->update([
                'status' => 'draft',
                'posted_at' => null,
                'posted_by' => null,
            ]);

            // Reverse product stock changes using voucher type's inventory_effect
            if ($invoice->meta_data) {
                $metaData = json_decode($invoice->meta_data, true);
                if (isset($metaData['inventory_items'])) {
                    $effect = $invoice->voucherType->inventory_effect ?? 'decrease';
                    // Reverse the effect: if original was 'decrease', now 'increase', and vice versa
                    $reverseEffect = $effect === 'increase' ? 'decrease' : ($effect === 'decrease' ? 'increase' : null);
                    if ($reverseEffect) {
                        $this->updateProductStock($metaData['inventory_items'], $reverseEffect, $invoice);
                    }
                    // If 'none', do not update stock
                }
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Invoice unposted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error unposting invoice: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while unposting the invoice. Please try again.');
        }
    }

    public function print(Tenant $tenant, Voucher $invoice)
    {
        // Ensure the invoice belongs to the tenant
        if ($invoice->tenant_id !== $tenant->id) {
            abort(404);
        }

        $invoice->load(['voucherType', 'entries.ledgerAccount', 'createdBy', 'postedBy']);

        // Get inventory items from meta data
        $inventoryItems = collect();
        if ($invoice->meta_data) {
            $metaData = json_decode($invoice->meta_data, true);
            if (isset($metaData['inventory_items'])) {
                $inventoryItems = collect($metaData['inventory_items']);
            }
        }

        // Get customer info from ledger account in voucher entries
        $customer = null;
        $customerLedgerEntry = $invoice->entries->where('debit_amount', '>', 0)->first();
        if ($customerLedgerEntry && $customerLedgerEntry->ledgerAccount) {
            // Check if this ledger account belongs to a customer
            $customer = Customer::where('ledger_account_id', $customerLedgerEntry->ledgerAccount->id)->first();

            // If no customer model found, use ledger account info directly
            if (!$customer) {
                $customer = (object) [
                    'name' => $customerLedgerEntry->ledgerAccount->name,
                    'company_name' => $customerLedgerEntry->ledgerAccount->name,
                    'address' => $customerLedgerEntry->ledgerAccount->address,
                    'phone' => $customerLedgerEntry->ledgerAccount->phone,
                    'email' => $customerLedgerEntry->ledgerAccount->email,
                    'customer_type' => 'business'
                ];
            }
        }

        return view('tenant.accounting.invoices.print', compact('tenant', 'invoice', 'inventoryItems', 'customer'));
    }

    public function searchCustomers(Request $request, Tenant $tenant)
    {
        $query = trim($request->get('q', ''));

        $customersQuery = Customer::where('tenant_id', $tenant->id)
            ->with('ledgerAccount');

        if (strlen($query) >= 2) {
            $customersQuery->where(function($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('company_name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            });
        } else {
            // Default list when user has not typed anything yet
            $customersQuery->orderBy('updated_at', 'desc');
        }

        $customers = $customersQuery
            ->limit(10)
            ->get()
            ->map(function($customer) {
                $ledgerAccount = $customer->ledgerAccount;

                return [
                    'id' => $customer->id,
                    'ledger_account_id' => $ledgerAccount?->id,
                    'ledger_account_name' => $ledgerAccount?->name,
                    'display_name' => $customer->company_name ?: trim($customer->first_name . ' ' . $customer->last_name),
                    'email' => $customer->email
                ];
            })
            ->filter(function($customer) {
                return !empty($customer['ledger_account_id']);
            })
            ->values();

        return response()->json($customers);
    }

    public function searchProducts(Request $request, Tenant $tenant)
    {
        $query = trim($request->get('q', ''));

        $productsQuery = Product::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->where('is_saleable', true)
            ->with(['primaryUnit']);

        if (strlen($query) >= 2) {
            $productsQuery->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            });
        } else {
            $productsQuery->orderBy('updated_at', 'desc');
        }

        $products = $productsQuery
            ->limit(15)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'sales_rate' => $product->sales_rate,
                    'purchase_rate' => $product->purchase_rate,
                    'current_stock' => $product->current_stock,
                    'unit' => $product->primaryUnit->abbreviation ?? 'Pcs',
                    'description' => $product->description
                ];
            });

        return response()->json($products);
    }

    public function searchLedgerAccounts(Request $request, Tenant $tenant)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $ledgerAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->whereHas('accountGroup', function($q) {
                $q->whereIn('nature', ['expenses', 'income', 'liabilities', 'assets'])
                  ->whereNotIn('code', ['AR', 'AP']); // Exclude Accounts Receivable/Payable
            })
            ->where('is_active', true)
            ->with('accountGroup')
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('code', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(function($account) {
                return [
                    'id' => $account->id,
                    'name' => $account->name,
                    'code' => $account->code,
                    'account_group_name' => $account->accountGroup->name ?? '',
                    'nature' => $account->accountGroup->nature ?? '',
                    'description' => $account->description
                ];
            });

        return response()->json($ledgerAccounts);
    }private function createAccountingEntries(Voucher $voucher, array $inventoryItems, Tenant $tenant, $customerLedger_id, array $additionalLedgerAccounts = [])
{
    // Get the customer/supplier account using the ID
    $partyAccount = LedgerAccount::find($customerLedger_id);
    if (!$partyAccount) {
        throw new \Exception('Party ledger account not found.');
    }

    // Determine if this is a sales or purchase voucher
    $isSales = in_array($voucher->voucherType->name, ['Sales', 'Sales Return']);
    $isPurchase = in_array($voucher->voucherType->name, ['Purchase', 'Purchase Return']);

    $totalAmount = collect($inventoryItems)->sum('amount');

    // Add additional ledger accounts to total
    $additionalTotal = collect($additionalLedgerAccounts)->sum('amount');
    $totalAmount += $additionalTotal;

    // Group items by their ledger account (sales_account_id or purchase_account_id)
    $groupedItems = [];

    foreach ($inventoryItems as $item) {
        $product = Product::find($item['product_id']);

        if (!$product) {
            throw new \Exception("Product with ID {$item['product_id']} not found.");
        }

        // Determine which account to use based on voucher type
        $accountId = null;
        if ($isSales) {
            // For SALES: Use sales_account_id (Income account like "Sales Revenue")
            $accountId = $product->sales_account_id;
        } elseif ($isPurchase) {
            // For PURCHASE: Use stock_asset_account_id (Asset account like "Inventory")
            // This is critical: Purchases go to Balance Sheet (Asset), NOT P&L (Expense)
            $accountId = $product->stock_asset_account_id;
        }

        if (!$accountId) {
            // Fallback to default account if product doesn't have specific account
            if ($isSales) {
                $defaultAccount = LedgerAccount::where('tenant_id', $tenant->id)
                    ->where('name', 'Sales Revenue')
                    ->first();
            } else {
                // For purchase, default to Inventory asset account
                $defaultAccount = LedgerAccount::where('tenant_id', $tenant->id)
                    ->where('name', 'Inventory')
                    ->orWhere('code', 'INV')
                    ->where('account_type', 'asset')
                    ->first();
            }

            if (!$defaultAccount) {
                throw new \Exception("Product {$product->name} does not have a " .
                    ($isSales ? 'sales' : 'stock asset') . " account assigned, and no default account found.");
            }
            $accountId = $defaultAccount->id;
        }

        if (!isset($groupedItems[$accountId])) {
            $groupedItems[$accountId] = 0;
        }
        $groupedItems[$accountId] += $item['amount'];
    }

    // Create accounting entries based on voucher type
    if ($isSales) {
        // SALES INVOICE:
        // Debit: Customer Account (Accounts Receivable)
        VoucherEntry::create([
            'voucher_id' => $voucher->id,
            'ledger_account_id' => $customerLedger_id,
            'debit_amount' => $totalAmount,
            'credit_amount' => 0,
            'particulars' => 'Sales invoice - ' . $voucher->getDisplayNumber(),
        ]);

        // Credit: Product's Sales Account(s) - one entry per unique sales account
        foreach ($groupedItems as $accountId => $amount) {
            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $accountId,
                'debit_amount' => 0,
                'credit_amount' => $amount,
                'particulars' => 'Sales invoice - ' . $voucher->getDisplayNumber(),
            ]);
        }

        // Credit: Additional Ledger Accounts (VAT, Transport, etc.)
        foreach ($additionalLedgerAccounts as $additionalLedger) {
            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $additionalLedger['ledger_account_id'],
                'debit_amount' => 0,
                'credit_amount' => $additionalLedger['amount'],
                'particulars' => $additionalLedger['narration'] ?: ('Additional charge - ' . $voucher->getDisplayNumber()),
            ]);
        }

        // COGS ENTRIES: Record Cost of Goods Sold and reduce Inventory
        // Get COGS and Inventory accounts
        $cogsAccount = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('name', 'Cost of Goods Sold')
            ->orWhere('code', 'COGS')
            ->first();

        $inventoryAccount = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('name', 'Inventory')
            ->orWhere('code', 'INV')
            ->first();

        if ($cogsAccount && $inventoryAccount) {
            // Calculate total cost from inventory items
            $totalCost = 0;
            foreach ($inventoryItems as $item) {
                $purchaseRate = $item['purchase_rate'] ?? 0;
                if ($purchaseRate > 0) {
                    $totalCost += $purchaseRate * $item['quantity'];
                }
            }

            // Only create COGS entries if we have cost data
            if ($totalCost > 0) {
                // Entry: Debit COGS (Expense increases)
                VoucherEntry::create([
                    'voucher_id' => $voucher->id,
                    'ledger_account_id' => $cogsAccount->id,
                    'debit_amount' => $totalCost,
                    'credit_amount' => 0,
                    'particulars' => 'Cost of goods sold - ' . $voucher->getDisplayNumber(),
                ]);

                // Entry: Credit Inventory (Asset decreases)
                VoucherEntry::create([
                    'voucher_id' => $voucher->id,
                    'ledger_account_id' => $inventoryAccount->id,
                    'debit_amount' => 0,
                    'credit_amount' => $totalCost,
                    'particulars' => 'Inventory reduction - ' . $voucher->getDisplayNumber(),
                ]);

                Log::info('COGS entries created for sales invoice', [
                    'voucher_id' => $voucher->id,
                    'total_cost' => $totalCost,
                    'cogs_account' => $cogsAccount->name,
                    'inventory_account' => $inventoryAccount->name,
                ]);
            } else {
                Log::warning('COGS not created - no purchase rate data', [
                    'voucher_id' => $voucher->id,
                    'items_count' => count($inventoryItems),
                ]);
            }
        } else {
            Log::warning('COGS not created - accounts not found', [
                'voucher_id' => $voucher->id,
                'cogs_account_exists' => $cogsAccount !== null,
                'inventory_account_exists' => $inventoryAccount !== null,
            ]);
        }
    } elseif ($isPurchase) {
        // PURCHASE INVOICE (Proper Accounting):
        // When you buy inventory, it becomes an ASSET on the Balance Sheet
        // It does NOT affect Profit & Loss until you sell it

        // Debit: Inventory/Stock Asset Account (Balance Sheet - Asset increases)
        foreach ($groupedItems as $accountId => $amount) {
            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $accountId, // This is stock_asset_account_id from product
                'debit_amount' => $amount,
                'credit_amount' => 0,
                'particulars' => 'Purchase of inventory - ' . $voucher->getDisplayNumber(),
            ]);
        }

        // Debit: Additional Ledger Accounts (VAT, Transport, etc.)
        Log::info('Creating Purchase Additional Ledger Entries', [
            'additional_ledger_accounts_count' => count($additionalLedgerAccounts),
            'additional_ledger_accounts' => $additionalLedgerAccounts,
        ]);

        foreach ($additionalLedgerAccounts as $additionalLedger) {
            $entry = VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $additionalLedger['ledger_account_id'],
                'debit_amount' => $additionalLedger['amount'],
                'credit_amount' => 0,
                'particulars' => $additionalLedger['narration'] ?: ('Additional charge - ' . $voucher->getDisplayNumber()),
            ]);

            Log::info('Purchase Additional Entry Created', [
                'entry_id' => $entry->id,
                'ledger_account_id' => $additionalLedger['ledger_account_id'],
                'debit_amount' => $additionalLedger['amount'],
                'narration' => $additionalLedger['narration'],
            ]);
        }

        // Credit: Supplier Account (Balance Sheet - Accounts Payable increases)
        VoucherEntry::create([
            'voucher_id' => $voucher->id,
            'ledger_account_id' => $customerLedger_id, // Note: variable name is customerLedger_id but is actually supplier
            'debit_amount' => 0,
            'credit_amount' => $totalAmount,
            'particulars' => 'Purchase from supplier - ' . $voucher->getDisplayNumber(),
        ]);
    }

    // Update ledger account balances
    try {
        // Update party (customer/supplier) account balance
        Log::info('Before updating party balance', [
            'party_account_id' => $partyAccount->id,
            'current_balance_before' => $partyAccount->current_balance
        ]);

        $partyBalance = $partyAccount->updateCurrentBalance();

        Log::info('After updating party balance', [
            'party_account_id' => $partyAccount->id,
            'current_balance_after' => $partyAccount->fresh()->current_balance,
            'calculated_balance' => $partyBalance
        ]);

        // Update all affected product ledger accounts
        foreach ($groupedItems as $accountId => $amount) {
            $productAccount = LedgerAccount::find($accountId);
            if ($productAccount) {
                Log::info('Before updating product account balance', [
                    'account_id' => $productAccount->id,
                    'account_name' => $productAccount->name,
                    'current_balance_before' => $productAccount->current_balance
                ]);

                $productBalance = $productAccount->updateCurrentBalance();

                Log::info('After updating product account balance', [
                    'account_id' => $productAccount->id,
                    'account_name' => $productAccount->name,
                    'current_balance_after' => $productAccount->fresh()->current_balance,
                    'calculated_balance' => $productBalance
                ]);
            }
        }

        // Update additional ledger accounts balances
        foreach ($additionalLedgerAccounts as $additionalLedger) {
            $additionalAccount = LedgerAccount::find($additionalLedger['ledger_account_id']);
            if ($additionalAccount) {
                Log::info('Before updating additional account balance', [
                    'account_id' => $additionalAccount->id,
                    'account_name' => $additionalAccount->name,
                    'current_balance_before' => $additionalAccount->current_balance
                ]);

                $additionalBalance = $additionalAccount->updateCurrentBalance();

                Log::info('After updating additional account balance', [
                    'account_id' => $additionalAccount->id,
                    'account_name' => $additionalAccount->name,
                    'current_balance_after' => $additionalAccount->fresh()->current_balance,
                    'calculated_balance' => $additionalBalance
                ]);
            }
        }

        // Update COGS and Inventory account balances (for sales invoices)
        if ($isSales) {
            $cogsAccount = LedgerAccount::where('tenant_id', $tenant->id)
                ->where(function($query) {
                    $query->where('name', 'Cost of Goods Sold')
                          ->orWhere('code', 'COGS');
                })
                ->first();

            $inventoryAccount = LedgerAccount::where('tenant_id', $tenant->id)
                ->where(function($query) {
                    $query->where('name', 'Inventory')
                          ->orWhere('code', 'INV');
                })
                ->first();

            if ($cogsAccount) {
                $cogsAccount->updateCurrentBalance();
                Log::info('COGS account balance updated', [
                    'account_id' => $cogsAccount->id,
                    'new_balance' => $cogsAccount->fresh()->current_balance,
                ]);
            }

            if ($inventoryAccount) {
                $inventoryAccount->updateCurrentBalance();
                Log::info('Inventory account balance updated', [
                    'account_id' => $inventoryAccount->id,
                    'new_balance' => $inventoryAccount->fresh()->current_balance,
                ]);
            }
        }

        // Also update customer/vendor outstanding balance if linked
        if ($isSales) {
            $customer = Customer::where('ledger_account_id', $partyAccount->id)->first();
            if ($customer) {
                $outstandingBalance = max(0, $partyBalance);
                $customer->update(['outstanding_balance' => $outstandingBalance]);

                Log::info('Updated customer outstanding balance', [
                    'customer_id' => $customer->id,
                    'outstanding_balance' => $outstandingBalance
                ]);
            }
        } elseif ($isPurchase) {
            $vendor = Vendor::where('ledger_account_id', $partyAccount->id)->first();
            if ($vendor) {
                $outstandingBalance = max(0, abs($partyBalance)); // Vendors have credit balances
                $vendor->update(['outstanding_balance' => $outstandingBalance]);

                Log::info('Updated vendor outstanding balance', [
                    'vendor_id' => $vendor->id,
                    'outstanding_balance' => $outstandingBalance
                ]);
            }
        }

    } catch (\Exception $e) {
        Log::error('Error updating account balances: ' . $e->getMessage());
        throw $e;
    }
}

    private function updateProductStock(array $inventoryItems, string $operation, $voucher = null)
    {

        if (!$voucher) {
            throw new \Exception('Voucher is required for stock movement tracking');
        }

        foreach ($inventoryItems as $item) {
            $product = Product::find($item['product_id']);
            if ($product && $product->maintain_stock) {
                // Determine movement type based on operation
                $movementType = ($operation === 'decrease') ? 'out' : 'in';

                // Create stock movement record using the StockMovement model method
                try {
                    StockMovement::createFromVoucher($voucher, $item, $movementType);
                } catch (\Exception $e) {
                    Log::error('Error creating stock movement: ' . $e->getMessage(), [
                        'voucher_id' => $voucher->id,
                        'product_id' => $item['product_id'],
                        'operation' => $operation,
                        'item' => $item
                    ]);
                    throw $e;
                }
            }
        }
    }

    public function pdf(Tenant $tenant, Voucher $invoice)
    {
        // Ensure the invoice belongs to the tenant
        if ($invoice->tenant_id !== $tenant->id) {
            abort(404);
        }

        $invoice->load(['voucherType', 'entries.ledgerAccount', 'createdBy', 'postedBy', 'items']);

        // Determine customer info (try Customer model first, otherwise use ledger account)
        $customerLedgerEntry = $invoice->entries->where('debit_amount', '>', 0)->first();
        $customer = null;
        $customerNameForFile = 'customer';

        if ($customerLedgerEntry && $customerLedgerEntry->ledgerAccount) {
            $ledger = $customerLedgerEntry->ledgerAccount;

            // Try to find a Customer model linked to this ledger account
            $custModel = \App\Models\Customer::where('ledger_account_id', $ledger->id)->first();

            if ($custModel) {
                $customer = $custModel;
                if (!empty($custModel->company_name)) {
                    $customerNameForFile = $custModel->company_name;
                } else {
                    $fullName = trim((($custModel->first_name ?? '') . ' ' . ($custModel->last_name ?? '')));
                    $customerNameForFile = $fullName !== '' ? $fullName : ($custModel->name ?? $ledger->name ?? 'customer');
                }
            } else {
                // No Customer model; use ledger account details
                $customer = $ledger;
                $customerNameForFile = $ledger->name ?? 'customer';
            }
        } else {
            $customerNameForFile = 'walk-in-customer';
        }

        // Sanitize and slugify the customer name for filename safety
        $prefix = strtolower($customerNameForFile);
        $prefix = preg_replace('/[^a-z0-9]+/i', '-', $prefix);
        $prefix = trim($prefix, '-');
        $prefix = $prefix === '' ? 'customer' : $prefix;

        $pdf = Pdf::loadView('tenant.accounting.invoices.pdf', compact('tenant', 'invoice', 'customer'));

        $filename = $prefix . '-invoice-' . ($invoice->voucherType->prefix ?? '') . $invoice->voucher_number . '.pdf';
        return $pdf->download($filename);
    }

    public function email(Request $request, Tenant $tenant, Voucher $invoice)
    {
        // Ensure the invoice belongs to the tenant
        if ($invoice->tenant_id !== $tenant->id) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'to' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            $invoice->load(['voucherType', 'entries.ledgerAccount.accountGroup', 'createdBy', 'postedBy', 'items']);
            $customer = $invoice->entries->where('debit_amount', '>', 0)->first()?->ledgerAccount;

            // Get inventory items from meta_data
            $inventoryItems = collect();
            if ($invoice->meta_data) {
                $metaData = json_decode($invoice->meta_data, true);
                $inventoryItems = collect($metaData['inventory_items'] ?? []);
            }

            // Generate PDF
            $pdf = Pdf::loadView('tenant.accounting.invoices.pdf', compact('tenant', 'invoice', 'customer', 'inventoryItems'));

            // Generate download URL for the PDF (using public route)
            $downloadUrl = route('tenant.public.invoices.pdf', [
                'tenant' => $tenant->slug,
                'invoice' => $invoice->id
            ]);

            // Send email with PDF attachment
            Mail::send('emails.invoice', [
                'invoice' => $invoice,
                'tenant' => $tenant,
                'emailMessage' => $request->message,
                'downloadUrl' => $downloadUrl,
            ], function ($mail) use ($request, $invoice, $pdf) {
                $mail->to($request->to)
                     ->subject($request->subject)
                     ->attachData($pdf->output(), 'invoice-' . $invoice->voucher_number . '.pdf', [
                         'mime' => 'application/pdf',
                     ]);
            });

            return response()->json(['message' => 'Invoice sent successfully']);

        } catch (\Exception $e) {
            Log::error('Error sending invoice email: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to send email'], 500);
        }
    }

    public function recordPayment(Request $request, Tenant $tenant, Voucher $invoice)
    {
        Log::info('recordPayment method called', [
            'tenant_id' => $tenant->id,
            'invoice_id' => $invoice->id,
            'request_all' => $request->all()
        ]);

        // Ensure the invoice belongs to the tenant
        if ($invoice->tenant_id !== $tenant->id) {
            Log::error('Invoice does not belong to tenant');
            abort(404);
        }

        if ($invoice->status !== 'posted') {
            Log::error('Invoice is not posted', ['status' => $invoice->status]);
            return response()->json(['message' => 'Only posted invoices can receive payments'], 422);
        }

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->total_amount,
            'bank_account_id' => 'required|exists:ledger_accounts,id',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', ['errors' => $validator->errors()]);
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        Log::info('Validation passed');

        try {
            DB::beginTransaction();

            Log::info('Recording payment for invoice', [
                'invoice_id' => $invoice->id,
                'tenant_id' => $tenant->id,
                'request_data' => $request->all()
            ]);

            // Get receipt voucher type
            $receiptVoucherType = VoucherType::where('tenant_id', $tenant->id)
                ->where('code', 'RV')
                ->first();

            if (!$receiptVoucherType) {
                Log::error('Receipt voucher type not found', ['tenant_id' => $tenant->id]);
                throw new \Exception('Receipt voucher type not found. Please create it first.');
            }

            Log::info('Found receipt voucher type', ['voucher_type_id' => $receiptVoucherType->id]);

            // Get bank account
            $bankAccount = LedgerAccount::findOrFail($request->bank_account_id);
            Log::info('Found bank account', ['bank_account' => $bankAccount->toArray()]);

            // Get customer account from the original invoice
            $customerAccount = $invoice->entries->where('debit_amount', '>', 0)->first()?->ledgerAccount;

            if (!$customerAccount) {
                throw new \Exception('Customer account not found in invoice entries');
            }

            // Generate voucher number for receipt
            $lastReceipt = Voucher::where('tenant_id', $tenant->id)
                ->where('voucher_type_id', $receiptVoucherType->id)
                ->latest('id')
                ->first();

            $nextNumber = $lastReceipt ? $lastReceipt->voucher_number + 1 : 1;

            // Create receipt voucher
            $voucherData = [
                'tenant_id' => $tenant->id,
                'voucher_type_id' => $receiptVoucherType->id,
                'voucher_number' => $nextNumber,
                'voucher_date' => $request->date,
                'reference_number' => $request->reference,
                'narration' => $request->notes ?? 'Payment received for Invoice ' . $invoice->voucherType->prefix . $invoice->voucher_number,
                'total_amount' => $request->amount,
                'status' => 'posted',
                'created_by' => auth()->id(),
                'posted_at' => now(),
                'posted_by' => auth()->id(),
            ];

            Log::info('Creating receipt voucher with data', $voucherData);

            $receiptVoucher = Voucher::create($voucherData);

            Log::info('Receipt voucher created', ['voucher_id' => $receiptVoucher->id]);

            // Create accounting entries for receipt
            // Debit: Bank/Cash Account
            $debitEntry = [
                'voucher_id' => $receiptVoucher->id,
                'ledger_account_id' => $bankAccount->id,
                'debit_amount' => $request->amount,
                'credit_amount' => 0,
                'particulars' => 'Payment received from ' . $customerAccount->name,
            ];

            Log::info('Creating debit entry', $debitEntry);
            VoucherEntry::create($debitEntry);

            // Credit: Customer Account (reducing their outstanding balance)
            $creditEntry = [
                'voucher_id' => $receiptVoucher->id,
                'ledger_account_id' => $customerAccount->id,
                'debit_amount' => 0,
                'credit_amount' => $request->amount,
                'particulars' => 'Payment received against Invoice ' . $invoice->voucherType->prefix . $invoice->voucher_number,
            ];

            Log::info('Creating credit entry', $creditEntry);
            VoucherEntry::create($creditEntry);

            // Force refresh the models to get latest data
            $bankAccount = $bankAccount->fresh();
            $customerAccount = $customerAccount->fresh();

            // Explicitly update ledger account balances
            try {
                Log::info('Before updating bank account balance', [
                    'bank_account_id' => $bankAccount->id,
                    'current_balance_before' => $bankAccount->current_balance
                ]);

                // Manual calculation for bank account (asset type)
                $bankTotalDebits = $bankAccount->voucherEntries()
                    ->whereHas('voucher', function($q) {
                        $q->where('status', 'posted');
                    })->sum('debit_amount');

                $bankTotalCredits = $bankAccount->voucherEntries()
                    ->whereHas('voucher', function($q) {
                        $q->where('status', 'posted');
                    })->sum('credit_amount');

                $bankBalance = $bankAccount->opening_balance + $bankTotalDebits - $bankTotalCredits;

                // Force update the bank account balance
                $bankAccount->update([
                    'current_balance' => $bankBalance,
                    'last_transaction_date' => $request->date
                ]);

                Log::info('After updating bank account balance', [
                    'bank_account_id' => $bankAccount->id,
                    'current_balance_after' => $bankAccount->fresh()->current_balance,
                    'calculated_balance' => $bankBalance,
                    'total_debits' => $bankTotalDebits,
                    'total_credits' => $bankTotalCredits
                ]);

                Log::info('Before updating customer account balance', [
                    'customer_account_id' => $customerAccount->id,
                    'current_balance_before' => $customerAccount->current_balance
                ]);

                // Manual calculation for customer account (asset type)
                $customerTotalDebits = $customerAccount->voucherEntries()
                    ->whereHas('voucher', function($q) {
                        $q->where('status', 'posted');
                    })->sum('debit_amount');

                $customerTotalCredits = $customerAccount->voucherEntries()
                    ->whereHas('voucher', function($q) {
                        $q->where('status', 'posted');
                    })->sum('credit_amount');

                $customerBalance = $customerAccount->opening_balance + $customerTotalDebits - $customerTotalCredits;

                // Force update the customer account balance
                $customerAccount->update([
                    'current_balance' => $customerBalance,
                    'last_transaction_date' => $request->date
                ]);

                Log::info('After updating customer account balance', [
                    'customer_account_id' => $customerAccount->id,
                    'current_balance_after' => $customerAccount->fresh()->current_balance,
                    'calculated_balance' => $customerBalance,
                    'total_debits' => $customerTotalDebits,
                    'total_credits' => $customerTotalCredits
                ]);

                // Update customer outstanding balance
                $customer = Customer::where('ledger_account_id', $customerAccount->id)->first();
                if ($customer) {
                    $outstandingBalance = max(0, $customerBalance); // Only positive balances are outstanding

                    // Force update customer outstanding balance
                    $customer->update(['outstanding_balance' => $outstandingBalance]);

                    Log::info('Updated customer outstanding balance', [
                        'customer_id' => $customer->id,
                        'outstanding_balance_before' => $customer->getOriginal('outstanding_balance'),
                        'outstanding_balance_after' => $customer->fresh()->outstanding_balance,
                        'ledger_balance' => $customerBalance
                    ]);
                } else {
                    Log::warning('Customer not found for ledger account', [
                        'ledger_account_id' => $customerAccount->id
                    ]);
                }

            } catch (\Exception $e) {
                Log::error('Error updating account balances: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
                // Don't throw here, as the main transaction should still succeed
            }

            DB::commit();            Log::info('Payment recording completed successfully');

            return response()->json(['message' => 'Payment recorded successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error recording payment: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to record payment: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Generate payment links (Nomba and Paystack) for the invoice
     */
    private function generatePaymentLinks(Voucher $voucher, Tenant $tenant, $customerLedgerId)
    {
        try {
            // Get customer email
            $customerLedger = LedgerAccount::find($customerLedgerId);
            if (!$customerLedger) {
                Log::warning('Customer ledger not found for payment link generation', [
                    'ledger_id' => $customerLedgerId
                ]);
                return;
            }

            // Try to get customer/vendor email
            $customer = Customer::where('ledger_account_id', $customerLedgerId)->first();
            $vendor = Vendor::where('ledger_account_id', $customerLedgerId)->first();

            $party = $customer ?? $vendor;
            $email = $party->email ?? null;

            if (!$email) {
                Log::info('No email found for payment link generation', [
                    'voucher_id' => $voucher->id
                ]);
                // Don't generate links if no email
                return;
            }

            $paymentLinks = [];
            $invoiceReference = $voucher->voucherType->prefix . $voucher->voucher_number;
            $callbackUrl = route('invoice.payment.callback', [
                'invoice' => $voucher->id
            ]);

            // Generate Nomba payment link
            try {
                $nombaHelper = new PaymentHelper();
                $nombaToken = $nombaHelper->nombaAccessToken();

                if ($nombaToken) {
                    $nombaResult = $nombaHelper->processPayment(
                        $voucher->total_amount,
                        'NGN',
                        $email,
                        $callbackUrl,
                        'INV_' . $invoiceReference
                    );

                    if ($nombaResult['status'] && isset($nombaResult['checkoutLink'])) {
                        $paymentLinks['nomba'] = [
                            'checkout_link' => $nombaResult['checkoutLink'],
                            'reference' => $nombaResult['orderReference'],
                            'amount' => $nombaResult['amount'],
                            'currency' => $nombaResult['currency']
                        ];

                        Log::info('Nomba payment link generated', [
                            'voucher_id' => $voucher->id,
                            'link' => $nombaResult['checkoutLink']
                        ]);
                    }
                } else {
                    Log::info('Nomba not configured or credentials missing');
                }
            } catch (\Exception $e) {
                Log::warning('Failed to generate Nomba payment link', [
                    'voucher_id' => $voucher->id,
                    'error' => $e->getMessage()
                ]);
            }

            // Generate Paystack payment link
            try {
                $paystackHelper = new PaystackPaymentHelper();

                if ($paystackHelper->isConfigured()) {
                    $paystackResult = $paystackHelper->initializeTransaction(
                        $voucher->total_amount,
                        $email,
                        $callbackUrl,
                        'INV_' . $invoiceReference,
                        [
                            'invoice_number' => $invoiceReference,
                            'voucher_id' => $voucher->id,
                            'tenant_id' => $tenant->id,
                            'customer_name' => $party->display_name ?? $customerLedger->name
                        ]
                    );

                    if ($paystackResult['status'] && isset($paystackResult['authorization_url'])) {
                        $paymentLinks['paystack'] = [
                            'authorization_url' => $paystackResult['authorization_url'],
                            'reference' => $paystackResult['reference'],
                            'access_code' => $paystackResult['access_code']
                        ];

                        Log::info('Paystack payment link generated', [
                            'voucher_id' => $voucher->id,
                            'link' => $paystackResult['authorization_url']
                        ]);
                    }
                } else {
                    Log::info('Paystack not configured');
                }
            } catch (\Exception $e) {
                Log::warning('Failed to generate Paystack payment link', [
                    'voucher_id' => $voucher->id,
                    'error' => $e->getMessage()
                ]);
            }

            // Store payment links in voucher meta_data
            if (!empty($paymentLinks)) {
                // Ensure meta_data is an array (decode if it's JSON string)
                $metaData = $voucher->meta_data;
                if (is_string($metaData)) {
                    $metaData = json_decode($metaData, true) ?? [];
                } elseif (!is_array($metaData)) {
                    $metaData = [];
                }

                $metaData['payment_links'] = $paymentLinks;
                $metaData['payment_links_generated_at'] = now()->toDateTimeString();

                $voucher->update(['meta_data' => $metaData]);

                Log::info('Payment links stored in voucher', [
                    'voucher_id' => $voucher->id,
                    'links_count' => count($paymentLinks)
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Payment link generation failed', [
                'voucher_id' => $voucher->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Don't throw - payment links are optional enhancement
        }
    }
}
