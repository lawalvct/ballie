<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Invitation - Ballie</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8fafc;
        }
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 16px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #2d3748;
            margin-bottom: 20px;
        }
        .invitation-details {
            background: #f7fafc;
            border-left: 4px solid #4299e1;
            padding: 20px;
            margin: 25px 0;
            border-radius: 0 8px 8px 0;
        }
        .invitation-details h3 {
            margin: 0 0 15px;
            color: #2d3748;
            font-size: 18px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .detail-label {
            color: #718096;
            font-weight: 500;
        }
        .detail-value {
            color: #2d3748;
            font-weight: 600;
        }
        .plan-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
            text-align: center;
        }
        .plan-info h4 {
            margin: 0 0 10px;
            font-size: 20px;
        }
        .plan-price {
            font-size: 32px;
            font-weight: bold;
            margin: 10px 0;
        }
        .plan-cycle {
            opacity: 0.9;
            font-size: 14px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
            padding: 16px 32px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            margin: 30px 0;
            box-shadow: 0 4px 12px rgba(72, 187, 120, 0.3);
            transition: all 0.3s ease;
        }
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(72, 187, 120, 0.4);
        }
        .personal-message {
            background: #fff5f5;
            border-left: 4px solid #f56565;
            padding: 20px;
            margin: 25px 0;
            border-radius: 0 8px 8px 0;
            font-style: italic;
        }
        .expiry-notice {
            background: #fffbf0;
            border: 1px solid #fbd38d;
            padding: 15px;
            border-radius: 8px;
            margin: 25px 0;
            text-align: center;
            color: #744210;
        }
        .footer {
            background: #f7fafc;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            margin: 5px 0;
            color: #718096;
            font-size: 14px;
        }
        .footer a {
            color: #4299e1;
            text-decoration: none;
        }
        .steps {
            margin: 25px 0;
        }
        .step {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        .step-number {
            background: #4299e1;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            margin-right: 15px;
            flex-shrink: 0;
        }
        .step-content {
            color: #4a5568;
            font-size: 14px;
        }
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .header, .content, .footer {
                padding: 20px;
            }
            .detail-row {
                flex-direction: column;
                margin-bottom: 12px;
            }
            .detail-label {
                margin-bottom: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>You're Invited!</h1>
            <p>Join {{ $invitationData['company_name'] }} on Ballie</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Hello {{ $invitationData['owner_name'] }},
            </div>

            <p>You've been invited to set up your company <strong>{{ $invitationData['company_name'] }}</strong> on Ballie, the comprehensive business management platform.</p>

            <!-- Company Details -->
            <div class="invitation-details">
                <h3>Company Details</h3>
                <div class="detail-row">
                    <span class="detail-label">Company Name:</span>
                    <span class="detail-value">{{ $invitationData['company_name'] }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Company Email:</span>
                    <span class="detail-value">{{ $invitationData['company_email'] }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Business Type:</span>
                    <span class="detail-value">{{ ucfirst(str_replace('_', ' ', $invitationData['business_type'])) }}</span>
                </div>
                @if($invitationData['phone'])
                <div class="detail-row">
                    <span class="detail-label">Phone:</span>
                    <span class="detail-value">{{ $invitationData['phone'] }}</span>
                </div>
                @endif
            </div>

            <!-- Subscription Plan -->
            <div class="plan-info">
                <h4>{{ $selectedPlan->name }} Plan</h4>
                <div class="plan-price">
                    ₦{{ number_format(($invitationData['billing_cycle'] === 'yearly' ? $selectedPlan->yearly_price : $selectedPlan->monthly_price) / 100) }}
                </div>
                <div class="plan-cycle">
                    per {{ $invitationData['billing_cycle'] === 'yearly' ? 'year' : 'month' }}
                    @if($invitationData['billing_cycle'] === 'yearly')
                        <br><small>Save ₦{{ number_format((($selectedPlan->monthly_price * 12) - $selectedPlan->yearly_price) / 100) }} annually!</small>
                    @endif
                </div>
                <p style="margin-top: 15px; opacity: 0.9; font-size: 14px;">
                    🎉 <strong>30-day free trial included!</strong>
                </p>
            </div>

            @if($invitationData['message'])
            <!-- Personal Message -->
            <div class="personal-message">
                <strong>Personal message from your administrator:</strong><br>
                "{{ $invitationData['message'] }}"
            </div>
            @endif

            <!-- What happens next -->
            <h3 style="color: #2d3748; margin-top: 30px;">What happens next?</h3>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-content">Click the button below to accept your invitation</div>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-content">Set up your password and complete your profile</div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-content">Start your 30-day free trial and explore all features</div>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <div class="step-content">Invite your team members and begin managing your business</div>
                </div>
            </div>

            <!-- CTA Button -->
            <div style="text-align: center;">
                <a href="{{ $acceptUrl }}" class="cta-button">
                    Accept Invitation & Get Started
                </a>
            </div>

            <!-- Expiry Notice -->
            <div class="expiry-notice">
                <strong>⏰ This invitation expires in 7 days</strong><br>
                Make sure to accept it before then to secure your account setup.
            </div>

            <p style="margin-top: 30px; color: #718096;">
                If you have any questions or need assistance, feel free to reply to this email or contact our support team.
            </p>

            <p style="color: #718096;">
                Best regards,<br>
                The Ballie Team
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                <strong>Ballie</strong> - Your Complete Business Management Solution
            </p>
            <p>
                If you can't click the button above, copy and paste this link into your browser:<br>
                <a href="{{ $acceptUrl }}">{{ $acceptUrl }}</a>
            </p>
            <p style="margin-top: 20px;">
                If you didn't expect this invitation, you can safely ignore this email.
            </p>
        </div>
    </div>
</body>
</html>
