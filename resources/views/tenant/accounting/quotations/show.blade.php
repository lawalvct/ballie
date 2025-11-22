@extends('layouts.tenant')

@section('title', 'Quotation ' . $quotation->getQuotationNumber() . ' - ' . $tenant->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Quotation {{ $quotation->getQuotationNumber() }}</h1>
            <p class="mt-2 text-gray-600">{{ $quotation->subject }}</p>
        </div>
        <div class="flex items-center space-x-2">
            @if($quotation->canBeEdited())
                <a href="{{ route('tenant.accounting.quotations.edit', [$tenant->slug, $quotation->id]) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
            @endif
            <a href="{{ route('tenant.accounting.quotations.index', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white hover:bg-gray-50">
                Back
            </a>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="flex items-center space-x-3">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
            @if($quotation->status === 'draft') bg-gray-100 text-gray-800
            @elseif($quotation->status === 'sent') bg-blue-100 text-blue-800
            @elseif($quotation->status === 'accepted') bg-green-100 text-green-800
            @elseif($quotation->status === 'rejected') bg-red-100 text-red-800
            @elseif($quotation->status === 'expired') bg-yellow-100 text-yellow-800
            @elseif($quotation->status === 'converted') bg-purple-100 text-purple-800
            @endif">
            {{ ucfirst($quotation->status) }}
        </span>
        @if($quotation->isExpired())
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                Expired
            </span>
        @endif
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Quotation Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Customer</h3>
                        <p class="text-base font-semibold text-gray-900">
                            {{ $quotation->customer ? ($quotation->customer->company_name ?: trim($quotation->customer->first_name . ' ' . $quotation->customer->last_name)) : 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Quotation Date</h3>
                        <p class="text-base text-gray-900">{{ $quotation->quotation_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Expiry Date</h3>
                        <p class="text-base text-gray-900">{{ $quotation->expiry_date ? $quotation->expiry_date->format('M d, Y') : 'N/A' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Reference Number</h3>
                        <p class="text-base text-gray-900">{{ $quotation->reference_number ?: 'N/A' }}</p>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Items</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Rate</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Discount</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tax</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($quotation->items as $item)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        <div class="font-medium">{{ $item->product_name }}</div>
                                        @if($item->description)
                                            <div class="text-xs text-gray-500">{{ $item->description }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-900">{{ $item->quantity }} {{ $item->unit }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-900">₦{{ number_format($item->rate, 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-900">₦{{ number_format($item->discount, 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-900">{{ $item->tax }}%</td>
                                    <td class="px-4 py-3 text-sm text-right font-medium text-gray-900">₦{{ number_format($item->getTotal(), 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="5" class="px-4 py-3 text-sm font-medium text-right text-gray-900">Subtotal:</td>
                                    <td class="px-4 py-3 text-sm font-medium text-right text-gray-900">₦{{ number_format($quotation->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="px-4 py-3 text-sm font-medium text-right text-gray-900">Total Discount:</td>
                                    <td class="px-4 py-3 text-sm font-medium text-right text-red-600">₦{{ number_format($quotation->total_discount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="px-4 py-3 text-sm font-medium text-right text-gray-900">Total Tax:</td>
                                    <td class="px-4 py-3 text-sm font-medium text-right text-gray-900">₦{{ number_format($quotation->total_tax, 2) }}</td>
                                </tr>
                                <tr class="border-t-2">
                                    <td colspan="5" class="px-4 py-3 text-base font-bold text-right text-gray-900">Total:</td>
                                    <td class="px-4 py-3 text-base font-bold text-right text-emerald-600">₦{{ number_format($quotation->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Terms & Notes -->
                @if($quotation->terms_and_conditions || $quotation->notes)
                <div class="mt-6 space-y-4">
                    @if($quotation->terms_and_conditions)
                    <div>
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Terms & Conditions</h3>
                        <p class="text-sm text-gray-600">{{ $quotation->terms_and_conditions }}</p>
                    </div>
                    @endif
                    @if($quotation->notes)
                    <div>
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Notes</h3>
                        <p class="text-sm text-gray-600">{{ $quotation->notes }}</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Actions Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6 space-y-3">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                
                <button type="button" onclick="openEmailModal()" class="w-full inline-flex justify-center items-center px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Send Email
                </button>

                @if($quotation->canBeSent())
                <form action="{{ route('tenant.accounting.quotations.send', [$tenant->slug, $quotation->id]) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                        Mark as Sent
                    </button>
                </form>
                @endif

                @if($quotation->canBeConverted())
                <form action="{{ route('tenant.accounting.quotations.convert', [$tenant->slug, $quotation->id]) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700">
                        Convert to Invoice
                    </button>
                </form>
                @endif

                <a href="{{ route('tenant.accounting.quotations.pdf', [$tenant->slug, $quotation->id]) }}" target="_blank"
                   class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50">
                    Download PDF
                </a>

                <a href="{{ route('tenant.accounting.quotations.print', [$tenant->slug, $quotation->id]) }}" target="_blank"
                   class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50">
                    Print
                </a>

                <form action="{{ route('tenant.accounting.quotations.duplicate', [$tenant->slug, $quotation->id]) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50">
                        Duplicate
                    </button>
                </form>

                @if($quotation->canBeDeleted())
                <form action="{{ route('tenant.accounting.quotations.destroy', [$tenant->slug, $quotation->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this quotation?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">
                        Delete
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Email Modal -->
<div id="emailModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Send Quotation via Email</h3>
            <button onclick="closeEmailModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="emailForm" onsubmit="sendEmail(event)">
            <div class="space-y-4">
                <div>
                    <label for="email_to" class="block text-sm font-medium text-gray-700 mb-1">To *</label>
                    <input type="email" id="email_to" name="to" required value="{{ $quotation->customer->email ?? '' }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                
                <div>
                    <label for="email_subject" class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                    <input type="text" id="email_subject" name="subject" required value="Quotation {{ $quotation->getQuotationNumber() }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                
                <div>
                    <label for="email_message" class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                    <textarea id="email_message" name="message" rows="4" required
                              class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">Dear {{ $quotation->customer ? ($quotation->customer->company_name ?: $quotation->customer->first_name) : 'Customer' }},

Please find attached quotation {{ $quotation->getQuotationNumber() }} for your review.

Best regards</textarea>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeEmailModal()"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" id="sendBtn"
                        class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">
                    Send Email
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openEmailModal() {
    document.getElementById('emailModal').classList.remove('hidden');
}

function closeEmailModal() {
    document.getElementById('emailModal').classList.add('hidden');
}

async function sendEmail(event) {
    event.preventDefault();
    
    const btn = document.getElementById('sendBtn');
    btn.disabled = true;
    btn.textContent = 'Sending...';
    
    const formData = new FormData(event.target);
    const data = {
        to: formData.get('to'),
        subject: formData.get('subject'),
        message: formData.get('message')
    };
    
    try {
        const response = await fetch('{{ route('tenant.accounting.quotations.email', [$tenant->slug, $quotation->id]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (response.ok) {
            alert('Email sent successfully!');
            closeEmailModal();
            location.reload();
        } else {
            alert('Failed to send email: ' + (result.message || 'Unknown error'));
        }
    } catch (error) {
        alert('Error sending email: ' + error.message);
    } finally {
        btn.disabled = false;
        btn.textContent = 'Send Email';
    }
}
</script>
@endpush
@endsection
