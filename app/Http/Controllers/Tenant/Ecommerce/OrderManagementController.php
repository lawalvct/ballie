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

    public function show(Request $request, $orderId)
    {
        $tenant = tenant();

        $order = Order::where('tenant_id', $tenant->id)
            ->with(['customer', 'items.product', 'shippingAddress', 'billingAddress', 'voucher'])
            ->findOrFail($orderId);

        return view('tenant.ecommerce.orders.show', compact('tenant', 'order'));
    }

    public function updateStatus(Request $request, $orderId)
    {
        $tenant = tenant();

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'admin_notes' => 'nullable|string'
        ]);

        $order = Order::where('tenant_id', $tenant->id)->findOrFail($orderId);

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

    public function updatePaymentStatus(Request $request, $orderId)
    {
        $tenant = tenant();

        $validated = $request->validate([
            'payment_status' => 'required|in:unpaid,paid,partially_paid,refunded'
        ]);

        $order = Order::where('tenant_id', $tenant->id)->findOrFail($orderId);
        $order->update(['payment_status' => $validated['payment_status']]);

        return redirect()->back()->with('success', 'Payment status updated successfully!');
    }

    public function createInvoice(Request $request, $orderId)
    {
        $tenant = tenant();
        $order = Order::where('tenant_id', $tenant->id)->with('items.product')->findOrFail($orderId);

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
        // Get Sales Invoice voucher type
        $voucherType = VoucherType::where('tenant_id', $tenant->id)
            ->where('code', 'SALES')
            ->firstOrFail();

        // Generate voucher number
        $lastVoucher = Voucher::where('tenant_id', $tenant->id)
            ->where('voucher_type_id', $voucherType->id)
            ->whereYear('voucher_date', date('Y'))
            ->latest('id')
            ->first();

        $number = $lastVoucher ? intval(substr($lastVoucher->voucher_number, -4)) + 1 : 1;
        $voucherNumber = str_pad($number, 4, '0', STR_PAD_LEFT);

        // Create voucher
        $voucher = Voucher::create([
            'tenant_id' => $tenant->id,
            'voucher_type_id' => $voucherType->id,
            'voucher_number' => $voucherNumber,
            'voucher_date' => now()->toDateString(),
            'reference' => 'Order #' . $order->order_number,
            'narration' => 'Sales invoice generated from e-commerce order #' . $order->order_number,
            'status' => 'posted', // Auto-post for confirmed orders
            'total_amount' => $order->total_amount,
            'created_by' => auth()->id(),
        ]);

        // Create voucher items from order items
        foreach ($order->items as $orderItem) {
            $product = $orderItem->product;

            $voucher->items()->create([
                'tenant_id' => $tenant->id,
                'product_id' => $product->id,
                'product_name' => $orderItem->product_name,
                'description' => $orderItem->product_name,
                'quantity' => $orderItem->quantity,
                'rate' => $orderItem->unit_price,
                'amount' => $orderItem->total_price,
            ]);

            // Create accounting entries
            if ($product->sales_account_id) {
                VoucherEntry::create([
                    'voucher_id' => $voucher->id,
                    'ledger_account_id' => $product->sales_account_id,
                    'debit_amount' => 0,
                    'credit_amount' => $orderItem->total_price,
                    'narration' => 'Sale of ' . $product->name,
                ]);
            }

            // Update stock if product maintains stock
            if ($product->maintain_stock) {
                $product->decrement('current_stock', $orderItem->quantity);
                $product->update([
                    'current_stock_value' => $product->current_stock * $product->purchase_rate
                ]);

                // Create stock movement
                $product->stockMovements()->create([
                    'tenant_id' => $tenant->id,
                    'transaction_type' => 'sales',
                    'transaction_reference' => $voucher->voucher_number,
                    'transaction_date' => now()->toDateString(),
                    'quantity' => -$orderItem->quantity,
                    'rate' => $product->purchase_rate ?? $product->sales_rate,
                    'reference' => 'E-commerce Order #' . $order->order_number,
                    'created_by' => auth()->id(),
                ]);
            }
        }

        // Customer entry (Accounts Receivable)
        if ($order->customer && $order->customer->ledger_account_id) {
            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $order->customer->ledger_account_id,
                'debit_amount' => $order->total_amount,
                'credit_amount' => 0,
                'narration' => 'Sales to customer - Order #' . $order->order_number,
            ]);
        }

        // Link voucher to order
        $order->update(['voucher_id' => $voucher->id]);

        return $voucher;
    }
}
