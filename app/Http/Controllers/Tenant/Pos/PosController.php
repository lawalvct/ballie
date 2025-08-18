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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
            ->where('status', 'open')
            ->with('cashRegister')
            ->first();

        $data = [
            'activeSession' => $activeSession,
            'paymentMethods' => PaymentMethod::where('tenant_id', $tenant->id)->active()->get(),
            'cashRegisters' => CashRegister::where('tenant_id', $tenant->id)->active()->get()
        ];

        // Only load additional data if session is active
        if ($activeSession) {
            $data = array_merge($data, [
                'products' => Product::where('tenant_id', $tenant->id)
                    ->where('is_active', true)
                    ->where('current_stock', '>', 0)
                    ->with(['category', 'unit'])
                    ->orderBy('name')
                    ->get(),
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

        return redirect()->route('tenant.pos.register-session', ['tenant' => $tenant->slug])
            ->with('success', 'Cash register session closed successfully.');
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
        // This would integrate with your accounting system
        // Create journal entries for sales, inventory, tax, etc.
        // Implementation depends on your accounting module structure
    }
}
