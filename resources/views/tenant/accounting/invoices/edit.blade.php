@extends('layouts.tenant')

@section('title', 'Edit Invoice - ' . $tenant->name)
@section('page-title', "Edit Invoice #{{ $invoice->voucher_number }}")
@section('page-description')
  <span class="hidden md:inline">
  Update invoice information
  </span>
@endsection

@section('content')
<div class="space-y-6" x-data="invoiceForm()">
    <!-- Header -->
    <div class="flex flex-col space-y-3 md:flex-row md:items-center md:justify-between md:space-y-0">
        <div class="flex items-center space-x-3">
            <a href="{{ route('tenant.accounting.invoices.show', ['tenant' => $tenant->slug, 'invoice' => $invoice->id]) }}"
               class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Invoice
            </a>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('tenant.accounting.invoices.update', ['tenant' => $tenant->slug, 'invoice' => $invoice->id]) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Invoice Header -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">📋 Invoice Details</h3>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Voucher Type -->
                    <div>
                        <label for="voucher_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Voucher Type <span class="text-red-500">*</span>
                        </label>
                        <select name="voucher_type_id"
                                id="voucher_type_id"
                                required
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('voucher_type_id') border-red-300 @enderror">
                            @foreach($voucherTypes as $type)
                                <option value="{{ $type->id }}" {{ $invoice->voucher_type_id == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('voucher_type_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Voucher Number -->
                    <div>
                        <label for="voucher_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Invoice Number
                        </label>
                        <input type="text"
                               name="voucher_number"
                               id="voucher_number"
                               value="{{ $invoice->voucher_number }}"
                               readonly
                               class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-500 cursor-not-allowed">
                    </div>

                    <!-- Invoice Date -->
                    <div>
                        <label for="voucher_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Invoice Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               name="voucher_date"
                               id="voucher_date"
                               value="{{ $invoice->voucher_date ? $invoice->voucher_date->format('Y-m-d') : date('Y-m-d') }}"
                               required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('voucher_date') border-red-300 @enderror">
                        @error('voucher_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Customer/Vendor Selection -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Customer/Vendor <span class="text-red-500">*</span>
                    </label>
                    <select name="customer_id"
                            id="customer_id"
                            required
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('customer_id') border-red-300 @enderror">
                        <option value="">-- Select Customer/Vendor --</option>
                        @foreach($customers as $customer)
                            @php
                                $customerLedger = $invoice->entries->firstWhere('ledger_account_id', $customer->id);
                                $isSelected = $customerLedger !== null;
                            @endphp
                            <option value="{{ $customer->id }}" {{ $isSelected ? 'selected' : '' }}>
                                {{ $customer->name ?? $customer->company_name ?? 'Unnamed' }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div class="mt-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Notes
                    </label>
                    <textarea name="notes"
                              id="notes"
                              rows="3"
                              placeholder="Add any additional notes or terms..."
                              class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500">{{ $invoice->notes }}</textarea>
                </div>
            </div>
        </div>

        <!-- Inventory Items Section -->
        @include('tenant.accounting.invoices.partials.invoice-items-edit')

        <!-- Submit Buttons -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="p-6">
                <div class="flex flex-col sm:flex-row gap-3 justify-end">
                    <a href="{{ route('tenant.accounting.invoices.show', ['tenant' => $tenant->slug, 'invoice' => $invoice->id]) }}"
                       class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Cancel
                    </a>

                    <button type="submit"
                            name="action"
                            value="save_draft"
                            class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Update Invoice
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Main Invoice Form Component
function invoiceForm() {
    return {
        init() {
            console.log('Invoice edit form initialized');
        }
    }
}
</script>
@endpush
@endsection
