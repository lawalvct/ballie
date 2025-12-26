<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingAddress;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    /**
     * Display checkout page
     */
    public function index(Request $request)
    {
        $tenant = $request->current_tenant;
        $storeSettings = $tenant->ecommerceSettings;

        if (!$storeSettings || !$storeSettings->is_store_enabled) {
            abort(404, 'Store not available');
        }

        // Get cart
        $cart = $this->getCart($tenant);

        if (!$cart || $cart->items->count() === 0) {
            return redirect()->route('storefront.cart', ['tenant' => $tenant->slug])
                ->with('error', 'Your cart is empty');
        }

        // Get customer addresses if logged in
        $addresses = [];
        $customer = null;
        if (Auth::guard('customer')->check()) {
            $customer = Auth::guard('customer')->user()->customer;
            $addresses = $customer->addresses ?? collect();
        }

        // Get shipping methods
        $shippingMethods = $storeSettings->shipping_enabled
            ? $tenant->shippingMethods()->where('is_active', true)->orderBy('name')->get()
            : collect();

        return view('storefront.checkout.index', compact('tenant', 'storeSettings', 'cart', 'addresses', 'shippingMethods', 'customer'));
    }

    /**
     * Apply coupon code
     */
    public function applyCoupon(Request $request)
    {
        $tenant = $request->current_tenant;

        $validated = $request->validate([
            'coupon_code' => 'required|string',
        ]);

        $coupon = Coupon::where('tenant_id', $tenant->id)
            ->where('code', strtoupper($validated['coupon_code']))
            ->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code'
            ], 404);
        }

        $cart = $this->getCart($tenant);
        $subtotal = $cart->getSubtotal();

        $customerId = Auth::guard('customer')->check() ? Auth::guard('customer')->user()->customer_id : null;

        // Validate coupon
        if (!$coupon->isValid($subtotal, $customerId)) {
            return response()->json([
                'success' => false,
                'message' => $coupon->getValidationMessage($subtotal, $customerId)
            ], 400);
        }

        // Calculate discount
        $discount = $coupon->calculateDiscount($subtotal);

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully!',
            'discount' => number_format($discount, 2),
            'coupon_code' => $coupon->code,
        ]);
    }

    /**
     * Process checkout and create order
     */
    public function process(Request $request)
    {
        $tenant = $request->current_tenant;
        $storeSettings = $tenant->ecommerceSettings;

        // Conditionally build validation rules based on whether existing address is used
        $rules = [
            'shipping_address_id' => 'nullable|exists:shipping_addresses,id',
            'shipping_method_id' => 'nullable|exists:shipping_methods,id',
            'payment_method' => 'required|in:cash_on_delivery,paystack,flutterwave',
            'coupon_code' => 'nullable|string',
            'notes' => 'nullable|string',
        ];

        // Only validate new_address fields if shipping_address_id is not provided
        if (!$request->filled('shipping_address_id')) {
            $rules['new_address'] = 'required|array';
            $rules['new_address.name'] = 'required|string|max:255';
            $rules['new_address.phone'] = 'required|string|max:20';
            $rules['new_address.address_line1'] = 'required|string';
            $rules['new_address.address_line2'] = 'nullable|string';
            $rules['new_address.city'] = 'required|string';
            $rules['new_address.state'] = 'required|string';
            $rules['new_address.postal_code'] = 'nullable|string';
            $rules['new_address.country'] = 'required|string';
        }

        $validated = $request->validate($rules);

        $cart = $this->getCart($tenant);

        if (!$cart || $cart->items->count() === 0) {
            return back()->with('error', 'Your cart is empty');
        }

        // Get customer information
        $customer = null;
        $customerName = 'Guest';
        $customerEmail = 'guest@example.com';
        $customerPhone = '';

        if (Auth::guard('customer')->check()) {
            $customer = Auth::guard('customer')->user()->customer;
            $customerName = $customer->first_name . ' ' . $customer->last_name;
            $customerEmail = $customer->email ?? 'no-email@example.com';
            $customerPhone = $customer->phone ?? '';
        }

        try {
            DB::beginTransaction();

            // Calculate order amounts
            $subtotal = $cart->getSubtotal();
            $taxAmount = 0;
            $shippingAmount = 0;
            $discountAmount = 0;

            // Apply tax
            if ($storeSettings->tax_enabled && $storeSettings->tax_percentage) {
                $taxAmount = ($subtotal * $storeSettings->tax_percentage) / 100;
            }

            // Apply shipping
            if (!empty($validated['shipping_method_id'])) {
                $shippingMethod = $tenant->shippingMethods()->findOrFail($validated['shipping_method_id']);
                $shippingAmount = $shippingMethod->cost;
            }

            // Apply coupon
            if (!empty($validated['coupon_code'])) {
                $coupon = Coupon::where('tenant_id', $tenant->id)
                    ->where('code', strtoupper($validated['coupon_code']))
                    ->first();

                if ($coupon && $coupon->isValid($subtotal, null)) {
                    $discountAmount = $coupon->calculateDiscount($subtotal);
                }
            }

            $totalAmount = $subtotal + $taxAmount + $shippingAmount - $discountAmount;

            // Create or get shipping address
            $shippingAddressId = $validated['shipping_address_id'] ?? null;
            if (!$shippingAddressId && isset($validated['new_address'])) {
                $shippingAddress = ShippingAddress::create([
                    'tenant_id' => $tenant->id,
                    'customer_id' => $customer ? $customer->id : null,
                    'name' => $validated['new_address']['name'],
                    'phone' => $validated['new_address']['phone'],
                    'address_line1' => $validated['new_address']['address_line1'],
                    'address_line2' => $validated['new_address']['address_line2'] ?? null,
                    'city' => $validated['new_address']['city'],
                    'state' => $validated['new_address']['state'],
                    'postal_code' => $validated['new_address']['postal_code'] ?? null,
                    'country' => $validated['new_address']['country'],
                    'is_default' => false,
                ]);
                $shippingAddressId = $shippingAddress->id;
            }

            // Create order
            $order = Order::create([
                'tenant_id' => $tenant->id,
                'order_number' => Order::generateOrderNumber($tenant->id),
                'customer_id' => $customer ? $customer->id : null,
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'customer_phone' => $customerPhone,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_method' => $validated['payment_method'],
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'shipping_amount' => $shippingAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'coupon_code' => $validated['coupon_code'] ?? null,
                'shipping_address_id' => $shippingAddressId,
                'notes' => $validated['notes'] ?? null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Create order items
            foreach ($cart->items as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'product_sku' => $cartItem->product->sku ?? '',
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->product->sales_rate,
                    'total_price' => $cartItem->product->sales_rate * $cartItem->quantity,
                ]);
            }

            // Update coupon usage if applied
            if (!empty($validated['coupon_code']) && isset($coupon)) {
                $coupon->increment('usage_count');
                if ($customer) {
                    $coupon->usages()->create([
                        'customer_id' => $customer->id,
                        'order_id' => $order->id,
                    ]);
                }
            }

            // Clear cart
            $cart->items()->delete();

            DB::commit();

            // Redirect based on payment method
            if ($validated['payment_method'] === 'cash_on_delivery') {
                return redirect()->route('storefront.order.success', ['tenant' => $tenant->slug, 'order' => $order->id])
                    ->with('success', 'Order placed successfully! You will pay on delivery.');
            } else {
                // Redirect to payment gateway
                return redirect()->route('storefront.payment.process', ['tenant' => $tenant->slug, 'order' => $order->id]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to process checkout. Please try again.');
        }
    }

    /**
     * Display order success page
     */
    public function success(Request $request, $tenant, $orderId)
    {
        // Get the tenant
        if (is_string($tenant)) {
            $tenant = Tenant::where('slug', $tenant)->firstOrFail();
        }

        Log::info('Order success page accessed', [
            'order_id' => $orderId,
            'tenant_id' => $tenant->id,
            'tenant_slug' => $tenant->slug,
        ]);

        // Find the order
        $order = Order::where('id', $orderId)
            ->where('tenant_id', $tenant->id)
            ->with(['items.product', 'shippingAddress'])
            ->first();

        if (!$order) {
            Log::error('Order not found', [
                'order_id' => $orderId,
                'tenant_id' => $tenant->id,
            ]);
            abort(404, 'Order not found');
        }

        $storeSettings = $tenant->ecommerceSettings;

        return view('storefront.checkout.success', compact('tenant', 'order', 'storeSettings'));
    }

    /**
     * Show customer orders list
     */
    public function orders(Request $request)
    {
        $tenant = $request->current_tenant;
        $storeSettings = $tenant->ecommerceSettings;
        $customer = Auth::guard('customer')->user()->customer;

        $orders = Order::where('tenant_id', $tenant->id)
            ->where('customer_id', $customer->id)
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('storefront.orders.index', compact('tenant', 'storeSettings', 'orders'));
    }

    /**
     * Show order detail page
     */
    public function orderDetail(Request $request, $tenant, $orderId)
    {
        // Get the tenant
        if (is_string($tenant)) {
            $tenant = Tenant::where('slug', $tenant)->firstOrFail();
        }

        $customer = Auth::guard('customer')->user()->customer;

        $order = Order::where('id', $orderId)
            ->where('tenant_id', $tenant->id)
            ->where('customer_id', $customer->id)
            ->with(['items.product', 'shippingAddress'])
            ->firstOrFail();

        $storeSettings = $tenant->ecommerceSettings;

        return view('storefront.orders.detail', compact('tenant', 'order', 'storeSettings'));
    }

    /**
     * Download order invoice
     */
    public function downloadInvoice(Request $request, $tenant, $orderId)
    {
        // Get the tenant
        if (is_string($tenant)) {
            $tenant = Tenant::where('slug', $tenant)->firstOrFail();
        }

        $customer = Auth::guard('customer')->user()->customer;

        $order = Order::where('id', $orderId)
            ->where('tenant_id', $tenant->id)
            ->where('customer_id', $customer->id)
            ->with(['items.product', 'shippingAddress'])
            ->firstOrFail();

        $storeSettings = $tenant->ecommerceSettings;

        return view('storefront.orders.invoice', compact('tenant', 'order', 'storeSettings'));
    }

    /**
     * Submit order dispute
     */
    public function submitDispute(Request $request, $tenant, $orderId)
    {
        // Get the tenant
        if (is_string($tenant)) {
            $tenant = Tenant::where('slug', $tenant)->firstOrFail();
        }

        $customer = Auth::guard('customer')->user()->customer;

        $validated = $request->validate([
            'dispute_reason' => 'required|string|in:damaged,wrong_item,not_delivered,poor_quality,other',
            'dispute_message' => 'required|string|max:1000',
        ]);

        $order = Order::where('id', $orderId)
            ->where('tenant_id', $tenant->id)
            ->where('customer_id', $customer->id)
            ->firstOrFail();

        // Update order with dispute information
        $order->update([
            'admin_notes' => ($order->admin_notes ? $order->admin_notes . "\n\n" : '') .
                "DISPUTE SUBMITTED (" . now()->format('Y-m-d H:i') . "):\n" .
                "Reason: " . ucfirst(str_replace('_', ' ', $validated['dispute_reason'])) . "\n" .
                "Message: " . $validated['dispute_message'],
        ]);

        // You can also create a separate disputes table for better tracking
        // For now, we're just adding it to admin_notes

        return back()->with('success', 'Your dispute has been submitted. Our team will review it shortly.');
    }

    /**
     * Get cart for current user/session
     */
    private function getCart($tenant)
    {
        if (Auth::guard('customer')->check()) {
            return Cart::where('tenant_id', $tenant->id)
                ->where('customer_id', Auth::guard('customer')->id())
                ->with('items.product.primaryImage')
                ->first();
        } else {
            $sessionId = session()->getId();
            return Cart::where('tenant_id', $tenant->id)
                ->where('session_id', $sessionId)
                ->with('items.product.primaryImage')
                ->first();
        }
    }
}
