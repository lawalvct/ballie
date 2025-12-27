@extends('layouts.tenant')

@section('title', 'Invoice ' . ($invoice->voucherType->prefix ?? '') . $invoice->voucher_number)

@php
    $partyType = ($invoice->voucherType->inventory_effect === 'increase') ? 'Vendor' : 'Customer';
@endphp

@section('page-title')
    Invoice {{ $invoice->voucherType->prefix ?? '' }}{{ $invoice->voucher_number }}
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

                        @php
                            // Get VAT and additional charges from voucher entries
                            $vatEntries = $invoice->entries->filter(function($entry) {
                                return str_contains(strtolower($entry->ledgerAccount->name ?? ''), 'vat') ||
                                       in_array($entry->ledgerAccount->code ?? '', ['VAT-OUT-001', 'VAT-IN-001']);
                            });

                            $additionalEntries = $invoice->entries->filter(function($entry) use ($invoice) {
                                // Exclude customer/vendor accounts (AR/AP) and product accounts and VAT accounts
                                $excludeGroups = ['AR', 'AP'];
                                $isVat = str_contains(strtolower($entry->ledgerAccount->name ?? ''), 'vat') ||
                                        in_array($entry->ledgerAccount->code ?? '', ['VAT-OUT-001', 'VAT-IN-001']);

                                return !in_array($entry->ledgerAccount->accountGroup->code ?? '', $excludeGroups) &&
                                       !$isVat &&
                                       ($entry->credit_amount > 0 && $invoice->voucherType->inventory_effect === 'decrease') || // Sales: credit entries are expenses/charges
                                       ($entry->debit_amount > 0 && $invoice->voucherType->inventory_effect === 'increase'); // Purchase: debit entries are expenses/charges
                            });

                            // Filter out product/inventory accounts by checking if they have corresponding items
                            $productAccountIds = $invoice->items->map(function($item) use ($invoice) {
                                $product = \App\Models\Product::find($item->product_id);
                                return $invoice->voucherType->inventory_effect === 'decrease' ?
                                       $product?->sales_account_id : $product?->purchase_account_id;
                            })->filter()->unique();

                            $additionalEntries = $additionalEntries->filter(function($entry) use ($productAccountIds) {
                                return !$productAccountIds->contains($entry->ledger_account_id);
                            });
                        @endphp

                        @foreach($additionalEntries as $entry)
                        <tr>
                            <td colspan="4" class="px-6 py-2 text-right text-sm text-gray-600">{{ $entry->ledgerAccount->name }}:</td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 text-right">
                                ₦{{ number_format($entry->credit_amount > 0 ? $entry->credit_amount : $entry->debit_amount, 2) }}
                            </td>
                        </tr>
                        @endforeach

                        @foreach($vatEntries as $entry)
                        <tr>
                            <td colspan="4" class="px-6 py-2 text-right text-sm text-gray-600">
                                @if($entry->narration && str_contains($entry->narration, 'VAT'))
                                    {{ $entry->narration }}
                                @else
                                    VAT (7.5%)
                                @endif:
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 text-right">
                                ₦{{ number_format($entry->credit_amount > 0 ? $entry->credit_amount : $entry->debit_amount, 2) }}
                            </td>
                        </tr>
                        @endforeach

                        <tr class="border-t border-gray-300">
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
                    $statusColors = match($paymentStatus) {
                        'Paid' => ['bg' => 'bg-green-600', 'text' => 'text-green-600'],
                        'Partially Paid' => ['bg' => 'bg-yellow-500', 'text' => 'text-yellow-600'],
                        'Unpaid' => ['bg' => 'bg-red-600', 'text' => 'text-red-600'],
                        default => ['bg' => 'bg-gray-600', 'text' => 'text-gray-600'],
                    };
                @endphp
                <div class="text-center">
                    <div class="text-4xl font-bold text-gray-800">₦{{ number_format($balanceDue, 2) }}</div>
                    <div class="text-sm font-medium text-gray-500">Balance Due</div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="{{ $statusColors['bg'] }} h-2.5 rounded-full" style="width: {{ $paymentPercentage }}%"></div>
                </div>
                <div class="flex justify-between text-sm font-medium">
                    <span class="{{ $statusColors['text'] }}">{{ $paymentStatus }}</span>
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

                @php
                    $paymentLinks = $invoice->meta_data['payment_links'] ?? [];
                @endphp

                @if(!empty($paymentLinks))
                    <div class="pt-3 border-t border-gray-200">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Payment Links</h4>
                        <div class="space-y-2">
                            @if(isset($paymentLinks['nomba']))
                                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-semibold text-green-800 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                            Nomba
                                        </span>
                                        <span class="text-xs text-green-600">₦{{ number_format($paymentLinks['nomba']['amount'], 2) }}</span>
                                    </div>
                                    <a href="{{ $paymentLinks['nomba']['checkout_link'] }}" target="_blank" class="block w-full text-center px-3 py-2 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700 transition-colors">
                                        Pay with Nomba
                                    </a>
                                    <button onclick="copyToClipboard('{{ $paymentLinks['nomba']['checkout_link'] }}')" class="mt-1 block w-full text-center px-3 py-1 bg-white border border-green-300 text-green-700 text-xs font-medium rounded hover:bg-green-50 transition-colors">
                                        Copy Link
                                    </button>
                                </div>
                            @endif

                            @if(isset($paymentLinks['paystack']))
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-semibold text-blue-800 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                            Paystack
                                        </span>
                                        <span class="text-xs text-blue-600">₦{{ number_format($invoice->total_amount, 2) }}</span>
                                    </div>
                                    <a href="{{ $paymentLinks['paystack']['authorization_url'] }}" target="_blank" class="block w-full text-center px-3 py-2 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 transition-colors">
                                        Pay with Paystack
                                    </a>
                                    <button onclick="copyToClipboard('{{ $paymentLinks['paystack']['authorization_url'] }}')" class="mt-1 block w-full text-center px-3 py-1 bg-white border border-blue-300 text-blue-700 text-xs font-medium rounded hover:bg-blue-50 transition-colors">
                                        Copy Link
                                    </button>
                                </div>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-2 italic">Share these links with your customer for easy payment</p>
                    </div>
                @endif

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
                        <a href="{{ route('tenant.accounting.invoices.edit', ['tenant' => $tenant->slug, 'invoice' => $invoice->id]) }}" class="mt-2 w-full text-center inline-block text-sm text-gray-600 hover:text-gray-900">Edit Invoice</a>
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
                        @php
                            $invoiceStatusColors = match($invoice->status) {
                                'posted' => ['bg' => 'bg-green-100', 'text' => 'text-green-800'],
                                'draft' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
                                default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800'],
                            };
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $invoiceStatusColors['bg'] }} {{ $invoiceStatusColors['text'] }}">
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
            subject: 'Invoice {{ ($invoice->voucherType->prefix ?? '') . $invoice->voucher_number }} from {{ $tenant->name }}',
            message: `Dear {{ $customer->display_name ?? 'Customer' }},

Please find attached your invoice ({{ ($invoice->voucherType->prefix ?? '') . $invoice->voucher_number }}) for the amount of ₦{{ number_format($invoice->total_amount, 2) }}.

Thank you for your business!

Best regards,
{{ $tenant->name }}`
        },
        receiptForm: {
            date: '{{ date("Y-m-d") }}',
            amount: '{{ $balanceDue > 0 ? $balanceDue : "" }}',
            bank_account_id: '',
            reference: '',
            notes: 'Payment for invoice {{ ($invoice->voucherType->prefix ?? '') . $invoice->voucher_number }}'
        },

        openEmailModal() { this.showEmailModal = true; },
        closeEmailModal() { this.showEmailModal = false; },
        openReceiptModal() { this.showReceiptModal = true; },
        closeReceiptModal() { this.showReceiptModal = false; },

        async sendEmail() {
            try {
                const response = await fetch('{{ route("tenant.accounting.invoices.email", ["tenant" => $tenant->slug, "invoice" => $invoice->id]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.emailForm)
                });

                if (response.ok) {
                    this.closeEmailModal();
                    alert('Invoice sent successfully!');
                } else {
                    const error = await response.json();
                    alert('Error sending email: ' + (error.message || 'Unknown error'));
                }
            } catch (error) {
                alert('Error sending email: ' + error.message);
            }
        },

        async recordPayment() {
            try {
                const response = await fetch('{{ route("tenant.accounting.invoices.record-payment", ["tenant" => $tenant->slug, "invoice" => $invoice->id]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.receiptForm)
                });

                if (response.ok) {
                    this.closeReceiptModal();
                    location.reload(); // Reload to show the new payment
                } else {
                    const error = await response.json();
                    alert('Error recording payment: ' + (error.message || 'Unknown error'));
                }
            } catch (error) {
                alert('Error recording payment: ' + error.message);
            }
        },
        downloadPDF() {
            window.open('{{ route("tenant.accounting.invoices.pdf", ["tenant" => $tenant->slug, "invoice" => $invoice->id]) }}', '_blank');
        }
    };
}

// Copy to clipboard function
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Show success message
        const message = document.createElement('div');
        message.className = 'fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg';
        message.textContent = '✓ Payment link copied to clipboard!';
        document.body.appendChild(message);

        setTimeout(() => {
            message.remove();
        }, 3000);
    }).catch(err => {
        alert('Failed to copy link. Please try again.');
        console.error('Copy failed:', err);
    });
}
</script>
@endpush
