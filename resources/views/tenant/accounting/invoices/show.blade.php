@extends('layouts.tenant')

@section('title', 'Invoice Details - ' . $tenant->name)

@section('content')
<div class="space-y-6" x-data="invoiceShow()">
    <!-- Header with Actions -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-center space-x-3">
            <div class="bg-gradient-to-br from-blue-400 to-blue-600 p-3 rounded-xl shadow-lg">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    Invoice {{ $invoice->voucherType->prefix ?? '' }}{{ $invoice->voucher_number }}
                </h1>
                <div class="flex items-center space-x-4 mt-1">
                    <p class="text-sm text-gray-500">{{ $invoice->voucher_date->format('M d, Y') }}</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($invoice->status === 'posted') bg-green-100 text-green-800
                        @elseif($invoice->status === 'draft') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="mt-4 lg:mt-0 flex flex-wrap gap-3">
            <a href="{{ route('tenant.accounting.invoices.index', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to List
            </a>

            <!-- Print Button -->
            <a href="{{ route('tenant.accounting.invoices.print', ['tenant' => $tenant->slug, 'invoice' => $invoice->id]) }}"
               target="_blank"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print
            </a>

            <!-- Download PDF Button -->
            <button @click="downloadPDF()"
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-4-4m4 4l4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Download PDF
            </button>

            <!-- Email Button -->
            <button @click="openEmailModal()"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Email Invoice
            </button>
        </div>
    </div>

    <!-- Invoice Details Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Invoice Details</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Invoice Number</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $invoice->voucherType->prefix ?? '' }}{{ $invoice->voucher_number }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Date</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $invoice->voucher_date->format('M d, Y') }}</dd>
                </div>
                @if($invoice->reference_number)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Reference</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $invoice->reference_number }}</dd>
                </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-gray-500">Total Amount</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">₦{{ number_format($invoice->total_amount, 2) }}</dd>
                </div>
            </div>

            @if($invoice->narration)
            <div class="mt-6">
                <dt class="text-sm font-medium text-gray-500">Description</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $invoice->narration }}</dd>
            </div>
            @endif
        </div>
    </div>

    <!-- Customer Information (if available) -->
    @if($invoice->entries->where('debit_amount', '>', 0)->first()?->ledgerAccount)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Customer Information</h3>
        </div>
        <div class="p-6">
            @php $customerLedger = $invoice->entries->where('debit_amount', '>', 0)->first()?->ledgerAccount @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Customer Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $customerLedger->name }}</dd>
                </div>
                @if($customerLedger->email)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $customerLedger->email }}</dd>
                </div>
                @endif
                @if($customerLedger->phone)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $customerLedger->phone }}</dd>
                </div>
                @endif
                @if($customerLedger->address)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Address</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $customerLedger->address }}</dd>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Invoice Items -->
    @if($invoice->items && $invoice->items->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Invoice Items</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($invoice->items as $index => $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->product_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $item->description }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($item->quantity, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">₦{{ number_format($item->rate, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">₦{{ number_format($item->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Total:</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">₦{{ number_format($invoice->items->sum('amount'), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif

    <!-- Receipt Voucher Section -->
    @if($invoice->status === 'posted')
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Record Payment</h3>
                <button @click="openReceiptModal()"
                        class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Record Payment
                </button>
            </div>
        </div>
        <div class="p-6">
            @if($payments && $payments->count() > 0)
                <!-- Payment Summary -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">₦{{ number_format($invoice->total_amount, 2) }}</div>
                            <div class="text-sm text-gray-600">Invoice Total</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">₦{{ number_format($totalPaid, 2) }}</div>
                            <div class="text-sm text-gray-600">Total Paid</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold {{ $balanceDue > 0 ? 'text-red-600' : 'text-green-600' }}">₦{{ number_format($balanceDue, 2) }}</div>
                            <div class="text-sm text-gray-600">Balance Due</div>
                        </div>
                    </div>
                </div>

                <!-- Payments List -->
                <div class="space-y-4">
                    @foreach($payments as $payment)
                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-green-100 p-2 rounded-full">
                                            <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-3a2 2 0 00-2-2H9a2 2 0 00-2 2v3a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900">
                                                {{ $payment->voucherType->name ?? 'Receipt' }} #{{ $payment->voucher_number }}
                                            </h4>
                                            <p class="text-sm text-gray-600">
                                                Received on {{ \Carbon\Carbon::parse($payment->voucher_date)->format('M d, Y') }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <span class="text-sm font-medium text-gray-700">Payment Method:</span>
                                            @php
                                                $bankEntry = $payment->entries->where('debit_amount', '>', 0)->first();
                                            @endphp
                                            <span class="text-sm text-gray-600">{{ $bankEntry->ledgerAccount->name ?? 'N/A' }}</span>
                                        </div>

                                        @if($payment->reference_number)
                                        <div>
                                            <span class="text-sm font-medium text-gray-700">Reference:</span>
                                            <span class="text-sm text-gray-600">{{ $payment->reference_number }}</span>
                                        </div>
                                        @endif
                                    </div>

                                    @if($payment->narration)
                                    <div class="mt-3">
                                        <span class="text-sm font-medium text-gray-700">Notes:</span>
                                        <p class="text-sm text-gray-600 mt-1">{{ $payment->narration }}</p>
                                    </div>
                                    @endif

                                    <div class="mt-3 text-xs text-gray-500">
                                        Recorded by {{ $payment->createdBy->name ?? 'System' }}
                                        on {{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y g:i A') }}
                                    </div>
                                </div>

                                <div class="text-right ml-4">
                                    <div class="text-2xl font-bold text-green-600">
                                        ₦{{ number_format($payment->total_amount, 2) }}
                                    </div>
                                    <div class="flex space-x-2 mt-2">
                                        <a href="{{ route('tenant.accounting.vouchers.show', ['tenant' => $tenant->slug, 'voucher' => $payment->id]) }}"
                                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            View Receipt
                                        </a>
                                        @if($payment->status === 'posted')
                                        <a href="{{ route('tenant.accounting.vouchers.pdf', ['tenant' => $tenant->slug, 'voucher' => $payment->id]) }}"
                                        target="_blank"
                                        class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                                            Print
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- No Payments -->
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-3a2 2 0 00-2-2H9a2 2 0 00-2 2v3a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No payments recorded</h3>
                    <p class="mt-1 text-sm text-gray-500">Record a payment to track customer payments for this invoice.</p>
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Accounting Entries -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Accounting Entries</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Particulars</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Debit</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Credit</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($invoice->entries as $entry)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $entry->ledgerAccount->name }}</div>
                            <div class="text-xs text-gray-500">{{ $entry->ledgerAccount->code }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $entry->particulars }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                            @if($entry->debit_amount > 0)
                                ₦{{ number_format($entry->debit_amount, 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                            @if($entry->credit_amount > 0)
                                ₦{{ number_format($entry->credit_amount, 2) }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="2" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Totals:</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">₦{{ number_format($invoice->entries->sum('debit_amount'), 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">₦{{ number_format($invoice->entries->sum('credit_amount'), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Email Modal -->
    <div x-show="showEmailModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;"
         @click.away="closeEmailModal()">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form @submit.prevent="sendEmail()">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Send Invoice via Email</h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">To Email</label>
                                        <input type="email"
                                               x-model="emailForm.to"
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                               placeholder="customer@example.com"
                                               required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Subject</label>
                                        <input type="text"
                                               x-model="emailForm.subject"
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Message</label>
                                        <textarea x-model="emailForm.message"
                                                  rows="4"
                                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Send Email
                        </button>
                        <button type="button"
                                @click="closeEmailModal()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div x-show="showReceiptModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;"
         @click.away="closeReceiptModal()">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <form @submit.prevent="recordPayment()">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-3a2 2 0 00-2-2H9a2 2 0 00-2 2v3a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Record Payment Receipt</h3>
                                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Payment Date</label>
                                        <input type="date"
                                               x-model="receiptForm.date"
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                               required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Amount Received</label>
                                        <input type="number"
                                               x-model="receiptForm.amount"
                                               step="0.01"
                                               min="0.01"
                                               :max="invoiceAmount"
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                               placeholder="0.00"
                                               required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">Bank/Cash Account</label>
                                        <select x-model="receiptForm.bank_account_id"
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                                required>
                                            <option value="">Select Bank/Cash Account</option>
                                            @foreach($bankAccounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->name }} ({{ $account->code }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">Reference Number</label>
                                        <input type="text"
                                               x-model="receiptForm.reference"
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                               placeholder="Transaction reference">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                                        <textarea x-model="receiptForm.notes"
                                                  rows="3"
                                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                                  placeholder="Payment notes..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Record Payment
                        </button>
                        <button type="button"
                                @click="closeReceiptModal()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function invoiceShow() {
    return {
        showEmailModal: false,
        showReceiptModal: false,
        invoiceAmount: {{ $invoice->total_amount }},
        emailForm: {
            to: '{{ $invoice->entries->where("debit_amount", ">", 0)->first()?->ledgerAccount->email ?? "" }}',
            subject: 'Invoice {{ $invoice->voucherType->prefix ?? "" }}{{ $invoice->voucher_number }} from {{ $tenant->name }}',
            message: 'Dear Customer,\n\nPlease find attached your invoice for the amount of ₦{{ number_format($invoice->total_amount, 2) }}.\n\nThank you for your business.\n\nBest regards,\n{{ $tenant->name }}'
        },
        receiptForm: {
            date: '{{ date("Y-m-d") }}',
            amount: '',
            bank_account_id: '',
            reference: '',
            notes: ''
        },

        openEmailModal() {
            this.showEmailModal = true;
        },

        closeEmailModal() {
            this.showEmailModal = false;
        },

        openReceiptModal() {
            this.showReceiptModal = true;
        },

        closeReceiptModal() {
            this.showReceiptModal = false;
        },

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
</script>
@endpush
@endsection
