@extends('layouts.tenant')

@section('title', 'Purchase Order ' . $purchaseOrder->lpo_number . ' - ' . $tenant->name)
@section('page-title', 'Purchase Order ' . $purchaseOrder->lpo_number)
@section('page-description', 'Purchase order details for ' . $purchaseOrder->vendor->getFullNameAttribute())

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Purchase Order {{ $purchaseOrder->lpo_number }}</h1>
            <p class="mt-2 text-gray-600">
                <span class="px-2 py-1 text-xs font-semibold rounded-full
                    @if($purchaseOrder->status === 'draft') bg-gray-100 text-gray-800
                    @elseif($purchaseOrder->status === 'sent') bg-blue-100 text-blue-800
                    @elseif($purchaseOrder->status === 'confirmed') bg-green-100 text-green-800
                    @elseif($purchaseOrder->status === 'received') bg-purple-100 text-purple-800
                    @else bg-red-100 text-red-800
                    @endif">
                    {{ ucfirst($purchaseOrder->status) }}
                </span>
            </p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('tenant.procurement.purchase-orders.print', [$tenant->slug, $purchaseOrder->id]) }}"
               target="_blank"
               class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print
            </a>
            <a href="{{ route('tenant.procurement.purchase-orders.pdf', [$tenant->slug, $purchaseOrder->id]) }}"
               class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                </svg>
                PDF
            </a>
            <button onclick="openEmailModal()"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Email
            </button>
            <a href="{{ route('tenant.procurement.purchase-orders.index', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back
            </a>
        </div>
    </div>

    <!-- Details Section -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Information</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">Vendor</h4>
                <p class="text-base font-semibold text-gray-900">{{ $purchaseOrder->vendor->getFullNameAttribute() }}</p>
                @if($purchaseOrder->vendor->email)
                    <p class="text-sm text-gray-600">{{ $purchaseOrder->vendor->email }}</p>
                @endif
                @if($purchaseOrder->vendor->phone)
                    <p class="text-sm text-gray-600">{{ $purchaseOrder->vendor->phone }}</p>
                @endif
                @if($purchaseOrder->vendor->getFullAddressAttribute())
                    <p class="text-sm text-gray-500 mt-1">{{ $purchaseOrder->vendor->getFullAddressAttribute() }}</p>
                @endif
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">LPO Date</h4>
                <p class="text-base text-gray-900">{{ $purchaseOrder->lpo_date->format('M d, Y') }}</p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">Expected Delivery</h4>
                <p class="text-base text-gray-900">{{ $purchaseOrder->expected_delivery_date ? $purchaseOrder->expected_delivery_date->format('M d, Y') : 'Not Set' }}</p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">Created By</h4>
                <p class="text-base text-gray-900">{{ $purchaseOrder->creator->name ?? 'N/A' }}</p>
                <p class="text-sm text-gray-500">{{ $purchaseOrder->created_at->format('M d, Y h:i A') }}</p>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Items ({{ $purchaseOrder->items->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">S/N</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item & Description</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Discount</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tax</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($purchaseOrder->items as $index => $item)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-gray-900">{{ $item->product->name ?? $item->description }}</div>
                                @if($item->description && $item->description !== ($item->product->name ?? ''))
                                    <div class="text-xs text-gray-500">{{ $item->description }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-right text-gray-900">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }} {{ $item->unit }}</td>
                            <td class="px-4 py-3 text-sm text-right text-gray-900">₦{{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-right text-gray-900">₦{{ number_format($item->discount, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-right text-gray-900">{{ rtrim(rtrim(number_format($item->tax_rate, 2), '0'), '.') }}%</td>
                            <td class="px-4 py-3 text-sm text-right font-medium text-gray-900">₦{{ number_format($item->getTotal(), 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-sm font-medium text-right text-gray-900">Subtotal:</td>
                        <td class="px-4 py-3 text-sm font-medium text-right text-gray-900">₦{{ number_format($purchaseOrder->subtotal, 2) }}</td>
                    </tr>
                    @if($purchaseOrder->discount_amount > 0)
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-sm font-medium text-right text-gray-900">Discount:</td>
                        <td class="px-4 py-3 text-sm font-medium text-right text-red-600">-₦{{ number_format($purchaseOrder->discount_amount, 2) }}</td>
                    </tr>
                    @endif
                    @if($purchaseOrder->tax_amount > 0)
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-sm font-medium text-right text-gray-900">Tax:</td>
                        <td class="px-4 py-3 text-sm font-medium text-right text-gray-900">₦{{ number_format($purchaseOrder->tax_amount, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="border-t-2">
                        <td colspan="6" class="px-4 py-3 text-base font-bold text-right text-gray-900">Total:</td>
                        <td class="px-4 py-3 text-base font-bold text-right text-orange-600">₦{{ number_format($purchaseOrder->total_amount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Notes & Terms -->
    @if($purchaseOrder->notes || $purchaseOrder->terms_conditions)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @if($purchaseOrder->notes)
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-2">Notes</h3>
            <p class="text-sm text-gray-600 whitespace-pre-line">{{ $purchaseOrder->notes }}</p>
        </div>
        @endif
        @if($purchaseOrder->terms_conditions)
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-2">Terms & Conditions</h3>
            <p class="text-sm text-gray-600 whitespace-pre-line">{{ $purchaseOrder->terms_conditions }}</p>
        </div>
        @endif
    </div>
    @endif
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
