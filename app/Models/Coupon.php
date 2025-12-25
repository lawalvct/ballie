<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_discount_amount',
        'usage_limit',
        'usage_count',
        'per_customer_limit',
        'valid_from',
        'valid_to',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'valid_from' => 'date',
        'valid_to' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the tenant that owns the coupon
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the usage records
     */
    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Check if coupon is valid for given order amount and customer
     */
    public function isValid($orderAmount, $customerId = null)
    {
        // Check active status
        if (!$this->is_active) {
            return false;
        }

        // Check date validity
        if ($this->valid_from && now()->lt($this->valid_from)) {
            return false;
        }

        if ($this->valid_to && now()->gt($this->valid_to)) {
            return false;
        }

        // Check minimum order amount
        if ($this->min_order_amount && $orderAmount < $this->min_order_amount) {
            return false;
        }

        // Check usage limit
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        // Check per-customer limit
        if ($customerId && $this->per_customer_limit) {
            $customerUsage = $this->usages()->where('customer_id', $customerId)->count();
            if ($customerUsage >= $this->per_customer_limit) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate discount amount for given order amount
     */
    public function calculateDiscount($orderAmount)
    {
        if ($this->type === 'percentage') {
            $discount = ($orderAmount * $this->value) / 100;

            // Apply maximum discount limit if set
            if ($this->max_discount_amount) {
                $discount = min($discount, $this->max_discount_amount);
            }

            return $discount;
        }

        // Fixed amount - cannot exceed order amount
        return min($this->value, $orderAmount);
    }

    /**
     * Get validation message if coupon is invalid
     */
    public function getValidationMessage($orderAmount, $customerId = null)
    {
        if (!$this->is_active) {
            return 'This coupon is no longer active.';
        }

        if ($this->valid_from && now()->lt($this->valid_from)) {
            return 'This coupon is not yet valid.';
        }

        if ($this->valid_to && now()->gt($this->valid_to)) {
            return 'This coupon has expired.';
        }

        if ($this->min_order_amount && $orderAmount < $this->min_order_amount) {
            return 'Minimum order amount of ₦' . number_format($this->min_order_amount, 2) . ' required.';
        }

        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return 'This coupon has reached its usage limit.';
        }

        if ($customerId && $this->per_customer_limit) {
            $customerUsage = $this->usages()->where('customer_id', $customerId)->count();
            if ($customerUsage >= $this->per_customer_limit) {
                return 'You have already used this coupon the maximum number of times.';
            }
        }

        return null;
    }

    /**
     * Scope for active coupons
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for valid coupons (active and within date range)
     */
    public function scopeValid($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('valid_from')
                    ->orWhere('valid_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('valid_to')
                    ->orWhere('valid_to', '>=', now());
            });
    }
}
