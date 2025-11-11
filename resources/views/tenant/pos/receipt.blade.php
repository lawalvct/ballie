@extends('layouts.tenant')

@section('title', 'Receipt #' . $sale->sale_number . ' - ' . $tenant->name)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Actions -->
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('tenant.pos.index', ['tenant' => $tenant->slug]) }}"
               class="text-gray-600 hover:text-gray-800 font-medium flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to POS
            </a>
            <div class="flex space-x-3">
                <button onclick="window.print()"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                    <i class="fas fa-print"></i>
                    <span>Print</span>
                </button>
                <button onclick="emailReceipt()"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                    <i class="fas fa-envelope"></i>
                    <span>Email</span>
                </button>
            </div>
        </div>

        <!-- Receipt Container -->
        <div id="receipt" class="bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden">
            <!-- Receipt Header -->
            <div class="text-center bg-gradient-to-r from-purple-600 to-purple-700 text-white py-6 px-6">
                <h1 class="text-2xl font-bold">{{ $tenant->name }}</h1>
                @if($tenant->address)
                    <p class="text-purple-100 text-sm mt-1">{{ $tenant->address }}</p>
                @endif
                <div class="flex justify-center space-x-4 mt-2 text-purple-100 text-sm">
                    @if($tenant->phone)
                        <span><i class="fas fa-phone mr-1"></i>{{ $tenant->phone }}</span>
                    @endif
                    @if($tenant->email)
                        <span><i class="fas fa-envelope mr-1"></i>{{ $tenant->email }}</span>
                    @endif
                </div>
            </div>

            <!-- Receipt Body -->
            <div class="p-6">
                <!-- Transaction Info -->
                <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">SALES RECEIPT</h2>
                        <p class="text-gray-600 text-sm">{{ $sale->sale_number }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold">{{ $sale->sale_date->format('M d, Y') }}</p>
                        <p class="text-gray-600 text-sm">{{ $sale->sale_date->format('h:i A') }}</p>
                    </div>
                </div>

                <!-- Customer Info -->
                <div class="mb-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-2">Customer</h3>
                            @if($sale->customer)
                                <p class="text-gray-900">
                                    @if($sale->customer->customer_type === 'individual')
                                        {{ $sale->customer->first_name }} {{ $sale->customer->last_name }}
                                    @else
                                        {{ $sale->customer->company_name }}
                                    @endif
                                </p>
                                @if($sale->customer->email)
                                    <p class="text-gray-600 text-sm">{{ $sale->customer->email }}</p>
                                @endif
                                @if($sale->customer->phone)
                                    <p class="text-gray-600 text-sm">{{ $sale->customer->phone }}</p>
                                @endif
                            @else
                                <p class="text-gray-900">Walk-in Customer</p>
                            @endif
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-2">Cashier</h3>
                            <p class="text-gray-900">{{ $sale->user->name }}</p>
                            <p class="text-gray-600 text-sm">{{ $sale->cashRegister->name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="mb-6">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-300">
                                <th class="text-left py-2 text-sm font-semibold text-gray-700">Item</th>
                                <th class="text-center py-2 text-sm font-semibold text-gray-700">Qty</th>
                                <th class="text-right py-2 text-sm font-semibold text-gray-700">Price</th>
                                <th class="text-right py-2 text-sm font-semibold text-gray-700">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->items as $item)
                                <tr class="border-b border-gray-100">
                                    <td class="py-3">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $item->product_name }}</p>
                                            @if($item->product_sku)
                                                <p class="text-xs text-gray-500">SKU: {{ $item->product_sku }}</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center py-3 text-gray-900">{{ number_format($item->quantity, 2) }}</td>
                                    <td class="text-right py-3 text-gray-900">₦{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-right py-3 font-semibold text-gray-900">₦{{ number_format($item->line_total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="border-t border-gray-300 pt-4">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="text-gray-900">₦{{ number_format($sale->subtotal, 2) }}</span>
                        </div>

                        @if($sale->discount_amount > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Discount:</span>
                                <span class="text-red-600">-₦{{ number_format($sale->discount_amount, 2) }}</span>
                            </div>
                        @endif

                        @if($sale->tax_amount > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax:</span>
                                <span class="text-gray-900">₦{{ number_format($sale->tax_amount, 2) }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between text-lg font-bold border-t pt-2">
                            <span>Total:</span>
                            <span>₦{{ number_format($sale->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <h3 class="font-semibold text-gray-700 mb-3">Payment Details</h3>
                    @foreach($sale->payments as $payment)
                        <div class="flex justify-between py-1">
                            <span class="text-gray-600">{{ $payment->paymentMethod->name }}:</span>
                            <div class="text-right">
                                <span class="text-gray-900">₦{{ number_format($payment->amount, 2) }}</span>
                                @if($payment->reference_number)
                                    <p class="text-xs text-gray-500">Ref: {{ $payment->reference_number }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <div class="flex justify-between font-semibold mt-2 pt-2 border-t">
                        <span>Amount Paid:</span>
                        <span>₦{{ number_format($sale->paid_amount, 2) }}</span>
                    </div>

                    @if($sale->change_amount > 0)
                        <div class="flex justify-between font-semibold text-green-600">
                            <span>Change:</span>
                            <span>₦{{ number_format($sale->change_amount, 2) }}</span>
                        </div>
                    @endif
                </div>

                <!-- Footer -->
                <div class="mt-8 pt-6 border-t border-gray-200 text-center">
                    <p class="text-gray-600 text-sm mb-2">Thank you for your business!</p>
                    <p class="text-xs text-gray-500">{{ $sale->created_at->format('Y-m-d H:i:s') }}</p>
                    @if($sale->notes)
                        <p class="text-xs text-gray-500 mt-2">{{ $sale->notes }}</p>
                    @endif
                </div>

                <!-- Barcode/QR Code placeholder -->
                <div class="mt-4 text-center">
                    <div class="inline-block bg-gray-100 px-4 py-2 rounded">
                        <span class="font-mono text-xs">{{ $sale->sale_number }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions for non-print view -->
        <div class="mt-6 flex justify-center space-x-4 print:hidden">
            <button onclick="duplicateTransaction()"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg">
                Duplicate Transaction
            </button>
            <button onclick="refundTransaction()"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg">
                Process Refund
            </button>
        </div>
    </div>
</div>

<script>
function emailReceipt() {
    // Implementation for emailing receipt
    @if($sale->customer && $sale->customer->email)
        if (confirm('Send receipt to {{ $sale->customer->email }}?')) {
            // Make AJAX call to email receipt
            fetch(`{{ route('tenant.pos.email-receipt', ['tenant' => $tenant->slug, 'sale' => $sale->id]) }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Receipt emailed successfully!');
                } else {
                    alert('Error sending email: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error sending email. Please try again.');
            });
        }
    @else
        alert('No customer email address available.');
    @endif
}

function duplicateTransaction() {
    if (confirm('Create a new transaction with the same items?')) {
        // Redirect to POS with items pre-filled
        window.location.href = `{{ route('tenant.pos.index', ['tenant' => $tenant->slug]) }}?duplicate={{ $sale->id }}`;
    }
}

function refundTransaction() {
    if (confirm('Process a refund for this transaction?')) {
        // Redirect to refund page


        window.location.href = `{{ route('tenant.pos.refund', ['tenant' => $tenant->slug, 'sale' => $sale->id]) }}`;
    }
}

// Print styles
const printStyles = `
    @media print {
        body * {
            visibility: hidden;
        }
        #receipt, #receipt * {
            visibility: visible;
        }
        #receipt {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            box-shadow: none;
            border: none;
        }
        .print\\:hidden {
            display: none !important;
        }
    }
`;

// Add print styles to document
const styleSheet = document.createElement("style");
styleSheet.type = "text/css";
styleSheet.innerText = printStyles;
document.head.appendChild(styleSheet);
</script>
@endsection
