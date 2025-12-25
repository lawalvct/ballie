<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'order_number',
        'customer_id',
        'customer_email',
        'customer_name',
        'customer_phone',
        'status',
        'payment_status',
        'payment_method',
        'payment_gateway_reference',
        'subtotal',
        'tax_amount',
        'shipping_amount',
        'discount_amount',
        'total_amount',
        'coupon_code',
        'shipping_address_id',
        'billing_same_as_shipping',
        'billing_address_id',
        'notes',
        'admin_notes',
        'ip_address',
        'user_agent',
        'voucher_id',
        'fulfilled_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'billing_same_as_shipping' => 'boolean',
        'fulfilled_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Get the tenant that owns the order
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the customer that placed the order
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the order items
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the shipping address
     */
    public function shippingAddress()
    {
        return $this->belongsTo(ShippingAddress::class, 'shipping_address_id');
    }

    /**
     * Get the billing address
     */
    public function billingAddress()
    {
        return $this->belongsTo(ShippingAddress::class, 'billing_address_id');
    }

    /**
     * Get the accounting voucher (invoice)
     */
    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    /**
     * Get the coupon usage record
     */
    public function couponUsage()
    {
        return $this->hasOne(CouponUsage::class);
    }

    /**
     * Generate a unique order number for a tenant
     */
    public static function generateOrderNumber($tenantId)
    {
        $year = date('Y');
        $lastOrder = static::where('tenant_id', $tenantId)
            ->whereYear('created_at', $year)
            ->latest('id')
            ->first();

        $number = $lastOrder ? intval(substr($lastOrder->order_number, -4)) + 1 : 1;
        return 'ORD-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Check if order is editable
     */
    public function isEditable()
    {
        return in_array($this->status, ['pending']);
    }

    /**
     * Check if order is cancellable
     */
    public function isCancellable()
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'confirmed' => 'info',
            'processing' => 'primary',
            'shipped' => 'secondary',
            'delivered' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get payment status badge color
     */
    public function getPaymentStatusColorAttribute()
    {
        return match($this->payment_status) {
            'paid' => 'success',
            'partially_paid' => 'warning',
            'unpaid' => 'danger',
            'refunded' => 'secondary',
            default => 'secondary'
        };
    }

    /**
     * Scope for filtering by status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by payment status
     */
    public function scopeWithPaymentStatus($query, $paymentStatus)
    {
        return $query->where('payment_status', $paymentStatus);
    }
}
