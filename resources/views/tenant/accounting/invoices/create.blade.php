@extends('layouts.tenant')

@section('title', 'Create Invoice - ' . $tenant->name)
@section('page-title', "Create Invoice")
@section('page-description')
  <span class="hidden md:inline">
  Create a new invoice with inventory management
  </span>
@endsection

@section('content')
<div class="space-y-6" x-data="invoiceForm()">
    <!-- Header -->
    <div class="flex flex-col space-y-3 md:flex-row md:items-center md:justify-between md:space-y-0">
        <div class="grid grid-cols-2 md:flex md:flex-wrap gap-2">
           <!-- Common Voucher Type Buttons -->
            <a href="{{ route('tenant.accounting.invoices.create', ['tenant' => $tenant->slug, 'type' => 'sv']) }}"
               class="inline-flex items-center justify-center px-2 py-2 md:px-4 border border-blue-200 rounded-lg shadow-sm text-xs md:text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                <svg class="w-3 h-3 md:w-4 md:h-4 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
               <span class="ml-1 md:ml-0">Sales</span>
            </a>

            <a href="{{ route('tenant.accounting.invoices.create', ['tenant' => $tenant->slug, 'type' => 'pur']) }}"
               class="inline-flex items-center justify-center px-2 py-2 md:px-4 border border-red-200 rounded-lg shadow-sm text-xs md:text-sm font-medium text-white bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                <svg class="w-3 h-3 md:w-4 md:h-4 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span class="ml-1 md:ml-0">Purchase</span>
            </a>

            <a href="{{ route('tenant.accounting.invoices.create', ['tenant' => $tenant->slug, 'type' => 'sr']) }}"
               class="inline-flex items-center justify-center px-2 py-2 md:px-4 border border-green-200 rounded-lg shadow-sm text-xs md:text-sm font-medium text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                <svg class="w-3 h-3 md:w-4 md:h-4 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8h6m-5 0a3 3 0 110 6H9l3 3m-3-6h6m6 1a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="ml-1 md:ml-0">S-Return</span>
            </a>

            <a href="{{ route('tenant.accounting.invoices.create', ['tenant' => $tenant->slug, 'type' => 'pr']) }}"
               class="inline-flex items-center justify-center px-2 py-2 md:px-4 border border-purple-200 rounded-lg shadow-sm text-xs md:text-sm font-medium text-white bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                <svg class="w-3 h-3 md:w-4 md:h-4 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
                <span class="ml-1 md:ml-0">P-Return</span>
            </a>
        </div>
        <div class="flex items-center space-x-2 md:space-x-3">
            <a href="{{ route('tenant.accounting.invoices.index', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-3 py-2 md:px-4 border border-gray-300 rounded-lg shadow-sm text-xs md:text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
              <svg class="w-4 h-4 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
              </svg>
              <span class="hidden md:inline">Back</span>
            </a>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('tenant.accounting.invoices.store', ['tenant' => $tenant->slug]) }}" class="space-y-6">
        @csrf

        <!-- Invoice Header -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Invoice Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Voucher Type -->
                    <div>
                        <label for="voucher_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Invoice Type <span class="text-red-500">*</span>
                        </label>
                        <select name="voucher_type_id"
                                id="voucher_type_id"
                                x-model="voucherTypeId"
                                @change="updateVoucherType()"
                                class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 rounded-lg @error('voucher_type_id') border-red-300 @enderror"
                                required>
                            <option value="">Select Invoice Type</option>
                                @php
                                    $defaultVoucherTypeId = old('voucher_type_id', $selectedType?->id ?? null);

                                    // Check URL parameter for type selection
                                    $urlType = request()->get('type');
                                    if ($urlType && strtolower($urlType) === 'pur' && !$defaultVoucherTypeId) {
                                        // Find purchase voucher type
                                        $purchaseVoucher = $voucherTypes->first(function($t) {
                                            return stripos($t->code, 'pur') !== false ||
                                                   stripos($t->code, 'purchase') !== false ||
                                                   stripos($t->name, 'purchase') !== false;
                                        });
                                        if ($purchaseVoucher) {
                                            $defaultVoucherTypeId = $purchaseVoucher->id;
                                        }
                                    }

                                    // Fallback to sales voucher if no type is selected
                                    if (!$defaultVoucherTypeId) {
                                        $salesVoucher = $voucherTypes->first(function($t) { return stripos($t->name, 'sales') !== false; });
                                        if ($salesVoucher) {
                                            $defaultVoucherTypeId = $salesVoucher->id;
                                        }
                                    }
                                @endphp
                                @foreach($voucherTypes as $type)
                                    <option value="{{ $type->id }}" {{ ($defaultVoucherTypeId == $type->id) ? 'selected' : '' }}>
                                        {{ $type->name }} ({{ $type->code }})
                                    </option>
                                @endforeach
                        </select>
                        @error('voucher_type_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Invoice Date -->
                    <div>
                        <label for="voucher_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Invoice Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               name="voucher_date"
                               id="voucher_date"
                               value="{{ old('voucher_date', date('Y-m-d')) }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('voucher_date') border-red-300 @enderror"
                               required>
                        @error('voucher_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Reference Number -->
                    <div>
                        <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Reference Number
                        </label>
                        <input type="text"
                               name="reference_number"
                               id="reference_number"
                               value="{{ old('reference_number') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('reference_number') border-red-300 @enderror"
                               placeholder="Optional reference">
                        @error('reference_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Invoice Number Preview -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Invoice Number
                        </label>
                        <div class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-500">
                            <span x-text="invoiceNumberPreview"></span>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Auto-generated on save</p>
                    </div>
                </div>

                <!-- Customer/Vendor Information -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Customer (for Sales transactions) -->
                    <div id="customerSection">
                        <label for="customer_search" class="block text-sm font-medium text-gray-700 mb-2">
                            Customer <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <div class="relative flex-1" x-data="customerSearch()">
                                <input type="text"
                                       x-model="searchTerm"
                                       @input="searchCustomers()"
                                       @focus="showDropdown = true"
                                       placeholder="Type to search customers..."
                                       class="w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 rounded-lg">
                                <input type="hidden" name="customer_id" x-model="selectedCustomerId" required>

                                <!-- Dropdown -->
                                <div x-show="showDropdown && (customers.length > 0 || loading)"
                                     x-transition
                                     class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">

                                    <!-- Loading -->
                                    <div x-show="loading" class="px-3 py-2 text-gray-500">
                                        Searching...
                                    </div>

                                    <!-- Results -->
                                    <template x-for="customer in customers" :key="customer.id">
                                        <div @click="selectCustomer(customer)"
                                             class="px-3 py-2 cursor-pointer hover:bg-gray-100 border-b border-gray-100 last:border-b-0">
                                            <div class="font-medium text-gray-900" x-text="customer.display_name"></div>
                                            <div class="text-sm text-gray-500" x-text="customer.email || 'No email'"></div>
                                        </div>
                                    </template>

                                    <!-- No results -->
                                    <div x-show="!loading && customers.length === 0 && searchTerm.length >= 2"
                                         class="px-3 py-2 text-gray-500">
                                        No customers found
                                    </div>
                                </div>
                            </div>
                            <button type="button"
                                    onclick="openQuickAddModal('customer')"
                                    class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                                    title="Quick Add Customer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Vendor (for Purchase transactions) -->
                    <div id="vendorSection" class="hidden">
                        <label for="vendor_select" class="block text-sm font-medium text-gray-700 mb-2">
                            Vendor <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <select name="customer_id"
                                    id="vendor_select"
                                    disabled
                                    class="flex-1 pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 rounded-lg">
                                <option value="">Select Vendor</option>
                                @if(isset($vendors))
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->ledgerAccount->id }}" {{ old('customer_id') == $vendor->ledgerAccount->id ? 'selected' : '' }}>
                                            {{ $vendor->display_name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <button type="button"
                                    onclick="openQuickAddModal('vendor')"
                                    class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors"
                                    title="Quick Add Vendor">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="mt-6 grid grid-cols-1 gap-6">
                    <!-- Narration -->
                    <div>
                        <label for="narration" class="block text-sm font-medium text-gray-700 mb-2">
                            Description/Notes
                        </label>
                        <textarea name="narration"
                                  id="narration"
                                  rows="1"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('narration') border-red-300 @enderror"
                                  placeholder="Invoice description or notes">{{ old('narration') }}</textarea>
                        @error('narration')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Items Section -->
        @include('tenant.accounting.invoices.partials.invoice-items')

        <!-- Hidden VAT Inputs -->
        <input type="hidden" name="vat_enabled" x-model="vatEnabled" value="0">
        <input type="hidden" name="vat_amount" x-bind:value="vatAmount.toFixed(2)">
        <input type="hidden" name="vat_applies_to" x-model="vatAppliesTo">

        <!-- Submit Buttons -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-4 md:px-6 py-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0">
                    <div class="text-xs md:text-sm text-gray-600">
                        <span class="font-medium">Total: </span>
                        <span class="text-base md:text-lg font-bold text-gray-900">₦<span x-text="formatNumber(totalAmount)">0.00</span></span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
                        <button type="button"
                                onclick="window.history.back()"
                                class="inline-flex items-center justify-center px-3 md:px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-xs md:text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancel
                        </button>
                        <button type="submit"
                                name="action"
                                value="save_draft"
                                class="inline-flex items-center justify-center px-3 md:px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-xs md:text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            <svg class="w-3 h-3 md:w-4 md:h-4 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <span class="md:ml-0 ml-1">Save Draft</span>
                        </button>
                        <button type="submit"
                                name="action"
                                value="save_and_post"
                                class="inline-flex items-center justify-center px-3 md:px-4 py-2 bg-primary-600 border border-transparent rounded-lg shadow-sm text-xs md:text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <svg class="w-3 h-3 md:w-4 md:h-4 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="md:ml-0 ml-1">Save & Post</span>
                            Save & Post Invoice
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Quick Add Customer/Vendor Modal -->
<div id="quickAddModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="quickAddForm" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                                Quick Add
                            </h3>

                            <!-- CRM Type Selection (Customer or Vendor) -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Create</label>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input type="radio" name="crm_type" value="customer" checked class="mr-2" onchange="updateCrmType()">
                                        <span class="text-sm">Customer</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="crm_type" value="vendor" class="mr-2" onchange="updateCrmType()">
                                        <span class="text-sm">Vendor</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Entity Type Selection (Individual or Business) -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input type="radio" name="entity_type" value="individual" checked class="mr-2" onchange="toggleTypeFields()">
                                        <span class="text-sm">Individual</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="entity_type" value="business" class="mr-2" onchange="toggleTypeFields()">
                                        <span class="text-sm">Business</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Individual Fields -->
                            <div id="individualFields" class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                                        <input type="text" name="first_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                                        <input type="text" name="last_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Business Fields -->
                            <div id="businessFields" class="space-y-4 hidden">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Company Name *</label>
                                    <input type="text" name="company_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            <!-- Common Fields -->
                            <div class="space-y-4 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                    <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                    <input type="tel" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                    <input type="text" name="address_line1" placeholder="Street address" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <input type="text" name="city" placeholder="City" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <input type="text" name="state" placeholder="State" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Opening Balance Section -->
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex items-center mb-3">
                                    <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <h4 class="text-sm font-medium text-gray-900">Opening Balance (Optional)</h4>
                                </div>
                                <div class="space-y-3">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Amount</label>
                                            <div class="relative">
                                                <span class="absolute left-3 top-2.5 text-gray-500 text-sm">₦</span>
                                                <input type="number"
                                                       name="opening_balance_amount"
                                                       id="opening_balance_amount"
                                                       step="0.01"
                                                       min="0"
                                                       value="0.00"
                                                       class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Balance Type</label>
                                            <select name="opening_balance_type"
                                                    id="opening_balance_type"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                                <option value="none">No Balance</option>
                                                <option value="debit" id="debitOption">Debit (Owes Us)</option>
                                                <option value="credit" id="creditOption">Credit (We Owe)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">As of Date</label>
                                        <input type="date"
                                               name="opening_balance_date"
                                               id="opening_balance_date"
                                               value="{{ date('Y-m-d') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                    </div>
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-2">
                                        <p class="text-xs text-blue-800" id="balanceTypeHelp">
                                            Set an opening balance if migrating from another system.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" id="submitBtn" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Create & Select Customer
                    </button>
                    <button type="button" onclick="closeQuickAddModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Add Product Modal -->
<div id="quickAddProductModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeQuickAddProduct()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="quickAddProductForm" onsubmit="event.preventDefault(); submitQuickAddProduct();">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                                Quick Add Product
                            </h3>

                            <div class="space-y-4">
                                <!-- Product Type -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Product Type</label>
                                    <div class="flex space-x-4">
                                        <label class="flex items-center">
                                            <input type="radio" name="type" value="item" checked class="mr-2" onchange="toggleQuickProductType()">
                                            <span class="text-sm">Item</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="type" value="service" class="mr-2" onchange="toggleQuickProductType()">
                                            <span class="text-sm">Service</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Product Name -->
                                <div>
                                    <label for="quick_product_name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Product Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="name" id="quick_product_name" required
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 text-sm"
                                           placeholder="Enter product name">
                                </div>

                                <!-- SKU -->
                                <div>
                                    <label for="quick_product_sku" class="block text-sm font-medium text-gray-700 mb-1">
                                        SKU (Optional)
                                    </label>
                                    <input type="text" name="sku" id="quick_product_sku"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 text-sm"
                                           placeholder="Product code">
                                </div>

                                <!-- Sales Rate -->
                                <div>
                                    <label for="quick_sales_rate" class="block text-sm font-medium text-gray-700 mb-1">
                                        Sales Rate <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="sales_rate" id="quick_sales_rate" required step="0.01" min="0"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 text-sm"
                                           placeholder="0.00">
                                </div>

                                <!-- Purchase Rate -->
                                <div>
                                    <label for="quick_purchase_rate" class="block text-sm font-medium text-gray-700 mb-1">
                                        Purchase Rate <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="purchase_rate" id="quick_purchase_rate" required step="0.01" min="0"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 text-sm"
                                           placeholder="0.00">
                                </div>

                                <!-- Unit (for items only) -->
                                <div id="quick_unit_section">
                                    <label for="quick_unit" class="block text-sm font-medium text-gray-700 mb-1">
                                        Unit <span class="text-red-500">*</span>
                                    </label>
                                    <select name="primary_unit_id" id="quick_unit" required
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 text-sm">
                                        <option value="">Select Unit</option>
                                        @if(isset($units))
                                            @foreach($units as $unit)
                                                <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->abbreviation }})</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <!-- Opening Stock (for items only) -->
                                <div id="quick_stock_section">
                                    <label for="quick_opening_stock" class="block text-sm font-medium text-gray-700 mb-1">
                                        Opening Stock
                                    </label>
                                    <input type="number" name="opening_stock" id="quick_opening_stock" step="0.01" min="0" value="0"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 text-sm"
                                           placeholder="0.00">
                                </div>

                                <!-- Hidden fields -->
                                <input type="hidden" name="maintain_stock" value="1">
                                <input type="hidden" name="is_active" value="1">
                                <input type="hidden" name="is_saleable" value="1">
                                <input type="hidden" name="is_purchasable" value="1">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <span id="quick-product-submit-text">Create Product</span>
                        <svg id="quick-product-submit-loading" class="hidden animate-spin ml-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                    <button type="button"
                            onclick="closeQuickAddProduct()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentModalType = 'customer';
let currentProductRowIndex = null;

// Quick Add Modal Functions
function openQuickAddModal(type = 'customer') {
    currentModalType = type;
    const modal = document.getElementById('quickAddModal');
    const form = document.getElementById('quickAddForm');

    // Reset form first
    form.reset();

    // Set the CRM type radio button
    const crmTypeRadio = document.querySelector(`input[name="crm_type"][value="${type}"]`);
    if (crmTypeRadio) {
        crmTypeRadio.checked = true;
    }

    // Ensure individual is selected by default
    const individualRadio = document.querySelector('input[value="individual"]');
    if (individualRadio) {
        individualRadio.checked = true;
    }

    // Update modal state
    updateCrmType();
    toggleTypeFields();

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}function updateCrmType() {
    const selectedCrmType = document.querySelector('input[name="crm_type"]:checked').value;
    currentModalType = selectedCrmType;

    const modalTitle = document.getElementById('modal-title');
    const form = document.getElementById('quickAddForm');
    const submitBtn = document.getElementById('submitBtn');

    const crmTypeCap = selectedCrmType.charAt(0).toUpperCase() + selectedCrmType.slice(1);

    modalTitle.textContent = `Quick Add ${crmTypeCap}`;
    submitBtn.textContent = `Create & Select ${crmTypeCap}`;

    form.action = selectedCrmType === 'customer'
        ? '{{ route("tenant.crm.customers.store", ["tenant" => $tenant->slug]) }}'
        : '{{ route("tenant.crm.vendors.store", ["tenant" => $tenant->slug]) }}';

    // Update opening balance help text and default type
    updateOpeningBalanceLabels(selectedCrmType);
}

function closeQuickAddModal() {
    const modal = document.getElementById('quickAddModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';

    // Reset form and restore default state
    const form = document.getElementById('quickAddForm');
    form.reset();

    // Reset to default customer and individual
    document.querySelector('input[name="crm_type"][value="customer"]').checked = true;
    document.querySelector('input[value="individual"]').checked = true;

    // Reset field visibility
    toggleTypeFields();
}

function toggleTypeFields() {
    const individualFields = document.getElementById('individualFields');
    const businessFields = document.getElementById('businessFields');

    // Get the selected type by checking all radio buttons with value 'individual' or 'business'
    let selectedType = 'individual'; // default
    const allRadios = document.querySelectorAll('input[type="radio"]');

    for (let radio of allRadios) {
        if (radio.checked && (radio.value === 'individual' || radio.value === 'business')) {
            selectedType = radio.value;
            break;
        }
    }

    console.log('Selected type:', selectedType); // Debug log

    if (selectedType === 'individual') {
        individualFields.classList.remove('hidden');
        businessFields.classList.add('hidden');
        // Make individual fields required
        const firstNameField = document.querySelector('input[name="first_name"]');
        const lastNameField = document.querySelector('input[name="last_name"]');
        const companyNameField = document.querySelector('input[name="company_name"]');

        if (firstNameField) firstNameField.required = true;
        if (lastNameField) lastNameField.required = true;
        if (companyNameField) companyNameField.required = false;
    } else {
        individualFields.classList.add('hidden');
        businessFields.classList.remove('hidden');
        // Make business fields required
        const firstNameField = document.querySelector('input[name="first_name"]');
        const lastNameField = document.querySelector('input[name="last_name"]');
        const companyNameField = document.querySelector('input[name="company_name"]');

        if (firstNameField) firstNameField.required = false;
        if (lastNameField) lastNameField.required = false;
        if (companyNameField) companyNameField.required = true;
    }

    // Update the radio button names after toggling (for backend processing)
    const selectedCrmType = document.querySelector('input[name="crm_type"]:checked')?.value || 'customer';
    const typeFieldName = selectedCrmType + '_type';
    const entityTypeRadios = document.querySelectorAll('input[value="individual"], input[value="business"]');

    entityTypeRadios.forEach(radio => {
        if (radio.value === 'individual' || radio.value === 'business') {
            radio.name = typeFieldName;
        }
    });
}

// Handle form submission
document.getElementById('quickAddForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;

    submitButton.disabled = true;
    submitButton.textContent = 'Creating...';

    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Determine which select element to update based on current visibility
            const customerSection = document.getElementById('customerSection');
            const vendorSection = document.getElementById('vendorSection');

            let targetSelect;
            if (!customerSection.classList.contains('hidden')) {
                // Customer section is visible
                targetSelect = document.getElementById('customer_id');
            } else if (!vendorSection.classList.contains('hidden')) {
                // Vendor section is visible
                targetSelect = document.getElementById('vendor_select');
            }

            if (targetSelect) {
                const option = new Option(data.display_name, data.ledger_account_id, true, true);
                targetSelect.add(option);
            }

            // Show success message
            showNotification('success', `${currentModalType.charAt(0).toUpperCase() + currentModalType.slice(1)} created successfully!`);

            closeQuickAddModal();
        } else {
            throw new Error(data.message || 'An error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', error.message || 'Failed to create ' + currentModalType);
    })
    .finally(() => {
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    });
});

// Add event listeners for type toggle
document.addEventListener('DOMContentLoaded', function() {
    // Close modal when clicking outside
    document.getElementById('quickAddModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeQuickAddModal();
        }
    });

    // Add event listeners for type radio buttons
    document.addEventListener('change', function(e) {
        // Handle entity type changes (individual/business)
        if (e.target.type === 'radio' && (e.target.value === 'individual' || e.target.value === 'business')) {
            toggleTypeFields();
        }
        // Handle CRM type changes (customer/vendor)
        if (e.target.name === 'crm_type') {
            updateCrmType();
        }
    });

    // Opening balance amount change handler
    const openingBalanceAmount = document.getElementById('opening_balance_amount');
    const openingBalanceType = document.getElementById('opening_balance_type');

    if (openingBalanceAmount) {
        openingBalanceAmount.addEventListener('input', function() {
            if (parseFloat(this.value) > 0 && openingBalanceType.value === 'none') {
                // Auto-select appropriate balance type based on CRM type
                const crmType = document.querySelector('input[name="crm_type"]:checked')?.value || 'customer';
                openingBalanceType.value = crmType === 'customer' ? 'debit' : 'credit';
                updateBalanceTypeHelp();
            } else if (parseFloat(this.value) === 0 || !this.value) {
                openingBalanceType.value = 'none';
                updateBalanceTypeHelp();
            }
        });
    }

    if (openingBalanceType) {
        openingBalanceType.addEventListener('change', function() {
            if (this.value === 'none') {
                openingBalanceAmount.value = '0.00';
            }
            updateBalanceTypeHelp();
        });
    }
});

// Update opening balance labels based on CRM type
function updateOpeningBalanceLabels(crmType) {
    const debitOption = document.getElementById('debitOption');
    const creditOption = document.getElementById('creditOption');

    if (crmType === 'customer') {
        debitOption.textContent = 'Debit (Customer Owes)';
        creditOption.textContent = 'Credit (We Owe Customer)';
    } else {
        debitOption.textContent = 'Debit (Vendor Owes)';
        creditOption.textContent = 'Credit (We Owe Vendor)';
    }

    updateBalanceTypeHelp();
}

// Update help text based on selected balance type
function updateBalanceTypeHelp() {
    const balanceType = document.getElementById('opening_balance_type')?.value;
    const crmType = document.querySelector('input[name="crm_type"]:checked')?.value || 'customer';
    const helpText = document.getElementById('balanceTypeHelp');

    if (!helpText) return;

    if (balanceType === 'none') {
        helpText.textContent = 'Set an opening balance if migrating from another system.';
    } else if (balanceType === 'debit') {
        if (crmType === 'customer') {
            helpText.textContent = 'Customer owes you money (Accounts Receivable).';
        } else {
            helpText.textContent = 'Vendor owes you money (advance payment/prepayment).';
        }
    } else if (balanceType === 'credit') {
        if (crmType === 'customer') {
            helpText.textContent = 'You owe customer money (overpayment/credit memo).';
        } else {
            helpText.textContent = 'You owe vendor money (Accounts Payable).';
        }
    }
}

// Notification function
function showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Invoice Items Component
window.invoiceItems = function() {
    return {
        items: [
            {
                product_id: '',
                product_name: '',
                description: '',
                quantity: 1,
                rate: 0,
                amount: 0,
                purchase_rate: 0,
                current_stock: null,
                unit: 'Pcs'
            }
        ],
        ledgerAccounts: [],
        vatEnabled: false,
        vatRate: 0.075, // 7.5%
        vatAppliesTo: 'items_only', // 'items_only' or 'items_and_charges'
        _updateTimeout: null,

        get totalAmount() {
            return this.items.reduce((sum, item) => {
                return sum + (parseFloat(item.amount) || 0);
            }, 0);
        },

        get ledgerAccountsTotal() {
            return this.ledgerAccounts.reduce((sum, ledger) => {
                return sum + (parseFloat(ledger.amount) || 0);
            }, 0);
        },

        get vatAmount() {
            if (!this.vatEnabled) return 0;
            // VAT calculation based on user choice
            if (this.vatAppliesTo === 'items_only') {
                // VAT only on products
                return this.totalAmount * this.vatRate;
            } else {
                // VAT on products + additional charges
                return (this.totalAmount + this.ledgerAccountsTotal) * this.vatRate;
            }
        },

        get grandTotal() {
            return this.totalAmount + this.ledgerAccountsTotal + this.vatAmount;
        },

        get hasStockWarnings() {
            return this.items.some(item => {
                return parseFloat(item.quantity) > parseFloat(item.current_stock) && !this.isPurchaseInvoice();
            });
        },

        isPurchaseInvoice() {
            const urlParams = new URLSearchParams(window.location.search);
            const typeParam = urlParams.get('type');
            return typeParam && typeParam.toLowerCase() === 'pur';
        },

        formatNumber(num) {
            if (!num || isNaN(num)) return '0.00';
            return parseFloat(num).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        },

        addItem() {
            this.items.push({
                product_id: '',
                product_name: '',
                description: '',
                quantity: 1,
                rate: 0,
                amount: 0,
                purchase_rate: 0,
                current_stock: null,
                unit: 'Pcs'
            });
            this.debouncedUpdateTotals();
        },

        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
                this.debouncedUpdateTotals();
            }
        },

        addLedgerAccount() {
            this.ledgerAccounts.push({
                ledger_account_id: '',
                amount: 0,
                narration: ''
            });
            this.debouncedUpdateTotals();
        },

        removeLedgerAccount(index) {
            this.ledgerAccounts.splice(index, 1);
            this.debouncedUpdateTotals();
        },

        debouncedUpdateTotals() {
            if (this._updateTimeout) {
                clearTimeout(this._updateTimeout);
            }
            this._updateTimeout = setTimeout(() => {
                this.$dispatch('invoice-total-changed', {
                    subtotal: this.totalAmount,
                    ledgerTotal: this.ledgerAccountsTotal,
                    grandTotal: this.grandTotal
                });
            }, 100);
        },

        updateProductDetails(index) {
            const item = this.items[index];
            if (item.product_id) {
                const selectElement = document.querySelector(`select[name="inventory_items[${index}][product_id]"]`);
                if (selectElement && selectElement.selectedIndex > 0) {
                    const selectedOption = selectElement.options[selectElement.selectedIndex];

                    item.product_name = selectedOption.getAttribute('data-name') || '';
                    item.rate = parseFloat(selectedOption.getAttribute('data-sales-rate')) || 0;
                    item.purchase_rate = parseFloat(selectedOption.getAttribute('data-purchase-rate')) || 0;
                    item.current_stock = parseFloat(selectedOption.getAttribute('data-stock')) || 0;
                    item.unit = selectedOption.getAttribute('data-unit') || 'Pcs';

                    if (!item.description) {
                        item.description = item.product_name;
                    }

                    this.calculateAmount(index);
                }
            } else {
                item.current_stock = null;
                item.purchase_rate = 0;
                item.unit = 'Pcs';
            }
        },

        calculateAmount(index) {
            const item = this.items[index];
            const quantity = parseFloat(item.quantity) || 0;
            const rate = parseFloat(item.rate) || 0;
            item.amount = (quantity * rate).toFixed(2);
            this.debouncedUpdateTotals();
        },

        init() {
            console.log('✅ Invoice items component initialized');
        }
    }
};

// Customer Search Component
function customerSearch() {
    return {
        searchTerm: '',
        customers: [],
        selectedCustomerId: '{{ old('customer_id') }}',
        showDropdown: false,
        loading: false,
        searchTimeout: null,

        searchCustomers() {
            if (this.searchTerm.length < 2) {
                this.customers = [];
                this.showDropdown = false;
                return;
            }

            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }

            this.searchTimeout = setTimeout(() => {
                this.loading = true;
                this.showDropdown = true;

                fetch('/{{ $tenant->slug }}/api/customers/search?q=' + encodeURIComponent(this.searchTerm))
                    .then(response => response.json())
                    .then(data => {
                        this.customers = data;
                        this.loading = false;
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        this.customers = [];
                        this.loading = false;
                    });
            }, 300);
        },

        selectCustomer(customer) {
            this.searchTerm = customer.display_name;
            this.selectedCustomerId = customer.ledger_account_id;
            this.showDropdown = false;
            this.customers = [];
        },

        init() {
            document.addEventListener('click', (e) => {
                if (!this.$el.contains(e.target)) {
                    this.showDropdown = false;
                }
            });
        }
    }
}

// Product Search Component
function productSearch(itemIndex) {
    return {
        searchTerm: '',
        products: [],
        selectedProductId: '',
        showDropdown: false,
        loading: false,
        searchTimeout: null,
        itemIndex: itemIndex,

        searchProducts() {
            if (this.searchTerm.length < 2) {
                this.products = [];
                this.showDropdown = false;
                return;
            }

            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }

            this.searchTimeout = setTimeout(() => {
                this.loading = true;
                this.showDropdown = true;

                fetch('/{{ $tenant->slug }}/api/products/search?q=' + encodeURIComponent(this.searchTerm))
                    .then(response => response.json())
                    .then(data => {
                        this.products = data;
                        this.loading = false;
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        this.products = [];
                        this.loading = false;
                    });
            }, 300);
        },

        selectProduct(product) {
            this.searchTerm = product.name;
            this.selectedProductId = product.id;
            this.showDropdown = false;
            this.products = [];

            // Get the parent Alpine component (invoiceItems)
            const invoiceItemsComponent = Alpine.$data(this.$el.closest('[x-data*="invoiceItems"]'));
            if (invoiceItemsComponent && invoiceItemsComponent.items[this.itemIndex]) {
                const item = invoiceItemsComponent.items[this.itemIndex];
                item.product_id = product.id;
                item.product_name = product.name;
                item.rate = parseFloat(product.sales_rate) || 0;
                item.purchase_rate = parseFloat(product.purchase_rate) || 0;
                item.current_stock = parseFloat(product.current_stock) || 0;
                item.unit = product.unit || 'Pcs';

                if (!item.description) {
                    item.description = product.name;
                }

                invoiceItemsComponent.calculateAmount(this.itemIndex);
            }
        },

        init() {
            document.addEventListener('click', (e) => {
                if (!this.$el.contains(e.target)) {
                    this.showDropdown = false;
                }
            });
        }
    }
}

// Ledger Account Search Component
function ledgerAccountSearch(ledgerIndex) {
    return {
        searchTerm: '',
        accounts: [],
        selectedLedgerAccountId: '',
        showDropdown: false,
        loading: false,
        searchTimeout: null,
        ledgerIndex: ledgerIndex,

        searchLedgerAccounts() {
            if (this.searchTerm.length < 2) {
                this.accounts = [];
                this.showDropdown = false;
                return;
            }

            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }

            this.searchTimeout = setTimeout(() => {
                this.loading = true;
                this.showDropdown = true;

                fetch('/{{ $tenant->slug }}/api/ledger-accounts/search?q=' + encodeURIComponent(this.searchTerm))
                    .then(response => response.json())
                    .then(data => {
                        this.accounts = data;
                        this.loading = false;
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        this.accounts = [];
                        this.loading = false;
                    });
            }, 300);
        },

        selectLedgerAccount(account) {
            this.searchTerm = account.name;
            this.selectedLedgerAccountId = account.id;
            this.showDropdown = false;
            this.accounts = [];

            // Get the parent Alpine component (invoiceItems)
            const invoiceItemsComponent = Alpine.$data(this.$el.closest('[x-data*="invoiceItems"]'));
            if (invoiceItemsComponent && invoiceItemsComponent.ledgerAccounts[this.ledgerIndex]) {
                const ledger = invoiceItemsComponent.ledgerAccounts[this.ledgerIndex];
                ledger.ledger_account_id = account.id;
                ledger.ledger_account_name = account.name;

                // Trigger totals update
                invoiceItemsComponent.debouncedUpdateTotals();
            }
        },

        init() {
            document.addEventListener('click', (e) => {
                if (!this.$el.contains(e.target)) {
                    this.showDropdown = false;
                }
            });
        }
    }
}// Main Invoice Form Component
function invoiceForm() {
    return {
        voucherTypeId: '{{ old('voucher_type_id', $selectedType?->id ?? '') }}',
        invoiceNumberPreview: 'Auto-generated',
        voucherTypes: @json($voucherTypes->keyBy('id')),
        totalAmount: 0,
        _eventListenersAdded: false,

        init() {
            this.handleUrlParameters();
            this.updateVoucherType();
            this.setupEventListeners();
            console.log('✅ Invoice form initialized');
        },

        setupEventListeners() {
            if (this._eventListenersAdded) return;

            this.$el.addEventListener('invoice-total-changed', (event) => {
                this.totalAmount = event.detail.grandTotal || event.detail.total || 0;
            });

            this._eventListenersAdded = true;
        },

        handleUrlParameters() {
            const urlParams = new URLSearchParams(window.location.search);
            const typeParam = urlParams.get('type');

            if (typeParam && typeParam.toLowerCase() === 'pur') {
                const purchaseVoucher = Object.values(this.voucherTypes).find(voucher =>
                    voucher.code.toLowerCase().includes('pur') ||
                    voucher.code.toLowerCase().includes('purchase') ||
                    voucher.name.toLowerCase().includes('purchase')
                );

                if (purchaseVoucher) {
                    this.voucherTypeId = purchaseVoucher.id;
                    this.$nextTick(() => {
                        const selectElement = document.getElementById('voucher_type_id');
                        if (selectElement) {
                            selectElement.value = this.voucherTypeId;
                        }
                    });
                }
            }
        },

        updateVoucherType() {
            if (this.voucherTypeId && this.voucherTypes[this.voucherTypeId]) {
                const voucherType = this.voucherTypes[this.voucherTypeId];
                this.invoiceNumberPreview = voucherType.prefix + 'XXXX';
                this.vchType = 'Create ' + voucherType.name + ' Invoice';
                this.toggleCustomerVendorFields(voucherType);
            } else {
                this.invoiceNumberPreview = 'Auto-generated';
            }
        },

        toggleCustomerVendorFields(voucherType) {
            const customerSection = document.getElementById('customerSection');
            const vendorSection = document.getElementById('vendorSection');
            const customerSelect = document.getElementById('customer_id');
            const vendorSelect = document.getElementById('vendor_select');

            if (!customerSection || !vendorSection || !customerSelect || !vendorSelect) return;

            const isPurchase = voucherType.code.includes('PUR') ||
                             voucherType.code.includes('PURCHASE') ||
                             voucherType.name.toLowerCase().includes('purchase');

            if (isPurchase) {
                customerSection.classList.add('hidden');
                vendorSection.classList.remove('hidden');
                vendorSelect.removeAttribute('disabled');
                vendorSelect.setAttribute('required', 'required');
                customerSelect.setAttribute('disabled', 'disabled');
                customerSelect.removeAttribute('required');
                customerSelect.value = '';
            } else {
                customerSection.classList.remove('hidden');
                vendorSection.classList.add('hidden');
                customerSelect.removeAttribute('disabled');
                customerSelect.setAttribute('required', 'required');
                vendorSelect.setAttribute('disabled', 'disabled');
                vendorSelect.removeAttribute('required');
                vendorSelect.value = '';
            }
        },

        formatNumber(num) {
            if (!num || isNaN(num)) return '0.00';
            return parseFloat(num).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    }
}

// Quick Add Product Functions
function openQuickAddProduct(index) {
    currentProductRowIndex = index;
    document.getElementById('quickAddProductModal').classList.remove('hidden');
    document.getElementById('quick_product_name').focus();
}

function closeQuickAddProduct() {
    document.getElementById('quickAddProductModal').classList.add('hidden');
    document.getElementById('quickAddProductForm').reset();
    document.getElementById('quick-product-submit-text').textContent = 'Create Product';
    document.getElementById('quick-product-submit-loading').classList.add('hidden');
    toggleQuickProductType();
    currentProductRowIndex = null;
}

function toggleQuickProductType() {
    const isService = document.querySelector('input[name="type"]:checked').value === 'service';
    const unitSection = document.getElementById('quick_unit_section');
    const stockSection = document.getElementById('quick_stock_section');
    const unitSelect = document.getElementById('quick_unit');

    if (isService) {
        unitSection.classList.add('hidden');
        stockSection.classList.add('hidden');
        unitSelect.required = false;
    } else {
        unitSection.classList.remove('hidden');
        stockSection.classList.remove('hidden');
        unitSelect.required = true;
    }
}

function submitQuickAddProduct() {
    const form = document.getElementById('quickAddProductForm');
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const submitText = document.getElementById('quick-product-submit-text');
    const submitLoading = document.getElementById('quick-product-submit-loading');

    // Validate required fields
    const name = document.getElementById('quick_product_name').value.trim();
    const salesRate = document.getElementById('quick_sales_rate').value;
    const purchaseRate = document.getElementById('quick_purchase_rate').value;

    if (!name) {
        alert('Please enter product name');
        return;
    }

    if (!salesRate || salesRate < 0) {
        alert('Please enter valid sales rate');
        return;
    }

    if (!purchaseRate || purchaseRate < 0) {
        alert('Please enter valid purchase rate');
        return;
    }

    // Show loading state
    submitButton.disabled = true;
    submitText.textContent = 'Creating...';
    submitLoading.classList.remove('hidden');

    // Make AJAX request
    fetch(`{{ route('tenant.inventory.products.store', ['tenant' => $tenant->slug]) }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            closeQuickAddProduct();

            // Show success notification
            showNotification('success', 'Product created successfully!');

            // If we have a row index, auto-select the product
            if (currentProductRowIndex !== null && data.product) {
                // Trigger product selection in the invoice items component
                setTimeout(() => {
                    const event = new CustomEvent('product-created', {
                        detail: {
                            index: currentProductRowIndex,
                            product: data.product
                        }
                    });
                    window.dispatchEvent(event);
                }, 300);
            }
        } else {
            alert(data.message || 'Error creating product. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creating product. Please try again.');
    })
    .finally(() => {
        submitButton.disabled = false;
        submitText.textContent = 'Create Product';
        submitLoading.classList.add('hidden');
    });
}

// Listen for product created event to auto-select in invoice items
window.addEventListener('product-created', function(e) {
    const { index, product } = e.detail;

    // Find the Alpine component and update the item
    const invoiceItemsEl = document.querySelector('[x-data*="invoiceItems"]');
    if (invoiceItemsEl && invoiceItemsEl.__x) {
        const component = invoiceItemsEl.__x.$data;
        if (component.items && component.items[index]) {
            // Update the item with the new product
            component.items[index] = {
                ...component.items[index],
                product_id: product.id,
                name: product.name,
                rate: product.sales_rate,
                purchase_rate: product.purchase_rate,
                current_stock: product.current_stock,
                unit: product.unit_name
            };

            // Calculate amount
            if (component.calculateAmount) {
                component.calculateAmount(index);
            }
        }
    }
});
</script>
@endpush
@endsection
