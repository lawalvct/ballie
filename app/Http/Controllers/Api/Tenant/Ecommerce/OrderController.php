<?php

namespace App\Http\Controllers\Api\Tenant\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Order;
use App\Models\Voucher;
use App\Models\VoucherType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class OrderController extends Controller
{
    /**
     * List orders with search, filters, pagination, and stats
     */
    public function index(Request $request, Tenant $tenant)
    {
        try {
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
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                      ->orWhere('customer_email', 'like', "%{$search}%")
                      ->orWhere('customer_name', 'like', "%{$search}%");
                });
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $perPage = min($request->integer('per_page', 20), 50);
            $orders = $query->latest()->paginate($perPage);

            // Statistics
            $stats = [
                'total' => Order::where('tenant_id', $tenant->id)->count(),
                'pending' => Order::where('tenant_id', $tenant->id)->where('status', 'pending')->count(),
                'processing' => Order::where('tenant_id', $tenant->id)->where('status', 'processing')->count(),
                'delivered' => Order::where('tenant_id', $tenant->id)->where('status', 'delivered')->count(),
                'total_revenue' => (float) Order::where('tenant_id', $tenant->id)->where('payment_status', 'paid')->sum('total_amount'),
            ];

            return response()->json([
                'success' => true,
                'data' => $orders->map(fn($order) => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'customer_email' => $order->customer_email,
                    'customer_phone' => $order->customer_phone,
                    'status' => $order->status,
                    'status_color' => $order->status_color,
                    'payment_status' => $order->payment_status,
                    'payment_status_color' => $order->payment_status_color,
                    'payment_method' => $order->payment_method,
                    'subtotal' => (float) $order->subtotal,
                    'tax_amount' => (float) $order->tax_amount,
                    'shipping_amount' => (float) $order->shipping_amount,
                    'discount_amount' => (float) $order->discount_amount,
                    'total_amount' => (float) $order->total_amount,
                    'items_count' => $order->items->count(),
                    'is_editable' => $order->isEditable(),
                    'is_cancellable' => $order->isCancellable(),
                    'created_at' => $order->created_at->toIso8601String(),
                ]),
                'stats' => $stats,
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                    'from' => $orders->firstItem(),
                    'to' => $orders->lastItem(),
                ],
            ]);
        } catch (Exception $e) {
            Log::error('E-commerce orders list API error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to load orders.',
            ], 500);
        }
    }

    /**
     * Show order details
     */
    public function show(Request $request, Tenant $tenant, $orderId)
    {
        try {
            $order = Order::with(['customer', 'items.product', 'shippingAddress', 'billingAddress', 'voucher'])
                ->where('tenant_id', $tenant->id)
                ->findOrFail($orderId);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer' => $order->customer ? [
                        'id' => $order->customer->id,
                        'name' => $order->customer->name ?? $order->customer->first_name . ' ' . $order->customer->last_name,
                        'email' => $order->customer->email,
                        'phone' => $order->customer->phone,
                    ] : null,
                    'customer_name' => $order->customer_name,
                    'customer_email' => $order->customer_email,
                    'customer_phone' => $order->customer_phone,
                    'status' => $order->status,
                    'status_color' => $order->status_color,
                    'payment_status' => $order->payment_status,
                    'payment_status_color' => $order->payment_status_color,
                    'payment_method' => $order->payment_method,
                    'payment_gateway_reference' => $order->payment_gateway_reference,
                    'subtotal' => (float) $order->subtotal,
                    'tax_amount' => (float) $order->tax_amount,
                    'shipping_amount' => (float) $order->shipping_amount,
                    'discount_amount' => (float) $order->discount_amount,
                    'total_amount' => (float) $order->total_amount,
                    'coupon_code' => $order->coupon_code,
                    'notes' => $order->notes,
                    'admin_notes' => $order->admin_notes,
                    'is_editable' => $order->isEditable(),
                    'is_cancellable' => $order->isCancellable(),
                    'has_invoice' => (bool) $order->voucher_id,
                    'voucher_id' => $order->voucher_id,
                    'items' => $order->items->map(fn($item) => [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product_name,
                        'product_sku' => $item->product_sku,
                        'quantity' => (float) $item->quantity,
                        'unit_price' => (float) $item->unit_price,
                        'tax_amount' => (float) $item->tax_amount,
                        'discount_amount' => (float) $item->discount_amount,
                        'total_price' => (float) $item->total_price,
                    ]),
                    'shipping_address' => $order->shippingAddress ? [
                        'name' => $order->shippingAddress->name,
                        'phone' => $order->shippingAddress->phone,
                        'address_line1' => $order->shippingAddress->address_line1,
                        'address_line2' => $order->shippingAddress->address_line2,
                        'city' => $order->shippingAddress->city,
                        'state' => $order->shippingAddress->state,
                        'postal_code' => $order->shippingAddress->postal_code,
                        'country' => $order->shippingAddress->country,
                        'full_address' => $order->shippingAddress->full_address,
                    ] : null,
                    'billing_address' => $order->billingAddress ? [
                        'name' => $order->billingAddress->name,
                        'phone' => $order->billingAddress->phone,
                        'address_line1' => $order->billingAddress->address_line1,
                        'city' => $order->billingAddress->city,
                        'state' => $order->billingAddress->state,
                        'country' => $order->billingAddress->country,
                        'full_address' => $order->billingAddress->full_address,
                    ] : null,
                    'billing_same_as_shipping' => (bool) $order->billing_same_as_shipping,
                    'fulfilled_at' => $order->fulfilled_at?->toIso8601String(),
                    'cancelled_at' => $order->cancelled_at?->toIso8601String(),
                    'created_at' => $order->created_at->toIso8601String(),
                    'updated_at' => $order->updated_at->toIso8601String(),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Tenant $tenant, $orderId)
    {
        $order = Order::where('tenant_id', $tenant->id)->findOrFail($orderId);

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'admin_notes' => 'nullable|string',
        ]);

        try {
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
                } catch (Exception $e) {
                    Log::error('Failed to auto-create invoice from order', ['error' => $e->getMessage()]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Order status updated from {$oldStatus} to {$validated['status']}.",
                'data' => [
                    'status' => $order->status,
                    'status_color' => $order->status_color,
                    'fulfilled_at' => $order->fulfilled_at?->toIso8601String(),
                    'cancelled_at' => $order->cancelled_at?->toIso8601String(),
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Order status update API error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status.',
            ], 500);
        }
    }

    /**
     * Update order payment status
     */
    public function updatePaymentStatus(Request $request, Tenant $tenant, $orderId)
    {
        $order = Order::where('tenant_id', $tenant->id)->findOrFail($orderId);

        $validated = $request->validate([
            'payment_status' => 'required|in:unpaid,paid,partially_paid,refunded',
            'payment_date' => 'nullable|date',
            'payment_reference' => 'nullable|string|max:255',
            'bank_account_id' => 'nullable|exists:ledger_accounts,id',
            'payment_notes' => 'nullable|string',
        ]);

        try {
            $oldPaymentStatus = $order->payment_status;
            $order->update(['payment_status' => $validated['payment_status']]);

            // If payment becomes "paid" and invoice exists, create receipt voucher
            if ($validated['payment_status'] === 'paid' && $oldPaymentStatus !== 'paid' && $order->voucher_id) {
                try {
                    DB::beginTransaction();
                    $invoice = Voucher::find($order->voucher_id);
                    if ($invoice && $invoice->status === 'posted') {
                        $this->createReceiptVoucher($order, $invoice, $tenant, $validated);
                    }
                    DB::commit();
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error('Failed to create receipt voucher', ['error' => $e->getMessage()]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment status updated successfully.',
                'data' => [
                    'payment_status' => $order->payment_status,
                    'payment_status_color' => $order->payment_status_color,
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Order payment status update API error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update payment status.',
            ], 500);
        }
    }

    /**
     * Create invoice from order
     */
    public function createInvoice(Request $request, Tenant $tenant, $orderId)
    {
        $order = Order::with('items.product')
            ->where('tenant_id', $tenant->id)
            ->findOrFail($orderId);

        if ($order->voucher_id) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice already exists for this order.',
            ], 422);
        }

        try {
            DB::beginTransaction();
            $voucher = $this->createInvoiceFromOrder($order, $tenant);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Invoice created successfully.',
                'data' => [
                    'voucher_id' => $voucher->id,
                    'voucher_number' => $voucher->voucher_number,
                    'total_amount' => (float) $voucher->total_amount,
                ],
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Create invoice from order API error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create invoice.',
            ], 500);
        }
    }

    /**
     * Private: Create invoice voucher from order
     */
    private function createInvoiceFromOrder($order, $tenant)
    {
        $order->load('items.product');

        $voucherType = VoucherType::where('tenant_id', $tenant->id)
            ->where(function ($q) {
                $q->where('code', 'SALES')->orWhere('code', 'SV');
            })
            ->where('affects_inventory', true)
            ->first();

        if (!$voucherType) {
            throw new Exception('Sales voucher type not found.');
        }

        $lastVoucher = Voucher::where('tenant_id', $tenant->id)
            ->where('voucher_type_id', $voucherType->id)
            ->whereYear('voucher_date', date('Y'))
            ->latest('id')
            ->first();

        $nextNumber = 1;
        if ($lastVoucher) {
            preg_match('/(\d+)$/', $lastVoucher->voucher_number, $matches);
            if (isset($matches[1])) {
                $nextNumber = intval($matches[1]) + 1;
            }
        }

        $voucherNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        $totalAmount = 0;
        $inventoryItems = [];

        foreach ($order->items as $orderItem) {
            $product = $orderItem->product;
            if (!$product) continue;

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

        $voucher = Voucher::create([
            'tenant_id' => $tenant->id,
            'voucher_type_id' => $voucherType->id,
            'voucher_number' => $voucherNumber,
            'voucher_date' => now()->toDateString(),
            'reference' => 'Order #' . $order->order_number,
            'narration' => 'Sales invoice from e-commerce order #' . $order->order_number,
            'status' => 'posted',
            'total_amount' => $totalAmount,
            'created_by' => auth()->id(),
            'posted_by' => auth()->id(),
            'posted_at' => now(),
        ]);

        foreach ($inventoryItems as $item) {
            $voucher->items()->create([
                'tenant_id' => $tenant->id,
                'product_id' => $item['product_id'],
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'rate' => $item['rate'],
                'amount' => $item['amount'],
            ]);
        }

        $order->update(['voucher_id' => $voucher->id]);

        return $voucher;
    }

    /**
     * Private: Create receipt voucher when payment is recorded
     */
    private function createReceiptVoucher($order, $invoice, $tenant, $paymentData)
    {
        $receiptType = VoucherType::where('tenant_id', $tenant->id)
            ->where(function ($q) {
                $q->where('code', 'RV')->orWhere('code', 'RECEIPT');
            })
            ->first();

        if (!$receiptType) return;

        $lastReceipt = Voucher::where('tenant_id', $tenant->id)
            ->where('voucher_type_id', $receiptType->id)
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

        Voucher::create([
            'tenant_id' => $tenant->id,
            'voucher_type_id' => $receiptType->id,
            'voucher_number' => $voucherNumber,
            'voucher_date' => $paymentData['payment_date'] ?? now()->toDateString(),
            'reference' => 'Payment for Order #' . $order->order_number,
            'narration' => 'Receipt for e-commerce order #' . $order->order_number,
            'status' => 'posted',
            'total_amount' => $order->total_amount,
            'created_by' => auth()->id(),
            'posted_by' => auth()->id(),
            'posted_at' => now(),
        ]);
    }
}
