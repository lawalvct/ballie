<?php

namespace App\Http\Controllers\Tenant\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Order;
use App\Models\Voucher;
use App\Models\VoucherType;
use App\Models\VoucherEntry;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderManagementController extends Controller
{
    public function index(Request $request)
    {
        $tenant = tenant();

        $query = Order::where('tenant_id', $tenant->id)
            ->with(['customer', 'items']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_email', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(20);

        // Statistics
        $stats = [
            'total' => Order::where('tenant_id', $tenant->id)->count(),
            'pending' => Order::where('tenant_id', $tenant->id)->where('status', 'pending')->count(),
            'processing' => Order::where('tenant_id', $tenant->id)->where('status', 'processing')->count(),
            'delivered' => Order::where('tenant_id', $tenant->id)->where('status', 'delivered')->count(),
            'total_revenue' => Order::where('tenant_id', $tenant->id)->where('payment_status', 'paid')->sum('total_amount'),
        ];

        return view('tenant.ecommerce.orders.index', compact('tenant', 'orders', 'stats'));
    }

    public function show(Tenant $tenant, Order $order)
    {
        // Ensure the order belongs to the current tenant
        if ($order->tenant_id !== $tenant->id) {
            abort(404);
        }

        $order->load(['customer', 'items.product', 'shippingAddress', 'billingAddress', 'voucher']);

        return view('tenant.ecommerce.orders.show', compact('tenant', 'order'));
    }

    public function updateStatus(Request $request, Tenant $tenant, Order $order)
    {
        // Ensure the order belongs to the current tenant
        if ($order->tenant_id !== $tenant->id) {
            abort(404);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'admin_notes' => 'nullable|string'
        ]);

        $oldStatus = $order->status;

        $order->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? $order->admin_notes,
            'fulfilled_at' => $validated['status'] === 'delivered' ? now() : $order->fulfilled_at,
            'cancelled_at' => $validated['status'] === 'cancelled' ? now() : $order->cancelled_at,
        ]);

        // If confirmed and no invoice exists, create one
        if ($validated['status'] === 'confirmed' && !$order->voucher_id) {
            try {
                $this->createInvoiceFromOrder($order, $tenant);
            } catch (\Exception $e) {
                Log::error('Failed to create invoice from order: ' . $e->getMessage());
            }
        }

        // TODO: Send notification email to customer
        // Mail::to($order->customer_email)->send(new OrderStatusUpdated($order));

        return redirect()->back()->with('success', "Order status updated from {$oldStatus} to {$validated['status']}!");
    }

    public function updatePaymentStatus(Request $request, Tenant $tenant, Order $order)
    {
        // Ensure the order belongs to the current tenant
        if ($order->tenant_id !== $tenant->id) {
            abort(404);
        }

        $validated = $request->validate([
            'payment_status' => 'required|in:unpaid,paid,partially_paid,refunded',
            'payment_date' => 'nullable|date',
            'payment_reference' => 'nullable|string|max:255',
            'bank_account_id' => 'nullable|exists:ledger_accounts,id',
            'payment_notes' => 'nullable|string'
        ]);

        $oldPaymentStatus = $order->payment_status;
        $order->update(['payment_status' => $validated['payment_status']]);

        // If payment status changed to "paid" and invoice exists, create receipt voucher
        if ($validated['payment_status'] === 'paid' && $oldPaymentStatus !== 'paid' && $order->voucher_id) {
            try {
                DB::beginTransaction();

                $invoice = Voucher::find($order->voucher_id);

                if ($invoice && $invoice->status === 'posted') {
                    $this->createReceiptVoucher($order, $invoice, $tenant, $validated);
                    Log::info('Receipt voucher created for order payment', [
                        'order_id' => $order->id,
                        'invoice_id' => $invoice->id
                    ]);
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Failed to create receipt voucher for order payment: ' . $e->getMessage(), [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Don't fail the payment status update, just log the error
            }
        }

        return redirect()->back()->with('success', 'Payment status updated successfully!');
    }

    public function createInvoice(Request $request, Tenant $tenant, Order $order)
    {
        // Ensure the order belongs to the current tenant
        if ($order->tenant_id !== $tenant->id) {
            abort(404);
        }

        $order->load('items.product');

        if ($order->voucher_id) {
            return redirect()->back()->with('error', 'Invoice already created for this order!');
        }

        try {
            DB::beginTransaction();

            $voucher = $this->createInvoiceFromOrder($order, $tenant);

            DB::commit();

            return redirect()->route('tenant.accounting.invoices.show', [
                'tenant' => $tenant->slug,
                'invoice' => $voucher->id
            ])->with('success', 'Invoice created successfully from order!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create invoice from order: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Failed to create invoice. Please try again.');
        }
    }

    private function createInvoiceFromOrder($order, $tenant)
    {
        Log::info('Creating invoice from order', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'tenant_id' => $tenant->id
        ]);

        // Get Sales Invoice voucher type - try 'SALES' first, then 'SV'
        $voucherType = VoucherType::where('tenant_id', $tenant->id)
            ->where(function($q) {
                $q->where('code', 'SALES')->orWhere('code', 'SV');
            })
            ->where('affects_inventory', true)
            ->first();

        if (!$voucherType) {
            throw new \Exception('Sales voucher type not found. Please create a Sales Invoice voucher type first.');
        }

        Log::info('Voucher type found', [
            'voucher_type_id' => $voucherType->id,
            'voucher_type_code' => $voucherType->code,
            'voucher_type_name' => $voucherType->name
        ]);

        // Generate voucher number
        $lastVoucher = Voucher::where('tenant_id', $tenant->id)
            ->where('voucher_type_id', $voucherType->id)
            ->whereYear('voucher_date', date('Y'))
            ->latest('id')
            ->first();

        $nextNumber = 1;
        if ($lastVoucher) {
            // Extract the numeric part from the last voucher number
            preg_match('/(\d+)$/', $lastVoucher->voucher_number, $matches);
            if (isset($matches[1])) {
                $nextNumber = intval($matches[1]) + 1;
            }
        }

        $voucherNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        Log::info('Voucher number generated', [
            'voucher_number' => $voucherNumber,
            'last_voucher_number' => $lastVoucher ? $lastVoucher->voucher_number : null
        ]);

        // Prepare inventory items array for accounting entries
        $inventoryItems = [];
        $totalAmount = 0;

        foreach ($order->items as $orderItem) {
            $product = $orderItem->product;

            if (!$product) {
                Log::warning('Product not found for order item', [
                    'order_item_id' => $orderItem->id,
                    'product_id' => $orderItem->product_id
                ]);
                continue;
            }

            $itemAmount = $orderItem->total_price;
            $totalAmount += $itemAmount;

            $inventoryItems[] = [
                'product_id' => $product->id,
                'product_name' => $orderItem->product_name,
                'description' => $orderItem->product_name,
                'quantity' => $orderItem->quantity,
                'rate' => $orderItem->unit_price,
                'amount' => $itemAmount,
                'purchase_rate' => $product->purchase_rate ?? 0,
            ];
        }

        // Create voucher
        $voucher = Voucher::create([
            'tenant_id' => $tenant->id,
            'voucher_type_id' => $voucherType->id,
            'voucher_number' => $voucherNumber,
            'voucher_date' => now()->toDateString(),
            'reference' => 'Order #' . $order->order_number,
            'narration' => 'Sales invoice generated from e-commerce order #' . $order->order_number,
            'status' => 'posted', // Auto-post for confirmed orders
            'total_amount' => $totalAmount,
            'created_by' => auth()->id(),
            'posted_by' => auth()->id(), // Admin who converted order to invoice
            'posted_at' => now(),
        ]);

        Log::info('Voucher created', [
            'voucher_id' => $voucher->id,
            'voucher_number' => $voucher->voucher_number,
            'total_amount' => $totalAmount
        ]);

        // Create voucher items
        foreach ($inventoryItems as $item) {
            $voucher->items()->create([
                'tenant_id' => $tenant->id,
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'rate' => $item['rate'],
                'amount' => $item['amount'],
                'purchase_rate' => $item['purchase_rate'],
            ]);
        }

        Log::info('Voucher items created', ['items_count' => count($inventoryItems)]);

        // Get customer ledger account
        $customerLedgerId = null;
        if ($order->customer && $order->customer->ledger_account_id) {
            $customerLedgerId = $order->customer->ledger_account_id;
        }

        if (!$customerLedgerId) {
            Log::warning('No customer ledger account found for order', [
                'order_id' => $order->id,
                'customer_id' => $order->customer_id
            ]);
        }

        // Create accounting entries using the same logic as InvoiceController
        // Pass tax amount from order for VAT Output recording
        $this->createAccountingEntries($voucher, $inventoryItems, $tenant, $customerLedgerId, $order->tax_amount ?? 0);

        Log::info('Accounting entries created');

        // Update product stock - using 'decrease' effect for sales
        $this->updateProductStock($inventoryItems, 'decrease', $voucher);

        Log::info('Product stock updated');

        // Link voucher to order
        $order->update(['voucher_id' => $voucher->id]);

        Log::info('Invoice created successfully from order', [
            'voucher_id' => $voucher->id,
            'order_id' => $order->id
        ]);

        return $voucher;
    }

    private function createAccountingEntries($voucher, $inventoryItems, $tenant, $customerLedgerId, $taxAmount = 0)
    {
        // Get the customer account
        $partyAccount = null;
        if ($customerLedgerId) {
            $partyAccount = \App\Models\LedgerAccount::find($customerLedgerId);
        }

        $totalAmount = collect($inventoryItems)->sum('amount');

        // Add tax to total amount (customer owes subtotal + tax)
        $totalAmount += $taxAmount;

        // Group items by their sales account
        $groupedItems = [];
        foreach ($inventoryItems as $item) {
            $product = Product::find($item['product_id']);

            if (!$product) {
                continue;
            }

            $accountId = $product->sales_account_id;

            if (!$accountId) {
                Log::warning('Product has no sales account', [
                    'product_id' => $product->id,
                    'product_name' => $product->name
                ]);
                continue;
            }

            if (!isset($groupedItems[$accountId])) {
                $groupedItems[$accountId] = 0;
            }
            $groupedItems[$accountId] += $item['amount'];
        }

        // SALES INVOICE:
        // Debit: Customer Account (Accounts Receivable)
        if ($partyAccount) {
            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $partyAccount->id,
                'debit_amount' => $totalAmount,
                'credit_amount' => 0,
                'particulars' => 'Sales invoice - ' . $voucher->voucher_number,
            ]);

            // Update customer account balance
            $partyAccount->updateCurrentBalance();
        }

        // Credit: Product's Sales Account(s)
        foreach ($groupedItems as $accountId => $amount) {
            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $accountId,
                'debit_amount' => 0,
                'credit_amount' => $amount,
                'particulars' => 'Sales - ' . $voucher->voucher_number,
            ]);

            // Update ledger account balance
            $ledgerAccount = \App\Models\LedgerAccount::find($accountId);
            if ($ledgerAccount) {
                $ledgerAccount->updateCurrentBalance();
            }
        }

        // Credit: VAT Output (if tax exists)
        if ($taxAmount > 0) {
            $vatOutputAccount = \App\Models\LedgerAccount::where('tenant_id', $tenant->id)
                ->where('code', 'VAT-OUT-001')
                ->first();

            if ($vatOutputAccount) {
                VoucherEntry::create([
                    'voucher_id' => $voucher->id,
                    'ledger_account_id' => $vatOutputAccount->id,
                    'debit_amount' => 0,
                    'credit_amount' => $taxAmount,
                    'particulars' => 'VAT @ 7.5% - ' . $voucher->voucher_number,
                ]);

                // Update VAT Output account balance
                $vatOutputAccount->updateCurrentBalance();

                Log::info('VAT Output recorded', [
                    'voucher_id' => $voucher->id,
                    'vat_amount' => $taxAmount
                ]);
            } else {
                Log::warning('VAT Output account not found', [
                    'tenant_id' => $tenant->id,
                    'voucher_id' => $voucher->id
                ]);
            }
        }

        // COGS ENTRIES: Record Cost of Goods Sold and reduce Inventory
        $cogsAccount = \App\Models\LedgerAccount::where('tenant_id', $tenant->id)
            ->whereHas('accountGroup', function($q) {
                $q->where('code', 'COGS');
            })
            ->where('is_active', true)
            ->first();

        $inventoryAccount = \App\Models\LedgerAccount::where('tenant_id', $tenant->id)
            ->whereHas('accountGroup', function($q) {
                $q->where('code', 'CA')->where('name', 'LIKE', '%Inventory%');
            })
            ->where('is_active', true)
            ->first();

        if ($cogsAccount && $inventoryAccount) {
            $totalCogs = 0;
            foreach ($inventoryItems as $item) {
                $product = Product::find($item['product_id']);
                if ($product && $product->maintain_stock) {
                    $cogs = ($product->purchase_rate ?? 0) * $item['quantity'];
                    $totalCogs += $cogs;
                }
            }

            if ($totalCogs > 0) {
                // Debit COGS (Expense increases)
                VoucherEntry::create([
                    'voucher_id' => $voucher->id,
                    'ledger_account_id' => $cogsAccount->id,
                    'debit_amount' => $totalCogs,
                    'credit_amount' => 0,
                    'particulars' => 'Cost of Goods Sold - ' . $voucher->voucher_number,
                ]);

                // Credit Inventory (Asset decreases)
                VoucherEntry::create([
                    'voucher_id' => $voucher->id,
                    'ledger_account_id' => $inventoryAccount->id,
                    'debit_amount' => 0,
                    'credit_amount' => $totalCogs,
                    'particulars' => 'Inventory reduction - ' . $voucher->voucher_number,
                ]);

                // Update account balances
                $cogsAccount->updateCurrentBalance();
                $inventoryAccount->updateCurrentBalance();
            }
        }
    }

    private function updateProductStock($inventoryItems, $operation, $voucher)
    {
        foreach ($inventoryItems as $item) {
            $product = Product::find($item['product_id']);

            if (!$product || !$product->maintain_stock) {
                continue;
            }

            $quantity = $item['quantity'];

            // Calculate old and new stock
            $oldStock = $product->current_stock;

            // Decrease stock for sales
            if ($operation === 'decrease') {
                $product->decrement('current_stock', $quantity);
                $product->refresh(); // Refresh to get updated current_stock
                $newStock = $product->current_stock;

                $product->update([
                    'current_stock_value' => $product->current_stock * ($product->purchase_rate ?? 0)
                ]);

                // Create stock movement with proper fields
                $product->stockMovements()->create([
                    'tenant_id' => $product->tenant_id,
                    'type' => 'out', // Required field
                    'transaction_type' => 'sales',
                    'transaction_reference' => $voucher->voucher_number,
                    'transaction_date' => now()->toDateString(),
                    'quantity' => -$quantity,
                    'old_stock' => $oldStock,
                    'new_stock' => $newStock,
                    'rate' => $product->purchase_rate ?? $product->sales_rate,
                    'reference' => $voucher->reference ?? 'Sales Invoice',
                    'created_by' => auth()->id(),
                ]);
            }
        }
    }

    private function createReceiptVoucher($order, $invoice, $tenant, $paymentData)
    {
        Log::info('Creating receipt voucher for order payment', [
            'order_id' => $order->id,
            'invoice_id' => $invoice->id,
            'tenant_id' => $tenant->id
        ]);

        // Get receipt voucher type (RV)
        $receiptVoucherType = VoucherType::where('tenant_id', $tenant->id)
            ->where('code', 'RV')
            ->first();

        if (!$receiptVoucherType) {
            throw new \Exception('Receipt voucher type (RV) not found. Please create it first.');
        }

        // Get bank account - try from payment data or use default cash account
        $bankAccount = null;
        if (!empty($paymentData['bank_account_id'])) {
            $bankAccount = \App\Models\LedgerAccount::find($paymentData['bank_account_id']);
        }

        // If no bank account specified, try to find a default cash/bank account
        if (!$bankAccount) {
            $bankAccount = \App\Models\LedgerAccount::where('tenant_id', $tenant->id)
                ->whereHas('accountGroup', function($q) {
                    $q->where('code', 'CA'); // Current Assets
                })
                ->where(function($q) {
                    $q->where('name', 'LIKE', '%Cash%')
                      ->orWhere('name', 'LIKE', '%Bank%');
                })
                ->where('is_active', true)
                ->first();
        }

        if (!$bankAccount) {
            Log::warning('No bank account found for receipt voucher', [
                'order_id' => $order->id,
                'tenant_id' => $tenant->id
            ]);
            throw new \Exception('Bank account not found. Please specify a bank account for payment.');
        }

        // Get customer account from the invoice
        $customerAccount = $invoice->entries->where('debit_amount', '>', 0)->first()?->ledgerAccount;

        if (!$customerAccount) {
            throw new \Exception('Customer account not found in invoice entries');
        }

        // Generate voucher number for receipt
        $lastReceipt = Voucher::where('tenant_id', $tenant->id)
            ->where('voucher_type_id', $receiptVoucherType->id)
            ->whereYear('voucher_date', date('Y'))
            ->latest('id')
            ->first();

        $nextNumber = 1;
        if ($lastReceipt) {
            preg_match('/(\d+)$/', $lastReceipt->voucher_number, $matches);
            if (isset($matches[1])) {
                $nextNumber = intval($matches[1]) + 1;
            }
        }

        $voucherNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // Create receipt voucher
        $paymentDate = $paymentData['payment_date'] ?? now()->toDateString();
        $paymentReference = $paymentData['payment_reference'] ?? $order->payment_method . ' - Order #' . $order->order_number;
        $paymentNotes = $paymentData['payment_notes'] ?? 'Payment received for e-commerce order #' . $order->order_number;

        $receiptVoucher = Voucher::create([
            'tenant_id' => $tenant->id,
            'voucher_type_id' => $receiptVoucherType->id,
            'voucher_number' => $voucherNumber,
            'voucher_date' => $paymentDate,
            'reference' => $paymentReference,
            'narration' => $paymentNotes,
            'total_amount' => $order->total_amount,
            'status' => 'posted',
            'created_by' => auth()->id(),
            'posted_at' => now(),
            'posted_by' => auth()->id(),
        ]);

        Log::info('Receipt voucher created', [
            'receipt_voucher_id' => $receiptVoucher->id,
            'voucher_number' => $receiptVoucher->voucher_number
        ]);

        // Create accounting entries for receipt
        // Debit: Bank/Cash Account (Asset increases)
        VoucherEntry::create([
            'voucher_id' => $receiptVoucher->id,
            'ledger_account_id' => $bankAccount->id,
            'debit_amount' => $order->total_amount,
            'credit_amount' => 0,
            'particulars' => 'Payment received from ' . $customerAccount->name . ' - Order #' . $order->order_number,
        ]);

        // Credit: Customer Account (Accounts Receivable decreases)
        VoucherEntry::create([
            'voucher_id' => $receiptVoucher->id,
            'ledger_account_id' => $customerAccount->id,
            'debit_amount' => 0,
            'credit_amount' => $order->total_amount,
            'particulars' => 'Payment received against Invoice ' . $invoice->voucherType->prefix . $invoice->voucher_number,
        ]);

        // Update ledger account balances
        $bankAccount->fresh()->updateCurrentBalance();
        $customerAccount->fresh()->updateCurrentBalance();

        // Update customer outstanding balance
        if ($order->customer && $order->customer->ledger_account_id) {
            $customer = $order->customer;
            $customerLedger = \App\Models\LedgerAccount::find($customer->ledger_account_id);
            if ($customerLedger) {
                $outstandingBalance = max(0, $customerLedger->current_balance);
                $customer->update(['outstanding_balance' => $outstandingBalance]);

                Log::info('Updated customer outstanding balance', [
                    'customer_id' => $customer->id,
                    'outstanding_balance' => $outstandingBalance
                ]);
            }
        }

        Log::info('Receipt voucher completed successfully', [
            'receipt_voucher_id' => $receiptVoucher->id,
            'order_id' => $order->id
        ]);

        return $receiptVoucher;
    }
}
