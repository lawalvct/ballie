<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Commission Rate
    |--------------------------------------------------------------------------
    |
    | The default commission rate percentage that affiliates will earn
    | on each successful referral. This can be overridden per affiliate.
    |
    */

    'default_commission_rate' => env('AFFILIATE_COMMISSION_RATE', 10.00),

    /*
    |--------------------------------------------------------------------------
    | Cookie Duration
    |--------------------------------------------------------------------------
    |
    | Number of days to track referrals via cookies. If a user clicks an
    | affiliate link, their referral is tracked for this many days.
    |
    */

    'cookie_duration' => env('AFFILIATE_COOKIE_DURATION', 30),

    /*
    |--------------------------------------------------------------------------
    | Cookie Name
    |--------------------------------------------------------------------------
    |
    | The name of the cookie used to track affiliate referrals.
    |
    */

    'cookie_name' => 'ballie_ref',

    /*
    |--------------------------------------------------------------------------
    | Minimum Payout Amount
    |--------------------------------------------------------------------------
    |
    | The minimum amount an affiliate must earn before they can request
    | a payout. This helps reduce transaction fees.
    |
    */

    'minimum_payout' => env('AFFILIATE_MINIMUM_PAYOUT', 50.00),

    /*
    |--------------------------------------------------------------------------
    | Payout Methods
    |--------------------------------------------------------------------------
    |
    | Available payout methods for affiliates.
    |
    */

    'payout_methods' => [
        'bank_transfer' => 'Bank Transfer',
        // 'nomba' => 'Nomba',
        // 'stripe' => 'Stripe',
        // 'paystack' => 'Paystack',
    ],

    /*
    |--------------------------------------------------------------------------
    | Commission Types
    |--------------------------------------------------------------------------
    |
    | Different types of commissions that can be earned.
    |
    */

    'commission_types' => [
        'first_payment' => 'First Payment',
        'recurring' => 'Recurring Payment',
        'bonus' => 'Bonus Commission',
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto Approval
    |--------------------------------------------------------------------------
    |
    | Automatically approve affiliate applications. If false, super admin
    | must manually approve each affiliate.
    |
    */

    'auto_approval' => env('AFFILIATE_AUTO_APPROVAL', false),

    /*
    |--------------------------------------------------------------------------
    | Commission Hold Period
    |--------------------------------------------------------------------------
    |
    | Number of days to hold commissions before they can be paid out.
    | This allows time for refunds or cancellations.
    |
    */

    'commission_hold_days' => env('AFFILIATE_COMMISSION_HOLD_DAYS', 30),

    /*
    |--------------------------------------------------------------------------
    | First Payment Bonus
    |--------------------------------------------------------------------------
    |
    | Additional bonus percentage for the first payment from a referral.
    | Set to 0 to disable first payment bonuses.
    |
    */

    'first_payment_bonus' => env('AFFILIATE_FIRST_PAYMENT_BONUS', 0),

    /*
    |--------------------------------------------------------------------------
    | Recurring Commission Enabled
    |--------------------------------------------------------------------------
    |
    | Whether affiliates earn commission on recurring payments from their
    | referrals, or only on the first payment.
    |
    */

    'recurring_commission_enabled' => env('AFFILIATE_RECURRING_COMMISSION', true),

    /*
    |--------------------------------------------------------------------------
    | Platform Fee Percentage
    |--------------------------------------------------------------------------
    |
    | Percentage fee deducted from payouts to cover transaction costs.
    |
    */

    'platform_fee_percentage' => env('AFFILIATE_PLATFORM_FEE', 0),

];
