@extends('layouts.tenant')

@section('title', 'Purchase Order ' . $purchaseOrder->lpo_number . ' - ' . $tenant->name)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Purchase Order {{ $purchaseOrder->lpo_number }}</h1>
            <p class="mt-2 text-gray-600">View purchase order details</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('tenant.procurement.purchase-orders.pdf', [$tenant->slug, $purchaseOrder->id]) }}"
               class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                </svg>
                Download PDF
            </a>
            <button onclick="openEmailModal()"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Send Email
            </button>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-sm font-medium text-gray-500">Vendor</h3>
                <p class="mt-1 text-lg font-semibold">{{ $purchaseOrder->vendor->getFullNameAttribute() }}</p>
                <p class="text-sm text-gray-600">{{ $purchaseOrder->vendor->email }}</p>
            </div>
            <div class="text-right">
                <h3 class="text-sm font-medium text-gray-500">Status</h3>
                <span class="mt-1 inline-block px-3 py-1 text-sm font-semibold rounded-full
                    @if($purchaseOrder->status === 'draft') bg-gray-100 text-gray-800
                    @elseif($purchaseOrder->status === 'sent') bg-blue-100 text-blue-800
                    @elseif($purchaseOrder->status === 'confirmed') bg-green-100 text-green-800
                    @else bg-purple-100 text-purple-800
                    @endif">
                    {{ ucfirst($purchaseOrder->status) }}
                </span>
            </div>
        </div>

        <div class="border-t border-gray-200 pt-6">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2">Item</th>
                        <th class="text-right py-2">Qty</th>
                        <th class="text-right py-2">Price</th>
                        <th class="text-right py-2">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseOrder->items as $item)
                        <tr class="border-b">
                            <td class="py-3">{{ $item->product->name }}</td>
                            <td class="text-right">{{ number_format($item->quantity, 2) }} {{ $item->unit }}</td>
                            <td class="text-right">₦{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-right">₦{{ number_format($item->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="font-bold">
                        <td colspan="3" class="text-right py-3">Total:</td>
                        <td class="text-right">₦{{ number_format($purchaseOrder->total_amount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Email Modal -->
<div id="emailModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Send Purchase Order</h3>
            <button onclick="closeEmailModal()" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="emailForm" onsubmit="sendEmail(event)">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">To</label>
                    <input type="email" id="emailTo" value="{{ $purchaseOrder->vendor->email }}" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Subject</label>
                    <input type="text" id="emailSubject" value="Purchase Order {{ $purchaseOrder->lpo_number }}" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Message</label>
                    <textarea id="emailMessage" rows="4" required
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">Please find attached Purchase Order {{ $purchaseOrder->lpo_number }}.</textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeEmailModal()"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Send
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function openEmailModal() {
    document.getElementById('emailModal').classList.remove('hidden');
}

function closeEmailModal() {
    document.getElementById('emailModal').classList.add('hidden');
}

function sendEmail(event) {
    event.preventDefault();
    
    const data = {
        to: document.getElementById('emailTo').value,
        subject: document.getElementById('emailSubject').value,
        message: document.getElementById('emailMessage').value,
    };

    fetch('{{ route("tenant.procurement.purchase-orders.email", [$tenant->slug, $purchaseOrder->id]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        alert('Purchase Order sent successfully!');
        closeEmailModal();
        location.reload();
    })
    .catch(error => {
        alert('Failed to send email');
    });
}
</script>
@endsection
