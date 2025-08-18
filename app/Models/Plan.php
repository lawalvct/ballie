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
        'yearly_price',
        'max_users',
        'max_customers',
        'has_pos',
        'has_payroll',
        'has_api_access',
        'has_advanced_reports',
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
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function getFormattedMonthlyPriceAttribute()
    {
        return '₦' . number_format($this->monthly_price / 100, 0);
    }

    public function getFormattedYearlyPriceAttribute()
    {
        return '₦' . number_format($this->yearly_price / 100, 0);
    }

    public function getYearlyMonthlySavingsAttribute()
    {
        $yearlyMonthly = $this->yearly_price / 12;
        return $this->monthly_price - $yearlyMonthly;
    }
}
