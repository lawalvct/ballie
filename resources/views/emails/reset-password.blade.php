<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Your Password - Ballie</title>
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
            width: 64px;
            height: 64px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 28px;
            font-weight: bold;
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
        .security-tips {
            background: #f3f4f6;
            padding: 20px;
            border-radius: 6px;
            margin: 24px 0;
        }
        .security-tips h3 {
            color: #374151;
            font-size: 16px;
            margin-top: 0;
            margin-bottom: 12px;
        }
        .security-tips ul {
            margin: 0;
            padding-left: 20px;
            color: #6b7280;
            font-size: 14px;
        }
        .security-tips li {
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
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="container">
            <!-- Header -->
            <div class="header">
                <div class="logo">B'</div>
                <h1>Password Reset Request</h1>
                <p>Secure your Ballie account</p>
            </div>

            <!-- Content -->
            <div class="content">
                <h2>Hello {{ $notifiable->name }},</h2>

                <p>We received a request to reset the password for your Ballie account. If you made this request, click the button below to reset your password:</p>

                <!-- Reset Button -->
                <div class="btn-container">
                    <a href="{{ $url }}" class="btn">Reset My Password</a>
                </div>

                <!-- Expiration Info -->
                <div class="info-box">
                    <p><strong>⏱️ Important:</strong> This password reset link will expire in {{ config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60) }} minutes. After that, you'll need to request a new one.</p>
                </div>

                <!-- Alternative Link -->
                <div class="alternative-link">
                    <p><strong>Button not working?</strong> Copy and paste this link into your browser:</p>
                    <p><a href="{{ $url }}">{{ $url }}</a></p>
                </div>

                <div class="divider"></div>

                <!-- Security Alert -->
                <div class="alert-box">
                    <p><strong>⚠️ Didn't request this?</strong> If you didn't request a password reset, please ignore this email and your password will remain unchanged. Your account is secure.</p>
                </div>

                <!-- Security Tips -->
                <div class="security-tips">
                    <h3>🔒 Password Security Tips</h3>
                    <ul>
                        <li>Choose a strong password with at least 8 characters</li>
                        <li>Include uppercase, lowercase, numbers, and symbols</li>
                        <li>Don't reuse passwords from other accounts</li>
                        <li>Never share your password with anyone</li>
                        <li>Consider using a password manager</li>
                    </ul>
                </div>

                <div class="divider"></div>

                <p style="margin-bottom: 0;">If you're having trouble or didn't request this reset, please contact our support team immediately.</p>
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
                </div>

                <p style="margin-top: 20px;"><strong>Ballie</strong> - Nigeria's #1 Business Management Platform</p>
                <p>Trusted by 5,000+ businesses across Nigeria</p>

                <p style="margin-top: 20px;">
                    Need help? <a href="mailto:support@ballie.com">Contact Support</a> |
                    <a href="{{ url('/') }}">Visit Website</a>
                </p>

                <p style="margin-top: 20px; font-size: 12px; color: #9ca3af;">
                    This email was sent to {{ $notifiable->email }}. This is an automated message, please do not reply directly to this email.
                </p>

                <p style="font-size: 12px; color: #9ca3af;">
                    © {{ date('Y') }} Ballie. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
