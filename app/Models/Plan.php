<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'features',
        'monthly_price',
        'quarterly_price',
        'biannual_price',
        'yearly_price',
        'max_users',
        'max_customers',
        'has_pos',
        'has_payroll',
        'has_api_access',
        'has_advanced_reports',
        'has_ecommerce',
        'has_audit_log',
        'has_multi_location',
        'has_multi_currency',
        'support_level',
        'is_popular',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'has_pos' => 'boolean',
        'has_payroll' => 'boolean',
        'has_api_access' => 'boolean',
        'has_advanced_reports' => 'boolean',
        'has_ecommerce' => 'boolean',
        'has_audit_log' => 'boolean',
        'has_multi_location' => 'boolean',
        'has_multi_currency' => 'boolean',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the price for a given billing cycle.
     */
    public function getPriceForCycle(string $cycle): int
    {
        return match ($cycle) {
            'quarterly' => $this->quarterly_price,
            'biannual' => $this->biannual_price,
            'yearly' => $this->yearly_price,
            default => $this->monthly_price,
        };
    }

    /**
     * Get the subscription duration in months for a given billing cycle.
     */
    public static function cycleDurationMonths(string $cycle): int
    {
        return match ($cycle) {
            'quarterly' => 3,
            'biannual' => 6,
            'yearly' => 12,
            default => 1,
        };
    }

    /**
     * Get the end date for a subscription starting now.
     */
    public static function cycleEndDate(string $cycle, $from = null)
    {
        $from = $from ?? now();
        return match ($cycle) {
            'quarterly' => $from->copy()->addMonths(3),
            'biannual' => $from->copy()->addMonths(6),
            'yearly' => $from->copy()->addYear(),
            default => $from->copy()->addMonth(),
        };
    }

    /**
     * Get cycle display label.
     */
    public static function cycleLabel(string $cycle): string
    {
        return match ($cycle) {
            'quarterly' => 'Quarterly',
            'biannual' => 'Bi-Annual',
            'yearly' => 'Yearly',
            default => 'Monthly',
        };
    }

    public function getFormattedMonthlyPriceAttribute()
    {
        return '₦' . number_format($this->monthly_price / 100, 0);
    }

    public function getFormattedQuarterlyPriceAttribute()
    {
        return '₦' . number_format($this->quarterly_price / 100, 0);
    }

    public function getFormattedBiannualPriceAttribute()
    {
        return '₦' . number_format($this->biannual_price / 100, 0);
    }

    public function getFormattedYearlyPriceAttribute()
    {
        return '₦' . number_format($this->yearly_price / 100, 0);
    }

    /**
     * Get formatted price for any cycle.
     */
    public function formattedPriceForCycle(string $cycle): string
    {
        return '₦' . number_format($this->getPriceForCycle($cycle) / 100, 0);
    }

    /**
     * Savings compared to paying monthly for a given cycle.
     */
    public function savingsForCycle(string $cycle): int
    {
        $months = self::cycleDurationMonths($cycle);
        $monthlyTotal = $this->monthly_price * $months;
        $cyclePrice = $this->getPriceForCycle($cycle);
        return $monthlyTotal - $cyclePrice;
    }

    /**
     * Formatted savings for a given cycle.
     */
    public function formattedSavingsForCycle(string $cycle): string
    {
        return '₦' . number_format($this->savingsForCycle($cycle) / 100, 0);
    }

    public function getYearlyMonthlySavingsAttribute()
    {
        $yearlyMonthly = $this->yearly_price / 12;
        return $this->monthly_price - $yearlyMonthly;
    }
}
