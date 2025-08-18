<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Team Invitation</title>
    <style>
        body { font-family: 'Inter', Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #2b6399 0%, #3c2c64 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: white; padding: 30px; border: 1px solid #e5e7eb; }
        .footer { background: #f9fafb; padding: 20px; text-align: center; border-radius: 0 0 8px 8px; color: #6b7280; font-size: 14px; }
        .btn { display: inline-block; background: #d1b05e; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600; margin: 20px 0; }
        .btn:hover { background: #b8965a; }
        .role-badge { background: #69a2a4; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .company-info { background: #f3f4f6; padding: 20px; border-radius: 6px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 You're Invited!</h1>
            <p>Join {{ $tenant->name }} on Ballie</p>
        </div>

        <div class="content">
            <h2>Hello {{ $user->name }},</h2>

            <p>Great news! You've been invited to join <strong>{{ $tenant->name }}</strong> as a <span class="role-badge">{{ ucfirst($role) }}</span> on Ballie - Nigeria's leading business management platform.</p>

            <div class="company-info">
                <h3>About {{ $tenant->name }}</h3>
                <p><strong>Business Type:</strong> {{ $tenant->business_type ?? 'Not specified' }}</p>
                <p><strong>Industry:</strong> {{ $tenant->industry ?? 'Not specified' }}</p>
                @if($tenant->website)
                    <p><strong>Website:</strong> <a href="{{ $tenant->website }}" target="_blank">{{ $tenant->website }}</a></p>
                @endif
            </div>

            <h3>Your Role: {{ ucfirst($role) }}</h3>
            <p>As a {{ $role }}, you'll have access to:</p>

            @switch($role)
                @case('admin')
                    <ul>
                        <li>✅ Full access to all features and settings</li>
                        <li>✅ User management and permissions</li>
                        <li>✅ Financial reports and analytics</li>
                        <li>✅ System configuration</li>
                    </ul>
                    @break
                @case('manager')
                    <ul>
                        <li>✅ Operations management</li>
                        <li>✅ View reports and analytics</li>
                        <li>✅ Limited settings access</li>
                        <li>✅ Team oversight</li>
                    </ul>
                    @break
                @case('accountant')
                    <ul>
                        <li>✅ Financial data and reports</li>
                        <li>✅ Invoicing and billing</li>
                        <li>✅ Tax management</li>
                        <li>✅ Payment tracking</li>
                    </ul>
                    @break
                @case('sales')
                    <ul>
                        <li>✅ Customer management</li>
                        <li>✅ Quotes and orders</li>
                        <li>✅ Sales reports</li>
                        <li>✅ Lead tracking</li>
                    </ul>
                    @break
                @case('employee')
                    <ul>
                        <li>✅ Basic access to assigned tasks</li>
                        <li>✅ Personal data management</li>
                        <li>✅ Time tracking</li>
                        <li>✅ Basic reporting</li>
                    </ul>
                    @break
            @endswitch

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $acceptUrl }}" class="btn">Accept Invitation & Get Started</a>
            </div>

            <h3>What is Ballie?</h3>
            <p>Ballie is a comprehensive business management software built specifically for Nigerian businesses. It helps you:</p>
            <ul>
                <li>📊 Manage your finances and accounting</li>
                <li>📦 Track inventory and stock levels</li>
                <li>👥 Handle customer relationships</li>
                <li>💰 Process invoices and payments</li>
                <li>📈 Generate detailed business reports</li>
                <li>🏪 Run point-of-sale operations</li>
            </ul>

            <p><strong>Why Nigerian businesses choose Ballie:</strong></p>
            <ul>
                <li>🇳🇬 Built for Nigerian tax and compliance requirements</li>
                <li>💰 Affordable pricing starting from ₦5,000/month</li>
                <li>📱 Works on all devices - desktop, tablet, mobile</li>
                <li>🔒 Bank-level security and data protection</li>
                <li>🎯 24/7 local customer support</li>
            </ul>

            <div style="background: #fef3c7; border: 1px solid #f59e0b; padding: 15px; border-radius: 6px; margin: 20px 0;">
                <p><strong>⏰ This invitation expires in 7 days.</strong></p>
                <p>Click the button above to accept your invitation and set up your account.</p>
            </div>

            <p>If you have any questions, feel free to reach out to our support team at <a href="mailto:support@ballie.ng">support@ballie.ng</a> or call us at +234 800 000 0000.</p>

            <p>Welcome to the Ballie family!</p>

            <p>Best regards,<br>
            The Ballie Team</p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} Ballie. All rights reserved.</p>
            <p>If you didn't expect this invitation, you can safely ignore this email.</p>
            <p>
                <a href="https://ballie.ng" style="color: #6b7280;">Visit our website</a> |
                <a href="mailto:support@ballie.ng" style="color: #6b7280;">Contact Support</a>
            </p>
        </div>
    </div>
</body>
</html>