@extends('layouts.tenant-onboarding')

@section('title', 'Company Information - Ballie Setup')

@section('content')
<!-- Progress Steps -->
<div class="mb-8">
    <div class="flex items-center justify-center">
        <div class="flex items-center space-x-4 md:space-x-8 overflow-x-auto pb-2">
            <!-- Step 1 - Active -->
            <div class="flex items-center flex-shrink-0">
                <div class="w-10 h-10 bg-brand-blue text-white rounded-full flex items-center justify-center font-semibold shadow-lg">
                    1
                </div>
                <span class="ml-3 text-sm font-medium text-brand-blue whitespace-nowrap">Company Info</span>
            </div>

            <!-- Connector -->
            <div class="w-8 md:w-16 h-1 bg-brand-blue rounded hidden sm:block"></div>

            <!-- Step 2 -->
            <div class="flex items-center flex-shrink-0">
                <div class="w-10 h-10 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center font-semibold">
                    2
                </div>
                <span class="ml-3 text-sm font-medium text-gray-500 whitespace-nowrap">Preferences</span>
            </div>

            <!-- Connector -->
            <div class="w-8 md:w-16 h-1 bg-gray-200 rounded hidden sm:block"></div>

            <!-- Step 3 -->
            <div class="flex items-center flex-shrink-0">
                <div class="w-10 h-10 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center font-semibold">
                    3
                </div>
                <span class="ml-3 text-sm font-medium text-gray-500 whitespace-nowrap">Team Setup</span>
            </div>

            <!-- Connector -->
            <div class="w-8 md:w-16 h-1 bg-gray-200 rounded hidden sm:block"></div>

            <!-- Step 4 -->
            <div class="flex items-center flex-shrink-0">
                <div class="w-10 h-10 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center font-semibold">
                    4
                </div>
                <span class="ml-3 text-sm font-medium text-gray-500 whitespace-nowrap">Complete</span>
            </div>
        </div>
    </div>
</div>

<!-- Main Form Card -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-brand-blue to-brand-dark-purple text-white p-6 md:p-8">
        <div class="text-center">
            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0h2M7 7h10M7 11h10M7 15h10"></path>
                </svg>
            </div>
            <h2 class="text-2xl md:text-3xl font-bold mb-2">Tell us about your business</h2>
            <p class="text-brand-light-blue">This information will be used on your invoices and official documents.</p>
        </div>
    </div>

    <!-- Form Content -->
    <div class="p-6 md:p-8">
        <form method="POST" action="{{ route('tenant.onboarding.save-step', ['tenant' => $currentTenant->slug, 'step' => 'company']) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Company Logo Section -->
            <div class="bg-gray-50 rounded-lg p-6">
                <label class="block text-sm font-medium text-gray-700 mb-4">Company Logo</label>
                <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-4 sm:space-y-0 sm:space-x-6">
                    <div class="w-24 h-24 bg-white rounded-lg flex items-center justify-center border-2 border-dashed border-gray-300 hover:border-brand-blue transition-colors cursor-pointer" onclick="document.getElementById('logo-input').click()">
                        <div id="logo-preview" class="hidden w-full h-full">
                            <img id="logo-image" src="" alt="Logo Preview" class="w-full h-full object-contain rounded-lg">
                        </div>
                        <div id="logo-placeholder" class="text-center">
                            <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 12m-6 6v-6m0 0V6a2 2 0 00-2-2H8a2 2 0 00-2 2v6"></path>
                            </svg>
                            <span class="text-xs text-gray-500">Upload</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <input type="file" id="logo-input" name="logo" accept="image/*" class="hidden" onchange="previewLogo(this)">
                        <button type="button" onclick="document.getElementById('logo-input').click()" class="bg-brand-blue text-white px-4 py-2 rounded-lg hover:bg-brand-dark-purple transition-colors text-sm font-medium">
                            Choose Logo
                        </button>
                        <p class="text-xs text-gray-500 mt-2">PNG, JPG or GIF up to 2MB. Recommended size: 200x200px</p>
                    </div>
                </div>
            </div>

            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Company Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name"
                           value="{{ old('name', $currentTenant->name) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-colors @error('name') border-red-500 @enderror"
                           placeholder="Enter your company name" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="business_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Business Type <span class="text-red-500">*</span>
                    </label>
                    <select id="business_type" name="business_type"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-colors @error('business_type') border-red-500 @enderror" required>
                        <option value="">Select business type</option>
                        <option value="sole_proprietorship" {{ old('business_type', $currentTenant->business_type) == 'sole_proprietorship' ? 'selected' : '' }}>Sole Proprietorship</option>
                        <option value="partnership" {{ old('business_type', $currentTenant->business_type) == 'partnership' ? 'selected' : '' }}>Partnership</option>
                        <option value="limited_liability" {{ old('business_type', $currentTenant->business_type) == 'limited_liability' ? 'selected' : '' }}>Limited Liability Company</option>
                        <option value="corporation" {{ old('business_type', $currentTenant->business_type) == 'corporation' ? 'selected' : '' }}>Corporation</option>
                        <option value="ngo" {{ old('business_type', $currentTenant->business_type) == 'ngo' ? 'selected' : '' }}>NGO/Non-Profit</option>
                        <option value="other" {{ old('business_type', $currentTenant->business_type) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('business_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Contact Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Business Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email', $currentTenant->email) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-colors @error('email') border-red-500 @enderror"
                           placeholder="business@company.com" required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" id="phone" name="phone"
                           value="{{ old('phone', $currentTenant->phone) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-colors @error('phone') border-red-500 @enderror"
                           placeholder="+234-XXX-XXX-XXXX" required>
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Address Information -->
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                    Business Address <span class="text-red-500">*</span>
                </label>
                <textarea id="address" name="address" rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-colors @error('address') border-red-500 @enderror"
                          placeholder="Enter your complete business address" required>{{ old('address', $currentTenant->address) }}</textarea>
                @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                        City <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="city" name="city"
                           value="{{ old('city', $currentTenant->city) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-colors @error('city') border-red-500 @enderror"
                           placeholder="Lagos" required>
                    @error('city')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="state" class="block text-sm font-medium text-gray-700 mb-2">
                        State <span class="text-red-500">*</span>
                    </label>
                    <select id="state" name="state"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-colors @error('state') border-red-500 @enderror" required>
                        <option value="">Select state</option>
                        <option value="Abia" {{ old('state', $currentTenant->state) == 'Abia' ? 'selected' : '' }}>Abia</option>
                        <option value="Adamawa" {{ old('state', $currentTenant->state) == 'Adamawa' ? 'selected' : '' }}>Adamawa</option>
                        <option value="Akwa Ibom" {{ old('state', $currentTenant->state) == 'Akwa Ibom' ? 'selected' : '' }}>Akwa Ibom</option>
                        <option value="Anambra" {{ old('state', $currentTenant->state) == 'Anambra' ? 'selected' : '' }}>Anambra</option>
                        <option value="Bauchi" {{ old('state', $currentTenant->state) == 'Bauchi' ? 'selected' : '' }}>Bauchi</option>
                        <option value="Bayelsa" {{ old('state', $currentTenant->state) == 'Bayelsa' ? 'selected' : '' }}>Bayelsa</option>
                        <option value="Benue" {{ old('state', $currentTenant->state) == 'Benue' ? 'selected' : '' }}>Benue</option>
                        <option value="Borno" {{ old('state', $currentTenant->state) == 'Borno' ? 'selected' : '' }}>Borno</option>
                        <option value="Cross River" {{ old('state', $currentTenant->state) == 'Cross River' ? 'selected' : '' }}>Cross River</option>
                        <option value="Delta" {{ old('state', $currentTenant->state) == 'Delta' ? 'selected' : '' }}>Delta</option>
                        <option value="Ebonyi" {{ old('state', $currentTenant->state) == 'Ebonyi' ? 'selected' : '' }}>Ebonyi</option>
                        <option value="Edo" {{ old('state', $currentTenant->state) == 'Edo' ? 'selected' : '' }}>Edo</option>
                        <option value="Ekiti" {{ old('state', $currentTenant->state) == 'Ekiti' ? 'selected' : '' }}>Ekiti</option>
                        <option value="Enugu" {{ old('state', $currentTenant->state) == 'Enugu' ? 'selected' : '' }}>Enugu</option>
                        <option value="FCT" {{ old('state', $currentTenant->state) == 'FCT' ? 'selected' : '' }}>FCT</option>
                        <option value="Gombe" {{ old('state', $currentTenant->state) == 'Gombe' ? 'selected' : '' }}>Gombe</option>
                        <option value="Imo" {{ old('state', $currentTenant->state) == 'Imo' ? 'selected' : '' }}>Imo</option>
                        <option value="Jigawa" {{ old('state', $currentTenant->state) == 'Jigawa' ? 'selected' : '' }}>Jigawa</option>
                        <option value="Kaduna" {{ old('state', $currentTenant->state) == 'Kaduna' ? 'selected' : '' }}>Kaduna</option>
                        <option value="Kano" {{ old('state', $currentTenant->state) == 'Kano' ? 'selected' : '' }}>Kano</option>
                        <option value="Katsina" {{ old('state', $currentTenant->state) == 'Katsina' ? 'selected' : '' }}>Katsina</option>
                        <option value="Kebbi" {{ old('state', $currentTenant->state) == 'Kebbi' ? 'selected' : '' }}>Kebbi</option>
                        <option value="Kogi" {{ old('state', $currentTenant->state) == 'Kogi' ? 'selected' : '' }}>Kogi</option>
                        <option value="Kwara" {{ old('state', $currentTenant->state) == 'Kwara' ? 'selected' : '' }}>Kwara</option>
                        <option value="Lagos" {{ old('state', $currentTenant->state) == 'Lagos' ? 'selected' : '' }}>Lagos</option>
                        <option value="Nasarawa" {{ old('state', $currentTenant->state) == 'Nasarawa' ? 'selected' : '' }}>Nasarawa</option>
                        <option value="Niger" {{ old('state', $currentTenant->state) == 'Niger' ? 'selected' : '' }}>Niger</option>
                        <option value="Ogun" {{ old('state', $currentTenant->state) == 'Ogun' ? 'selected' : '' }}>Ogun</option>
                        <option value="Ondo" {{ old('state', $currentTenant->state) == 'Ondo' ? 'selected' : '' }}>Ondo</option>
                        <option value="Osun" {{ old('state', $currentTenant->state) == 'Osun' ? 'selected' : '' }}>Osun</option>
                        <option value="Oyo" {{ old('state', $currentTenant->state) == 'Oyo' ? 'selected' : '' }}>Oyo</option>
                        <option value="Plateau" {{ old('state', $currentTenant->state) == 'Plateau' ? 'selected' : '' }}>Plateau</option>
                        <option value="Rivers" {{ old('state', $currentTenant->state) == 'Rivers' ? 'selected' : '' }}>Rivers</option>
                        <option value="Sokoto" {{ old('state', $currentTenant->state) == 'Sokoto' ? 'selected' : '' }}>Sokoto</option>
                        <option value="Taraba" {{ old('state', $currentTenant->state) == 'Taraba' ? 'selected' : '' }}>Taraba</option>
                        <option value="Yobe" {{ old('state', $currentTenant->state) == 'Yobe' ? 'selected' : '' }}>Yobe</option>
                        <option value="Zamfara" {{ old('state', $currentTenant->state) == 'Zamfara' ? 'selected' : '' }}>Zamfara</option>
                    </select>
                    @error('state')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                        Country <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="country" name="country"
                           value="{{ old('country', $currentTenant->country ?? 'Nigeria') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-colors bg-gray-50"
                           readonly>
                </div>
            </div>

            <!-- Registration Information -->
            <div class="bg-blue-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Registration Details (Optional)
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="business_registration_number" class="block text-sm font-medium text-gray-700 mb-2">
                            CAC Registration Number
                        </label>
                        <input type="text" id="business_registration_number" name="business_registration_number"
                               value="{{ old('business_registration_number', $currentTenant->business_registration_number) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-colors @error('business_registration_number') border-red-500 @enderror"
                               placeholder="RC123456">
                        @error('business_registration_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tax_identification_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Tax Identification Number (TIN)
                        </label>
                        <input type="text" id="tax_identification_number" name="tax_identification_number"
                               value="{{ old('tax_identification_number', $currentTenant->tax_identification_number) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-colors @error('tax_identification_number') border-red-500 @enderror"
                               placeholder="12345678-0001">
                        @error('tax_identification_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Website (Optional) -->
            <div>
                <label for="website" class="block text-sm font-medium text-gray-700 mb-2">
                    Website (Optional)
                </label>
                <input type="url" id="website" name="website"
                       value="{{ old('website', $currentTenant->website) }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-colors @error('website') border-red-500 @enderror"
                       placeholder="https://www.yourcompany.com">
                @error('website')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row items-center justify-between pt-6 border-t border-gray-200 space-y-4 sm:space-y-0">
                <div class="text-sm text-gray-500">
                    Step 1 of 4 - Company Information
                </div>

                <div class="flex space-x-4">
                    <button type="button" onclick="window.history.back()"
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                        Back
                    </button>
                    <button type="submit"
                            class="px-8 py-3 bg-brand-blue text-white rounded-lg hover:bg-brand-dark-purple transition-colors font-medium flex items-center">
                        Continue
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Help Section -->
<div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="flex items-start">
        <div class="w-10 h-10 bg-brand-teal bg-opacity-10 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
            <svg class="w-5 h-5 text-brand-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Need Help?</h3>
            <p class="text-gray-600 mb-4">
                This information will appear on your invoices, receipts, and other business documents.
                Make sure all details are accurate as they represent your business officially.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="#" class="text-brand-blue hover:text-brand-dark-purple font-medium text-sm">
                    📞 Contact Support
                </a>
                <a href="#" class="text-brand-blue hover:text-brand-dark-purple font-medium text-sm">
                    📖 Setup Guide
                </a>
                <a href="#" class="text-brand-blue hover:text-brand-dark-purple font-medium text-sm">
                    💬 Live Chat
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            document.getElementById('logo-image').src = e.target.result;
            document.getElementById('logo-preview').classList.remove('hidden');
            document.getElementById('logo-placeholder').classList.add('hidden');
        }

        reader.readAsDataURL(input.files[0]);
    }
}

// Auto-format phone number
document.getElementById('phone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');

    if (value.startsWith('234')) {
        value = value.substring(3);
    } else if (value.startsWith('0')) {
        value = value.substring(1);
    }

    if (value.length > 0) {
        if (value.length <= 3) {
            value = `+234-${value}`;
        } else if (value.length <= 6) {
            value = `+234-${value.substring(0, 3)}-${value.substring(3)}`;
        } else {
            value = `+234-${value.substring(0, 3)}-${value.substring(3, 6)}-${value.substring(6, 10)}`;
        }
    }

    e.target.value = value;
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const requiredFields = ['company_name', 'business_type', 'email', 'phone', 'address', 'city', 'state'];
    let hasErrors = false;

    requiredFields.forEach(function(fieldName) {
        const field = document.getElementById(fieldName);
        if (!field.value.trim()) {
            field.classList.add('border-red-500');
            hasErrors = true;
        } else {
            field.classList.remove('border-red-500');
        }
    });

    if (hasErrors) {
        e.preventDefault();
        alert('Please fill in all required fields.');
        return false;
    }
});

// Remove error styling on input
document.querySelectorAll('input, select, textarea').forEach(function(element) {
    element.addEventListener('input', function() {
        this.classList.remove('border-red-500');
    });
});

// Auto-save draft functionality (optional)
let autoSaveTimeout;
document.querySelectorAll('input, select, textarea').forEach(function(element) {
    element.addEventListener('input', function() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(function() {
            // Auto-save logic can be implemented here
            console.log('Auto-saving draft...');
        }, 2000);
    });
});
</script>
@endpush