# Affiliate Commission Tracking System

## Overview

The affiliate commission system automatically tracks and creates commission records when referred tenants make successful subscription payments.

## How It Works

### 1. **Tenant Registers via Affiliate Link**

When a tenant registers using an affiliate referral link:

-   `AffiliateReferral` record is created with `status='pending'`
-   The referral is tracked but no commission is created yet

### 2. **First Successful Payment**

When the referred tenant makes their **first subscription payment**:

-   The `AffiliateReferral.status` changes from `pending` to `confirmed`
-   `AffiliateReferral.conversion_type` is set to `first_payment`
-   `AffiliateReferral.conversion_value` is updated with payment amount
-   `AffiliateReferral.conversion_date` is set to current timestamp
-   An `AffiliateCommission` record is created with:
    -   `commission_type`: `first_payment`
    -   `status`: `pending` (requires super admin approval)
    -   `commission_rate`: Affiliate's rate + first payment bonus (if configured)
    -   `commission_amount`: Calculated based on payment amount
    -   `due_date`: Current date + commission hold period (default: 30 days)
-   Affiliate's `total_commissions` is incremented

### 3. **Recurring Payments (Optional)**

For subsequent subscription renewals (if `recurring_commission_enabled=true` in config):

-   Additional `AffiliateCommission` records are created for each renewal
-   `commission_type`: `recurring`
-   Same approval and hold period applies
-   Affiliate continues earning on renewals from their referrals

### 4. **Commission Approval & Payout**

Super admin can:

-   Review pending commissions
-   Approve commissions (changes status from `pending` to `approved`)
-   Process payouts (changes status from `approved` to `paid`)
-   Track total earnings per affiliate

## Configuration

### Environment Variables

Add these to your `.env` file:

```env
# Default commission rate (percentage)
AFFILIATE_COMMISSION_RATE=10.00

# Days before commission can be paid out (refund protection)
AFFILIATE_COMMISSION_HOLD_DAYS=30

# Bonus percentage for first payment (added to commission rate)
AFFILIATE_FIRST_PAYMENT_BONUS=5

# Enable recurring commissions on subscription renewals
AFFILIATE_RECURRING_COMMISSION=true

# Minimum amount before affiliate can request payout
AFFILIATE_MINIMUM_PAYOUT=50.00
```

### Config File (`config/affiliate.php`)

Key settings:

-   `default_commission_rate`: Default commission percentage (can be overridden per affiliate)
-   `commission_hold_days`: Days to hold commissions before payout eligibility
-   `first_payment_bonus`: Additional bonus for first payment
-   `recurring_commission_enabled`: Whether to pay commissions on renewals
-   `minimum_payout`: Minimum accumulated commission before payout

## Commission Calculation Examples

### Example 1: First Payment with Bonus

-   **Subscription Amount**: ₦10,000
-   **Commission Rate**: 10%
-   **First Payment Bonus**: 5%
-   **Total Rate**: 15%
-   **Commission**: ₦10,000 × 15% = **₦1,500**

### Example 2: Recurring Payment (No Bonus)

-   **Subscription Amount**: ₦10,000
-   **Commission Rate**: 10%
-   **Commission**: ₦10,000 × 10% = **₦1,000**

### Example 3: Custom Affiliate Rate

-   **Subscription Amount**: ₦10,000
-   **Custom Commission Rate**: 15% (set in affiliate profile)
-   **Commission**: ₦10,000 × 15% = **₦1,500**

## Database Schema

### affiliate_referrals

```
- status: pending → confirmed (on first payment)
- conversion_type: registration → first_payment
- conversion_value: 0 → actual payment amount
- conversion_date: null → payment timestamp
```

### affiliate_commissions

```
- affiliate_id: FK to affiliates table
- referred_tenant_id: FK to tenants table
- affiliate_referral_id: FK to affiliate_referrals table
- payment_reference: Subscription payment reference
- payment_amount: Amount paid by tenant
- commission_rate: Percentage used for calculation
- commission_amount: Final commission earned
- commission_type: first_payment | recurring | bonus
- status: pending → approved → paid
- payment_date: When tenant paid
- due_date: When commission can be paid out
- paid_date: When commission was actually paid
```

### affiliates

```
- total_commissions: Running total of all commissions (incremented on each commission)
- total_paid: Total amount paid out to affiliate
- custom_commission_rate: Override default rate for specific affiliate
```

## Code Flow

### SubscriptionController::paymentCallback()

```php
1. Verify payment with gateway
2. Update payment status to 'successful'
3. Update subscription status to 'active'
4. Update tenant subscription (upgrade or renewal)
5. Call processAffiliateCommission() ← NEW
6. Commit transaction
```

### SubscriptionController::processAffiliateCommission()

```php
1. Find affiliate referral for tenant
2. Check if affiliate is active
3. Determine if first payment or renewal
4. Check if commission should be created:
   - First payment: Always create
   - Renewal: Only if recurring_commission_enabled=true
5. Calculate commission amount
6. Create AffiliateCommission record
7. Update affiliate.total_commissions
8. Log commission creation
```

## Commission Status Flow

### Pending → Approved → Paid

**Pending** (Initial State)

-   Created automatically on successful payment
-   Waiting for super admin review
-   Subject to hold period

**Approved** (Super Admin Action)

-   Super admin has verified the commission
-   Can be included in next payout batch
-   Still subject to hold period (due_date)

**Paid** (Super Admin Action)

-   Commission has been paid to affiliate
-   Sets `paid_date` timestamp
-   Updates `affiliate.total_paid`
-   Creates `AffiliatePayout` record

## Super Admin Actions

### View Pending Commissions

```
Route: /super-admin/affiliates/{affiliate}/commissions
Shows: All commissions for an affiliate with filters
Actions: Approve, Reject, View Details
```

### Approve Commission

```php
// In AffiliateController
public function approveCommission(AffiliateCommission $commission)
{
    $commission->update(['status' => 'approved']);
    return back()->with('success', 'Commission approved');
}
```

### Process Payout

```php
// In AffiliateController
public function processPayout(Request $request, Affiliate $affiliate)
{
    $approvedCommissions = $affiliate->commissions()
        ->where('status', 'approved')
        ->where('due_date', '<=', now())
        ->get();

    $totalAmount = $approvedCommissions->sum('commission_amount');

    // Create payout record
    $payout = AffiliatePayout::create([
        'affiliate_id' => $affiliate->id,
        'amount' => $totalAmount,
        'status' => 'processing',
        // ... other fields
    ]);

    // Mark commissions as paid
    $approvedCommissions->each(function($commission) use ($payout) {
        $commission->update([
            'status' => 'paid',
            'paid_date' => now()
        ]);
    });

    // Update affiliate total_paid
    $affiliate->increment('total_paid', $totalAmount);
}
```

## Testing

### Test Scenario 1: First Payment Commission

1. Create affiliate account
2. Get affiliate referral link
3. Register new tenant using referral link
4. Check `affiliate_referrals` table - status should be `pending`
5. Make first subscription payment
6. Check `affiliate_referrals` table - status should be `confirmed`
7. Check `affiliate_commissions` table - new record with `first_payment` type
8. Check `affiliates` table - `total_commissions` should be incremented

### Test Scenario 2: Recurring Commission

1. Use tenant from Test Scenario 1
2. Wait for subscription renewal or manually renew
3. Check `affiliate_commissions` table - new record with `recurring` type
4. Verify commission amount is correct (no bonus on recurring)

### Test Scenario 3: Custom Commission Rate

1. Set `custom_commission_rate` on affiliate (e.g., 15%)
2. Referred tenant makes payment
3. Verify commission uses custom rate instead of default

### SQL Queries for Testing

```sql
-- Check referral status
SELECT * FROM affiliate_referrals
WHERE referred_tenant_id = <tenant_id>;

-- Check commissions for affiliate
SELECT * FROM affiliate_commissions
WHERE affiliate_id = <affiliate_id>
ORDER BY created_at DESC;

-- Check total commissions by type
SELECT
    affiliate_id,
    commission_type,
    COUNT(*) as count,
    SUM(commission_amount) as total
FROM affiliate_commissions
GROUP BY affiliate_id, commission_type;

-- Check pending commissions ready for payout
SELECT * FROM affiliate_commissions
WHERE status = 'approved'
AND due_date <= NOW()
ORDER BY affiliate_id, created_at;
```

## Notifications (Future Enhancement)

### Affiliate Notifications

-   ✉️ New commission earned
-   ✉️ Commission approved
-   ✉️ Payout processed

### Super Admin Notifications

-   ✉️ New pending commission requiring review
-   ✉️ Payout requested by affiliate

## Security & Fraud Prevention

### Hold Period

-   Commissions have a hold period (default: 30 days)
-   Protects against refunds and chargebacks
-   Affiliate cannot withdraw until `due_date` passes

### Manual Approval

-   All commissions require super admin approval
-   Prevents fraudulent referrals
-   Allows review of suspicious activity

### Tracking Data

-   IP address stored in referral tracking
-   User agent stored for device tracking
-   UTM parameters for campaign analysis
-   Can identify patterns of fraud

## Reports & Analytics

### Affiliate Performance Report

```php
// Top performing affiliates
SELECT
    a.id,
    a.affiliate_code,
    a.company_name,
    COUNT(DISTINCT ar.id) as total_referrals,
    COUNT(DISTINCT CASE WHEN ar.status = 'confirmed' THEN ar.id END) as confirmed_referrals,
    SUM(ac.commission_amount) as total_earnings,
    SUM(CASE WHEN ac.status = 'paid' THEN ac.commission_amount ELSE 0 END) as paid_earnings
FROM affiliates a
LEFT JOIN affiliate_referrals ar ON a.id = ar.affiliate_id
LEFT JOIN affiliate_commissions ac ON a.id = ac.affiliate_id
GROUP BY a.id
ORDER BY total_earnings DESC;
```

### Conversion Rate by Affiliate

```php
SELECT
    a.affiliate_code,
    COUNT(DISTINCT ar.id) as total_referrals,
    COUNT(DISTINCT CASE WHEN ar.status = 'confirmed' THEN ar.id END) as conversions,
    ROUND(
        COUNT(DISTINCT CASE WHEN ar.status = 'confirmed' THEN ar.id END) * 100.0 /
        NULLIF(COUNT(DISTINCT ar.id), 0),
        2
    ) as conversion_rate
FROM affiliates a
LEFT JOIN affiliate_referrals ar ON a.id = ar.affiliate_id
GROUP BY a.id
ORDER BY conversion_rate DESC;
```

## Troubleshooting

### Commission Not Created

**Check:**

1. Is affiliate status `active`?
2. Is `AffiliateReferral` record present?
3. Check logs: `storage/logs/laravel.log` for "Affiliate commission" entries
4. Verify payment completed successfully
5. Check `recurring_commission_enabled` config if it's a renewal

### Wrong Commission Amount

**Check:**

1. Verify `commission_rate` in affiliate record
2. Check `first_payment_bonus` in config
3. Confirm payment amount is correct
4. Review calculation: `(payment_amount / 100) * commission_rate`

### Referral Not Found

**Check:**

1. Was registration completed using affiliate link?
2. Check `affiliate_referrals` table for tenant
3. Verify cookie/session tracking worked
4. Check `TrackAffiliateReferral` middleware is active

## Next Steps

1. ✅ Implement commission tracking on payments
2. 🔲 Add super admin commission approval interface
3. 🔲 Add affiliate dashboard showing earnings
4. 🔲 Implement payout request system
5. 🔲 Add email notifications
6. 🔲 Create analytics dashboard
7. 🔲 Add commission dispute resolution
8. 🔲 Implement automatic payouts
9. 🔲 Add webhook for real-time tracking
10. 🔲 Create mobile app for affiliates
