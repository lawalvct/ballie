@extends('layouts.tenant')

@section('title', 'Create Project')
@section('page-title', 'Create New Project')
@section('page-description', 'Set up a new project with details, timeline, and budget.')

@section('content')
<div class="space-y-6">
    <!-- Header with Back Button -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('tenant.projects.index', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Projects
            </a>
        </div>
        <div class="flex items-center space-x-3">
            <span class="text-sm text-gray-500">Creating new project</span>
            <div class="w-3 h-3 bg-violet-500 rounded-full animate-pulse"></div>
        </div>
    </div>

    <!-- Validation Errors -->
    @if ($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('tenant.projects.store', ['tenant' => $tenant->slug]) }}" method="POST">
        @csrf

        <!-- Project Details -->
        <div class="bg-white rounded-2xl p-6 shadow-lg mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6 flex items-center">
                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-violet-100 text-violet-600 mr-3 text-sm font-semibold">1</span>
                Project Details
            </h3>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="lg:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">Project Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 @error('name') border-red-300 @enderror"
                           placeholder="e.g. Website Redesign for ABC Corp">
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Description -->
                <div class="lg:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="8"
                              class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 @error('description') border-red-300 @enderror"
                              placeholder="Brief summary of the project scope and objectives...">{{ old('description') }}</textarea>
                    @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Client -->
                <div>
                    <div class="flex items-center justify-between">
                        <label for="customer_id" class="block text-sm font-medium text-gray-700">Client</label>
                        <button type="button" onclick="openQuickAddClient()"
                                class="inline-flex items-center text-xs font-medium text-violet-600 hover:text-violet-800 transition-colors">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add New Client
                        </button>
                    </div>
                    <select name="customer_id" id="customer_id"
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500">
                        <option value="">— Select Client —</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->first_name }} {{ $customer->last_name }}
                                @if($customer->company_name) ({{ $customer->company_name }}) @endif
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Assigned To -->
                <div>
                    <label for="assigned_to" class="block text-sm font-medium text-gray-700">Project Manager</label>
                    <select name="assigned_to" id="assigned_to"
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500">
                        <option value="">— Select Team Member —</option>
                        @foreach($teamMembers as $member)
                            <option value="{{ $member->id }}" {{ old('assigned_to') == $member->id ? 'selected' : '' }}>
                                {{ $member->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('assigned_to') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500">
                        <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="on_hold" {{ old('status') === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                    </select>
                    @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700">Priority <span class="text-red-500">*</span></label>
                    <select name="priority" id="priority" required
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500">
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                    @error('priority') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Timeline & Budget -->
        <div class="bg-white rounded-2xl p-6 shadow-lg mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6 flex items-center">
                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-violet-100 text-violet-600 mr-3 text-sm font-semibold">2</span>
                Timeline & Budget
            </h3>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Start Date -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 @error('start_date') border-red-300 @enderror">
                    @error('start_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- End Date -->
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 @error('end_date') border-red-300 @enderror">
                    @error('end_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Budget -->
                <div>
                    <label for="budget" class="block text-sm font-medium text-gray-700">Budget (₦)</label>
                    <div class="mt-1 relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">₦</span>
                        </div>
                        <input type="number" name="budget" id="budget" value="{{ old('budget') }}" step="0.01" min="0"
                               class="block w-full pl-8 border-gray-300 rounded-lg focus:ring-violet-500 focus:border-violet-500 @error('budget') border-red-300 @enderror"
                               placeholder="0.00">
                    </div>
                    @error('budget') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex items-center justify-end space-x-4">
            <a href="{{ route('tenant.projects.index', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition ease-in-out duration-150">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center px-6 py-3 bg-violet-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition ease-in-out duration-150">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Project
            </button>
        </div>
    </form>
</div>

<!-- Quick Add Client Modal -->
<div id="quickAddClientModal" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeQuickAddClient()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-violet-100 text-violet-600 mr-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </span>
                    Add New Client
                </h3>
                <button type="button" onclick="closeQuickAddClient()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-5 space-y-4">
                <!-- Alert -->
                <div id="quickAddAlert" class="hidden rounded-lg p-3 text-sm"></div>

                <!-- Customer Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Client Type <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex cursor-pointer rounded-lg border p-3 transition-colors has-[:checked]:border-violet-500 has-[:checked]:bg-violet-50">
                            <input type="radio" name="qc_customer_type" value="individual" class="sr-only" checked onchange="toggleQuickAddType(this.value)">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">Individual</span>
                            </span>
                        </label>
                        <label class="relative flex cursor-pointer rounded-lg border p-3 transition-colors has-[:checked]:border-violet-500 has-[:checked]:bg-violet-50">
                            <input type="radio" name="qc_customer_type" value="business" class="sr-only" onchange="toggleQuickAddType(this.value)">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">Business</span>
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Individual Name Fields -->
                <div id="qc_individual_fields" class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="qc_first_name" class="block text-sm font-medium text-gray-700">First Name <span class="text-red-500">*</span></label>
                        <input type="text" id="qc_first_name" placeholder="John"
                               class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 text-sm">
                    </div>
                    <div>
                        <label for="qc_last_name" class="block text-sm font-medium text-gray-700">Last Name <span class="text-red-500">*</span></label>
                        <input type="text" id="qc_last_name" placeholder="Doe"
                               class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 text-sm">
                    </div>
                </div>

                <!-- Business Name Field -->
                <div id="qc_business_fields" class="hidden">
                    <label for="qc_company_name" class="block text-sm font-medium text-gray-700">Company Name <span class="text-red-500">*</span></label>
                    <input type="text" id="qc_company_name" placeholder="Acme Corporation"
                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 text-sm">
                </div>

                <!-- Email -->
                <div>
                    <label for="qc_email" class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                    <input type="email" id="qc_email" placeholder="client@example.com"
                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 text-sm">
                </div>

                <!-- Phone -->
                <div>
                    <label for="qc_phone" class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="text" id="qc_phone" placeholder="+234 800 000 0000"
                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 text-sm">
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end space-x-3 px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                <button type="button" onclick="closeQuickAddClient()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="button" onclick="submitQuickAddClient()" id="quickAddSubmitBtn"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-violet-600 rounded-lg hover:bg-violet-700 transition-colors disabled:opacity-60 disabled:cursor-not-allowed">
                    <svg id="quickAddSpinner" class="hidden w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    Add Client
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.tiny.cloud/1/{{ config('services.tinymce.key') }}/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#description',
        height: 300,
        menubar: false,
        plugins: 'lists link table paste wordcount autolink',
        toolbar: 'undo redo | formatselect | bold italic underline strikethrough | bullist numlist | link table | alignleft aligncenter alignright | removeformat',
        paste_as_text: false,
        paste_word_valid_elements: 'b,strong,i,em,h1,h2,h3,h4,h5,h6,p,ul,ol,li,a[href],table,thead,tbody,tr,td,th,br,span,div,blockquote,pre,code,hr,sub,sup',
        valid_elements: 'p[style],br,strong/b,em/i,u,s,strike,h1,h2,h3,h4,h5,h6,ul,ol,li,a[href|target],table[border|cellpadding|cellspacing],thead,tbody,tr,td[colspan|rowspan],th[colspan|rowspan],blockquote,pre,code,hr,sub,sup,span[style],div[style],img[src|alt|width|height]',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 14px; line-height: 1.6; }',
        branding: false,
        promotion: false,
        setup: function(editor) {
            editor.on('change', function() {
                editor.save();
            });
        }
    });
</script>
<script>
    function openQuickAddClient() {
        document.getElementById('quickAddClientModal').classList.remove('hidden');
        document.getElementById('qc_first_name').focus();
        // Reset form state
        ['qc_first_name','qc_last_name','qc_company_name','qc_email','qc_phone'].forEach(id => {
            const el = document.getElementById(id);
            if (el) { el.value = ''; el.classList.remove('border-red-500'); }
        });
        document.querySelectorAll('input[name="qc_customer_type"]').forEach(r => r.value === 'individual' ? r.checked = true : null);
        toggleQuickAddType('individual');
        showQuickAddAlert('', '');
    }

    function closeQuickAddClient() {
        document.getElementById('quickAddClientModal').classList.add('hidden');
    }

    function toggleQuickAddType(type) {
        const indFields = document.getElementById('qc_individual_fields');
        const bizFields = document.getElementById('qc_business_fields');
        if (type === 'business') {
            indFields.classList.add('hidden');
            bizFields.classList.remove('hidden');
        } else {
            indFields.classList.remove('hidden');
            bizFields.classList.add('hidden');
        }
    }

    function showQuickAddAlert(message, type) {
        const alert = document.getElementById('quickAddAlert');
        if (!message) { alert.classList.add('hidden'); return; }
        alert.className = 'rounded-lg p-3 text-sm ' + (type === 'error'
            ? 'bg-red-50 text-red-700 border border-red-200'
            : 'bg-green-50 text-green-700 border border-green-200');
        alert.textContent = message;
        alert.classList.remove('hidden');
    }

    function submitQuickAddClient() {
        const type = document.querySelector('input[name="qc_customer_type"]:checked').value;
        const email = document.getElementById('qc_email').value.trim();
        const firstName = document.getElementById('qc_first_name').value.trim();
        const lastName = document.getElementById('qc_last_name').value.trim();
        const companyName = document.getElementById('qc_company_name').value.trim();
        const phone = document.getElementById('qc_phone').value.trim();

        // Basic validation
        if (!email) {
            showQuickAddAlert('Email is required.', 'error');
            document.getElementById('qc_email').classList.add('border-red-500');
            return;
        }
        if (type === 'individual' && (!firstName || !lastName)) {
            showQuickAddAlert('First and last name are required.', 'error');
            return;
        }
        if (type === 'business' && !companyName) {
            showQuickAddAlert('Company name is required.', 'error');
            document.getElementById('qc_company_name').classList.add('border-red-500');
            return;
        }

        const btn = document.getElementById('quickAddSubmitBtn');
        const spinner = document.getElementById('quickAddSpinner');
        btn.disabled = true;
        spinner.classList.remove('hidden');

        const formData = new FormData();
        formData.append('customer_type', type);
        formData.append('email', email);
        formData.append('phone', phone);
        if (type === 'individual') {
            formData.append('first_name', firstName);
            formData.append('last_name', lastName);
        } else {
            formData.append('company_name', companyName);
        }
        formData.append('opening_balance_type', 'none');
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ route('tenant.crm.customers.store', ['tenant' => $tenant->slug]) }}', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Add new option to dropdown and select it
                const select = document.getElementById('customer_id');
                const option = new Option(data.display_name, data.customer_id, true, true);
                select.add(option);
                closeQuickAddClient();
            } else {
                showQuickAddAlert(data.message || 'Failed to create client.', 'error');
            }
        })
        .catch(() => showQuickAddAlert('An unexpected error occurred. Please try again.', 'error'))
        .finally(() => {
            btn.disabled = false;
            spinner.classList.add('hidden');
        });
    }

    // Close modal on Escape key
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeQuickAddClient();
    });
</script>
@endpush
