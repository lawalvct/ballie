<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingAddress;
use App\Models\Cart;
use App\Models\Coupon;
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
            ? $tenant->shippingMethods()->where('is_active', true)->orderBy('sort_order')->get()
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

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string|max:20',
            'shipping_address_id' => 'nullable|exists:shipping_addresses,id',
            'shipping_name' => 'required_without:shipping_address_id|string|max:255',
            'shipping_phone' => 'required_without:shipping_address_id|string|max:20',
            'shipping_address_line1' => 'required_without:shipping_address_id|string',
            'shipping_address_line2' => 'nullable|string',
            'shipping_city' => 'required_without:shipping_address_id|string',
            'shipping_state' => 'required_without:shipping_address_id|string',
            'shipping_postal_code' => 'required_without:shipping_address_id|string',
            'shipping_country' => 'required_without:shipping_address_id|string',
            'shipping_method_id' => 'nullable|exists:shipping_methods,id',
            'payment_method' => 'required|in:cod,paystack,flutterwave',
            'coupon_code' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $cart = $this->getCart($tenant);

        if (!$cart || $cart->items->count() === 0) {
            return back()->with('error', 'Your cart is empty');
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
            if ($validated['shipping_method_id']) {
                $shippingMethod = $tenant->shippingMethods()->findOrFail($validated['shipping_method_id']);
                $shippingAmount = $shippingMethod->cost;
            }

            // Apply coupon
            if ($validated['coupon_code']) {
                $coupon = Coupon::where('tenant_id', $tenant->id)
                    ->where('code', strtoupper($validated['coupon_code']))
                    ->first();

                if ($coupon && $coupon->isValid($subtotal, null)) {
                    $discountAmount = $coupon->calculateDiscount($subtotal);
                }
            }

            $totalAmount = $subtotal + $taxAmount + $shippingAmount - $discountAmount;

            // Create or get shipping address
            $shippingAddressId = $validated['shipping_address_id'];
            if (!$shippingAddressId) {
                $shippingAddress = ShippingAddress::create([
                    'customer_id' => Auth::guard('customer')->check() ? Auth::guard('customer')->user()->customer_id : null,
                    'name' => $validated['shipping_name'],
                    'phone' => $validated['shipping_phone'],
                    'address_line1' => $validated['shipping_address_line1'],
                    'address_line2' => $validated['shipping_address_line2'],
                    'city' => $validated['shipping_city'],
                    'state' => $validated['shipping_state'],
                    'postal_code' => $validated['shipping_postal_code'],
                    'country' => $validated['shipping_country'],
                    'is_default' => false,
                ]);
                $shippingAddressId = $shippingAddress->id;
            }

            // Create order
            $order = Order::create([
                'tenant_id' => $tenant->id,
                'order_number' => Order::generateOrderNumber($tenant->id),
                'customer_id' => Auth::guard('customer')->check() ? Auth::guard('customer')->user()->customer_id : null,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'status' => 'pending',
                'payment_status' => $validated['payment_method'] === 'cod' ? 'pending' : 'pending',
                'payment_method' => $validated['payment_method'],
                'subtotal_amount' => $subtotal,
                'tax_amount' => $taxAmount,
                'shipping_amount' => $shippingAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'coupon_code' => $validated['coupon_code'] ?? null,
                'shipping_address_id' => $shippingAddressId,
                'notes' => $validated['notes'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Create order items
            foreach ($cart->items as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'product_sku' => $cartItem->product->sku,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->unit_price,
                    'total_price' => $cartItem->total_price,
                ]);
            }

            // Update coupon usage if applied
            if ($validated['coupon_code'] && isset($coupon)) {
                $coupon->increment('usage_count');
                if (Auth::guard('customer')->check()) {
                    $coupon->usages()->create([
                        'customer_id' => Auth::guard('customer')->user()->customer_id,
                        'order_id' => $order->id,
                    ]);
                }
            }

            // Clear cart
            $cart->items()->delete();

            DB::commit();

            // Redirect based on payment method
            if ($validated['payment_method'] === 'cod') {
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
    public function success(Request $request, $orderId)
    {
        $tenant = $request->current_tenant;
        $storeSettings = $tenant->ecommerceSettings;

        $order = Order::where('tenant_id', $tenant->id)
            ->where('id', $orderId)
            ->with('items.product', 'shippingAddress')
            ->firstOrFail();

        return view('storefront.checkout.success', compact('tenant', 'storeSettings', 'order'));
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
