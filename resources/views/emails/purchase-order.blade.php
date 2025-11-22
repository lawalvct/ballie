<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <p>Dear {{ $purchaseOrder->vendor->getFullNameAttribute() }},</p>
    
    <p>{{ $emailMessage }}</p>
    
    <p>Please find the purchase order attached.</p>
    
    <p>Best regards,<br>
    {{ $tenant->name }}</p>
</body>
</html>
