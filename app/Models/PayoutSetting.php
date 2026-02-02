<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayoutSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'deduction_type',
        'deduction_value',
        'deduction_name',
        'minimum_payout',
        'maximum_payout',
        'processing_time',
        'payouts_enabled',
        'payout_terms',
    ];

    protected $casts = [
        'deduction_value' => 'decimal:2',
        'minimum_payout' => 'decimal:2',
        'maximum_payout' => 'decimal:2',
        'payouts_enabled' => 'boolean',
    ];

    /**
     * Get the current payout settings (singleton pattern).
     */
    public static function getSettings(): self
    {
        $settings = self::first();

        if (!$settings) {
            $settings = self::create([
                'deduction_type' => 'percentage',
                'deduction_value' => 5.00,
                'deduction_name' => 'Processing Fee',
                'minimum_payout' => 5000.00,
                'maximum_payout' => null,
                'processing_time' => '3-5 business days',
                'payouts_enabled' => true,
                'payout_terms' => 'Payout requests are processed within 3-5 business days.',
            ]);
        }

        return $settings;
    }

    /**
     * Get deduction description.
     */
    public function getDeductionDescriptionAttribute(): string
    {
        if ($this->deduction_type === 'percentage') {
            return $this->deduction_name . ' (' . number_format($this->deduction_value, 2) . '%)';
        }

        return $this->deduction_name . ' (₦' . number_format($this->deduction_value, 2) . ')';
    }

    /**
     * Validate payout amount.
     */
    public function validateAmount(float $amount, float $availableBalance): array
    {
        $errors = [];

        if (!$this->payouts_enabled) {
            $errors[] = 'Payouts are currently disabled.';
            return $errors;
        }

        if ($amount < $this->minimum_payout) {
            $errors[] = 'Minimum payout amount is ₦' . number_format($this->minimum_payout, 2);
        }

        if ($this->maximum_payout && $amount > $this->maximum_payout) {
            $errors[] = 'Maximum payout amount is ₦' . number_format($this->maximum_payout, 2);
        }

        if ($amount > $availableBalance) {
            $errors[] = 'Requested amount exceeds available balance of ₦' . number_format($availableBalance, 2);
        }

        return $errors;
    }
}
