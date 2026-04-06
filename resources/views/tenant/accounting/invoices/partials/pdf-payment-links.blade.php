{{-- Payment Links Section for Invoice PDFs --}}
@if(!empty($paymentLinks) && ($onlinePaymentsEnabled ?? false))
<div style="margin-bottom: 8px; border: 1px solid {{ $linkColor ?? '#2c5aa0' }}; border-radius: 4px; overflow: hidden;">
    <div style="background: {{ $linkColor ?? '#2c5aa0' }}; color: #fff; padding: 5px 10px; font-size: 10px; font-weight: bold; text-transform: uppercase;">
        Pay Online
    </div>
    <div style="padding: 8px 10px; font-size: 10px; line-height: 1.5;">
        <div style="margin-bottom: 4px; color: #555; font-size: 9px;">You can pay this invoice online using any of the links below:</div>
        @if(isset($paymentLinks['nomba']) && !empty($paymentLinks['nomba']['checkout_link']))
            <div style="margin-bottom: 4px;">
                <strong style="color: {{ $linkColor ?? '#2c5aa0' }};">Nomba Payment:</strong><br>
                <span style="color: #333; word-break: break-all;">{{ $paymentLinks['nomba']['checkout_link'] }}</span>
            </div>
        @endif
        @if(isset($paymentLinks['paystack']) && !empty($paymentLinks['paystack']['authorization_url']))
            <div style="margin-bottom: 4px;">
                <strong style="color: {{ $linkColor ?? '#2c5aa0' }};">Paystack Payment:</strong><br>
                <span style="color: #333; word-break: break-all;">{{ $paymentLinks['paystack']['authorization_url'] }}</span>
            </div>
        @endif
    </div>
</div>
@endif
