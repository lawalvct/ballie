<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Your Affiliate Account - Ballie</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }
        .email-wrapper {
            background-color: #f3f4f6;
            padding: 40px 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #2b6399 0%, #3c2c64 50%, #4a3570 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .logo {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            padding: 10px;
        }
        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 16px;
        }
        .content {
            padding: 40px 30px;
        }
        .content h2 {
            color: #2b6399;
            font-size: 20px;
            margin-top: 0;
            margin-bottom: 20px;
        }
        .content p {
            margin: 16px 0;
            color: #4b5563;
            font-size: 15px;
        }
        .verification-code-box {
            background: linear-gradient(135deg, #2b6399 0%, #3c2c64 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            margin: 32px 0;
            box-shadow: 0 8px 16px rgba(43, 99, 153, 0.3);
        }
        .verification-code-box p {
            margin: 0 0 12px 0;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.9);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        .verification-code {
            font-size: 48px;
            font-weight: 800;
            letter-spacing: 12px;
            color: #d1b05e;
            font-family: 'Courier New', monospace;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            margin: 8px 0;
        }
        .code-expires {
            margin: 12px 0 0 0;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.8);
        }
        .affiliate-highlight {
            background: linear-gradient(135deg, #d1b05e 0%, #b8965a 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 24px 0;
            text-align: center;
        }
        .affiliate-highlight h3 {
            margin: 0 0 8px 0;
            font-size: 18px;
            font-weight: 700;
        }
        .affiliate-highlight p {
            margin: 0;
            color: rgba(255, 255, 255, 0.95);
            font-size: 14px;
        }
        .alert-box {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 16px 20px;
            border-radius: 6px;
            margin: 24px 0;
        }
        .alert-box p {
            margin: 0;
            color: #92400e;
            font-size: 14px;
        }
        .info-box {
            background: #eff6ff;
            border-left: 4px solid #2b6399;
            padding: 16px 20px;
            border-radius: 6px;
            margin: 24px 0;
        }
        .info-box p {
            margin: 0;
            color: #1e3a8a;
            font-size: 14px;
        }
        .success-box {
            background: #ecfdf5;
            border-left: 4px solid #10b981;
            padding: 16px 20px;
            border-radius: 6px;
            margin: 24px 0;
        }
        .success-box p {
            margin: 0;
            color: #065f46;
            font-size: 14px;
        }
        .btn-container {
            text-align: center;
            margin: 32px 0;
        }
        .btn {
            display: inline-block;
            background: #d1b05e;
            color: white !important;
            padding: 16px 32px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(209, 176, 94, 0.3);
        }
        .btn:hover {
            background: #b8965a;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(209, 176, 94, 0.4);
        }
        .alternative-link {
            background: #f9fafb;
            padding: 20px;
            border-radius: 6px;
            margin: 24px 0;
            border: 1px dashed #d1d5db;
        }
        .alternative-link p {
            margin: 8px 0;
            font-size: 13px;
            color: #6b7280;
        }
        .alternative-link a {
            color: #2b6399;
            word-break: break-all;
            text-decoration: none;
        }
        .alternative-link a:hover {
            text-decoration: underline;
        }
        .footer {
            background: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 8px 0;
            color: #6b7280;
            font-size: 14px;
        }
        .footer a {
            color: #2b6399;
            text-decoration: none;
            font-weight: 500;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        .benefits-list {
            background: #f9fafb;
            padding: 20px;
            border-radius: 6px;
            margin: 24px 0;
        }
        .benefits-list h3 {
            color: #374151;
            font-size: 16px;
            margin-top: 0;
            margin-bottom: 12px;
        }
        .benefits-list ul {
            margin: 0;
            padding-left: 20px;
            color: #6b7280;
            font-size: 14px;
        }
        .benefits-list li {
            margin: 8px 0;
        }
        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #e5e7eb, transparent);
            margin: 30px 0;
        }
        .trust-badges {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px 0;
            flex-wrap: wrap;
        }
        .trust-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #6b7280;
            font-size: 12px;
        }
        .trust-badge svg {
            width: 16px;
            height: 16px;
        }
        .highlight {
            color: #d1b05e;
            font-weight: 600;
        }
        .commission-rate {
            background: #f0f9ff;
            color: #1e40af;
            font-weight: 700;
            font-size: 18px;
            padding: 2px 8px;
            border-radius: 4px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="container">
            <!-- Header -->
            <div class="header">
                <div class="logo">
                    <img src="{{ asset('images/ballie.png') }}" alt="Ballie Logo">
                </div>
                <h1>Welcome to Ballie Affiliate Program!</h1>
                <p>Start earning commissions today</p>
            </div>

            <!-- Content -->
            <div class="content">
                <h2>Hello {{ $notifiable->first_name ?? $notifiable->name }},</h2>

                <p>Congratulations! Thank you for joining the <strong class="highlight">Ballie Affiliate Program</strong>. We're excited to have you as a partner!</p>

                <p>To activate your affiliate account and start earning <span class="commission-rate">{{ config('affiliate.default_commission_rate') }}% commission</span>, please verify your email address using the verification code below:</p>

                <!-- Verification Code Box -->
                <div class="verification-code-box">
                    <p>Your Verification Code</p>
                    <div class="verification-code">{{ $verificationCode }}</div>
                    <p class="code-expires">⏱️ This code will expire in 60 minutes</p>
                </div>

                <!-- Affiliate Highlight -->
                <div class="affiliate-highlight">
                    <h3>🎉 You're About to Start Earning!</h3>
                    <p>Once verified, you'll get your unique referral link and can start earning {{ config('affiliate.default_commission_rate') }}% recurring commission on every payment from your referrals.</p>
                </div>

                <!-- Success Info -->
                <div class="success-box">
                    <p><strong>✅ Quick Verification:</strong> Enter this code on the verification page to activate your affiliate account and get your personalized dashboard!</p>
                </div>

                <!-- Verify Button -->
                <div class="btn-container">
                    <a href="{{ route('affiliate.verification.notice') }}" class="btn">Verify Email & Activate Account</a>
                </div>

                <!-- Alternative Link -->
                <div class="alternative-link">
                    <p><strong>Button not working?</strong> Copy and paste this link into your browser:</p>
                    <p><a href="{{ route('affiliate.verification.notice') }}">{{ route('affiliate.verification.notice') }}</a></p>
                </div>

                <div class="divider"></div>

                <!-- What's Next -->
                <div class="benefits-list">
                    <h3>🚀 What's Next After Verification?</h3>
                    <ul>
                        <li>Access your personalized affiliate dashboard</li>
                        <li>Get your unique referral link and promotional materials</li>
                        <li>Start promoting Ballie to your network</li>
                        <li>Track referrals and commissions in real-time</li>
                        <li>Earn {{ config('affiliate.default_commission_rate') }}% recurring commission on all payments</li>
                        <li>Request payouts once you reach ₦{{ number_format(config('affiliate.minimum_payout', 50)) }} minimum</li>
                    </ul>
                </div>

                <div class="divider"></div>

                <!-- Security Alert -->
                <div class="alert-box">
                    <p><strong>⚠️ Didn't apply for affiliate program?</strong> If you didn't register for the Ballie Affiliate Program, please ignore this email. No further action is required and your email won't be used.</p>
                </div>

                <!-- Info Box -->
                <div class="info-box">
                    <p><strong>💡 Need Help?</strong> If you're experiencing any issues or have questions about the affiliate program, our support team is here to help. Contact us at <a href="mailto:affiliates@ballie.com" style="color: #2b6399; font-weight: 600;">affiliates@ballie.com</a></p>
                </div>

                <div class="divider"></div>

                <p style="margin-bottom: 0;">We're thrilled to have you as a partner in growing the Ballie community. Let's start earning together!</p>
            </div>

            <!-- Footer -->
            <div class="footer">
                <div class="trust-badges">
                    <div class="trust-badge">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                        </svg>
                        SSL Secured
                    </div>
                    <div class="trust-badge">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Privacy Protected
                    </div>
                    <div class="trust-badge">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Verified Affiliate Program
                    </div>
                </div>

                <p style="margin-top: 20px;"><strong>Ballie Affiliate Program</strong> - Earn while you grow our community</p>
                <p>Join thousands of successful affiliates earning with Nigeria's #1 Business Platform</p>

                <p style="margin-top: 20px;">
                    Need help? <a href="mailto:affiliates@ballie.com">Contact Affiliate Support</a> |
                    <a href="{{ url('/') }}">Visit Website</a> |
                    <a href="{{ route('affiliate.index') }}">Affiliate Program</a>
                </p>

                <p style="margin-top: 20px; font-size: 12px; color: #9ca3af;">
                    This email was sent to {{ $notifiable->email ?? $notifiable->user->email }}. This is an automated message, please do not reply directly to this email.
                </p>

                <p style="font-size: 12px; color: #9ca3af;">
                    © {{ date('Y') }} Ballie. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html>