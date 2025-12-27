<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display the shopping cart
     */
    public function index(Request $request)
    {
        $tenant = $request->current_tenant;
        $storeSettings = $tenant->ecommerceSettings;

        if (!$storeSettings || !$storeSettings->is_store_enabled) {
            abort(404, 'Store not available');
        }

        $cart = $this->getOrCreateCart($tenant);

        return view('storefront.cart.index', compact('tenant', 'storeSettings', 'cart'));
    }

    /**
     * Add product to cart
     */
    public function add(Request $request)
    {
        $tenant = $request->current_tenant;

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::where('tenant_id', $tenant->id)
            ->where('id', $validated['product_id'])
            ->where('is_visible_online', true)
            ->where('is_active', true)
            ->firstOrFail();

        // Check stock availability
        if ($product->maintain_stock && $product->current_stock < $validated['quantity']) {
            return back()->with('error', 'Insufficient stock available. Only ' . $product->current_stock . ' items remaining.');
        }

        $cart = $this->getOrCreateCart($tenant);

        // Check if product already in cart
        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            // Update quantity
            $newQuantity = $cartItem->quantity + $validated['quantity'];

            // Check stock for new quantity
            if ($product->maintain_stock && $product->current_stock < $newQuantity) {
                return back()->with('error', 'Cannot add more. Maximum available: ' . $product->current_stock);
            }

            $cartItem->update([
                'quantity' => $newQuantity,
                'unit_price' => $product->sales_rate,
                'total_price' => $newQuantity * $product->sales_rate,
            ]);
        } else {
            // Add new cart item
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $validated['quantity'],
                'unit_price' => $product->sales_rate,
                'total_price' => $validated['quantity'] * $product->sales_rate,
            ]);
        }

        // Recalculate cart totals
        $cart = $cart->fresh();

        // Return JSON for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product added to cart!',
                'cart_count' => $cart->items->sum('quantity'),
                'cart_subtotal' => $cart->getSubtotal(),
            ]);
        }

        return back()->with('success', 'Product added to cart!');
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $tenant, $item)
    {
        $tenant = $request->current_tenant;

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = $this->getOrCreateCart($tenant);

        $cartItem = $cart->items()->findOrFail($item);
        $product = $cartItem->product;

        // Check stock
        if ($product->maintain_stock && $product->current_stock < $validated['quantity']) {
            return back()->with('error', 'Insufficient stock. Only ' . $product->current_stock . ' items available.');
        }

        $cartItem->update([
            'quantity' => $validated['quantity'],
            'unit_price' => $product->sales_rate,
            'total_price' => $validated['quantity'] * $product->sales_rate,
        ]);

        return back()->with('success', 'Cart updated successfully');
    }

    /**
     * Remove item from cart
     */
    public function remove(Request $request, $tenant, $item)
    {
        $tenant = $request->current_tenant;
        $cart = $this->getOrCreateCart($tenant);

        $cartItem = $cart->items()->findOrFail($item);
        $cartItem->delete();

        return back()->with('success', 'Item removed from cart');
    }

    /**
     * Clear entire cart
     */
    public function clear(Request $request)
    {
        $tenant = $request->current_tenant;
        $cart = $this->getOrCreateCart($tenant);

        $cart->items()->delete();

        return back()->with('success', 'Cart cleared');
    }

    /**
     * Get or create cart for current user/session
     */
    private function getOrCreateCart($tenant)
    {
        if (Auth::guard('customer')->check()) {
            // Authenticated customer
            $cart = Cart::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'customer_id' => Auth::guard('customer')->id(),
                ],
                [
                    'expires_at' => now()->addDays(30),
                ]
            );
        } else {
            // Guest cart using session
            $sessionId = session()->getId();
            $cart = Cart::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'session_id' => $sessionId,
                ],
                [
                    'expires_at' => now()->addDays(7),
                ]
            );
        }

        return $cart->load('items.product.primaryImage');
    }

    /**
     * Get cart item count (for AJAX)
     */
    public function count(Request $request)
    {
        $tenant = $request->current_tenant;
        $cart = $this->getOrCreateCart($tenant);

        return response()->json([
            'count' => $cart->items->sum('quantity'),
            'subtotal' => $cart->getSubtotal(),
        ]);
    }
}
