@extends('layouts.tenant')

@section('title', 'Edit Vendor')
@section('page-title', 'Edit Vendor')
@section('page-description', 'Update vendor information and sync with ledger account.')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <form action="{{ route('tenant.vendors.update', ['tenant' => tenant()->slug, 'vendor' => $vendor->id]) }}" method="POST" id="vendorForm">
            @csrf
            @method('PUT')

            <!-- Vendor Type Selection -->
            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-4">Vendor Type</label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="relative">
                        <input type="radio" name="vendor_type" value="individual" class="sr-only peer" {{ old('vendor_type', $vendor->vendor_type) == 'individual' ? 'checked' : '' }}>
                        <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer peer-checked:border-purple-500 peer-checked:bg-purple-50 transition-all duration-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-900">Individual</h3>
                                    <p class="text-sm text-gray-500">Personal vendor/freelancer</p>
                                </div>
                            </div>
                        </div>
                    </label>

                    <label class="relative">
                        <input type="radio" name="vendor_type" value="business" class="sr-only peer" {{ old('vendor_type', $vendor->vendor_type) == 'business' ? 'checked' : '' }}>
                        <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer peer-checked:border-purple-500 peer-checked:bg-purple-50 transition-all duration-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m4 0v-3.5a1.5 1.5 0 013 0V21m-4-3h4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-900">Business</h3>
                                    <p class="text-sm text-gray-500">Company or organization</p>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Individual Fields -->
            <div id="individualFields" class="space-y-6 mb-8" style="display: {{ old('vendor_type', $vendor->vendor_type) == 'individual' ? 'block' : 'none' }}">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $vendor->first_name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                        @error('first_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $vendor->last_name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                        @error('last_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Business Fields -->
            <div id="businessFields" class="space-y-6 mb-8" style="display: {{ old('vendor_type', $vendor->vendor_type) == 'business' ? 'block' : 'none' }}">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                        <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $vendor->company_name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                        @error('company_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tax_id" class="block text-sm font-medium text-gray-700 mb-2">Tax ID</label>
                        <input type="text" name="tax_id" id="tax_id" value="{{ old('tax_id', $vendor->tax_id) }}" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                        @error('tax_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="registration_number" class="block text-sm font-medium text-gray-700 mb-2">Registration Number</label>
                    <input type="text" name="registration_number" id="registration_number" value="{{ old('registration_number', $vendor->registration_number) }}" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                    @error('registration_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Contact Information -->
            <div class="space-y-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">Contact Information</h3>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $vendor->email) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone', $vendor->phone) }}" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="mobile" class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                        <input type="tel" name="mobile" id="mobile" value="{{ old('mobile', $vendor->mobile) }}" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                        @error('mobile')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="website" class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                        <input type="url" name="website" id="website" value="{{ old('website', $vendor->website) }}" placeholder="https://" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                        @error('website')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="space-y-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">Address Information</h3>

                <div>
                    <label for="address_line1" class="block text-sm font-medium text-gray-700 mb-2">Address Line 1</label>
                    <input type="text" name="address_line1" id="address_line1" value="{{ old('address_line1', $vendor->address_line1) }}" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                    @error('address_line1')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="address_line2" class="block text-sm font-medium text-gray-700 mb-2">Address Line 2</label>
                    <input type="text" name="address_line2" id="address_line2" value="{{ old('address_line2', $vendor->address_line2) }}" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                    @error('address_line2')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-3 gap-6">
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                        <input type="text" name="city" id="city" value="{{ old('city', $vendor->city) }}" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                        @error('city')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-2">State</label>
                        <input type="text" name="state" id="state" value="{{ old('state', $vendor->state) }}" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                        @error('state')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                        <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $vendor->postal_code) }}" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                        @error('postal_code')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                    <input type="text" name="country" id="country" value="{{ old('country', $vendor->country) }}" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                    @error('country')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Banking Information -->
            <div class="space-y-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">Banking Information</h3>

                <div>
                    <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-2">Bank Name</label>
                    <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $vendor->bank_name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                    @error('bank_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="bank_account_number" class="block text-sm font-medium text-gray-700 mb-2">Account Number</label>
                        <input type="text" name="bank_account_number" id="bank_account_number" value="{{ old('bank_account_number', $vendor->bank_account_number) }}" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                        @error('bank_account_number')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="bank_account_name" class="block text-sm font-medium text-gray-700 mb-2">Account Name</label>
                        <input type="text" name="bank_account_name" id="bank_account_name" value="{{ old('bank_account_name', $vendor->bank_account_name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                        @error('bank_account_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Financial Settings -->
            <div class="space-y-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">Financial Settings</h3>

                <div class="grid grid-cols-3 gap-6">
                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                        <select name="currency" id="currency" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            <option value="NGN" {{ old('currency', $vendor->currency) == 'NGN' ? 'selected' : '' }}>Nigerian Naira (NGN)</option>
                            <option value="USD" {{ old('currency', $vendor->currency) == 'USD' ? 'selected' : '' }}>US Dollar (USD)</option>
                            <option value="GBP" {{ old('currency', $vendor->currency) == 'GBP' ? 'selected' : '' }}>British Pound (GBP)</option>
                            <option value="EUR" {{ old('currency', $vendor->currency) == 'EUR' ? 'selected' : '' }}>Euro (EUR)</option>
                        </select>
                        @error('currency')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="payment_terms" class="block text-sm font-medium text-gray-700 mb-2">Payment Terms</label>
                        <select name="payment_terms" id="payment_terms" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            <option value="">Select payment terms</option>
                            <option value="Net 15" {{ old('payment_terms', $vendor->payment_terms) == 'Net 15' ? 'selected' : '' }}>Net 15</option>
                            <option value="Net 30" {{ old('payment_terms', $vendor->payment_terms) == 'Net 30' ? 'selected' : '' }}>Net 30</option>
                            <option value="Net 45" {{ old('payment_terms', $vendor->payment_terms) == 'Net 45' ? 'selected' : '' }}>Net 45</option>
                            <option value="Net 60" {{ old('payment_terms', $vendor->payment_terms) == 'Net 60' ? 'selected' : '' }}>Net 60</option>
                            <option value="COD" {{ old('payment_terms', $vendor->payment_terms) == 'COD' ? 'selected' : '' }}>Cash on Delivery</option>
                            <option value="Prepaid" {{ old('payment_terms', $vendor->payment_terms) == 'Prepaid' ? 'selected' : '' }}>Prepaid</option>
                        </select>
                        @error('payment_terms')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            <option value="active" {{ old('status', $vendor->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $vendor->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="space-y-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">Additional Information</h3>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" id="notes" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200" placeholder="Any additional notes about this vendor...">{{ old('notes', $vendor->notes) }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Ledger Integration Info -->
            @if($vendor->ledgerAccount)
                <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-8">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-green-900">Ledger Account Connected</h4>
                            <p class="text-sm text-green-700 mt-1">
                                This vendor is linked to ledger account: <strong>{{ $vendor->ledgerAccount->code }} - {{ $vendor->ledgerAccount->name }}</strong>
                            </p>
                            <p class="text-xs text-green-600 mt-1">
                                Current Balance: ₦{{ number_format($vendor->ledgerAccount->getCurrentBalance(), 2) }}
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-8">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-yellow-900">No Ledger Account</h4>
                            <p class="text-sm text-yellow-700 mt-1">
                                This vendor doesn't have a linked ledger account. One will be created automatically when updated.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('tenant.vendors.show', ['tenant' => tenant()->slug, 'vendor' => $vendor->id]) }}" class="px-6 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-xl hover:from-purple-700 hover:to-purple-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 shadow-md">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update Vendor
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const vendorTypeRadios = document.querySelectorAll('input[name="vendor_type"]');
    const individualFields = document.getElementById('individualFields');
    const businessFields = document.getElementById('businessFields');

    // Toggle fields based on vendor type
    vendorTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'individual') {
                individualFields.style.display = 'block';
                businessFields.style.display = 'none';
            } else {
                individualFields.style.display = 'none';
                businessFields.style.display = 'block';
            }
        });
    });

    // Form validation
    const form = document.getElementById('vendorForm');
    form.addEventListener('submit', function(e) {
        const vendorType = document.querySelector('input[name="vendor_type"]:checked').value;
        let isValid = true;
        let errorMessage = '';

        if (vendorType === 'individual') {
            const firstName = document.getElementById('first_name').value.trim();
            const lastName = document.getElementById('last_name').value.trim();

            if (!firstName || !lastName) {
                isValid = false;
                errorMessage = 'First name and last name are required for individual vendors.';
            }
        } else {
            const companyName = document.getElementById('company_name').value.trim();

            if (!companyName) {
                isValid = false;
                errorMessage = 'Company name is required for business vendors.';
            }
        }

        const email = document.getElementById('email').value.trim();
        if (!email) {
            isValid = false;
            errorMessage = 'Email address is required.';
        }

        if (!isValid) {
            e.preventDefault();
            alert(errorMessage);
        }
    });

    // Auto-populate bank account name
    const companyNameField = document.getElementById('company_name');
    const firstNameField = document.getElementById('first_name');
    const lastNameField = document.getElementById('last_name');
    const bankAccountNameField = document.getElementById('bank_account_name');

    function updateBankAccountName() {
        const vendorType = document.querySelector('input[name="vendor_type"]:checked').value;

        if (vendorType === 'individual') {
            const firstName = firstNameField.value.trim();
            const lastName = lastNameField.value.trim();
            if (firstName && lastName && !bankAccountNameField.value) {
                bankAccountNameField.value = firstName + ' ' + lastName;
            }
        } else {
            const companyName = companyNameField.value.trim();
            if (companyName && !bankAccountNameField.value) {
                bankAccountNameField.value = companyName;
            }
        }
    }

    companyNameField.addEventListener('blur', updateBankAccountName);
    firstNameField.addEventListener('blur', updateBankAccountName);
    lastNameField.addEventListener('blur', updateBankAccountName);
});
</script>
@endsection