# E-commerce Module Implementation Plan

## Overview
Add a public storefront for each tenant where buyers can browse products, add to cart, checkout, and pay online or via cash-on-delivery. Reuses existing inventory/products system and integrates with accounting.

---

## URL Structure

### Storefront (Public - No Auth Required Initially)
```
ballie.co/{tenant-slug}/store              - Store homepage
ballie.co/{tenant-slug}/store/products     - Product listing
ballie.co/{tenant-slug}/store/products/{slug} - Product detail
ballie.co/{tenant-slug}/store/cart         - Shopping cart
ballie.co/{tenant-slug}/store/wishlist     - Wishlist
ballie.co/{tenant-slug}/store/checkout     - Checkout page
ballie.co/{tenant-slug}/store/orders       - Order history (requires auth)
ballie.co/{tenant-slug}/store/orders/{id}  - Order tracking
```

### Admin Store Management (Tenant Dashboard)
```
ballie.co/{tenant-slug}/ecommerce/settings          - Store settings & configuration
ballie.co/{tenant-slug}/ecommerce/products          - Product management (existing inventory)
ballie.co/{tenant-slug}/ecommerce/orders            - Order management
ballie.co/{tenant-slug}/ecommerce/orders/{id}       - Order detail & fulfillment
ballie.co/{tenant-slug}/ecommerce/coupons           - Coupon management
ballie.co/{tenant-slug}/ecommerce/shipping-methods  - Shipping configuration
ballie.co/{tenant-slug}/ecommerce/payment-gateways  - Payment gateway settings
ballie.co/{tenant-slug}/ecommerce/reports           - Sales reports
```

---

## User Authentication Flow

### Customer Account Creation (Buyer)
1. **Trigger Points**:
   - When clicking "Add to Cart"
   - When clicking "Add to Wishlist"
   - When clicking "Checkout"

2. **Authentication Options**:
   - Email & Password (simple registration)
   - Google Social Login
   - Admin can toggle each option on/off in store settings

3. **Guest Checkout**:
   - Admin can enable/disable guest checkout
   - If enabled, buyers can checkout without account (email only)
   - After order, prompt to create account for order tracking

4. **Account Features**:
   - Order history
   - Saved addresses
   - Wishlist persistence
   - Email notifications

---

## Database Schema

### 1. New Tables

#### `ecommerce_settings` Table
```sql
- id
- tenant_id (FK)
- is_store_enabled (boolean) - Master on/off switch
- store_name
- store_description
- store_logo
- store_banner
- allow_guest_checkout (boolean)
- allow_email_registration (boolean)
- allow_google_login (boolean)
- require_phone_number (boolean)
- default_currency (default: NGN)
- tax_enabled (boolean)
- tax_percentage
- shipping_enabled (boolean)
- meta_title
- meta_description
- social_facebook
- social_instagram
- social_twitter
- theme_primary_color
- theme_secondary_color
- created_at
- updated_at
```

#### `orders` Table
```sql
- id
- tenant_id (FK)
- order_number (unique per tenant) - e.g., ORD-2025-0001
- customer_id (FK) - nullable for guest orders
- customer_email
- customer_name
- customer_phone
- status (enum: pending, confirmed, processing, shipped, delivered, cancelled)
- payment_status (enum: unpaid, paid, partially_paid, refunded)
- payment_method (enum: cash_on_delivery, paystack, flutterwave, bank_transfer)
- payment_gateway_reference - Transaction reference from payment gateway
- subtotal (decimal)
- tax_amount (decimal)
- shipping_amount (decimal)
- discount_amount (decimal)
- total_amount (decimal)
- coupon_code
- shipping_address_id (FK)
- billing_same_as_shipping (boolean)
- billing_address_id (FK) - nullable
- notes - Customer notes
- admin_notes - Internal notes
- ip_address
- user_agent
- voucher_id (FK) - Link to accounting invoice
- fulfilled_at
- cancelled_at
- cancellation_reason
- created_at
- updated_at
```

#### `order_items` Table
```sql
- id
- order_id (FK)
- product_id (FK)
- product_name - Snapshot at order time
- product_sku
- quantity (decimal)
- unit_price (decimal)
- tax_amount (decimal)
- discount_amount (decimal)
- total_price (decimal)
- created_at
- updated_at
```

#### `carts` Table
```sql
- id
- tenant_id (FK)
- customer_id (FK) - nullable for guest carts
- session_id - For guest carts
- expires_at - Auto-cleanup old carts
- created_at
- updated_at
```

#### `cart_items` Table
```sql
- id
- cart_id (FK)
- product_id (FK)
- quantity (decimal)
- created_at
- updated_at
```

#### `wishlists` Table
```sql
- id
- tenant_id (FK)
- customer_id (FK)
- created_at
- updated_at
```

#### `wishlist_items` Table
```sql
- id
- wishlist_id (FK)
- product_id (FK)
- created_at
- updated_at
```

#### `shipping_addresses` Table
```sql
- id
- customer_id (FK)
- tenant_id (FK)
- name - Recipient name
- phone
- address_line1
- address_line2
- city
- state
- postal_code
- country (default: Nigeria)
- is_default (boolean)
- created_at
- updated_at
```

#### `shipping_methods` Table
```sql
- id
- tenant_id (FK)
- name - e.g., "Standard Delivery", "Express Delivery"
- description
- cost (decimal)
- estimated_days - e.g., "3-5 business days"
- is_active (boolean)
- created_at
- updated_at
```

#### `coupons` Table
```sql
- id
- tenant_id (FK)
- code (unique per tenant)
- type (enum: percentage, fixed)
- value (decimal) - Percentage or fixed amount
- min_order_amount (decimal) - nullable
- max_discount_amount (decimal) - nullable, for percentage types
- usage_limit - Total times it can be used (nullable = unlimited)
- usage_count - Current usage count
- per_customer_limit - Times per customer (nullable = unlimited)
- valid_from
- valid_to
- is_active (boolean)
- created_at
- updated_at
```

#### `coupon_usage` Table
```sql
- id
- coupon_id (FK)
- order_id (FK)
- customer_id (FK)
- discount_amount
- created_at
```

#### `product_images` Table
```sql
- id
- product_id (FK)
- image_path
- is_primary (boolean)
- sort_order
- created_at
- updated_at
```

#### `customer_authentications` Table (extends existing customers)
```sql
- id
- customer_id (FK) - Links to existing customers table
- email (unique)
- password - Hashed
- google_id - For social login
- email_verified_at
- remember_token
- last_login_at
- created_at
- updated_at
```

### 2. Modify Existing `products` Table
Add new columns via migration:
```sql
ALTER TABLE products ADD COLUMN:
- slug (unique per tenant) - URL-friendly name
- short_description (text) - For product listing cards
- long_description (text) - Detailed product page
- is_visible_online (boolean, default: false) - Show in storefront
- is_featured (boolean, default: false) - Featured on homepage
- weight (decimal) - nullable, for shipping calculations
- length (decimal) - nullable
- width (decimal) - nullable
- height (decimal) - nullable
- meta_title (string) - SEO
- meta_description (text) - SEO
- view_count (integer, default: 0) - Analytics
- online_stock_alert_level (integer) - When to show "low stock" warning
```

### 3. Modify Existing `customers` Table
Add new columns:
```sql
ALTER TABLE customers ADD COLUMN:
- has_online_account (boolean, default: false) - Distinguishes store customers
- registration_source (enum: admin, online_store, import) - Track origin
```

---

## Models & Relationships

### New Models

#### `EcommerceSetting` Model
```php
namespace App\Models;

class EcommerceSetting extends Model
{
    protected $fillable = [
        'tenant_id', 'is_store_enabled', 'store_name', 'store_description',
        'allow_guest_checkout', 'allow_email_registration', 'allow_google_login',
        // ... all settings fields
    ];

    protected $casts = [
        'is_store_enabled' => 'boolean',
        'allow_guest_checkout' => 'boolean',
        'allow_email_registration' => 'boolean',
        'allow_google_login' => 'boolean',
        'tax_enabled' => 'boolean',
        'shipping_enabled' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
```

#### `Order` Model
```php
namespace App\Models;

class Order extends Model
{
    protected $fillable = [
        'tenant_id', 'order_number', 'customer_id', 'customer_email',
        'customer_name', 'customer_phone', 'status', 'payment_status',
        'payment_method', 'subtotal', 'tax_amount', 'shipping_amount',
        'discount_amount', 'total_amount', 'voucher_id', // ... all fields
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'fulfilled_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function items() { return $this->hasMany(OrderItem::class); }
    public function shippingAddress() { return $this->belongsTo(ShippingAddress::class); }
    public function billingAddress() { return $this->belongsTo(ShippingAddress::class, 'billing_address_id'); }
    public function voucher() { return $this->belongsTo(Voucher::class); }

    // Generate order number
    public static function generateOrderNumber($tenant_id)
    {
        $year = date('Y');
        $lastOrder = static::where('tenant_id', $tenant_id)
            ->whereYear('created_at', $year)
            ->latest('id')
            ->first();

        $number = $lastOrder ? intval(substr($lastOrder->order_number, -4)) + 1 : 1;
        return 'ORD-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
```

#### `OrderItem` Model
```php
namespace App\Models;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_id', 'product_name', 'product_sku',
        'quantity', 'unit_price', 'tax_amount', 'discount_amount', 'total_price'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function order() { return $this->belongsTo(Order::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
```

#### `Cart` & `CartItem` Models
```php
namespace App\Models;

class Cart extends Model
{
    protected $fillable = ['tenant_id', 'customer_id', 'session_id', 'expires_at'];
    protected $casts = ['expires_at' => 'datetime'];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function items() { return $this->hasMany(CartItem::class); }

    public function getSubtotal()
    {
        return $this->items->sum(fn($item) => $item->product->sales_rate * $item->quantity);
    }
}

class CartItem extends Model
{
    protected $fillable = ['cart_id', 'product_id', 'quantity'];
    protected $casts = ['quantity' => 'decimal:2'];

    public function cart() { return $this->belongsTo(Cart::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
```

#### `Wishlist` & `WishlistItem` Models
```php
namespace App\Models;

class Wishlist extends Model
{
    protected $fillable = ['tenant_id', 'customer_id'];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function items() { return $this->hasMany(WishlistItem::class); }
}

class WishlistItem extends Model
{
    protected $fillable = ['wishlist_id', 'product_id'];

    public function wishlist() { return $this->belongsTo(Wishlist::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
```

#### `ShippingAddress` Model
```php
namespace App\Models;

class ShippingAddress extends Model
{
    protected $fillable = [
        'customer_id', 'tenant_id', 'name', 'phone', 'address_line1',
        'address_line2', 'city', 'state', 'postal_code', 'country', 'is_default'
    ];

    protected $casts = ['is_default' => 'boolean'];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function tenant() { return $this->belongsTo(Tenant::class); }
}
```

#### `ShippingMethod` Model
```php
namespace App\Models;

class ShippingMethod extends Model
{
    protected $fillable = [
        'tenant_id', 'name', 'description', 'cost', 'estimated_days', 'is_active'
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
}
```

#### `Coupon` Model
```php
namespace App\Models;

class Coupon extends Model
{
    protected $fillable = [
        'tenant_id', 'code', 'type', 'value', 'min_order_amount',
        'max_discount_amount', 'usage_limit', 'usage_count',
        'per_customer_limit', 'valid_from', 'valid_to', 'is_active'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'valid_from' => 'date',
        'valid_to' => 'date',
        'is_active' => 'boolean',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function usages() { return $this->hasMany(CouponUsage::class); }

    public function isValid($orderAmount, $customerId = null)
    {
        // Check active status
        if (!$this->is_active) return false;

        // Check date validity
        if ($this->valid_from && now()->lt($this->valid_from)) return false;
        if ($this->valid_to && now()->gt($this->valid_to)) return false;

        // Check minimum order amount
        if ($this->min_order_amount && $orderAmount < $this->min_order_amount) return false;

        // Check usage limit
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) return false;

        // Check per-customer limit
        if ($customerId && $this->per_customer_limit) {
            $customerUsage = $this->usages()->where('customer_id', $customerId)->count();
            if ($customerUsage >= $this->per_customer_limit) return false;
        }

        return true;
    }

    public function calculateDiscount($orderAmount)
    {
        if ($this->type === 'percentage') {
            $discount = ($orderAmount * $this->value) / 100;
            if ($this->max_discount_amount) {
                $discount = min($discount, $this->max_discount_amount);
            }
            return $discount;
        }

        // Fixed amount
        return min($this->value, $orderAmount);
    }
}
```

#### `CustomerAuthentication` Model (for buyer accounts)
```php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class CustomerAuthentication extends Authenticatable
{
    protected $guard = 'customer';

    protected $fillable = [
        'customer_id', 'email', 'password', 'google_id',
        'email_verified_at', 'last_login_at'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    public function customer() { return $this->belongsTo(Customer::class); }
}
```

#### `ProductImage` Model
```php
namespace App\Models;

class ProductImage extends Model
{
    protected $fillable = ['product_id', 'image_path', 'is_primary', 'sort_order'];
    protected $casts = ['is_primary' => 'boolean'];

    public function product() { return $this->belongsTo(Product::class); }
}
```

### Update Existing Models

#### `Product` Model - Add Relationships
```php
public function images()
{
    return $this->hasMany(ProductImage::class)->orderBy('sort_order');
}

public function primaryImage()
{
    return $this->hasOne(ProductImage::class)->where('is_primary', true);
}

// Generate slug from name
public static function boot()
{
    parent::boot();

    static::creating(function ($product) {
        if (!$product->slug) {
            $product->slug = Str::slug($product->name);

            // Ensure uniqueness within tenant
            $count = static::where('tenant_id', $product->tenant_id)
                ->where('slug', 'like', $product->slug . '%')
                ->count();

            if ($count > 0) {
                $product->slug .= '-' . ($count + 1);
            }
        }
    });
}
```

#### `Customer` Model - Add Relationships
```php
public function authentication()
{
    return $this->hasOne(CustomerAuthentication::class);
}

public function orders()
{
    return $this->hasMany(Order::class);
}

public function shippingAddresses()
{
    return $this->hasMany(ShippingAddress::class);
}

public function cart()
{
    return $this->hasOne(Cart::class);
}

public function wishlist()
{
    return $this->hasOne(Wishlist::class);
}
```

#### `Tenant` Model - Add Relationships
```php
public function ecommerceSettings()
{
    return $this->hasOne(EcommerceSetting::class);
}

public function orders()
{
    return $this->hasMany(Order::class);
}

public function shippingMethods()
{
    return $this->hasMany(ShippingMethod::class);
}

public function coupons()
{
    return $this->hasMany(Coupon::class);
}
```

---

## Controllers

### Storefront Controllers (Public)

#### `StorefrontController.php`
```php
namespace App\Http\Controllers\Storefront;

class StorefrontController extends Controller
{
    // GET /{tenant}/store
    public function index(Tenant $tenant)
    {
        // Check if store is enabled
        $storeSettings = $tenant->ecommerceSettings;
        if (!$storeSettings || !$storeSettings->is_store_enabled) {
            abort(404, 'Store not available');
        }

        // Get featured products
        $featuredProducts = Product::where('tenant_id', $tenant->id)
            ->where('is_visible_online', true)
            ->where('is_featured', true)
            ->where('is_active', true)
            ->with('primaryImage')
            ->take(8)
            ->get();

        // Get new arrivals
        $newProducts = Product::where('tenant_id', $tenant->id)
            ->where('is_visible_online', true)
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        return view('storefront.index', compact('tenant', 'storeSettings', 'featuredProducts', 'newProducts'));
    }

    // GET /{tenant}/store/products
    public function products(Request $request, Tenant $tenant)
    {
        $query = Product::where('tenant_id', $tenant->id)
            ->where('is_visible_online', true)
            ->where('is_active', true);

        // Category filter
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        // Search
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Sort
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('sales_rate', 'asc');
                break;
            case 'price_high':
                $query->orderBy('sales_rate', 'desc');
                break;
            case 'popular':
                $query->orderBy('view_count', 'desc');
                break;
            default:
                $query->latest();
        }

        $products = $query->with('primaryImage', 'category')->paginate(12);
        $categories = ProductCategory::where('tenant_id', $tenant->id)->get();

        return view('storefront.products', compact('tenant', 'products', 'categories'));
    }

    // GET /{tenant}/store/products/{slug}
    public function product(Tenant $tenant, $slug)
    {
        $product = Product::where('tenant_id', $tenant->id)
            ->where('slug', $slug)
            ->where('is_visible_online', true)
            ->where('is_active', true)
            ->with('images', 'category')
            ->firstOrFail();

        // Increment view count
        $product->increment('view_count');

        // Get related products
        $relatedProducts = Product::where('tenant_id', $tenant->id)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_visible_online', true)
            ->where('is_active', true)
            ->take(4)
            ->get();

        return view('storefront.product-detail', compact('tenant', 'product', 'relatedProducts'));
    }
}
```

#### `CartController.php`
```php
namespace App\Http\Controllers\Storefront;

class CartController extends Controller
{
    // GET /{tenant}/store/cart
    public function index(Tenant $tenant)
    {
        $cart = $this->getOrCreateCart($tenant);
        $cart->load('items.product.primaryImage');

        return view('storefront.cart', compact('tenant', 'cart'));
    }

    // POST /{tenant}/store/cart/add - Requires Auth or prompts login
    public function add(Request $request, Tenant $tenant)
    {
        $storeSettings = $tenant->ecommerceSettings;

        // Check if customer needs to login/register
        if (!auth('customer')->check()) {
            // Store intent to add to cart in session
            session()->put('cart_intent', [
                'product_id' => $request->product_id,
                'quantity' => $request->quantity
            ]);

            return response()->json([
                'requireAuth' => true,
                'message' => 'Please login or create an account to add items to cart',
                'allowGuest' => $storeSettings->allow_guest_checkout
            ]);
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1'
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Check stock
        if ($product->maintain_stock && $product->current_stock < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available'
            ], 400);
        }

        $cart = $this->getOrCreateCart($tenant);

        // Check if item already in cart
        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $validated['quantity']);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $validated['quantity']
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart',
            'cartCount' => $cart->items()->sum('quantity')
        ]);
    }

    // PUT /{tenant}/store/cart/update/{item}
    public function update(Request $request, Tenant $tenant, CartItem $item)
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:1'
        ]);

        $item->update(['quantity' => $validated['quantity']]);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated'
        ]);
    }

    // DELETE /{tenant}/store/cart/remove/{item}
    public function remove(Tenant $tenant, CartItem $item)
    {
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart'
        ]);
    }

    private function getOrCreateCart($tenant)
    {
        if (auth('customer')->check()) {
            return Cart::firstOrCreate([
                'tenant_id' => $tenant->id,
                'customer_id' => auth('customer')->id()
            ]);
        }

        // Guest cart using session
        $sessionId = session()->getId();
        return Cart::firstOrCreate([
            'tenant_id' => $tenant->id,
            'session_id' => $sessionId
        ], [
            'expires_at' => now()->addDays(7)
        ]);
    }
}
```

#### `CheckoutController.php`
```php
namespace App\Http\Controllers\Storefront;

class CheckoutController extends Controller
{
    // GET /{tenant}/store/checkout
    public function index(Tenant $tenant)
    {
        $cart = $this->getCart($tenant);

        if ($cart->items->count() === 0) {
            return redirect()->route('storefront.cart', $tenant->slug)
                ->with('error', 'Your cart is empty');
        }

        $customer = auth('customer')->user()->customer ?? null;
        $addresses = $customer ? $customer->shippingAddresses : collect();
        $shippingMethods = $tenant->shippingMethods()->where('is_active', true)->get();

        return view('storefront.checkout', compact('tenant', 'cart', 'addresses', 'shippingMethods'));
    }

    // POST /{tenant}/store/checkout
    public function process(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'shipping_address_id' => 'nullable|exists:shipping_addresses,id',
            'new_address' => 'required_without:shipping_address_id|array',
            'shipping_method_id' => 'required|exists:shipping_methods,id',
            'payment_method' => 'required|in:cash_on_delivery,paystack,flutterwave',
            'coupon_code' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            $cart = $this->getCart($tenant);
            $customer = auth('customer')->user()->customer;
            $storeSettings = $tenant->ecommerceSettings;

            // Calculate amounts
            $subtotal = $cart->getSubtotal();
            $taxAmount = $storeSettings->tax_enabled ? ($subtotal * $storeSettings->tax_percentage / 100) : 0;
            $shippingMethod = ShippingMethod::findOrFail($validated['shipping_method_id']);
            $shippingAmount = $shippingMethod->cost;

            // Apply coupon
            $discountAmount = 0;
            $couponCode = null;
            if ($request->filled('coupon_code')) {
                $coupon = Coupon::where('tenant_id', $tenant->id)
                    ->where('code', $request->coupon_code)
                    ->first();

                if ($coupon && $coupon->isValid($subtotal, $customer->id)) {
                    $discountAmount = $coupon->calculateDiscount($subtotal);
                    $couponCode = $coupon->code;
                }
            }

            $totalAmount = $subtotal + $taxAmount + $shippingAmount - $discountAmount;

            // Handle shipping address
            if ($request->filled('shipping_address_id')) {
                $shippingAddressId = $validated['shipping_address_id'];
            } else {
                $address = $customer->shippingAddresses()->create($validated['new_address']);
                $shippingAddressId = $address->id;
            }

            // Create order
            $order = Order::create([
                'tenant_id' => $tenant->id,
                'order_number' => Order::generateOrderNumber($tenant->id),
                'customer_id' => $customer->id,
                'customer_email' => $customer->email,
                'customer_name' => $customer->first_name . ' ' . $customer->last_name,
                'customer_phone' => $customer->phone,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_method' => $validated['payment_method'],
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'shipping_amount' => $shippingAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'coupon_code' => $couponCode,
                'shipping_address_id' => $shippingAddressId,
                'notes' => $validated['notes'] ?? null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Create order items from cart
            foreach ($cart->items as $cartItem) {
                $product = $cartItem->product;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $product->sales_rate,
                    'total_price' => $product->sales_rate * $cartItem->quantity
                ]);
            }

            // Record coupon usage
            if ($coupon) {
                $coupon->usages()->create([
                    'order_id' => $order->id,
                    'customer_id' => $customer->id,
                    'discount_amount' => $discountAmount
                ]);
                $coupon->increment('usage_count');
            }

            // Clear cart
            $cart->items()->delete();

            DB::commit();

            // Handle payment
            if ($validated['payment_method'] === 'cash_on_delivery') {
                // Redirect to order confirmation
                return redirect()->route('storefront.order.confirmation', [
                    'tenant' => $tenant->slug,
                    'order' => $order->id
                ])->with('success', 'Order placed successfully!');
            } else {
                // Redirect to payment gateway
                return $this->initiatePayment($order, $validated['payment_method']);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout failed: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to process order. Please try again.')
                ->withInput();
        }
    }

    private function initiatePayment($order, $method)
    {
        // Payment gateway integration will be implemented
        // Return redirect to payment page
    }
}
```

#### `CustomerAuthController.php`
```php
namespace App\Http\Controllers\Storefront;

class CustomerAuthController extends Controller
{
    // Show login/register modal
    public function showLoginForm(Tenant $tenant)
    {
        $storeSettings = $tenant->ecommerceSettings;
        return view('storefront.auth.login', compact('tenant', 'storeSettings'));
    }

    // POST /{tenant}/store/register
    public function register(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customer_authentications,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            // Create customer record
            $customer = Customer::create([
                'tenant_id' => $tenant->id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'has_online_account' => true,
                'registration_source' => 'online_store',
                'customer_type' => 'individual'
            ]);

            // Create authentication record
            $auth = CustomerAuthentication::create([
                'customer_id' => $customer->id,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password'])
            ]);

            DB::commit();

            // Auto-login
            auth('customer')->login($auth);

            // Process cart intent if exists
            $this->processCartIntent($tenant, $customer);

            return response()->json([
                'success' => true,
                'message' => 'Account created successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again.'
            ], 500);
        }
    }

    // POST /{tenant}/store/login
    public function login(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if (auth('customer')->attempt($validated)) {
            $request->session()->regenerate();

            // Process cart intent if exists
            $this->processCartIntent($tenant, auth('customer')->user()->customer);

            return response()->json([
                'success' => true,
                'message' => 'Login successful!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    // POST /{tenant}/store/logout
    public function logout(Request $request, Tenant $tenant)
    {
        auth('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('storefront.index', $tenant->slug);
    }

    // Google Login
    public function redirectToGoogle(Tenant $tenant)
    {
        return Socialite::driver('google')
            ->with(['state' => json_encode(['tenant_slug' => $tenant->slug])])
            ->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        $state = json_decode($request->state, true);
        $tenant = Tenant::where('slug', $state['tenant_slug'])->firstOrFail();

        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if customer exists
            $auth = CustomerAuthentication::where('google_id', $googleUser->id)
                ->orWhere('email', $googleUser->email)
                ->first();

            if (!$auth) {
                // Create new customer
                DB::transaction(function() use ($tenant, $googleUser, &$auth) {
                    $customer = Customer::create([
                        'tenant_id' => $tenant->id,
                        'first_name' => $googleUser->user['given_name'] ?? '',
                        'last_name' => $googleUser->user['family_name'] ?? '',
                        'email' => $googleUser->email,
                        'has_online_account' => true,
                        'registration_source' => 'online_store'
                    ]);

                    $auth = CustomerAuthentication::create([
                        'customer_id' => $customer->id,
                        'email' => $googleUser->email,
                        'google_id' => $googleUser->id,
                        'email_verified_at' => now()
                    ]);
                });
            } else if (!$auth->google_id) {
                // Link Google account
                $auth->update(['google_id' => $googleUser->id]);
            }

            auth('customer')->login($auth);

            // Process cart intent
            $this->processCartIntent($tenant, $auth->customer);

            return redirect()->route('storefront.index', $tenant->slug)
                ->with('success', 'Welcome back!');

        } catch (\Exception $e) {
            return redirect()->route('storefront.index', $tenant->slug)
                ->with('error', 'Login failed. Please try again.');
        }
    }

    private function processCartIntent($tenant, $customer)
    {
        if (session()->has('cart_intent')) {
            $intent = session()->get('cart_intent');

            // Add product to cart
            $cart = Cart::firstOrCreate([
                'tenant_id' => $tenant->id,
                'customer_id' => $customer->id
            ]);

            $cart->items()->create([
                'product_id' => $intent['product_id'],
                'quantity' => $intent['quantity']
            ]);

            session()->forget('cart_intent');
        }
    }
}
```

### Admin Controllers (Tenant Dashboard)

#### `EcommerceSettingsController.php`
```php
namespace App\Http\Controllers\Tenant\Ecommerce;

class EcommerceSettingsController extends Controller
{
    public function index(Tenant $tenant)
    {
        $settings = $tenant->ecommerceSettings ?? new EcommerceSetting();
        return view('tenant.ecommerce.settings', compact('tenant', 'settings'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'is_store_enabled' => 'boolean',
            'store_name' => 'required|string|max:255',
            'store_description' => 'nullable|string',
            'allow_guest_checkout' => 'boolean',
            'allow_email_registration' => 'boolean',
            'allow_google_login' => 'boolean',
            'default_currency' => 'required|string|max:3',
            'tax_enabled' => 'boolean',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'shipping_enabled' => 'boolean',
            // ... other fields
        ]);

        $tenant->ecommerceSettings()->updateOrCreate(
            ['tenant_id' => $tenant->id],
            $validated
        );

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}
```

#### `OrderManagementController.php`
```php
namespace App\Http\Controllers\Tenant\Ecommerce;

class OrderManagementController extends Controller
{
    public function index(Tenant $tenant, Request $request)
    {
        $query = Order::where('tenant_id', $tenant->id)
            ->with('customer', 'items');

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->latest()->paginate(20);

        return view('tenant.ecommerce.orders.index', compact('tenant', 'orders'));
    }

    public function show(Tenant $tenant, Order $order)
    {
        $order->load('customer', 'items.product', 'shippingAddress', 'voucher');

        return view('tenant.ecommerce.orders.show', compact('tenant', 'order'));
    }

    public function updateStatus(Request $request, Tenant $tenant, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled'
        ]);

        $order->update(['status' => $validated['status']]);

        // If confirmed and no invoice exists, create one
        if ($validated['status'] === 'confirmed' && !$order->voucher_id) {
            $this->createInvoiceFromOrder($order);
        }

        // Send notification email to customer
        // Mail::to($order->customer_email)->send(new OrderStatusUpdated($order));

        return redirect()->back()->with('success', 'Order status updated!');
    }

    private function createInvoiceFromOrder($order)
    {
        // Create sales invoice from order
        // Similar to POS sale creation but for online orders
        // This integrates with existing accounting system
    }
}
```

#### `ShippingMethodController.php`, `CouponController.php`, etc.
Standard CRUD controllers for managing shipping methods, coupons, etc.

---

## Routes

### Add to `routes/web.php`

```php
// Public Storefront Routes (no auth required initially)
Route::prefix('{tenant}/store')->name('storefront.')->group(function () {
    Route::get('/', [StorefrontController::class, 'index'])->name('index');
    Route::get('/products', [StorefrontController::class, 'products'])->name('products');
    Route::get('/products/{slug}', [StorefrontController::class, 'product'])->name('product');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/update/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{item}', [CartController::class, 'remove'])->name('cart.remove');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
    Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::delete('/wishlist/remove/{item}', [WishlistController::class, 'remove'])->name('wishlist.remove');

    // Customer Authentication
    Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [CustomerAuthController::class, 'login']);
    Route::post('/register', [CustomerAuthController::class, 'register'])->name('register');
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');
    Route::get('/auth/google', [CustomerAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [CustomerAuthController::class, 'handleGoogleCallback']);

    // Protected routes (require customer auth)
    Route::middleware('auth:customer')->group(function () {
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
        Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');

        Route::get('/orders', [OrderController::class, 'index'])->name('orders');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('order.show');

        Route::get('/profile', [CustomerProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [CustomerProfileController::class, 'update'])->name('profile.update');
    });

    // Order confirmation (accessible with order ID)
    Route::get('/order/{order}/confirmation', [OrderController::class, 'confirmation'])->name('order.confirmation');

    // Payment callbacks
    Route::get('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
});
```

### Add to `routes/tenant.php`

```php
// Admin E-commerce Management (requires tenant auth)
Route::prefix('ecommerce')->name('tenant.ecommerce.')->group(function () {
    // Settings
    Route::get('/settings', [EcommerceSettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [EcommerceSettingsController::class, 'update'])->name('settings.update');

    // Order Management
    Route::get('/orders', [OrderManagementController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderManagementController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}/status', [OrderManagementController::class, 'updateStatus'])->name('orders.update-status');
    Route::put('/orders/{order}/payment-status', [OrderManagementController::class, 'updatePaymentStatus'])->name('orders.update-payment');
    Route::post('/orders/{order}/create-invoice', [OrderManagementController::class, 'createInvoice'])->name('orders.create-invoice');

    // Shipping Methods
    Route::resource('shipping-methods', ShippingMethodController::class);

    // Coupons
    Route::resource('coupons', CouponController::class);
    Route::post('/coupons/{coupon}/toggle', [CouponController::class, 'toggle'])->name('coupons.toggle');

    // Product Management (extends existing)
    Route::get('/products', [ProductController::class, 'ecommerceIndex'])->name('products.index');
    Route::put('/products/{product}/ecommerce-settings', [ProductController::class, 'updateEcommerceSettings'])->name('products.ecommerce-settings');
    Route::post('/products/{product}/images', [ProductController::class, 'uploadImages'])->name('products.upload-images');
    Route::delete('/products/images/{image}', [ProductController::class, 'deleteImage'])->name('products.delete-image');

    // Reports
    Route::get('/reports', [EcommerceReportsController::class, 'index'])->name('reports');
});
```

---

## Payment Gateway Integration

### Paystack Integration

#### `PaymentService.php`
```php
namespace App\Services;

class PaymentService
{
    private $paystackSecretKey;

    public function __construct()
    {
        // Will be configured per tenant in ecommerce_settings
    }

    public function initializePaystackPayment($order, $tenant)
    {
        $settings = $tenant->ecommerceSettings;

        $url = "https://api.paystack.co/transaction/initialize";

        $fields = [
            'email' => $order->customer_email,
            'amount' => $order->total_amount * 100, // Convert to kobo
            'reference' => $order->order_number,
            'callback_url' => route('storefront.payment.callback', ['tenant' => $tenant->slug]),
            'metadata' => [
                'order_id' => $order->id,
                'tenant_id' => $tenant->id
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $settings->paystack_secret_key,
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        if ($result['status']) {
            return $result['data']['authorization_url'];
        }

        throw new \Exception('Payment initialization failed');
    }

    public function verifyPaystackPayment($reference, $tenant)
    {
        $settings = $tenant->ecommerceSettings;

        $url = "https://api.paystack.co/transaction/verify/" . $reference;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $settings->paystack_secret_key
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}
```

---

## Configuration & Settings

### Add Customer Guard to `config/auth.php`

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'customer' => [
        'driver' => 'session',
        'provider' => 'customers',
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],

    'customers' => [
        'driver' => 'eloquent',
        'model' => App\Models\CustomerAuthentication::class,
    ],
],
```

### Add Payment Gateway Config to `.env`

```env
# Payment Gateways (can be overridden per tenant in store settings)
PAYSTACK_PUBLIC_KEY=
PAYSTACK_SECRET_KEY=

FLUTTERWAVE_PUBLIC_KEY=
FLUTTERWAVE_SECRET_KEY=
FLUTTERWAVE_ENCRYPTION_KEY=
```

---

## Implementation Phases

### Phase 1: Database & Models (Week 1)
- Create all migrations
- Create all models with relationships
- Seed initial data for testing

### Phase 2: Admin Store Management (Week 2)
- Store settings UI
- Product e-commerce settings (extend existing)
- Shipping methods CRUD
- Coupons CRUD
- Order management interface

### Phase 3: Storefront (Week 3-4)
- Homepage design
- Product listing & detail pages
- Shopping cart
- Wishlist
- Customer authentication (email/Google)

### Phase 4: Checkout & Payments (Week 5)
- Checkout flow
- Address management
- Payment gateway integration (Paystack)
- Order confirmation

### Phase 5: Order Fulfillment & Accounting (Week 6)
- Order status workflow
- Order-to-invoice conversion
- Stock deduction on order
- Email notifications
- Reports & analytics

### Phase 6: Polish & Testing (Week 7)
- UI/UX improvements
- Mobile responsiveness
- Security audit
- Performance optimization
- Documentation

---

## Key Features Summary

✅ **Multi-tenant storefront** - Each tenant gets their own store URL
✅ **Customer accounts** - Email/password or Google login
✅ **Shopping cart & wishlist** - With session persistence
✅ **Product management** - Extends existing inventory system
✅ **Checkout flow** - Address, shipping, payment
✅ **Payment gateways** - Paystack, Flutterwave, Cash on Delivery
✅ **Order management** - Complete fulfillment workflow
✅ **Coupon system** - Percentage or fixed discounts
✅ **Shipping methods** - Configurable per tenant
✅ **Accounting integration** - Orders create sales invoices
✅ **Admin controls** - Toggle features on/off
✅ **Guest checkout option** - If enabled by admin
✅ **Stock management** - Integrated with existing inventory

---

## Questions & Clarifications

1. ✅ **Customer Authentication**: Prompt to create account on add to cart/wishlist - email/password or Google
2. ✅ **Admin Configuration**: Can toggle email registration, Google login, guest checkout on/off
3. ✅ **Stock Reservation**: No automatic reservation, admin can manually follow up
4. ✅ **Order-to-Invoice**: Yes, confirmed orders auto-create sales invoices for accounting

---

## Next Steps

1. Review and approve this plan
2. Set up development environment
3. Create database migrations (Phase 1)
4. Begin implementation following the phases above

Let me know if you need any clarification or changes to this plan!
