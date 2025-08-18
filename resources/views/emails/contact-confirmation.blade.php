<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thank you for contacting Ballie</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #3B82F6; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .cta { background: #3B82F6; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Thank You for Contacting Ballie!</h1>
        </div>

        <div class="content">
            <p>Dear {{ $contactData['first_name'] }},</p>

            <p>Thank you for reaching out to us! We've received your message and our team will get back to you within 24 hours.</p>

            <p><strong>Your message details:</strong></p>
            <ul>
                <li><strong>Subject:</strong> {{ ucfirst($contactData['subject']) }}</li>
                <li><strong>Message:</strong> {{ Str::limit($contactData['message'], 100) }}</li>
            </ul>

            <p>In the meantime, feel free to explore our resources:</p>

            <a href="{{ url('/features') }}" class="cta">Explore Features</a>
            <a href="{{ url('/pricing') }}" class="cta">View Pricing</a>

            <p>If you have any urgent questions, you can also reach us at:</p>
            <ul>
                <li>Phone: +234 801 234 5678</li>
                <li>Email: support@ballie.ng</li>
            </ul>

            <p>Best regards,<br>The Ballie Team</p>
        </div>
    </div>
</body>
</html>
