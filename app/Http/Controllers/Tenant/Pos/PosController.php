<?php

namespace App\Http\Controllers\Tenant\Pos;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\CashRegister;
use App\Models\CashRegisterSession;
use App\Models\PaymentMethod;
use App\Models\Receipt;
use App\Models\StockMovement;
use App\Models\LedgerAccount;
use App\Models\Voucher;
use App\Models\VoucherEntry;
use App\Models\VoucherType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PosController extends Controller
{
    public function index(Tenant $tenant)
    {
        // Initialize POS data if needed
        \App\Services\PosInitializerService::initializeForTenant();

        // Check for active session
        $activeSession = CashRegisterSession::whereHas('cashRegister', function($query) use ($tenant) {
                $query->where('tenant_id', $tenant->id);
            })
            ->where('user_id', Auth::id())
            ->whereNull('closed_at')
            ->with('cashRegister')
            ->first();

        $data = [
            'activeSession' => $activeSession,
            'paymentMethods' => PaymentMethod::where('tenant_id', $tenant->id)->active()->get(),
            'cashRegisters' => CashRegister::where('tenant_id', $tenant->id)->active()->get()
        ];

        // If no active session, redirect to register session page
        if (!$activeSession) {
            return redirect()->route('tenant.pos.register-session', ['tenant' => $tenant->slug])
                ->with('info', 'Please open a cash register session to start selling.');
        }

        // Only load additional data if session is active
        if ($activeSession) {
            // Load products and filter by calculated stock (not database column)
            $allProducts = Product::where('tenant_id', $tenant->id)
                ->where('is_active', true)
                ->with(['category', 'unit'])
                ->orderBy('name')
                ->get();

            // Filter products with stock > 0 using calculated stock from movements
            $productsWithStock = $allProducts->filter(function ($product) {
                return $product->current_stock > 0; // Uses calculated stock from movements
            })->values();

            $data = array_merge($data, [
                'products' => $productsWithStock,
                'categories' => ProductCategory::where('tenant_id', $tenant->id)
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get(),
                'customers' => Customer::where('tenant_id', $tenant->id)
                    ->where('status', 'active')
                    ->orderBy('first_name')
                    ->orderBy('company_name')
                    ->get(),
                'recentSales' => Sale::where('tenant_id', $tenant->id)
                    ->where('cash_register_session_id', $activeSession->id)
                    ->with(['customer', 'items.product'])
                    ->latest()
                    ->limit(10)
                    ->get()
            ]);
        }

        return view('tenant.pos.index', $data, ['tenant' => $tenant]);
    }

    public function store(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'payments' => 'required|array|min:1',
            'payments.*.method_id' => 'required|exists:payment_methods,id',
            'payments.*.amount' => 'required|numeric|min:0.01',
            'payments.*.reference' => 'nullable|string',
            'notes' => 'nullable|string|max:1000',
        ]);

        return DB::transaction(function () use ($validated, $tenant, $request) {
            $activeSession = $this->getActiveCashRegisterSession($tenant);

            if (!$activeSession) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active cash register session found.'
                ], 400);
            }

            // Create sale
            $sale = Sale::create([
                'tenant_id' => $tenant->id,
                'sale_number' => Sale::generateSaleNumber($tenant),
                'customer_id' => $validated['customer_id'] ?? null,
                'user_id' => Auth::id(),
                'cash_register_id' => $activeSession->cash_register_id,
                'cash_register_session_id' => $activeSession->id,
                'subtotal' => 0,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 0,
                'paid_amount' => array_sum(array_column($validated['payments'], 'amount')),
                'change_amount' => 0,
                'status' => 'completed',
                'sale_date' => now(),
                'notes' => $validated['notes'] ?? null,
            ]);

            $subtotal = 0;
            $taxAmount = 0;
            $discountAmount = 0;

            // Create sale items and update inventory
            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);

                // Check stock availability
                if ($product->track_stock && $product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}. Available: {$product->stock_quantity}");
                }

                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $itemDiscount = $item['discount_amount'] ?? 0;
                $itemTax = ($itemSubtotal - $itemDiscount) * ($product->tax_rate ?? 0) / 100;
                $lineTotal = $itemSubtotal - $itemDiscount + $itemTax;

                SaleItem::create([
                    'tenant_id' => $tenant->id,
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => $itemDiscount,
                    'tax_amount' => $itemTax,
                    'line_total' => $lineTotal,
                ]);

                // Update product stock
                if ($product->track_stock) {
                    $product->decrement('stock_quantity', $item['quantity']);

                    // Create stock movement record
                    $this->createStockMovement($product, $item['quantity'], 'sale', $sale->id);
                }

                $subtotal += $itemSubtotal;
                $taxAmount += $itemTax;
                $discountAmount += $itemDiscount;
            }

            // Create payments
            foreach ($validated['payments'] as $payment) {
                SalePayment::create([
                    'tenant_id' => $tenant->id,
                    'sale_id' => $sale->id,
                    'payment_method_id' => $payment['method_id'],
                    'amount' => $payment['amount'],
                    'reference_number' => $payment['reference'] ?? null,
                ]);
            }

            $totalAmount = $subtotal - $discountAmount + $taxAmount;
            $changeAmount = max(0, $sale->paid_amount - $totalAmount);

            // Update sale totals
            $sale->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'change_amount' => $changeAmount,
            ]);

            // Generate receipt
            $receipt = $this->generateReceipt($sale);

            // Create accounting entries
            $this->createAccountingEntries($sale);

            return response()->json([
                'success' => true,
                'sale_id' => $sale->id,
                'receipt_url' => route('tenant.pos.receipt', ['tenant' => $tenant->slug, 'sale' => $sale->id]),
                'change_amount' => $changeAmount,
                'message' => 'Sale completed successfully!'
            ]);
        });
    }

    public function receipt(Request $request, Tenant $tenant, Sale $sale)
    {
        $sale->load(['customer', 'items.product', 'payments.paymentMethod', 'cashRegister', 'user']);

        $receipt = $sale->receipts()->where('type', 'original')->first();

        if (!$receipt) {
            $receipt = $this->generateReceipt($sale);
        }

        return view('tenant.pos.receipt', compact('tenant', 'sale', 'receipt'));
    }

    public function registerSession(Request $request, Tenant $tenant)
    {
        $cashRegisters = CashRegister::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->get();

        $activeSessions = CashRegisterSession::whereNull('closed_at')
            ->whereHas('cashRegister', function($query) use ($tenant) {
                $query->where('tenant_id', $tenant->id);
            })
            ->with('cashRegister', 'user')
            ->get();

        return view('tenant.pos.register-session', compact('tenant', 'cashRegisters', 'activeSessions'));
    }

    public function openSession(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'cash_register_id' => 'required|exists:cash_registers,id',
            'opening_balance' => 'required|numeric|min:0',
            'opening_notes' => 'nullable|string|max:1000',
        ]);

        $cashRegister = CashRegister::find($validated['cash_register_id']);

        // Check if user already has an active session
        $existingSession = CashRegisterSession::where('user_id', Auth::id())
            ->whereNull('closed_at')
            ->first();

        if ($existingSession) {
            return back()->with('error', 'You already have an active cash register session.');
        }

        // Create new session
        $session = CashRegisterSession::create([
            'tenant_id' => $tenant->id,
            'cash_register_id' => $validated['cash_register_id'],
            'user_id' => Auth::id(),
            'opening_balance' => $validated['opening_balance'],
            'opened_at' => now(),
            'opening_notes' => $validated['opening_notes'],
        ]);

        // Update cash register current balance
        $cashRegister->update(['current_balance' => $validated['opening_balance']]);

        return redirect()->route('tenant.pos.index', ['tenant' => $tenant->slug])
            ->with('success', 'Cash register session opened successfully.');
    }

    public function closeSession(Request $request, Tenant $tenant)
    {
        $activeSession = $this->getActiveCashRegisterSession($tenant);

        if (!$activeSession) {
            return back()->with('error', 'No active cash register session found.');
        }

        return view('tenant.pos.close-session', compact('tenant', 'activeSession'));
    }

    public function storeCloseSession(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'closing_balance' => 'required|numeric|min:0',
            'closing_notes' => 'nullable|string|max:1000',
        ]);

        $activeSession = $this->getActiveCashRegisterSession($tenant);

        if (!$activeSession) {
            return back()->with('error', 'No active cash register session found.');
        }

        // Calculate expected balance
        $totalCashSales = $activeSession->total_cash_sales;
        $expectedBalance = $activeSession->opening_balance + $totalCashSales;
        $difference = $validated['closing_balance'] - $expectedBalance;

        // Close session
        $activeSession->update([
            'closing_balance' => $validated['closing_balance'],
            'expected_balance' => $expectedBalance,
            'difference' => $difference,
            'closed_at' => now(),
            'closing_notes' => $validated['closing_notes'],
        ]);

        // Update cash register current balance
        $activeSession->cashRegister->update(['current_balance' => $validated['closing_balance']]);

        return redirect()->route('tenant.pos.index', ['tenant' => $tenant->slug])
            ->with('success', 'Cash register session closed successfully. Please open a new session to continue.');
    }

    public function customerDisplay(Tenant $tenant)
    {
        return view('tenant.pos.customer-display', compact('tenant'));
    }

    public function transactions(Tenant $tenant)
    {
        $sales = Sale::where('tenant_id', $tenant->id)
            ->with(['customer', 'user', 'cashRegister'])
            ->latest()
            ->paginate(20);

        return view('tenant.pos.transactions', compact('tenant', 'sales'));
    }

    public function reports(Tenant $tenant)
    {
        return view('tenant.pos.reports', compact('tenant'));
    }

    public function dailySalesReport(Tenant $tenant)
    {
        return view('tenant.pos.reports.daily-sales', compact('tenant'));
    }

    public function topProductsReport(Tenant $tenant)
    {
        return view('tenant.pos.reports.top-products', compact('tenant'));
    }

    private function getActiveCashRegisterSession($tenant)
    {
        return CashRegisterSession::where('user_id', Auth::id())
            ->whereNull('closed_at')
            ->whereHas('cashRegister', function($query) use ($tenant) {
                $query->where('tenant_id', $tenant->id);
            })
            ->with('cashRegister')
            ->first();
    }

    private function createStockMovement($product, $quantity, $type, $referenceId)
    {
        // Create stock movement record if StockMovement model exists
        if (class_exists(StockMovement::class)) {
            StockMovement::create([
                'tenant_id' => $product->tenant_id,
                'product_id' => $product->id,
                'type' => $type,
                'quantity' => -$quantity, // Negative for sale
                'reference_type' => 'sale',
                'reference_id' => $referenceId,
                'date' => now(),
                'notes' => "Sale transaction",
            ]);
        }
    }

    private function generateReceipt($sale)
    {
        $receiptData = [
            'company' => [
                'name' => $sale->tenant->name,
                'email' => $sale->tenant->email,
                'phone' => $sale->tenant->phone,
                'address' => $sale->tenant->address,
            ],
            'sale' => [
                'number' => $sale->sale_number,
                'date' => $sale->sale_date->format('Y-m-d H:i:s'),
                'cashier' => $sale->user->name,
                'customer' => $sale->customer ? [
                    'name' => $sale->customer->customer_type === 'individual'
                        ? $sale->customer->first_name . ' ' . $sale->customer->last_name
                        : $sale->customer->company_name,
                    'email' => $sale->customer->email,
                    'phone' => $sale->customer->phone,
                ] : null,
            ],
            'items' => $sale->items->map(function($item) {
                return [
                    'name' => $item->product_name,
                    'sku' => $item->product_sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'discount' => $item->discount_amount,
                    'tax' => $item->tax_amount,
                    'total' => $item->line_total,
                ];
            }),
            'payments' => $sale->payments->map(function($payment) {
                return [
                    'method' => $payment->paymentMethod->name,
                    'amount' => $payment->amount,
                    'reference' => $payment->reference_number,
                ];
            }),
            'totals' => [
                'subtotal' => $sale->subtotal,
                'discount' => $sale->discount_amount,
                'tax' => $sale->tax_amount,
                'total' => $sale->total_amount,
                'paid' => $sale->paid_amount,
                'change' => $sale->change_amount,
            ],
        ];

        return Receipt::create([
            'tenant_id' => $sale->tenant_id,
            'sale_id' => $sale->id,
            'receipt_number' => Receipt::generateReceiptNumber($sale),
            'type' => 'original',
            'receipt_data' => $receiptData,
        ]);
    }

    private function createAccountingEntries($sale)
    {
        try {
            $tenant = tenant();

            // Find or get required ledger accounts
            $cashAccount = LedgerAccount::where('tenant_id', $tenant->id)
                ->where('code', 'CASH-001')
                ->where('is_active', true)
                ->first();

            if (!$cashAccount) {
                // Fallback: Find any cash account
                $cashAccount = LedgerAccount::where('tenant_id', $tenant->id)
                    ->where('account_type', 'asset')
                    ->where(function($q) {
                        $q->where('name', 'LIKE', '%Cash%')
                          ->orWhere('code', 'LIKE', 'CASH%');
                    })
                    ->where('is_active', true)
                    ->first();
            }

            $salesAccount = LedgerAccount::where('tenant_id', $tenant->id)
                ->where('code', 'SALES-001')
                ->where('is_active', true)
                ->first();

            if (!$salesAccount) {
                // Fallback: Find any sales revenue account
                $salesAccount = LedgerAccount::where('tenant_id', $tenant->id)
                    ->where('account_type', 'income')
                    ->where(function($q) {
                        $q->where('name', 'LIKE', '%Sales%')
                          ->orWhere('code', 'LIKE', 'SALES%');
                    })
                    ->where('is_active', true)
                    ->first();
            }

            $cogsAccount = LedgerAccount::where('tenant_id', $tenant->id)
                ->where('code', 'COGS-001')
                ->where('is_active', true)
                ->first();

            if (!$cogsAccount) {
                // Fallback: Find cost of goods sold account
                $cogsAccount = LedgerAccount::where('tenant_id', $tenant->id)
                    ->where('account_type', 'expense')
                    ->where(function($q) {
                        $q->where('name', 'LIKE', '%Cost of Goods%')
                          ->orWhere('code', 'LIKE', 'COGS%');
                    })
                    ->where('is_active', true)
                    ->first();
            }

            $inventoryAccount = LedgerAccount::where('tenant_id', $tenant->id)
                ->where('code', 'INV-001')
                ->where('is_active', true)
                ->first();

            if (!$inventoryAccount) {
                // Fallback: Find inventory account
                $inventoryAccount = LedgerAccount::where('tenant_id', $tenant->id)
                    ->where('account_type', 'asset')
                    ->where(function($q) {
                        $q->where('name', 'LIKE', '%Inventory%')
                          ->orWhere('name', 'LIKE', '%Stock%')
                          ->orWhere('code', 'LIKE', 'INV%')
                          ->orWhere('code', 'LIKE', 'STOCK%');
                    })
                    ->where('is_active', true)
                    ->first();
            }

            // Validate required accounts exist
            if (!$cashAccount || !$salesAccount) {
                Log::warning('POS: Missing required ledger accounts for accounting entries', [
                    'sale_id' => $sale->id,
                    'cash_account' => $cashAccount ? 'found' : 'missing',
                    'sales_account' => $salesAccount ? 'found' : 'missing',
                ]);
                return; // Skip accounting if core accounts missing
            }

            // Get or create Sales voucher type
            $salesVoucherType = VoucherType::where('tenant_id', $tenant->id)
                ->where('code', 'SV')
                ->first();

            if (!$salesVoucherType) {
                // Create if doesn't exist
                $salesVoucherType = VoucherType::create([
                    'tenant_id' => $tenant->id,
                    'name' => 'Sales',
                    'code' => 'SV',
                    'abbreviation' => 'S',
                    'description' => 'Sales vouchers from POS',
                    'numbering_method' => 'auto',
                    'prefix' => 'SV-',
                    'starting_number' => 1,
                    'current_number' => 0,
                    'has_reference' => true,
                    'affects_inventory' => true,
                    'inventory_effect' => 'decrease',
                    'affects_cashbank' => false,
                    'is_system_defined' => true,
                    'is_active' => true,
                ]);
            }

            // Generate voucher number
            $voucherNumber = $salesVoucherType->getNextVoucherNumber();

            // Create voucher (journal entry)
            $voucher = Voucher::create([
                'tenant_id' => $tenant->id,
                'voucher_type_id' => $salesVoucherType->id,
                'voucher_number' => $voucherNumber,
                'voucher_date' => $sale->sale_date ?? now(),
                'reference_number' => $sale->sale_number,
                'narration' => 'POS Sale - ' . $sale->sale_number . ($sale->customer ? ' - ' . $sale->customer->name : ''),
                'total_amount' => $sale->total_amount,
                'status' => 'posted', // Auto-post POS entries
                'created_by' => $sale->user_id,
                'posted_at' => now(),
                'posted_by' => $sale->user_id,
            ]);

            // Entry 1: Debit Cash Account (Money received)
            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $cashAccount->id,
                'debit_amount' => $sale->total_amount,
                'credit_amount' => 0,
                'particulars' => 'Cash received - ' . $sale->sale_number,
            ]);

            // Entry 2: Credit Sales Revenue Account (Income earned)
            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $salesAccount->id,
                'debit_amount' => 0,
                'credit_amount' => $sale->total_amount,
                'particulars' => 'Sales revenue - ' . $sale->sale_number,
            ]);

            // Entry 3 & 4: COGS and Inventory (if accounts exist and we have cost data)
            if ($cogsAccount && $inventoryAccount) {
                $totalCost = 0;

                // Calculate total cost from sale items
                foreach ($sale->items as $item) {
                    $product = $item->product;
                    if ($product && $product->purchase_rate > 0) {
                        $totalCost += $product->purchase_rate * $item->quantity;
                    }
                }

                // Only create COGS entries if we have cost data
                if ($totalCost > 0) {
                    // Entry 3: Debit COGS (Expense)
                    VoucherEntry::create([
                        'voucher_id' => $voucher->id,
                        'ledger_account_id' => $cogsAccount->id,
                        'debit_amount' => $totalCost,
                        'credit_amount' => 0,
                        'particulars' => 'Cost of goods sold - ' . $sale->sale_number,
                    ]);

                    // Entry 4: Credit Inventory (Asset reduction)
                    VoucherEntry::create([
                        'voucher_id' => $voucher->id,
                        'ledger_account_id' => $inventoryAccount->id,
                        'debit_amount' => 0,
                        'credit_amount' => $totalCost,
                        'particulars' => 'Inventory reduction - ' . $sale->sale_number,
                    ]);
                }
            }

            Log::info('POS: Accounting entries created successfully', [
                'sale_id' => $sale->id,
                'voucher_id' => $voucher->id,
                'voucher_number' => $voucher->voucher_number,
            ]);

        } catch (\Exception $e) {
            Log::error('POS: Failed to create accounting entries', [
                'sale_id' => $sale->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Don't throw - let the sale complete even if accounting fails
        }
    }
}
