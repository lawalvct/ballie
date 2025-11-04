@extends('layouts.tenant')

@section('title', 'Edit Invoice')
@section('page-title', 'Edit Invoice')
@section('page-description', 'Update invoice information.')

@section('content')
<div class="space-y-6">
    <form action="{{ route('tenant.accounting.invoices.update', ['tenant' => tenant()->slug, 'invoice' => $invoice->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Invoice Header -->
        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Invoice Information</h3>

                    <div class="space-y-4">
                        <div>
                            <label for="voucher_number" class="block text-sm font-medium text-gray-700">Invoice Number</label>
                            <input type="text" name="voucher_number" id="voucher_number" value="{{ $invoice->voucher_number }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" readonly>
                        </div>

                        <div>
                            <label for="voucher_date" class="block text-sm font-medium text-gray-700">Invoice Date</label>
                            <input type="date" name="voucher_date" id="voucher_date" value="{{ $invoice->voucher_date ? $invoice->voucher_date->format('Y-m-d') : '' }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Customer Information</h3>

                    <div class="space-y-4">
                        <div>
                            <label for="customer_id" class="block text-sm font-medium text-gray-700">Select Customer</label>
                            <select name="customer_id" id="customer_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">-- Select Customer --</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ $invoice->customer_id == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Items -->
        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Invoice Items</h3>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="invoice-items-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Item
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Quantity
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Unit Price
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="invoice-items-body">
                        @foreach($inventoryItems as $index => $item)
                            <tr class="invoice-item">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <select name="inventory_items[{{ $index }}][product_id]" class="product-select block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="">-- Select Product --</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-description="{{ $product->description }}" {{ isset($item['product_id']) && $item['product_id'] == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="text" name="inventory_items[{{ $index }}][description]" class="item-description block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="{{ $item['description'] ?? '' }}">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="number" name="inventory_items[{{ $index }}][quantity]" class="item-quantity block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="{{ $item['quantity'] ?? 0 }}" min="0.01" step="0.01">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="number" name="inventory_items[{{ $index }}][rate]" class="item-price block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" step="0.01" value="{{ $item['rate'] ?? 0 }}">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="number" name="inventory_items[{{ $index }}][total]" class="item-total block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" readonly value="{{ isset($item['quantity']) && isset($item['rate']) ? ($item['quantity'] * $item['rate']) : 0 }}">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button type="button" class="remove-item text-red-600 hover:text-red-900">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <button type="button" id="add-item" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Item
                </button>
            </div>
        </div>

        <!-- Invoice Totals -->
        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" id="notes" rows="4" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Thank you for your business!">{{ $invoice->notes }}</textarea>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Invoice Summary</h3>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        @php
                            $subtotal = $inventoryItems->sum(function($item) {
                                return ($item['quantity'] ?? 0) * ($item['rate'] ?? 0);
                            });
                            $tax = $subtotal * 0.075;
                            $total = $subtotal + $tax;
                        @endphp

                        <div class="flex justify-between py-2 border-b border-gray-200">
                            <span class="text-sm text-gray-500">Subtotal:</span>
                            <span class="text-sm font-medium text-gray-900" id="subtotal">₦{{ number_format($subtotal, 2) }}</span>
                        </div>

                        <div class="flex justify-between py-2 border-b border-gray-200">
                            <span class="text-sm text-gray-500">Tax (7.5%):</span>
                            <span class="text-sm font-medium text-gray-900" id="tax">₦{{ number_format($tax, 2) }}</span>
                        </div>

                        <div class="flex justify-between py-2 font-bold">
                            <span class="text-base text-gray-900">Total:</span>
                            <span class="text-base text-blue-600" id="total">₦{{ number_format($total, 2) }}</span>
                            <input type="hidden" name="total_amount" id="total_amount" value="{{ $total }}">
                        </div>
                    </div>

                    @if($invoice->status != 'draft')
                    <div class="mt-4">
                        <label for="status" class="block text-sm font-medium text-gray-700">Invoice Status</label>
                        <select name="status" id="status" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="draft" {{ $invoice->status == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="pending" {{ $invoice->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="sent" {{ $invoice->status == 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="paid" {{ $invoice->status == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="overdue" {{ $invoice->status == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        </select>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('tenant.accounting.invoices.show', ['tenant' => tenant()->slug, 'invoice' => $invoice->id]) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancel
            </a>

            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Update Invoice
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize variables
        let itemCount = {{ $inventoryItems->count() }};
        const taxRate = 0.075; // 7.5%

        // Function to update item total
        function updateItemTotal(row) {
            const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const total = quantity * price;

            row.querySelector('.item-total').value = total.toFixed(2);
            updateInvoiceTotal();
        }

        // Function to update invoice total
        function updateInvoiceTotal() {
            let subtotal = 0;
            document.querySelectorAll('.item-total').forEach(function(input) {
                subtotal += parseFloat(input.value) || 0;
            });

            const tax = subtotal * taxRate;
            const total = subtotal + tax;

            document.getElementById('subtotal').textContent = '₦' + subtotal.toFixed(2);
            document.getElementById('tax').textContent = '₦' + tax.toFixed(2);
            document.getElementById('total').textContent = '₦' + total.toFixed(2);
            document.getElementById('total_amount').value = total.toFixed(2);
        }

        // Function to add new item row
        document.getElementById('add-item').addEventListener('click', function() {
            const tbody = document.getElementById('invoice-items-body');
            const template = document.querySelector('.invoice-item').cloneNode(true);

            // Update the name attributes with the new index
            template.querySelectorAll('input, select').forEach(function(input) {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/\[\d+\]/, '[' + itemCount + ']'));
                }

                // Clear values except quantity
                if (!input.classList.contains('item-quantity')) {
                    input.value = '';
                } else {
                    input.value = '1';
                }
            });

            tbody.appendChild(template);
            itemCount++;

            // Add event listeners to the new row
            addRowEventListeners(template);
        });

        // Function to add event listeners to a row
        function addRowEventListeners(row) {
            // Product select change
            row.querySelector('.product-select').addEventListener('change', function() {
                const option = this.options[this.selectedIndex];
                const price = option.getAttribute('data-price');
                const description = option.getAttribute('data-description');

                row.querySelector('.item-price').value = price || '';
                row.querySelector('.item-description').value = description || '';
                updateItemTotal(row);
            });

            // Quantity change
            row.querySelector('.item-quantity').addEventListener('input', function() {
                updateItemTotal(row);
            });

            // Price change
            row.querySelector('.item-price').addEventListener('input', function() {
                updateItemTotal(row);
            });

            // Remove item
            row.querySelector('.remove-item').addEventListener('click', function() {
                if (document.querySelectorAll('.invoice-item').length > 1) {
                    row.remove();
                    updateInvoiceTotal();
                } else {
                    alert('You must have at least one item in the invoice.');
                }
            });
        }

        // Add event listeners to the initial rows
        document.querySelectorAll('.invoice-item').forEach(function(row) {
            addRowEventListeners(row);
        });

        // Initialize totals
        updateInvoiceTotal();
    });
</script>
@endsection
