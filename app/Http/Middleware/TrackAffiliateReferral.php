<?php

namespace App\Http\Middleware;

use App\Models\Affiliate;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class TrackAffiliateReferral
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if there's a referral code in the URL
        if ($request->has('ref')) {
            $affiliateCode = $request->get('ref');

            // Verify the affiliate code exists and is active
            $affiliate = Affiliate::where('affiliate_code', $affiliateCode)
                ->where('status', 'active')
                ->first();

            if ($affiliate) {
                // Store the referral code in a cookie
                $cookieName = config('affiliate.cookie_name', 'ballie_ref');
                $cookieDuration = config('affiliate.cookie_duration', 30) * 24 * 60; // Convert days to minutes

                Cookie::queue($cookieName, $affiliateCode, $cookieDuration);

                // Store tracking data in session
                session([
                    'affiliate_code' => $affiliateCode,
                    'affiliate_id' => $affiliate->id,
                    'referral_source' => $request->get('source', 'direct'),
                    'referral_timestamp' => now(),
                    'tracking_data' => [
                        'utm_source' => $request->get('utm_source'),
                        'utm_medium' => $request->get('utm_medium'),
                        'utm_campaign' => $request->get('utm_campaign'),
                        'utm_term' => $request->get('utm_term'),
                        'utm_content' => $request->get('utm_content'),
                        'user_agent' => $request->userAgent(),
                        'ip_address' => $request->ip(),
                    ],
                ]);
            }
        }

        // If no ref parameter, check for existing cookie
        elseif (!session()->has('affiliate_code')) {
            $cookieName = config('affiliate.cookie_name', 'ballie_ref');

            if ($request->hasCookie($cookieName)) {
                $affiliateCode = $request->cookie($cookieName);

                // Verify the affiliate is still active
                $affiliate = Affiliate::where('affiliate_code', $affiliateCode)
                    ->where('status', 'active')
                    ->first();

                if ($affiliate) {
                    session([
                        'affiliate_code' => $affiliateCode,
                        'affiliate_id' => $affiliate->id,
                    ]);
                }
            }
        }

        return $next($request);
    }
}
