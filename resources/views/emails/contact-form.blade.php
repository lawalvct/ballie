<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Contact Form Submission</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #3B82F6; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; color: #555; }
        .value { margin-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Contact Form Submission</h1>
        </div>

        <div class="content">
            <div class="field">
                <div class="label">Name:</div>
                <div class="value">{{ $contactData['first_name'] }} {{ $contactData['last_name'] }}</div>
            </div>

            <div class="field">
                <div class="label">Email:</div>
                <div class="value">{{ $contactData['email'] }}</div>
            </div>

            @if($contactData['phone'])
            <div class="field">
                <div class="label">Phone:</div>
                <div class="value">{{ $contactData['phone'] }}</div>
            </div>
            @endif

            @if($contactData['company'])
            <div class="field">
                <div class="label">Company:</div>
                <div class="value">{{ $contactData['company'] }}</div>
            </div>
            @endif

            <div class="field">
                <div class="label">Subject:</div>
                <div class="value">{{ ucfirst($contactData['subject']) }}</div>
            </div>

            <div class="field">
                <div class="label">Message:</div>
                <div class="value">{{ nl2br(e($contactData['message'])) }}</div>
            </div>

            <div class="field">
                <div class="label">Newsletter Subscription:</div>
                <div class="value">{{ isset($contactData['newsletter']) && $contactData['newsletter'] ? 'Yes' : 'No' }}</div>
            </div>

            <div class="field">
                <div class="label">Submitted:</div>
                <div class="value">{{ now()->format('F j, Y \a\t g:i A') }}</div>
            </div>
        </div>
    </div>
</body>
</html>
