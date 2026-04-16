@extends('layouts.tenant-onboarding')

@section('title', 'Company Information - Ballie Setup')

@section('content')
<!-- Progress Steps -->
<div class="mb-10">
    <div class="flex items-center justify-center">
        <div class="flex items-center space-x-3 md:space-x-6 overflow-x-auto pb-2">
            @foreach (['Company Info', 'Preferences', 'Accounts', 'Complete'] as $i => $label)
            @if ($i > 0)
            <div class="w-6 md:w-12 h-0.5 {{ $i == 0 ? 'bg-brand-blue' : 'bg-gray-200' }} rounded hidden sm:block"></div>
            @endif
            <div class="flex items-center flex-shrink-0">
                @if ($i == 0)
                <div class="w-8 h-8 bg-brand-blue text-white rounded-full flex items-center justify-center font-semibold shadow-sm">
                    1
                </div>
                @else
                <div class="w-8 h-8 bg-gray-200 text-gray-400 rounded-full flex items-center justify-center font-semibold">
                    {{ $i + 1 }}
                </div>
                @endif
                <span class="ml-2 text-xs font-medium {{ $i == 0 ? 'text-brand-blue' : 'text-gray-400' }} whitespace-nowrap hidden sm:inline">{{ $label }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Main Form Card -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-br from-brand-blue to-brand-dark-purple text-white p-6 md:p-8">
        <div class="text-center">
            <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0h2M7 7h10M7 11h10M7 15h10"></path>
                </svg>
            </div>
            <h2 class="text-2xl md:text-3xl font-bold mb-2">Tell us about your business</h2>
            <p class="text-brand-light-blue max-w-lg mx-auto">This information will be used on your invoices and official documents.</p>
        </div>
    </div>

    <!-- Form Content -->
    <div class="p-6 md:p-8">
        <form method="POST" action="{{ route('tenant.onboarding.save-step', ['tenant' => $currentTenant->slug, 'step' => 'company']) }}" enctype="multipart/form-data" class="space-y-6" x-data="{ loading: false }" @submit="loading = true">
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
                    <label for="business_structure" class="block text-sm font-medium text-gray-700 mb-2">
                        Business Structure <span class="text-red-500">*</span>
                    </label>
                    <select id="business_structure" name="business_structure"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-colors @error('business_structure') border-red-500 @enderror" required>
                        <option value="">Select business Structure</option>
                        <option selected value="sole_proprietorship" {{ old('business_structure', $currentTenant->business_structure) == 'sole_proprietorship' ? 'selected' : '' }}>Sole Proprietorship</option>
                        <option value="partnership" {{ old('business_structure', $currentTenant->business_structure) == 'partnership' ? 'selected' : '' }}>Partnership</option>
                        <option value="limited_liability" {{ old('business_structure', $currentTenant->business_structure) == 'limited_liability' ? 'selected' : '' }}>Limited Liability Company</option>
                        <option value="corporation" {{ old('business_structure', $currentTenant->business_structure) == 'corporation' ? 'selected' : '' }}>Corporation</option>
                        <option value="ngo" {{ old('business_structure', $currentTenant->business_structure) == 'ngo' ? 'selected' : '' }}>NGO/Non-Profit</option>
                        <option value="other" {{ old('business_structure', $currentTenant->business_structure) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('business_structure')
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
                        City/ LGA <span class="text-red-500">*</span>
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
                <input  id="website" name="website"
                       value="{{ old('website', $currentTenant->website) }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-colors @error('website') border-red-500 @enderror"
                       placeholder="https://www.yourcompany.com">
                @error('website')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row items-center justify-between pt-6 border-t border-gray-200 space-y-4 sm:space-y-0">
                <div class="text-sm text-gray-400">
                    Step 1 of 4
                </div>

                <div class="flex items-center space-x-3">
                    <button type="button" onclick="window.history.back()"
                            class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-600 rounded-xl hover:bg-gray-50 transition-all font-medium">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                        </svg>
                        Back
                    </button>
                    <button type="submit"
                            :disabled="loading"
                            :class="loading ? 'opacity-75 cursor-not-allowed' : 'hover:bg-brand-dark-purple hover:shadow-lg'"
                            class="inline-flex items-center px-8 py-3 bg-brand-blue text-white rounded-xl transition-all font-semibold shadow-md">
                        <svg x-show="loading" class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span x-text="loading ? 'Saving...' : 'Continue'"></span>
                        <svg x-show="!loading" class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </button>
                </div>
            </div>
        </form>
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
    const requiredFields = ['company_name', 'business_structure', 'email', 'phone', 'address', 'city', 'state'];
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
