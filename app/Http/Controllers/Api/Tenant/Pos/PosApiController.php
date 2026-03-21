<?php

namespace App\Http\Controllers\Api\Tenant\Pos;

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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PosApiController extends Controller
{
    // ─────────────────────────────────────────────────
    //  SESSION MANAGEMENT
    // ─────────────────────────────────────────────────

    /**
     * Check if the authenticated user has an active session.
     */
    public function session(Request $request, Tenant $tenant)
    {
        $activeSession = $this->getActiveSession($tenant);

        if ($activeSession) {
            $activeSession->load('cashRegister');
            return response()->json([
                'success' => true,
                'has_active_session' => true,
                'session' => $this->formatSession($activeSession),
            ]);
        }

        return response()->json([
            'success' => true,
            'has_active_session' => false,
            'session' => null,
        ]);
    }

    /**
     * List all cash registers.
     */
    public function cashRegisters(Request $request, Tenant $tenant)
    {
        $registers = CashRegister::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->with(['sessions' => function ($q) {
                $q->whereNull('closed_at')->with('user');
            }])
            ->get()
            ->map(function ($register) {
                $activeSession = $register->sessions->first();
                return [
                    'id' => $register->id,
                    'name' => $register->name,
                    'location' => $register->location,
                    'current_balance' => $register->current_balance,
                    'is_active' => $register->is_active,
                    'active_session' => $activeSession ? [
                        'id' => $activeSession->id,
                        'user' => [
                            'id' => $activeSession->user->id,
                            'name' => $activeSession->user->name,
                        ],
                        'opened_at' => $activeSession->opened_at,
                    ] : null,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $registers,
        ]);
    }

    /**
     * Open a new cash register session.
     */
    public function openSession(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'cash_register_id' => 'required|integer',
            'opening_balance' => 'required|numeric|min:0',
            'opening_notes' => 'nullable|string|max:1000',
        ]);

        $cashRegister = CashRegister::where('id', $validated['cash_register_id'])
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->firstOrFail();

        // Check if user already has an active session
        $existing = CashRegisterSession::where('user_id', Auth::id())
            ->whereNull('closed_at')
            ->whereHas('cashRegister', fn ($q) => $q->where('tenant_id', $tenant->id))
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an active cash register session.',
            ], 422);
        }

        // Check if register already has an active session from another user
        $registerInUse = CashRegisterSession::where('cash_register_id', $cashRegister->id)
            ->whereNull('closed_at')
            ->first();

        if ($registerInUse) {
            return response()->json([
                'success' => false,
                'message' => 'This cash register is already in use by another user.',
            ], 422);
        }

        $session = CashRegisterSession::create([
            'tenant_id' => $tenant->id,
            'cash_register_id' => $cashRegister->id,
            'user_id' => Auth::id(),
            'opening_balance' => $validated['opening_balance'],
            'opened_at' => now(),
            'opening_notes' => $validated['opening_notes'] ?? null,
        ]);

        $cashRegister->update(['current_balance' => $validated['opening_balance']]);

        $session->load('cashRegister');

        return response()->json([
            'success' => true,
            'message' => 'Cash register session opened successfully.',
            'session' => $this->formatSession($session),
        ], 201);
    }

    /**
     * Close the active cash register session.
     */
    public function closeSession(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'closing_balance' => 'required|numeric|min:0',
            'closing_notes' => 'nullable|string|max:1000',
        ]);

        $activeSession = $this->getActiveSession($tenant);

        if (!$activeSession) {
            return response()->json([
                'success' => false,
                'message' => 'No active cash register session found.',
            ], 400);
        }

        $totalCashSales = $activeSession->total_cash_sales;
        $expectedBalance = $activeSession->opening_balance + $totalCashSales;
        $difference = $validated['closing_balance'] - $expectedBalance;

        $activeSession->update([
            'closing_balance' => $validated['closing_balance'],
            'expected_balance' => $expectedBalance,
            'difference' => $difference,
            'closed_at' => now(),
            'closing_notes' => $validated['closing_notes'] ?? null,
        ]);

        $activeSession->cashRegister->update(['current_balance' => $validated['closing_balance']]);

        return response()->json([
            'success' => true,
            'message' => 'Cash register session closed successfully.',
            'summary' => [
                'session_id' => $activeSession->id,
                'opening_balance' => $activeSession->opening_balance,
                'closing_balance' => $validated['closing_balance'],
                'expected_balance' => $expectedBalance,
                'difference' => $difference,
                'total_sales' => $activeSession->total_sales,
                'total_cash_sales' => $totalCashSales,
                'opened_at' => $activeSession->opened_at,
                'closed_at' => $activeSession->fresh()->closed_at,
            ],
        ]);
    }

    // ─────────────────────────────────────────────────
    //  POS DATA (Products, Categories, Customers, etc.)
    // ─────────────────────────────────────────────────

    /**
     * All-in-one init endpoint: session + products + categories + customers + payment methods + recent sales.
     */
    public function init(Request $request, Tenant $tenant)
    {
        $activeSession = $this->getActiveSession($tenant);

        if (!$activeSession) {
            return response()->json([
                'success' => true,
                'data' => [
                    'session' => null,
                    'products' => [],
                    'categories' => [],
                    'customers' => [],
                    'payment_methods' => [],
                    'recent_sales' => [],
                ],
            ]);
        }

        $activeSession->load('cashRegister');

        // Products with stock > 0
        $allProducts = Product::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->with(['category', 'unit'])
            ->orderBy('name')
            ->get();

        $products = $allProducts->filter(fn ($p) => $p->current_stock > 0)->values();

        $categories = ProductCategory::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->withCount('products')
            ->orderBy('name')
            ->get();

        $customers = Customer::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->orderBy('company_name')
            ->get();

        $paymentMethods = PaymentMethod::where('tenant_id', $tenant->id)
            ->active()
            ->get();

        $recentSales = Sale::where('tenant_id', $tenant->id)
            ->where('cash_register_session_id', $activeSession->id)
            ->with('customer')
            ->withCount('items')
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'session' => $this->formatSession($activeSession),
                'products' => $products->map(fn ($p) => $this->formatProduct($p)),
                'categories' => $categories->map(fn ($c) => [
                    'id' => $c->id,
                    'name' => $c->name,
                    'slug' => $c->slug,
                    'product_count' => $c->products_count,
                ]),
                'customers' => $customers->map(fn ($c) => $this->formatCustomer($c)),
                'payment_methods' => $paymentMethods->map(fn ($m) => $this->formatPaymentMethod($m)),
                'recent_sales' => $recentSales->map(fn ($s) => $this->formatSaleSummary($s)),
            ],
        ]);
    }

    /**
     * List products (paginated, searchable).
     */
    public function products(Request $request, Tenant $tenant)
    {
        $query = Product::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->with(['category', 'unit']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $query->orderBy('name');
        $perPage = min((int) $request->input('per_page', 50), 100);
        $paginator = $query->paginate($perPage);

        // Filter by stock if requested (default: only in-stock)
        $inStockOnly = filter_var($request->input('in_stock', true), FILTER_VALIDATE_BOOLEAN);

        $items = collect($paginator->items());
        if ($inStockOnly) {
            $items = $items->filter(fn ($p) => $p->current_stock > 0)->values();
        }

        return response()->json([
            'success' => true,
            'data' => $items->map(fn ($p) => $this->formatProduct($p)),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    /**
     * List product categories.
     */
    public function categories(Request $request, Tenant $tenant)
    {
        $categories = ProductCategory::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->withCount('products')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'slug' => $c->slug,
                'product_count' => $c->products_count,
            ]),
        ]);
    }

    /**
     * List customers (searchable).
     */
    public function customers(Request $request, Tenant $tenant)
    {
        $query = Customer::where('tenant_id', $tenant->id)
            ->where('status', 'active');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('first_name')->orderBy('company_name')->get();

        return response()->json([
            'success' => true,
            'data' => $customers->map(fn ($c) => $this->formatCustomer($c)),
        ]);
    }

    /**
     * List active payment methods.
     */
    public function paymentMethods(Request $request, Tenant $tenant)
    {
        $methods = PaymentMethod::where('tenant_id', $tenant->id)
            ->active()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $methods->map(fn ($m) => $this->formatPaymentMethod($m)),
        ]);
    }

    // ─────────────────────────────────────────────────
    //  SALES
    // ─────────────────────────────────────────────────

    /**
     * Create a new sale (checkout).
     */
    public function store(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id,tenant_id,' . $tenant->id],
            'items' => 'required|array|min:1',
            'items.*.product_id' => ['required', 'exists:products,id,tenant_id,' . $tenant->id],
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'payments' => 'required|array|min:1',
            'payments.*.method_id' => ['required', 'exists:payment_methods,id,tenant_id,' . $tenant->id],
            'payments.*.amount' => 'required|numeric|min:0.01',
            'payments.*.reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        return DB::transaction(function () use ($validated, $tenant) {
            $activeSession = $this->getActiveSession($tenant);

            if (!$activeSession) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active cash register session found.',
                ], 400);
            }

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

            foreach ($validated['items'] as $item) {
                $product = Product::where('id', $item['product_id'])
                    ->where('tenant_id', $tenant->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($product->maintain_stock) {
                    $freshStock = $product->getStockAsOfDate(now(), true);
                    if ($freshStock < $item['quantity']) {
                        throw new \Exception("Insufficient stock for {$product->name}. Available: " . number_format($freshStock, 2));
                    }
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

                if ($product->maintain_stock) {
                    $this->createStockMovement($product, $item['quantity'], $sale);
                }

                $subtotal += $itemSubtotal;
                $taxAmount += $itemTax;
                $discountAmount += $itemDiscount;
            }

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

            $sale->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'change_amount' => $changeAmount,
            ]);

            $this->generateReceipt($sale);
            $this->createAccountingEntries($sale);

            return response()->json([
                'success' => true,
                'sale_id' => $sale->id,
                'sale_number' => $sale->sale_number,
                'change_amount' => $changeAmount,
                'message' => 'Sale completed successfully!',
            ]);
        });
    }

    /**
     * List transactions (paginated, filterable).
     */
    public function transactions(Request $request, Tenant $tenant)
    {
        $query = Sale::where('tenant_id', $tenant->id)
            ->with(['customer', 'user', 'cashRegister'])
            ->withCount('items');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $query->where('sale_number', 'like', '%' . $request->input('search') . '%');
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('sale_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('sale_date', '<=', $request->input('date_to'));
        }

        if ($request->filled('session_id')) {
            $query->where('cash_register_session_id', $request->input('session_id'));
        }

        $query->latest('sale_date');
        $perPage = min((int) $request->input('per_page', 20), 100);
        $paginator = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => collect($paginator->items())->map(fn ($s) => $this->formatSaleSummary($s)),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    /**
     * Get a single transaction with full details.
     */
    public function showTransaction(Request $request, Tenant $tenant, Sale $sale)
    {
        if ($sale->tenant_id !== $tenant->id) {
            return response()->json(['success' => false, 'message' => 'Sale not found.'], 404);
        }

        $sale->load(['customer', 'user', 'items.product', 'payments.paymentMethod', 'cashRegister']);

        return response()->json([
            'success' => true,
            'data' => $this->formatSaleDetail($sale),
        ]);
    }

    /**
     * Void a sale.
     */
    public function voidSale(Request $request, Tenant $tenant, Sale $sale)
    {
        if ($sale->tenant_id !== $tenant->id) {
            return response()->json(['success' => false, 'message' => 'Sale not found.'], 404);
        }

        if ($sale->status === 'voided') {
            return response()->json([
                'success' => false,
                'message' => 'This sale is already voided.',
            ], 422);
        }

        return DB::transaction(function () use ($sale, $tenant) {
            foreach ($sale->items as $item) {
                $product = $item->product;
                if ($product && $product->maintain_stock) {
                    $oldStock = $product->getStockAsOfDate(now(), true);
                    $movementQuantity = abs($item->quantity);
                    $newStock = $oldStock + $movementQuantity;

                    StockMovement::create([
                        'tenant_id' => $tenant->id,
                        'product_id' => $product->id,
                        'type' => 'in',
                        'quantity' => $movementQuantity,
                        'old_stock' => $oldStock,
                        'new_stock' => $newStock,
                        'rate' => $product->purchase_rate ?? $product->sales_rate ?? 0,
                        'reference' => 'POS Void - ' . $sale->sale_number,
                        'remarks' => 'Stock reversal from voided POS sale',
                        'created_by' => Auth::id(),
                        'transaction_type' => 'sales_return',
                        'transaction_date' => now()->toDateString(),
                        'transaction_reference' => $sale->sale_number,
                        'source_transaction_type' => Sale::class,
                        'source_transaction_id' => $sale->id,
                    ]);

                    $today = now()->toDateString();
                    Cache::forget("product_stock_{$product->id}_{$today}");
                    Cache::forget("product_stock_value_{$product->id}_{$today}_weighted_average");
                }
            }

            $sale->update(['status' => 'voided']);

            return response()->json([
                'success' => true,
                'message' => 'Sale voided successfully. Stock has been restored.',
            ]);
        });
    }

    /**
     * Refund a sale.
     */
    public function refundSale(Request $request, Tenant $tenant, Sale $sale)
    {
        if ($sale->tenant_id !== $tenant->id) {
            return response()->json(['success' => false, 'message' => 'Sale not found.'], 404);
        }

        if ($sale->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Only completed sales can be refunded.',
            ], 422);
        }

        return DB::transaction(function () use ($sale, $tenant) {
            foreach ($sale->items as $item) {
                $product = $item->product;
                if ($product && $product->maintain_stock) {
                    $oldStock = $product->getStockAsOfDate(now(), true);
                    $movementQuantity = abs($item->quantity);
                    $newStock = $oldStock + $movementQuantity;

                    StockMovement::create([
                        'tenant_id' => $tenant->id,
                        'product_id' => $product->id,
                        'type' => 'in',
                        'quantity' => $movementQuantity,
                        'old_stock' => $oldStock,
                        'new_stock' => $newStock,
                        'rate' => $product->purchase_rate ?? $product->sales_rate ?? 0,
                        'reference' => 'POS Refund - ' . $sale->sale_number,
                        'remarks' => 'Stock reversal from refunded POS sale',
                        'created_by' => Auth::id(),
                        'transaction_type' => 'sales_return',
                        'transaction_date' => now()->toDateString(),
                        'transaction_reference' => $sale->sale_number,
                        'source_transaction_type' => Sale::class,
                        'source_transaction_id' => $sale->id,
                    ]);

                    $today = now()->toDateString();
                    Cache::forget("product_stock_{$product->id}_{$today}");
                    Cache::forget("product_stock_value_{$product->id}_{$today}_weighted_average");
                }
            }

            $sale->update(['status' => 'refunded']);

            return response()->json([
                'success' => true,
                'message' => 'Sale refunded successfully. Stock has been restored.',
            ]);
        });
    }

    // ─────────────────────────────────────────────────
    //  RECEIPTS
    // ─────────────────────────────────────────────────

    /**
     * Get receipt data for a sale.
     */
    public function receipt(Request $request, Tenant $tenant, Sale $sale)
    {
        if ($sale->tenant_id !== $tenant->id) {
            return response()->json(['success' => false, 'message' => 'Sale not found.'], 404);
        }

        $receipt = $sale->receipts()->where('type', 'original')->first();

        if (!$receipt) {
            $sale->load(['customer', 'items.product', 'payments.paymentMethod', 'cashRegister', 'user']);
            $receipt = $this->generateReceipt($sale);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'receipt_number' => $receipt->receipt_number,
                'type' => $receipt->type,
                'receipt_data' => $receipt->receipt_data,
            ],
        ]);
    }

    /**
     * Email receipt to customer.
     */
    public function emailReceipt(Request $request, Tenant $tenant, Sale $sale)
    {
        if ($sale->tenant_id !== $tenant->id) {
            return response()->json(['success' => false, 'message' => 'Sale not found.'], 404);
        }

        $customer = $sale->customer;

        if (!$customer || !$customer->email) {
            return response()->json([
                'success' => false,
                'message' => 'Customer has no email address.',
            ], 422);
        }

        // TODO: Implement email sending via Mailable
        return response()->json([
            'success' => false,
            'message' => 'Email receipt feature is coming soon.',
        ], 501);
    }

    // ─────────────────────────────────────────────────
    //  REPORTS
    // ─────────────────────────────────────────────────

    /**
     * Daily sales summary report.
     */
    public function dailySalesReport(Request $request, Tenant $tenant)
    {
        $date = $request->input('date', now()->toDateString());
        $dateFrom = $request->input('date_from', $date);
        $dateTo = $request->input('date_to', $date);

        $salesQuery = Sale::where('tenant_id', $tenant->id)
            ->where('status', 'completed')
            ->whereDate('sale_date', '>=', $dateFrom)
            ->whereDate('sale_date', '<=', $dateTo);

        $totalSales = (clone $salesQuery)->sum('total_amount');
        $totalTransactions = (clone $salesQuery)->count();
        $totalDiscount = (clone $salesQuery)->sum('discount_amount');
        $totalTax = (clone $salesQuery)->sum('tax_amount');

        $totalItemsSold = SaleItem::whereIn(
            'sale_id',
            (clone $salesQuery)->pluck('id')
        )->sum('quantity');

        // Payment breakdown
        $saleIds = (clone $salesQuery)->pluck('id');
        $paymentBreakdown = SalePayment::whereIn('sale_id', $saleIds)
            ->join('payment_methods', 'sale_payments.payment_method_id', '=', 'payment_methods.id')
            ->select(
                'payment_methods.name as method',
                DB::raw('SUM(sale_payments.amount) as total'),
                DB::raw('COUNT(sale_payments.id) as count')
            )
            ->groupBy('payment_methods.name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'total_sales' => $totalSales,
                'total_transactions' => $totalTransactions,
                'total_items_sold' => $totalItemsSold,
                'total_discount' => $totalDiscount,
                'total_tax' => $totalTax,
                'payment_breakdown' => $paymentBreakdown,
            ],
        ]);
    }

    /**
     * Top selling products report.
     */
    public function topProductsReport(Request $request, Tenant $tenant)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());
        $limit = min((int) $request->input('limit', 10), 50);

        $completedSaleIds = Sale::where('tenant_id', $tenant->id)
            ->where('status', 'completed')
            ->whereDate('sale_date', '>=', $dateFrom)
            ->whereDate('sale_date', '<=', $dateTo)
            ->pluck('id');

        $topProducts = SaleItem::whereIn('sale_id', $completedSaleIds)
            ->select(
                'product_id',
                'product_name',
                'product_sku',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(line_total) as total_revenue'),
                DB::raw('COUNT(DISTINCT sale_id) as transaction_count')
            )
            ->groupBy('product_id', 'product_name', 'product_sku')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $topProducts,
        ]);
    }

    // ─────────────────────────────────────────────────
    //  PRIVATE HELPERS
    // ─────────────────────────────────────────────────

    private function getActiveSession(Tenant $tenant): ?CashRegisterSession
    {
        return CashRegisterSession::where('user_id', Auth::id())
            ->whereNull('closed_at')
            ->whereHas('cashRegister', fn ($q) => $q->where('tenant_id', $tenant->id))
            ->with('cashRegister')
            ->first();
    }

    private function formatSession(CashRegisterSession $session): array
    {
        return [
            'id' => $session->id,
            'cash_register_id' => $session->cash_register_id,
            'cash_register' => $session->cashRegister ? [
                'id' => $session->cashRegister->id,
                'name' => $session->cashRegister->name,
                'location' => $session->cashRegister->location,
            ] : null,
            'user_id' => $session->user_id,
            'opening_balance' => $session->opening_balance,
            'opened_at' => $session->opened_at,
            'opening_notes' => $session->opening_notes,
            'total_sales' => $session->total_sales,
            'total_cash_sales' => $session->total_cash_sales,
        ];
    }

    private function formatProduct(Product $product): array
    {
        $imageUrl = null;
        if ($product->image_path) {
            $imageUrl = Storage::disk('public')->url($product->image_path);
        }

        return [
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'selling_price' => $product->selling_price,
            'tax_rate' => $product->tax_rate,
            'tax_inclusive' => (bool) $product->tax_inclusive,
            'current_stock' => $product->current_stock,
            'image_url' => $imageUrl,
            'category_id' => $product->category_id,
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
            ] : null,
            'unit' => $product->unit ? [
                'id' => $product->unit->id,
                'name' => $product->unit->name,
                'abbreviation' => $product->unit->abbreviation ?? $product->unit->short_name ?? null,
            ] : null,
            'maintain_stock' => (bool) $product->maintain_stock,
        ];
    }

    private function formatCustomer(Customer $customer): array
    {
        return [
            'id' => $customer->id,
            'customer_type' => $customer->customer_type,
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'full_name' => $customer->full_name,
            'company_name' => $customer->company_name,
            'email' => $customer->email,
            'phone' => $customer->phone,
        ];
    }

    private function formatPaymentMethod(PaymentMethod $method): array
    {
        return [
            'id' => $method->id,
            'name' => $method->name,
            'code' => $method->code,
            'requires_reference' => (bool) $method->requires_reference,
            'charge_percentage' => $method->charge_percentage,
            'charge_amount' => $method->charge_amount,
        ];
    }

    private function formatSaleSummary(Sale $sale): array
    {
        return [
            'id' => $sale->id,
            'sale_number' => $sale->sale_number,
            'customer' => $sale->customer ? [
                'id' => $sale->customer->id,
                'full_name' => $sale->customer->full_name,
            ] : null,
            'user' => $sale->user ? [
                'id' => $sale->user->id,
                'name' => $sale->user->name,
            ] : null,
            'cash_register' => $sale->cashRegister ? [
                'id' => $sale->cashRegister->id,
                'name' => $sale->cashRegister->name,
            ] : null,
            'subtotal' => $sale->subtotal,
            'tax_amount' => $sale->tax_amount,
            'discount_amount' => $sale->discount_amount,
            'total_amount' => $sale->total_amount,
            'paid_amount' => $sale->paid_amount,
            'change_amount' => $sale->change_amount,
            'status' => $sale->status,
            'sale_date' => $sale->sale_date,
            'items_count' => $sale->items_count ?? $sale->items()->count(),
            'notes' => $sale->notes,
        ];
    }

    private function formatSaleDetail(Sale $sale): array
    {
        return [
            'id' => $sale->id,
            'sale_number' => $sale->sale_number,
            'status' => $sale->status,
            'sale_date' => $sale->sale_date,
            'notes' => $sale->notes,
            'customer' => $sale->customer ? [
                'id' => $sale->customer->id,
                'full_name' => $sale->customer->full_name,
                'email' => $sale->customer->email,
                'phone' => $sale->customer->phone,
            ] : null,
            'user' => $sale->user ? [
                'id' => $sale->user->id,
                'name' => $sale->user->name,
            ] : null,
            'cash_register' => $sale->cashRegister ? [
                'id' => $sale->cashRegister->id,
                'name' => $sale->cashRegister->name,
                'location' => $sale->cashRegister->location,
            ] : null,
            'items' => $sale->items->map(fn ($item) => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'product_sku' => $item->product_sku,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'discount_amount' => $item->discount_amount,
                'tax_amount' => $item->tax_amount,
                'line_total' => $item->line_total,
            ]),
            'payments' => $sale->payments->map(fn ($p) => [
                'id' => $p->id,
                'payment_method' => $p->paymentMethod ? [
                    'id' => $p->paymentMethod->id,
                    'name' => $p->paymentMethod->name,
                ] : null,
                'amount' => $p->amount,
                'reference_number' => $p->reference_number,
            ]),
            'totals' => [
                'subtotal' => $sale->subtotal,
                'discount' => $sale->discount_amount,
                'tax' => $sale->tax_amount,
                'total' => $sale->total_amount,
                'paid' => $sale->paid_amount,
                'change' => $sale->change_amount,
            ],
        ];
    }

    private function createStockMovement(Product $product, $quantity, Sale $sale): void
    {
        if (!class_exists(StockMovement::class)) {
            return;
        }

        $oldStock = $product->getStockAsOfDate(now(), true);
        $movementQuantity = -abs($quantity);
        $newStock = $oldStock + $movementQuantity;

        StockMovement::create([
            'tenant_id' => $product->tenant_id,
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => $movementQuantity,
            'old_stock' => $oldStock,
            'new_stock' => $newStock,
            'rate' => $product->purchase_rate ?? $product->sales_rate ?? 0,
            'reference' => 'POS Sale - ' . $sale->sale_number,
            'remarks' => 'POS sale stock deduction',
            'created_by' => Auth::id(),
            'transaction_type' => 'sales',
            'transaction_date' => optional($sale->sale_date)->toDateString() ?? now()->toDateString(),
            'transaction_reference' => $sale->sale_number,
            'source_transaction_type' => Sale::class,
            'source_transaction_id' => $sale->id,
        ]);

        $today = now()->toDateString();
        $saleDate = optional($sale->sale_date)->toDateString() ?? $today;
        Cache::forget("product_stock_{$product->id}_{$today}");
        Cache::forget("product_stock_{$product->id}_{$saleDate}");
        Cache::forget("product_stock_value_{$product->id}_{$today}_weighted_average");
        Cache::forget("product_stock_value_{$product->id}_{$saleDate}_weighted_average");
    }

    private function generateReceipt(Sale $sale): Receipt
    {
        $sale->loadMissing(['customer', 'items.product', 'payments.paymentMethod', 'user']);

        $receiptData = [
            'company' => [
                'name' => $sale->tenant->name ?? null,
                'email' => $sale->tenant->email ?? null,
                'phone' => $sale->tenant->phone ?? null,
                'address' => $sale->tenant->address ?? null,
            ],
            'sale' => [
                'number' => $sale->sale_number,
                'date' => $sale->sale_date->format('Y-m-d H:i:s'),
                'cashier' => $sale->user->name ?? 'N/A',
                'customer' => $sale->customer ? [
                    'name' => $sale->customer->full_name,
                    'email' => $sale->customer->email,
                    'phone' => $sale->customer->phone,
                ] : null,
            ],
            'items' => $sale->items->map(fn ($item) => [
                'name' => $item->product_name,
                'sku' => $item->product_sku,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'discount' => $item->discount_amount,
                'tax' => $item->tax_amount,
                'total' => $item->line_total,
            ]),
            'payments' => $sale->payments->map(fn ($p) => [
                'method' => $p->paymentMethod->name ?? 'Unknown',
                'amount' => $p->amount,
                'reference' => $p->reference_number,
            ]),
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

    private function createAccountingEntries(Sale $sale): void
    {
        try {
            $tenant = Tenant::find($sale->tenant_id);

            $cashAccount = LedgerAccount::where('tenant_id', $tenant->id)
                ->where('account_type', 'asset')
                ->where(function ($q) {
                    $q->where('code', 'CASH-001')
                      ->orWhere('name', 'LIKE', '%Cash%');
                })
                ->where('is_active', true)
                ->first();

            $salesAccount = LedgerAccount::where('tenant_id', $tenant->id)
                ->where('account_type', 'income')
                ->where(function ($q) {
                    $q->where('code', 'SALES-001')
                      ->orWhere('name', 'LIKE', '%Sales%');
                })
                ->where('is_active', true)
                ->first();

            if (!$cashAccount || !$salesAccount) {
                Log::warning('POS API: Missing ledger accounts for accounting entries', [
                    'sale_id' => $sale->id,
                    'tenant_id' => $tenant->id,
                ]);
                return;
            }

            $voucherType = VoucherType::where('tenant_id', $tenant->id)
                ->where('code', 'RV')
                ->first();

            if (!$voucherType) {
                Log::warning('POS API: No RV voucher type found', ['tenant_id' => $tenant->id]);
                return;
            }

            $netSalesRevenue = $sale->subtotal - $sale->discount_amount;

            $voucher = Voucher::create([
                'tenant_id' => $tenant->id,
                'voucher_type_id' => $voucherType->id,
                'voucher_number' => Voucher::generateVoucherNumber($voucherType),
                'date' => $sale->sale_date->toDateString(),
                'narration' => 'POS Sale - ' . $sale->sale_number,
                'status' => 'posted',
                'total_amount' => $sale->total_amount,
                'created_by' => Auth::id(),
            ]);

            // Debit Cash (amount received)
            VoucherEntry::create([
                'tenant_id' => $tenant->id,
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $cashAccount->id,
                'debit' => $sale->total_amount,
                'credit' => 0,
                'narration' => 'Cash from POS Sale ' . $sale->sale_number,
            ]);

            // Credit Sales Revenue (net of discount)
            VoucherEntry::create([
                'tenant_id' => $tenant->id,
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $salesAccount->id,
                'debit' => 0,
                'credit' => $netSalesRevenue,
                'narration' => 'Sales revenue from POS Sale ' . $sale->sale_number,
            ]);

            // Credit Tax Payable (if any)
            if ($sale->tax_amount > 0) {
                $taxAccount = LedgerAccount::where('tenant_id', $tenant->id)
                    ->where('account_type', 'liability')
                    ->where(function ($q) {
                        $q->where('code', 'TAX-001')
                          ->orWhere('name', 'LIKE', '%Tax Payable%');
                    })
                    ->where('is_active', true)
                    ->first();

                if ($taxAccount) {
                    VoucherEntry::create([
                        'tenant_id' => $tenant->id,
                        'voucher_id' => $voucher->id,
                        'ledger_account_id' => $taxAccount->id,
                        'debit' => 0,
                        'credit' => $sale->tax_amount,
                        'narration' => 'Tax payable from POS Sale ' . $sale->sale_number,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('POS API: Failed to create accounting entries', [
                'sale_id' => $sale->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
