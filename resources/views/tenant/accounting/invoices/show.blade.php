@extends('layouts.tenant')

@section('title', 'Invoice ' . $invoice->getDisplayNumber())

@php
    $partyType = ($invoice->voucherType->inventory_effect === 'increase') ? 'Vendor' : 'Customer';
@endphp

@section('page-title')
    Invoice {{ $invoice->getDisplayNumber() }}
@endsection

@section('page-description')
    Details for {{ $invoice->voucherType->name }} #{{ $invoice->voucher_number }}
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 lg:gap-8" x-data="invoiceShow()">

    <!-- Left Column (Main Content) -->
    <div class="lg:col-span-2 space-y-6">

        <!-- Customer/Vendor Information -->
        @if($customer)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">{{ $partyType }} Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $customer->display_name ?? $customerLedger->name }}</dd>
                    </div>
                    @if($customer->email)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $customer->email }}</dd>
                    </div>
                    @endif
                    @if($customer->phone)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Phone</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $customer->phone }}</dd>
                    </div>
                    @endif
                    @if($customer->address_line1)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Address</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $customer->address_line1 }}</dd>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Invoice Items -->
        @if($invoice->items && $invoice->items->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Invoice Items</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($invoice->items as $index => $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div>{{ $item->product_name }}</div>
                                <div class="text-xs text-gray-500">{{ $item->description }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($item->quantity, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">₦{{ number_format($item->rate, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">₦{{ number_format($item->amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="4" class="px-6 py-3 text-right text-sm font-medium text-gray-700">Subtotal:</td>
                            <td class="px-6 py-3 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">₦{{ number_format($invoice->items->sum('amount'), 2) }}</td>
                        </tr>
                        <!-- You can add rows for VAT, other charges here if they are in the entries -->
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-right text-sm font-bold text-gray-900">Total:</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">₦{{ number_format($invoice->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif

        <!-- Payment History -->
        @if($payments && $payments->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Payment History</h3>
            </div>
            <div class="p-6 space-y-4">
                @foreach($payments as $payment)
                    <div class="border rounded-lg p-4 hover:shadow-md transition-shadow bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-green-100 p-2 rounded-full">
                                        <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-3a2 2 0 00-2-2H9a2 2 0 00-2 2v3a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900">Payment of ₦{{ number_format($payment->total_amount, 2) }}</h4>
                                        <p class="text-sm text-gray-600">Received on {{ $payment->voucher_date->format('M d, Y') }} via {{ $payment->entries->where('debit_amount', '>', 0)->first()?->ledgerAccount->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right ml-4">
                                <a href="{{ route('tenant.accounting.vouchers.show', ['tenant' => $tenant->slug, 'voucher' => $payment->id]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View Receipt</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Accounting Entries -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Accounting Entries</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Debit</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Credit</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($invoice->entries as $entry)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap"><div class="text-sm text-gray-900">{{ $entry->ledgerAccount->name }}</div></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">@if($entry->debit_amount > 0) ₦{{ number_format($entry->debit_amount, 2) }} @else - @endif</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">@if($entry->credit_amount > 0) ₦{{ number_format($entry->credit_amount, 2) }} @else - @endif</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column (Status & Actions) -->
    <div class="space-y-6 lg:sticky lg:top-6">
        
        <!-- Payment Status Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Payment Status</h3>
            </div>
            <div class="p-6 space-y-4">
                @php
                    $statusColor = 'gray';
                    if ($paymentStatus === 'Paid') $statusColor = 'green';
                    if ($paymentStatus === 'Partially Paid') $statusColor = 'yellow';
                    if ($paymentStatus === 'Unpaid') $statusColor = 'red';
                @endphp
                <div class="text-center">
                    <div class="text-4xl font-bold text-gray-800">₦{{ number_format($balanceDue, 2) }}</div>
                    <div class="text-sm font-medium text-gray-500">Balance Due</div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-{{$statusColor}}-600 h-2.5 rounded-full" style="width: {{ $paymentPercentage }}%"></div>
                </div>
                <div class="flex justify-between text-sm font-medium">
                    <span class="text-{{$statusColor}}-600">{{ $paymentStatus }}</span>
                    <span class="text-gray-500">₦{{ number_format($totalPaid, 2) }} of ₦{{ number_format($invoice->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Actions Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Actions</h3>
            </div>
            <div class="p-6 space-y-3">
                @if($invoice->status === 'posted')
                    <button @click="openReceiptModal()" class="w-full inline-flex items-center justify-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-3a2 2 0 00-2-2H9a2 2 0 00-2 2v3a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Record Payment
                    </button>
                @endif

                <button @click="openEmailModal()" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Email Invoice
                </button>
                
                <div class="flex space-x-3">
                    <a href="{{ route('tenant.accounting.invoices.print', ['tenant' => $tenant->slug, 'invoice' => $invoice->id]) }}" target="_blank" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Print
                    </a>
                    <button @click="downloadPDF()" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-4-4m4 4l4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        PDF
                    </button>
                </div>

                <div class="pt-3 border-t border-gray-200">
                    @if ($invoice->status === 'draft')
                        <form action="{{ route('tenant.accounting.invoices.post', ['tenant' => $tenant->slug, 'invoice' => $invoice->id]) }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-500 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-green-600">Post Invoice</button>
                        </form>
                        <a href="#" class="mt-2 w-full text-center inline-block text-sm text-gray-600 hover:text-gray-900">Edit Invoice</a>
                    @elseif ($invoice->status === 'posted')
                        <form action="{{ route('tenant.accounting.invoices.unpost', ['tenant' => $tenant->slug, 'invoice' => $invoice->id]) }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-yellow-500 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-yellow-600">Unpost Invoice</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Invoice Summary Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Summary</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Invoice Date</dt>
                    <dd class="text-sm text-gray-900">{{ $invoice->voucher_date->format('M d, Y') }}</dd>
                </div>
                @if($invoice->reference_number)
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Reference</dt>
                    <dd class="text-sm text-gray-900">{{ $invoice->reference_number }}</dd>
                </div>
                @endif
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Created By</dt>
                    <dd class="text-sm text-gray-900">{{ $invoice->createdBy->name ?? 'N/A' }}</dd>
                </div>
                @if($invoice->posted_at)
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Posted On</dt>
                    <dd class="text-sm text-gray-900">{{ $invoice->posted_at->format('M d, Y') }} by {{ $invoice->postedBy->name ?? 'N/A' }}</dd>
                </div>
                @endif
                 <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="text-sm text-gray-900">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{$statusColor}}-100 text-{{$statusColor}}-800">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </dd>
                </div>
            </div>
        </div>

    </div>

    <!-- Modals -->
    @include('tenant.accounting.invoices.partials.email-modal')
    @include('tenant.accounting.invoices.partials.receipt-modal')
</div>

@endsection

@push('scripts')
<script>
function invoiceShow() {
    return {
        showEmailModal: false,
        showReceiptModal: false,
        invoiceAmount: {{ $invoice->total_amount }},
        balanceDue: {{ $balanceDue }},

        emailForm: {
            to: '{{ $customer->email ?? "" }}',
            subject: 'Invoice {{ $invoice->getDisplayNumber() }} from {{ $tenant->name }}',
            message: `Dear {{ $customer->display_name ?? 'Customer' }},

Please find attached your invoice ({{ $invoice->getDisplayNumber() }}) for the amount of ₦{{ number_format($invoice->total_amount, 2) }}.

Thank you for your business!

Best regards,
{{ $tenant->name }}`
        },
        receiptForm: {
            date: '{{ date("Y-m-d") }}',
            amount: '{{ $balanceDue > 0 ? $balanceDue : "" }}',
            bank_account_id: '',
            reference: '',
            notes: 'Payment for invoice {{ $invoice->getDisplayNumber() }}'
        },

        openEmailModal() { this.showEmailModal = true; },
        closeEmailModal() { this.showEmailModal = false; },
        openReceiptModal() { this.showReceiptModal = true; },
        closeReceiptModal() { this.showReceiptModal = false; },

        async sendEmail() {
            // ... existing implementation ...
        },
        async recordPayment() {
            // ... existing implementation ...
        },
        downloadPDF() {
            window.open('{{ route("tenant.accounting.invoices.pdf", ["tenant" => $tenant->slug, "invoice" => $invoice->id]) }}', '_blank');
        }
    };
}
</script>
@endpush