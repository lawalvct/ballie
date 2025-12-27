<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingAddress;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Tenant;
use App\Models\Voucher;
use App\Models\VoucherType;
use App\Models\VoucherEntry;
use App\Models\Product;
use App\Models\LedgerAccount;
use App\Helpers\PaymentHelper;
use App\Helpers\PaystackPaymentHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
            'payment_method' => 'required|in:cash_on_delivery,nomba,paystack,stripe,flutterwave,bank_transfer',
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
            } elseif ($validated['payment_method'] === 'nomba') {
                // Process Nomba payment
                return $this->processNombaPayment($order, $tenant, $customerEmail);
            } elseif ($validated['payment_method'] === 'paystack') {
                // Process Paystack payment
                return $this->processPaystackPayment($order, $tenant, $customerEmail);
            } else {
                // Redirect to other payment gateways (flutterwave, stripe)
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

    /**
     * Process Nomba payment for order
     */
    private function processNombaPayment(Order $order, Tenant $tenant, string $customerEmail)
    {
        try {
            // Initialize payment helper
            $paymentHelper = new PaymentHelper();

            // Check if Nomba credentials are configured
            $tokenData = $paymentHelper->nombaAccessToken();
            if (!$tokenData) {
                Log::error('Nomba credentials not configured for storefront checkout', [
                    'tenant_id' => $tenant->id,
                    'order_id' => $order->id
                ]);
                return redirect()->route('storefront.cart', ['tenant' => $tenant->slug])
                    ->with('error', 'Payment gateway not configured. Please try another payment method or contact the store.');
            }

            // Generate unique payment reference
            $paymentReference = 'ORD_' . strtoupper(Str::random(8)) . '_' . $order->id;

            // Store payment reference in order
            $order->update([
                'payment_gateway_reference' => $paymentReference,
            ]);

            // Prepare callback URL
            $callbackUrl = route('storefront.payment.callback', [
                'tenant' => $tenant->slug,
                'order' => $order->id
            ]);

            Log::info('Initiating Nomba payment for storefront order', [
                'order_id' => $order->id,
                'amount' => $order->total_amount,
                'email' => $customerEmail,
                'callbackUrl' => $callbackUrl,
                'paymentReference' => $paymentReference
            ]);

            // Process payment with Nomba
            $paymentResult = $paymentHelper->processPayment(
                $order->total_amount,
                'NGN',
                $customerEmail,
                $callbackUrl,
                $paymentReference
            );

            Log::info('Nomba payment result for storefront', $paymentResult);

            if ($paymentResult['status']) {
                // Update order with gateway reference
                $order->update([
                    'payment_gateway_reference' => $paymentResult['orderReference'],
                ]);

                Log::info('Redirecting to Nomba checkout for storefront order', [
                    'order_id' => $order->id,
                    'checkoutLink' => $paymentResult['checkoutLink']
                ]);

                // Redirect to Nomba checkout
                return redirect($paymentResult['checkoutLink']);

            } else {
                Log::error('Nomba payment initiation failed for storefront', [
                    'order_id' => $order->id,
                    'error' => $paymentResult['message'] ?? 'Unknown error',
                    'full_result' => $paymentResult
                ]);

                return redirect()->route('storefront.cart', ['tenant' => $tenant->slug])
                    ->with('error', 'Failed to initiate payment: ' . ($paymentResult['message'] ?? 'Payment service unavailable'));
            }

        } catch (\Exception $e) {
            Log::error('Nomba payment processing exception', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('storefront.cart', ['tenant' => $tenant->slug])
                ->with('error', 'Payment processing failed. Please try again.');
        }
    }

    /**
     * Process Paystack payment for order
     */
    private function processPaystackPayment(Order $order, Tenant $tenant, string $customerEmail)
    {
        try {
            // Initialize Paystack helper
            $paystackHelper = new PaystackPaymentHelper();

            // Check if Paystack is configured
            if (!$paystackHelper->isConfigured()) {
                Log::error('Paystack credentials not configured for storefront checkout', [
                    'tenant_id' => $tenant->id,
                    'order_id' => $order->id
                ]);
                return redirect()->route('storefront.cart', ['tenant' => $tenant->slug])
                    ->with('error', 'Payment gateway not configured. Please try another payment method or contact the store.');
            }

            // Generate unique payment reference
            $paymentReference = 'ORD_PS_' . strtoupper(Str::random(8)) . '_' . $order->id;

            // Store payment reference in order
            $order->update([
                'payment_gateway_reference' => $paymentReference,
            ]);

            // Prepare callback URL
            $callbackUrl = route('storefront.payment.callback', [
                'tenant' => $tenant->slug,
                'order' => $order->id
            ]);

            Log::info('Initiating Paystack payment for storefront order', [
                'order_id' => $order->id,
                'amount' => $order->total_amount,
                'email' => $customerEmail,
                'callbackUrl' => $callbackUrl,
                'paymentReference' => $paymentReference
            ]);

            // Initialize transaction with Paystack
            $paymentResult = $paystackHelper->initializeTransaction(
                $order->total_amount,
                $customerEmail,
                $callbackUrl,
                $paymentReference,
                [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'tenant_id' => $tenant->id
                ]
            );

            Log::info('Paystack payment result for storefront', $paymentResult);

            if ($paymentResult['status']) {
                // Update order with gateway reference
                $order->update([
                    'payment_gateway_reference' => $paymentResult['reference'],
                ]);

                Log::info('Redirecting to Paystack checkout for storefront order', [
                    'order_id' => $order->id,
                    'authorization_url' => $paymentResult['authorization_url']
                ]);

                // Redirect to Paystack checkout
                return redirect($paymentResult['authorization_url']);

            } else {
                Log::error('Paystack payment initiation failed for storefront', [
                    'order_id' => $order->id,
                    'error' => $paymentResult['message'] ?? 'Unknown error',
                    'full_result' => $paymentResult
                ]);

                return redirect()->route('storefront.cart', ['tenant' => $tenant->slug])
                    ->with('error', 'Failed to initiate payment: ' . ($paymentResult['message'] ?? 'Payment service unavailable'));
            }

        } catch (\Exception $e) {
            Log::error('Paystack payment processing exception', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('storefront.cart', ['tenant' => $tenant->slug])
                ->with('error', 'Payment processing failed. Please try again.');
        }
    }

    /**
     * Handle payment callback (supports Nomba and Paystack)
     */
    public function paymentCallback(Request $request, $tenant, $orderId)
    {
        Log::info('Storefront payment callback started', [
            'tenant' => $tenant,
            'order_id' => $orderId,
            'request_data' => $request->all()
        ]);

        try {
            // Get the tenant
            if (is_string($tenant)) {
                $tenant = Tenant::where('slug', $tenant)->firstOrFail();
            }

            // Find the order
            $order = Order::where('id', $orderId)
                ->where('tenant_id', $tenant->id)
                ->firstOrFail();

            // Verify payment based on payment method
            $verificationResult = $this->verifyPaymentByMethod($order);

            Log::info('Payment verification result', [
                'order_id' => $order->id,
                'payment_method' => $order->payment_method,
                'verification_result' => $verificationResult
            ]);

            if ($verificationResult['status'] && $verificationResult['payment_status'] === 'successful') {
                // Payment successful
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed', // Auto-confirm on payment
                ]);

                Log::info('Storefront order payment successful', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number
                ]);

                // Create invoice and receipt voucher for online payment
                try {
                    DB::beginTransaction();

                    // Load order items
                    $order->load('items.product');

                    // Create invoice from order
                    $invoice = $this->createInvoiceFromOrder($order, $tenant);
                    Log::info('Invoice created for online payment', [
                        'order_id' => $order->id,
                        'invoice_id' => $invoice->id
                    ]);

                    // Create receipt voucher
                    $paymentData = [
                        'payment_date' => now()->toDateString(),
                        'payment_reference' => $order->payment_method . ' - ' . $order->payment_gateway_reference,
                        'payment_notes' => 'Payment received via ' . strtoupper($order->payment_method) . ' for e-commerce order #' . $order->order_number,
                    ];
                    $this->createReceiptVoucher($order, $invoice, $tenant, $paymentData);
                    Log::info('Receipt voucher created for online payment', [
                        'order_id' => $order->id,
                        'invoice_id' => $invoice->id
                    ]);

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Failed to create invoice/receipt for online payment', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    // Don't fail the order, just log the error
                }

                $storeSettings = $tenant->ecommerceSettings;

                return redirect()->route('storefront.order.success', [
                    'tenant' => $tenant->slug,
                    'order' => $order->id
                ])->with('success', 'Payment successful! Your order has been confirmed.');

            } else {
                // Payment failed
                $order->update([
                    'payment_status' => 'failed',
                ]);

                Log::warning('Storefront order payment failed', [
                    'order_id' => $order->id,
                    'verification_result' => $verificationResult
                ]);

                return redirect()->route('storefront.order.detail', [
                    'tenant' => $tenant->slug,
                    'order' => $order->id
                ])->with('error', 'Payment was not successful. Please try again or contact the store.');
            }

        } catch (\Exception $e) {
            Log::error('Storefront payment callback failed', [
                'order_id' => $orderId,
                'tenant' => $tenant,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('storefront.orders', ['tenant' => is_string($tenant) ? $tenant : $tenant->slug])
                ->with('error', 'Payment verification failed. Please contact the store if you were charged.');
        }
    }

    /**
     * Retry payment for an existing order
     */
    public function retryPayment(Request $request, $tenant, $orderId)
    {
        // Get the tenant
        if (is_string($tenant)) {
            $tenant = Tenant::where('slug', $tenant)->firstOrFail();
        }

        $customer = Auth::guard('customer')->user()->customer;

        $order = Order::where('id', $orderId)
            ->where('tenant_id', $tenant->id)
            ->where('customer_id', $customer->id)
            ->where('payment_status', '!=', 'paid')
            ->firstOrFail();

        // Only allow retry for online payments
        if (!in_array($order->payment_method, ['nomba', 'paystack'])) {
            return back()->with('error', 'Payment retry is only available for online payments.');
        }

        if ($order->payment_method === 'nomba') {
            return $this->processNombaPayment($order, $tenant, $customer->email ?? 'customer@example.com');
        } elseif ($order->payment_method === 'paystack') {
            return $this->processPaystackPayment($order, $tenant, $customer->email ?? 'customer@example.com');
        }

        return back()->with('error', 'Payment method not supported for retry.');
    }

    /**
     * Verify payment based on payment method
     */
    private function verifyPaymentByMethod(Order $order)
    {
        $paymentMethod = $order->payment_method;
        $reference = $order->payment_gateway_reference;

        if ($paymentMethod === 'nomba') {
            $paymentHelper = new PaymentHelper();
            return $paymentHelper->verifyPayment($reference);
        } elseif ($paymentMethod === 'paystack') {
            $paystackHelper = new PaystackPaymentHelper();
            return $paystackHelper->verifyTransaction($reference);
        }

        return [
            'status' => false,
            'payment_status' => 'unknown',
            'message' => 'Unsupported payment method'
        ];
    }

    /**
     * Create invoice from order (copied from OrderManagementController)
     */
    private function createInvoiceFromOrder($order, $tenant)
    {
        Log::info('Creating invoice from order', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'tenant_id' => $tenant->id
        ]);

        // Get Sales Invoice voucher type
        $voucherType = VoucherType::where('tenant_id', $tenant->id)
            ->where(function($q) {
                $q->where('code', 'SALES')->orWhere('code', 'SV');
            })
            ->where('affects_inventory', true)
            ->first();

        if (!$voucherType) {
            throw new \Exception('Sales voucher type not found. Please create a Sales Invoice voucher type first.');
        }

        // Generate voucher number
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

        // Prepare inventory items array
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
            'status' => 'posted',
            'total_amount' => $totalAmount,
            'created_by' => 1, // System user for storefront orders
            'posted_by' => 1,
            'posted_at' => now(),
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

        // Get customer ledger account
        $customerLedgerId = null;
        if ($order->customer && $order->customer->ledger_account_id) {
            $customerLedgerId = $order->customer->ledger_account_id;
        }

        // Create accounting entries
        $this->createAccountingEntries($voucher, $inventoryItems, $tenant, $customerLedgerId, $order->tax_amount ?? 0);

        // Update product stock
        $this->updateProductStock($inventoryItems, 'decrease', $voucher);

        // Link voucher to order
        $order->update(['voucher_id' => $voucher->id]);

        return $voucher;
    }

    /**
     * Create accounting entries for invoice
     */
    private function createAccountingEntries($voucher, $inventoryItems, $tenant, $customerLedgerId, $taxAmount = 0)
    {
        // Get the customer account
        $partyAccount = null;
        if ($customerLedgerId) {
            $partyAccount = LedgerAccount::find($customerLedgerId);
        }

        $totalAmount = collect($inventoryItems)->sum('amount');

        // Add tax to total amount
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

        // Debit: Customer Account (Accounts Receivable)
        if ($partyAccount) {
            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $partyAccount->id,
                'debit_amount' => $totalAmount,
                'credit_amount' => 0,
                'particulars' => 'Sales invoice - ' . $voucher->voucher_number,
            ]);

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

            $ledgerAccount = LedgerAccount::find($accountId);
            if ($ledgerAccount) {
                $ledgerAccount->updateCurrentBalance();
            }
        }

        // Credit: VAT Output (if tax exists)
        if ($taxAmount > 0) {
            $vatOutputAccount = LedgerAccount::where('tenant_id', $tenant->id)
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

                $vatOutputAccount->updateCurrentBalance();
            }
        }

        // COGS ENTRIES
        $cogsAccount = LedgerAccount::where('tenant_id', $tenant->id)
            ->whereHas('accountGroup', function($q) {
                $q->where('code', 'COGS');
            })
            ->where('is_active', true)
            ->first();

        $inventoryAccount = LedgerAccount::where('tenant_id', $tenant->id)
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
                // Debit COGS
                VoucherEntry::create([
                    'voucher_id' => $voucher->id,
                    'ledger_account_id' => $cogsAccount->id,
                    'debit_amount' => $totalCogs,
                    'credit_amount' => 0,
                    'particulars' => 'Cost of Goods Sold - ' . $voucher->voucher_number,
                ]);

                // Credit Inventory
                VoucherEntry::create([
                    'voucher_id' => $voucher->id,
                    'ledger_account_id' => $inventoryAccount->id,
                    'debit_amount' => 0,
                    'credit_amount' => $totalCogs,
                    'particulars' => 'Inventory reduction - ' . $voucher->voucher_number,
                ]);

                $cogsAccount->updateCurrentBalance();
                $inventoryAccount->updateCurrentBalance();
            }
        }
    }

    /**
     * Update product stock
     */
    private function updateProductStock($inventoryItems, $operation, $voucher)
    {
        foreach ($inventoryItems as $item) {
            $product = Product::find($item['product_id']);

            if (!$product || !$product->maintain_stock) {
                continue;
            }

            $quantity = $item['quantity'];
            $oldStock = $product->current_stock;

            if ($operation === 'decrease') {
                $product->decrement('current_stock', $quantity);
                $product->refresh();
                $newStock = $product->current_stock;

                $product->update([
                    'current_stock_value' => $product->current_stock * ($product->purchase_rate ?? 0)
                ]);

                $product->stockMovements()->create([
                    'tenant_id' => $product->tenant_id,
                    'type' => 'out',
                    'transaction_type' => 'sales',
                    'transaction_reference' => $voucher->voucher_number,
                    'transaction_date' => now()->toDateString(),
                    'quantity' => -$quantity,
                    'old_stock' => $oldStock,
                    'new_stock' => $newStock,
                    'rate' => $product->purchase_rate ?? $product->sales_rate,
                    'reference' => $voucher->reference ?? 'Sales Invoice',
                    'created_by' => 1,
                ]);
            }
        }
    }

    /**
     * Create receipt voucher for order payment
     */
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

        // Get default cash/bank account
        $bankAccount = LedgerAccount::where('tenant_id', $tenant->id)
            ->whereHas('accountGroup', function($q) {
                $q->where('code', 'CA');
            })
            ->where(function($q) {
                $q->where('name', 'LIKE', '%Cash%')
                  ->orWhere('name', 'LIKE', '%Bank%');
            })
            ->where('is_active', true)
            ->first();

        if (!$bankAccount) {
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
            'created_by' => 1,
            'posted_at' => now(),
            'posted_by' => 1,
        ]);

        // Create accounting entries for receipt
        // Debit: Bank/Cash Account
        VoucherEntry::create([
            'voucher_id' => $receiptVoucher->id,
            'ledger_account_id' => $bankAccount->id,
            'debit_amount' => $order->total_amount,
            'credit_amount' => 0,
            'particulars' => 'Payment received from ' . $customerAccount->name . ' - Order #' . $order->order_number,
        ]);

        // Credit: Customer Account
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
            $customerLedger = LedgerAccount::find($customer->ledger_account_id);
            if ($customerLedger) {
                $outstandingBalance = max(0, $customerLedger->current_balance);
                $customer->update(['outstanding_balance' => $outstandingBalance]);
            }
        }

        return $receiptVoucher;
    }
}
