@php
    $statusColors = [
        'draft' => 'bg-yellow-100 text-yellow-800',
        'sent' => 'bg-blue-100 text-blue-800',
        'accepted' => 'bg-green-100 text-green-800',
        'rejected' => 'bg-red-100 text-red-800',
        'expired' => 'bg-gray-100 text-gray-800',
        'converted' => 'bg-purple-100 text-purple-800',
    ];

    $statusLabels = [
        'draft' => 'Draft',
        'sent' => 'Sent',
        'accepted' => 'Accepted',
        'rejected' => 'Rejected',
        'expired' => 'Expired',
        'converted' => 'Converted',
    ];
@endphp

<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$quotation->status] ?? 'bg-gray-100 text-gray-800' }}">
    {{ $statusLabels[$quotation->status] ?? ucfirst($quotation->status) }}
</span>
