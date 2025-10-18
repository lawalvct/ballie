@extends('layouts.tenant')

@section('title', 'Create Invoice - ' . $tenant->name)

@section('content')
<div class="space-y-6" x-data="invoiceForm()">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">    <span x-text="vchType">Create Invoice</span></h1>
            <p class="mt-1 text-sm text-gray-500">
                Create a new invoice with inventory management
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('tenant.accounting.invoices.index', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Invoices
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
                        <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Customer <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <select required name="customer_id"
                                    id="customer_id"
                                    class="flex-1 pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 rounded-lg">
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->ledgerAccount->id }}" {{ old('customer_id') == $customer->ledgerAccount->id ? 'selected' : '' }}>
                                        {{ $customer->display_name }}
                                    </option>
                                @endforeach
                            </select>
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

        <!-- Submit Buttons -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">Total Amount: </span>
                        <span class="text-lg font-bold text-gray-900">₦<span x-text="formatNumber(totalAmount)">0.00</span></span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button type="button"
                                onclick="window.history.back()"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancel
                        </button>
                        <button type="submit"
                                name="action"
                                value="save_draft"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            Save as Draft
                        </button>
                        <button type="submit"
                                name="action"
                                value="save_and_post"
                                class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
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

@push('scripts')
<script>
let currentModalType = 'customer';

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
});

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
                description: '',
                quantity: '',
                rate: '',
                amount: '',
                purchase_rate: '',
                current_stock: null,
                unit: 'Pcs'
            }
        ],

        get totalAmount() {
            const total = this.items.reduce((sum, item) => {
                return sum + (parseFloat(item.amount) || 0);
            }, 0);

            // Notify parent component about total change
            this.$nextTick(() => {
                document.dispatchEvent(new CustomEvent('inventory-total-updated', {
                    detail: { total: total }
                }));
            });

            return total;
        },

        get hasStockWarnings() {
            return this.items.some(item => {
                return item.product_id &&
                       item.quantity &&
                       item.current_stock !== null &&
                       parseFloat(item.quantity) > parseFloat(item.current_stock);
            });
        },

        isPurchaseInvoice() {
            // Check URL parameter for purchase type
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
                description: '',
                quantity: '',
                rate: '',
                amount: '',
                purchase_rate: '',
                current_stock: null,
                unit: 'Pcs'
            });
        },

        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        },

        updateProductDetails(index) {
            const item = this.items[index];
            if (item.product_id) {
                const selectElement = document.querySelector(`select[name="inventory_items[${index}][product_id]"]`);
                const selectedOption = selectElement.options[selectElement.selectedIndex];

                if (selectedOption) {
                    const productName = selectedOption.getAttribute('data-name');
                    const salesRate = selectedOption.getAttribute('data-sales-rate');
                    const purchaseRate = selectedOption.getAttribute('data-purchase-rate');
                    const currentStock = selectedOption.getAttribute('data-stock');
                    const unit = selectedOption.getAttribute('data-unit');

                    // Set description if empty
                    if (!item.description) {
                        item.description = productName;
                    }

                    // Set sales rate and purchase rate
                    item.rate = salesRate;
                    item.purchase_rate = purchaseRate;
                    item.current_stock = currentStock;
                    item.unit = unit;

                    // Calculate amount if quantity is already set
                    if (item.quantity) {
                        this.calculateAmount(index);
                    }
                }
            } else {
                // Clear related fields when product is deselected
                item.current_stock = null;
                item.purchase_rate = '';
                item.unit = 'Pcs';
            }
        },

        calculateAmount(index) {
            const item = this.items[index];
            const quantity = parseFloat(item.quantity) || 0;
            const rate = parseFloat(item.rate) || 0;
            item.amount = (quantity * rate).toFixed(2);
        },

        init() {
            console.log('✅ Invoice items component initialized');
            console.log('Initial items:', this.items);
        }
    }
};

// Main Invoice Form Component
function invoiceForm() {
    return {
        voucherTypeId: '{{ old('voucher_type_id', $selectedType?->id ?? '') }}',
        invoiceNumberPreview: 'Auto-generated',
        voucherTypes: @json($voucherTypes->keyBy('id')),
        totalAmount: 0,

        init() {
            // Check URL parameters for type selection
            this.handleUrlParameters();
            this.updateVoucherType();

            // Listen for inventory total updates
            document.addEventListener('inventory-total-updated', (e) => {
                this.totalAmount = e.detail.total;
            });

            console.log('✅ Invoice form initialized');
        },

        handleUrlParameters() {
            const urlParams = new URLSearchParams(window.location.search);
            const typeParam = urlParams.get('type');
            
            if (typeParam && typeParam.toLowerCase() === 'pur') {
                // Find purchase voucher type
                const purchaseVoucher = Object.values(this.voucherTypes).find(voucher => 
                    voucher.code.toLowerCase().includes('pur') || 
                    voucher.code.toLowerCase().includes('purchase') ||
                    voucher.name.toLowerCase().includes('purchase')
                );
                
                if (purchaseVoucher) {
                    this.voucherTypeId = purchaseVoucher.id;
                    
                    // Update the select element
                    this.$nextTick(() => {
                        const selectElement = document.getElementById('voucher_type_id');
                        if (selectElement) {
                            selectElement.value = this.voucherTypeId;
                        }
                    });
                    
                    console.log('✅ Purchase voucher type auto-selected from URL parameter');
                }
            }
        },

        updateVoucherType() {
            if (this.voucherTypeId && this.voucherTypes[this.voucherTypeId]) {
                const voucherType = this.voucherTypes[this.voucherTypeId];
                this.invoiceNumberPreview = voucherType.prefix + 'XXXX';
                this.vchType = 'Create '+voucherType.name+ ' Invoice';

                // Switch between customer and vendor fields based on voucher type
                this.toggleCustomerVendorFields(voucherType);

                // Notify inventory component about voucher type change
                document.dispatchEvent(new CustomEvent('voucher-type-changed', {
                    detail: { voucherType: voucherType, vchType: this.vchType }
                }));
            } else {
                this.invoiceNumberPreview = 'Auto-generated';
            }
        },

        toggleCustomerVendorFields(voucherType) {
            const customerSection = document.getElementById('customerSection');
            const vendorSection = document.getElementById('vendorSection');
            const customerSelect = document.getElementById('customer_id');
            const vendorSelect = document.getElementById('vendor_select');

            // Check if this is a purchase voucher type
            const isPurchase = voucherType.code.includes('PUR') ||
                             voucherType.code.includes('PURCHASE') ||
                             voucherType.name.toLowerCase().includes('purchase');

            if (isPurchase) {
                // Show vendor field, hide customer field
                customerSection.classList.add('hidden');
                vendorSection.classList.remove('hidden');

                // Enable vendor select and disable customer select
                vendorSelect.required = true;
                customerSelect.required = false;
                customerSelect.value = ''; // Clear customer selection
            } else {
                // Show customer field, hide vendor field (default for sales)
                customerSection.classList.remove('hidden');
                vendorSection.classList.add('hidden');

                // Enable customer select and disable vendor select
                customerSelect.required = true;
                vendorSelect.required = false;
                vendorSelect.value = ''; // Clear vendor selection
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
</script>
@endpush
@endsection
