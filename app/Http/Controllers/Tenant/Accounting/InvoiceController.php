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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index(Request $request, Tenant $tenant)
    {
        $query = Voucher::where('tenant_id', $tenant->id)
            ->whereHas('voucherType', function($q) {
                $q->where('affects_inventory', true);
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
            $item->display_name = 'Customer - ' . $getName($item, 'customer');
            $item->type = 'customer';
        });

        $vendors->each(function($item) use ($getName) {
            $item->display_name = 'Vendor - ' . $getName($item, 'vendor');
            $item->type = 'vendor';
        });

        // Merge both collections
        $customers = $buyers->concat($vendors);

        // Get default sales voucher type
        $selectedType = $voucherTypes->where('code', 'SALES')->first() ?? $voucherTypes->first();

        return view('tenant.accounting.invoices.create', compact(
            'tenant',
            'voucherTypes',
            'products',
            'customers',
            'selectedType'
        ));
    }

    public function store(Request $request, Tenant $tenant)
    {
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
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Get voucher type
            $voucherType = VoucherType::findOrFail($request->voucher_type_id);

            // Calculate total amount
            $totalAmount = 0;
            $inventoryItems = [];

            foreach ($request->inventory_items as $item) {
                $product = Product::findOrFail($item['product_id']);
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
            }

            // Generate voucher number
            $lastVoucher = Voucher::where('tenant_id', $tenant->id)
                ->where('voucher_type_id', $voucherType->id)
                ->latest('id')
                ->first();

            $nextNumber = $lastVoucher ? $lastVoucher->voucher_number + 1 : 1;

            // Create voucher
            $voucher = Voucher::create([
                'tenant_id' => $tenant->id,
                'voucher_type_id' => $voucherType->id,
                'voucher_number' => $nextNumber,
                'voucher_date' => $request->voucher_date,
                'reference_number' => $request->reference_number,
                'narration' => $request->narration,
                'total_amount' => $totalAmount,
                'status' => $request->action === 'save_and_post' ? 'posted' : 'draft',
                'created_by' => auth()->id(),
                'posted_at' => $request->action === 'save_and_post' ? now() : null,
                'posted_by' => $request->action === 'save_and_post' ? auth()->id() : null,
                'meta_data' => json_encode(['inventory_items' => $inventoryItems]),
            ]);

          foreach ($inventoryItems as $item) {
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

            // Create accounting entries
            $this->createAccountingEntries($voucher, $inventoryItems, $tenant, $request->customer_id);

            // Update product stock if posted, using voucher type's inventory_effect
            if ($request->action === 'save_and_post') {
                $effect = $voucherType->inventory_effect ?? 'decrease';
                if ($effect === 'increase' || $effect === 'decrease') {
                    $this->updateProductStock($inventoryItems, $effect);
                }
                // If 'none', do not update stock
            }

            DB::commit();

            $message = $request->action === 'save_and_post'
                ? 'Invoice created and posted successfully!'
                : 'Invoice saved as draft successfully!';

            return redirect()
                ->route('tenant.accounting.invoices.show', ['tenant' => $tenant->slug, 'invoice' => $voucher->id])
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating invoice: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while creating the invoice. Please try again.')
                ->withInput();
        }
    }

    public function show(Tenant $tenant, Voucher $invoice)
    {
        // Ensure the invoice belongs to the tenant
        if ($invoice->tenant_id !== $tenant->id) {
            abort(404);
        }

        $invoice->load(['voucherType', 'entries.ledgerAccount', 'createdBy', 'postedBy', 'items']);

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

        Log::info('Found bank accounts and payments for invoice', [
            'tenant_id' => $tenant->id,
            'invoice_id' => $invoice->id,
            'invoice_reference' => $invoiceReference,
            'bank_accounts_count' => $bankAccounts->count(),
            'payments_count' => $payments->count(),
            'total_paid' => $totalPaid,
            'balance_due' => $balanceDue
        ]);

        return view('tenant.accounting.invoices.show', compact('tenant', 'invoice', 'bankAccounts', 'payments', 'totalPaid', 'balanceDue'));
    }

    public function edit(Tenant $tenant, Voucher $invoice)
    {
        // Ensure the invoice belongs to the tenant and is editable
        if ($invoice->tenant_id !== $tenant->id) {
            abort(404);
        }

        if ($invoice->status !== 'draft') {
            return redirect()->back()
                ->with('error', 'Only draft invoices can be edited.');
        }

        // Get voucher types
        $voucherTypes = VoucherType::where('tenant_id', $tenant->id)
            ->where('affects_inventory', true)
            ->where('code', 'LIKE', '%SALES%')
            ->orderBy('name')
            ->get();

        // Get products
        $products = Product::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->where('is_saleable', true)
            ->with(['primaryUnit'])
            ->orderBy('name')
            ->get();

        // Get customers
        $customers = collect();

        // Get inventory items
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
            'inventoryItems'
        ));
    }

    public function update(Request $request, Tenant $tenant, Voucher $invoice)
    {
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
            'customer_id' => 'nullable|exists:customers,id',
            'inventory_items' => 'required|array|min:1',
            'inventory_items.*.product_id' => 'required|exists:products,id',
            'inventory_items.*.quantity' => 'required|numeric|min:0.01',
            'inventory_items.*.rate' => 'required|numeric|min:0',
            'inventory_items.*.description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Calculate total amount
            $totalAmount = 0;
            $inventoryItems = [];

            foreach ($request->inventory_items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $amount = $item['quantity'] * $item['rate'];
                $totalAmount += $amount;

                $inventoryItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'description' => $item['description'] ?? $product->name,
                    'quantity' => $item['quantity'],
                    'rate' => $item['rate'],
                    'amount' => $amount,
                ];
            }

            // Update voucher
            $invoice->update([
                'voucher_type_id' => $request->voucher_type_id,
                'voucher_date' => $request->voucher_date,
                'reference_number' => $request->reference_number,
                'narration' => $request->narration,
                'total_amount' => $totalAmount,
                'status' => $request->action === 'save_and_post' ? 'posted' : 'draft',
                'posted_at' => $request->action === 'save_and_post' ? now() : null,
                'posted_by' => $request->action === 'save_and_post' ? auth()->id() : null,
                'meta_data' => json_encode(['inventory_items' => $inventoryItems])
            ]);

            // Delete old entries and create new ones
            $invoice->entries()->delete();
            $this->createAccountingEntries($invoice, $inventoryItems, $tenant);

            // Update product stock if posted
            if ($request->action === 'save_and_post') {
                $this->updateProductStock($inventoryItems, 'decrease');
            }

            DB::commit();

            $message = $request->action === 'save_and_post'
                ? 'Invoice updated and posted successfully!'
                : 'Invoice updated successfully!';

            return redirect()
                ->route('tenant.accounting.invoices.show', ['tenant' => $tenant->slug, 'invoice' => $invoice->id])
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating invoice: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while updating the invoice. Please try again.')
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
            \Log::error('Error deleting invoice: ' . $e->getMessage());

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
                        $this->updateProductStock($metaData['inventory_items'], $effect);
                    }
                    // If 'none', do not update stock
                }
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Invoice posted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error posting invoice: ' . $e->getMessage());

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
                        $this->updateProductStock($metaData['inventory_items'], $reverseEffect);
                    }
                    // If 'none', do not update stock
                }
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Invoice unposted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error unposting invoice: ' . $e->getMessage());

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

private function createAccountingEntries(Voucher $voucher, array $inventoryItems, Tenant $tenant, $customerLedger_id)
{
    // Get default accounts
    $salesAccount = LedgerAccount::where('tenant_id', $tenant->id)
        ->where('name', 'LIKE', '%Sales%')
        ->first();

    if (!$salesAccount || !$customerLedger_id) {
        throw new \Exception('Required ledger accounts (Sales, and customer ledger id) not found. Please create them first.');
    }

    // Get the customer account using the ID
    $customerAccount = LedgerAccount::find($customerLedger_id);
    if (!$customerAccount) {
        throw new \Exception('Customer ledger account not found.');
    }

    $totalAmount = collect($inventoryItems)->sum('amount');

    // Debit: Customer Account (Accounts Receivable)
    VoucherEntry::create([
        'voucher_id' => $voucher->id,
        'ledger_account_id' => $customerLedger_id,
        'debit_amount' => $totalAmount,
        'credit_amount' => 0,
        'particulars' => 'Sales invoice - ' . $voucher->getDisplayNumber(),
    ]);

    // Credit: Sales Account
    VoucherEntry::create([
        'voucher_id' => $voucher->id,
        'ledger_account_id' => $salesAccount->id,
        'debit_amount' => 0,
        'credit_amount' => $totalAmount,
        'particulars' => 'Sales invoice - ' . $voucher->getDisplayNumber(),
    ]);

    // Explicitly update ledger account balances and last transaction date
    try {
        Log::info('Before updating customer balance', [
            'customer_account_id' => $customerAccount->id,
            'current_balance_before' => $customerAccount->current_balance
        ]);

        $customerBalance = $customerAccount->updateCurrentBalance();

        Log::info('After updating customer balance', [
            'customer_account_id' => $customerAccount->id,
            'current_balance_after' => $customerAccount->fresh()->current_balance,
            'calculated_balance' => $customerBalance
        ]);

        Log::info('Before updating sales balance', [
            'sales_account_id' => $salesAccount->id,
            'current_balance_before' => $salesAccount->current_balance
        ]);

        $salesBalance = $salesAccount->updateCurrentBalance();

        Log::info('After updating sales balance', [
            'sales_account_id' => $salesAccount->id,
            'current_balance_after' => $salesAccount->fresh()->current_balance,
            'calculated_balance' => $salesBalance
        ]);

        // Manual backup calculation if the automatic update didn't work
        if ($customerAccount->fresh()->current_balance == $customerAccount->opening_balance) {
            // Calculate manually for customer account (asset type)
            $totalDebits = $customerAccount->voucherEntries()->sum('debit_amount');
            $totalCredits = $customerAccount->voucherEntries()->sum('credit_amount');
            $manualBalance = $customerAccount->opening_balance + $totalDebits - $totalCredits;

            $customerAccount->update(['current_balance' => $manualBalance]);

            Log::info('Manual customer balance update', [
                'manual_balance' => $manualBalance,
                'total_debits' => $totalDebits,
                'total_credits' => $totalCredits
            ]);
        }

        if ($salesAccount->fresh()->current_balance == $salesAccount->opening_balance) {
            // Calculate manually for sales account (income type)
            $totalDebits = $salesAccount->voucherEntries()->sum('debit_amount');
            $totalCredits = $salesAccount->voucherEntries()->sum('credit_amount');
            $manualBalance = $salesAccount->opening_balance + $totalCredits - $totalDebits;

            $salesAccount->update(['current_balance' => $manualBalance]);

            Log::info('Manual sales balance update', [
                'manual_balance' => $manualBalance,
                'total_debits' => $totalDebits,
                'total_credits' => $totalCredits
            ]);
        }

        Log::info('After updating sales balance', [
            'sales_account_id' => $salesAccount->id,
            'current_balance_after' => $salesAccount->fresh()->current_balance,
            'calculated_balance' => $salesBalance
        ]);

        // Also update customer outstanding balance if linked
        $customer = Customer::where('ledger_account_id', $customerAccount->id)->first();
        if ($customer) {
            $outstandingBalance = max(0, $customerBalance);
            $customer->update(['outstanding_balance' => $outstandingBalance]);

            Log::info('Updated customer outstanding balance', [
                'customer_id' => $customer->id,
                'outstanding_balance' => $outstandingBalance
            ]);
        }

    } catch (\Exception $e) {
        Log::error('Error updating account balances: ' . $e->getMessage());
        throw $e;
    }

    // If you want to track Cost of Goods Sold (COGS), add those entries here
    // This would require tracking purchase costs of products
}

    private function updateProductStock(array $inventoryItems, string $operation)
    {
        foreach ($inventoryItems as $item) {
            $product = Product::find($item['product_id']);
            if ($product && $product->maintain_stock) {
                $quantity = $item['quantity'];

                if ($operation === 'decrease') {
                    $product->decrement('current_stock', $quantity);
                } else {
                    $product->increment('current_stock', $quantity);
                }

                // Update stock value
                $product->current_stock_value = $product->current_stock * $product->purchase_rate;
                $product->save();
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

        // Get customer info if available
        $customer = $invoice->entries->where('debit_amount', '>', 0)->first()?->ledgerAccount;

        $pdf = Pdf::loadView('tenant.accounting.invoices.pdf', compact('tenant', 'invoice', 'customer'));

        return $pdf->download('invoice-' . $invoice->voucherType->prefix . $invoice->voucher_number . '.pdf');
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
            $invoice->load(['voucherType', 'entries.ledgerAccount', 'createdBy', 'postedBy', 'items']);
            $customer = $invoice->entries->where('debit_amount', '>', 0)->first()?->ledgerAccount;

            // Generate PDF
            $pdf = Pdf::loadView('tenant.accounting.invoices.pdf', compact('tenant', 'invoice', 'customer'));

            // Send email with PDF attachment
            Mail::send('emails.invoice', [
                'invoice' => $invoice,
                'tenant' => $tenant,
                'message' => $request->message,
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
}
