# Affiliate Referral System Integration

## Overview

The affiliate referral system is now fully integrated with the tenant registration process. When someone registers using an affiliate's referral link, the system automatically tracks and records the referral.

## How It Works

### 1. **Affiliate Shares Referral Link**

Affiliates can share links like:

-   `https://yourapp.com/register?ref=ABC123`
-   `https://yourapp.com/register?ref=ABC123&utm_source=facebook&utm_campaign=summer2025`

### 2. **Visitor Arrives**

When a visitor clicks the affiliate link:

-   The `TrackAffiliateReferral` middleware (already in web middleware group) captures the `ref` parameter
-   Validates the affiliate code is active
-   Stores the code in a cookie (`ballie_ref`) for 30 days
-   Stores tracking data in session (UTM parameters, IP, user agent, etc.)

### 3. **Registration Process**

During registration (`RegisteredUserController`):

-   Hidden form fields capture UTM parameters and ref code
-   After tenant is created, the system checks for affiliate code from:
    1. Session (preferred)
    2. Cookie
    3. Request input
-   If valid affiliate found, creates `AffiliateReferral` record with:
    -   affiliate_id
    -   referred_tenant_id
    -   status: 'pending' (confirmed when first payment is made)
    -   conversion_type: 'registration'
    -   tracking_data: UTM params, IP, user agent, plan selected, etc.
-   Increments affiliate's `total_referrals` counter
-   Clears session data

### 4. **Visual Confirmation**

On the registration page, if a referral code is detected, shows:

```
✓ You're registering via a referral! 🎉
```

## Database Schema

### Using Existing `affiliate_referrals` Table

```
affiliate_referrals
├─ id
├─ affiliate_id (FK to affiliates)
├─ referred_tenant_id (FK to tenants)
├─ referral_source (direct, social, email, etc.)
├─ conversion_type (registration, first_payment)
├─ conversion_value (0 initially, updated on first payment)
├─ status (pending, confirmed, cancelled)
├─ conversion_date (when first payment made)
└─ tracking_data (JSON: UTM params, IP, user agent, etc.)
```

## Files Modified

### 1. **RegisteredUserController.php**

-   Added imports: `Affiliate`, `AffiliateReferral`, `Cookie`
-   Added affiliate tracking logic after tenant creation
-   Captures tracking data from session/cookie/request
-   Creates referral record
-   Increments affiliate referral count

### 2. **register.blade.php**

-   Added hidden form fields for ref and UTM parameters
-   Added visual indicator when referral code detected

### 3. **Affiliate.php Model**

-   Added `tenants()` relationship

## Tracking Data Captured

-   `utm_source` - Traffic source (e.g., facebook, google)
-   `utm_medium` - Marketing medium (e.g., cpc, email)
-   `utm_campaign` - Campaign name
-   `utm_term` - Keywords
-   `utm_content` - Ad variation
-   `ip_address` - User's IP
-   `user_agent` - Browser/device info
-   `registered_at` - Registration timestamp
-   `plan_selected` - Which plan they chose

## Referral Status Flow

### Pending → Confirmed

-   **Pending**: Set when tenant registers
-   **Confirmed**: Updated when tenant makes first subscription payment
-   **Cancelled**: If tenant cancels before first payment

## Next Steps (Future Enhancements)

### 1. **First Payment Tracking**

When a tenant makes their first subscription payment:

```php
// In payment controller after successful payment
$referral = AffiliateReferral::where('referred_tenant_id', $tenant->id)
    ->where('status', 'pending')
    ->first();

if ($referral) {
    $referral->update([
        'status' => 'confirmed',
        'conversion_value' => $payment->amount,
        'conversion_date' => now(),
    ]);

    // Create commission record
    AffiliateCommission::create([
        'affiliate_id' => $referral->affiliate_id,
        'referred_tenant_id' => $tenant->id,
        'affiliate_referral_id' => $referral->id,
        'payment_amount' => $payment->amount,
        'commission_rate' => $referral->affiliate->getCommissionRate(),
        'commission_amount' => $payment->amount * ($referral->affiliate->getCommissionRate() / 100),
        'status' => 'pending', // Requires approval
        'payment_date' => now(),
    ]);
}
```

### 2. **Affiliate Dashboard Enhancement**

Show affiliates their referral status:

-   Total clicks
-   Registrations (pending referrals)
-   Confirmed customers
-   Conversion rate

### 3. **Email Notifications**

-   Notify affiliate when someone registers using their link
-   Notify when referral is confirmed (first payment)
-   Notify when commission is approved/paid

### 4. **Analytics & Reports**

-   Track conversion rates by source
-   Identify best-performing affiliates
-   UTM campaign performance

## Testing

### Test the Flow:

1. Get an active affiliate code from database or create one
2. Visit: `http://yourapp.test/register?ref=ABC123&utm_source=test&utm_campaign=integration`
3. Complete registration
4. Check `affiliate_referrals` table for new record
5. Verify `affiliates.total_referrals` incremented
6. Check tracking_data JSON contains all parameters

### Expected Database Records:

-   New tenant in `tenants` table
-   New user in `users` table
-   New referral in `affiliate_referrals` table with status='pending'
-   Updated `total_referrals` in `affiliates` table

## Configuration

Cookie settings in `config/affiliate.php`:

-   `cookie_name`: 'ballie_ref'
-   `cookie_duration`: 30 days
-   `default_commission_rate`: 10%

## Security

-   ✅ Validates affiliate code exists and is active
-   ✅ Uses encrypted cookies
-   ✅ Clears session data after registration
-   ✅ Prevents duplicate referrals (one per tenant)
-   ✅ Tracks IP and user agent for fraud prevention
