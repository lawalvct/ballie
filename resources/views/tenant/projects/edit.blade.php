@extends('layouts.tenant')

@section('title', 'Edit Project')
@section('page-title', 'Edit Project')
@section('page-description')
    <span class="hidden md:inline">
        Update project details for {{ $project->name }}.
    </span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header with Back Button -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('tenant.projects.show', [$tenant->slug, $project->id]) }}"
               class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Project
            </a>
        </div>
        <span class="font-mono text-sm text-gray-400">{{ $project->project_number }}</span>
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

    <form action="{{ route('tenant.projects.update', [$tenant->slug, $project->id]) }}" method="POST">
        @csrf
        @method('PUT')

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
                    <input type="text" name="name" id="name" value="{{ old('name', $project->name) }}" required
                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 @error('name') border-red-300 @enderror">
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Description -->
                <div class="lg:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="8"
                              class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 @error('description') border-red-300 @enderror"
                              placeholder="Brief summary of the project scope and objectives...">{{ old('description', $project->description) }}</textarea>
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
                            <option value="{{ $customer->id }}" {{ old('customer_id', $project->customer_id) == $customer->id ? 'selected' : '' }}>
                                {{ $customer->first_name }} {{ $customer->last_name }}
                                @if($customer->company_name) ({{ $customer->company_name }}) @endif
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Assigned To -->
                <div>
                    <div class="flex items-center justify-between">
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700">Project Manager</label>
                        <button type="button" onclick="openQuickAddManager()"
                                class="inline-flex items-center text-xs font-medium text-violet-600 hover:text-violet-800 transition-colors">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add New Manager
                        </button>
                    </div>
                    <select name="assigned_to" id="assigned_to"
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500">
                        <option value="">— Select Team Member —</option>
                        @foreach($teamMembers as $member)
                            <option value="{{ $member->id }}" {{ old('assigned_to', $project->assigned_to) == $member->id ? 'selected' : '' }}>
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
                        <option value="draft" {{ old('status', $project->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status', $project->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="on_hold" {{ old('status', $project->status) === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                        <option value="completed" {{ old('status', $project->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="archived" {{ old('status', $project->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                    @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700">Priority <span class="text-red-500">*</span></label>
                    <select name="priority" id="priority" required
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500">
                        <option value="low" {{ old('priority', $project->priority) === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', $project->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority', $project->priority) === 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ old('priority', $project->priority) === 'urgent' ? 'selected' : '' }}>Urgent</option>
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
                    <input type="date" name="start_date" id="start_date"
                           value="{{ old('start_date', $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') : '') }}"
                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 @error('start_date') border-red-300 @enderror">
                    @error('start_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- End Date -->
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" name="end_date" id="end_date"
                           value="{{ old('end_date', $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('Y-m-d') : '') }}"
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
                        <input type="number" name="budget" id="budget" value="{{ old('budget', $project->budget) }}" step="0.01" min="0"
                               class="block w-full pl-8 border-gray-300 rounded-lg focus:ring-violet-500 focus:border-violet-500 @error('budget') border-red-300 @enderror"
                               placeholder="0.00"
                               oninput="updateBudgetPreview('budget', 'budget_preview')">
                    </div>
                    <p id="budget_preview" class="mt-1 text-sm text-violet-600 font-medium hidden"></p>
                    @error('budget') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex items-center justify-between">
            <!-- Delete -->
            <div>
                <button type="button" onclick="confirmDelete()" class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Delete Project
                </button>
            </div>

            <div class="flex items-center space-x-4">
                <a href="{{ route('tenant.projects.show', [$tenant->slug, $project->id]) }}"
                   class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition ease-in-out duration-150">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center px-6 py-3 bg-violet-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition ease-in-out duration-150">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update Project
                </button>
            </div>
        </div>
    </form>

    <!-- Delete Form (hidden) -->
    <form id="deleteForm" action="{{ route('tenant.projects.destroy', [$tenant->slug, $project->id]) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
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
<script>
    // ── Budget thousands preview ──────────────────────────────
    function updateBudgetPreview(inputId, previewId) {
        const val   = parseFloat(document.getElementById(inputId).value);
        const el    = document.getElementById(previewId);
        if (isNaN(val) || val === 0) { el.classList.add('hidden'); el.textContent = ''; return; }
        el.textContent = '₦ ' + val.toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        el.classList.remove('hidden');
    }
    document.addEventListener('DOMContentLoaded', () => updateBudgetPreview('budget', 'budget_preview'));
</script>
<script>
    function openQuickAddClient() {
        document.getElementById('quickAddClientModal').classList.remove('hidden');
        document.getElementById('qc_first_name').focus();
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

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            closeQuickAddClient();
            closeQuickAddManager();
        }
    });

    // ── Quick Add Manager ──────────────────────────────
    function openQuickAddManager() {
        document.getElementById('quickAddManagerModal').classList.remove('hidden');
        document.getElementById('qm_first_name').focus();
        ['qm_first_name','qm_last_name','qm_email','qm_password'].forEach(id => {
            const el = document.getElementById(id);
            if (el) { el.value = ''; el.classList.remove('border-red-500'); }
        });
        document.getElementById('qm_role_id').value = '';
        showManagerAlert('', '');
    }

    function closeQuickAddManager() {
        document.getElementById('quickAddManagerModal').classList.add('hidden');
    }

    function toggleQmPassword() {
        const input = document.getElementById('qm_password');
        const isPass = input.type === 'password';
        input.type = isPass ? 'text' : 'password';
        document.getElementById('qm_eye_open').classList.toggle('hidden', isPass);
        document.getElementById('qm_eye_closed').classList.toggle('hidden', !isPass);
    }

    function showManagerAlert(message, type) {
        const alert = document.getElementById('quickAddManagerAlert');
        if (!message) { alert.classList.add('hidden'); return; }
        alert.className = 'rounded-lg p-3 text-sm ' + (type === 'error'
            ? 'bg-red-50 text-red-700 border border-red-200'
            : 'bg-green-50 text-green-700 border border-green-200');
        alert.textContent = message;
        alert.classList.remove('hidden');
    }

    function submitQuickAddManager() {
        const firstName = document.getElementById('qm_first_name').value.trim();
        const lastName  = document.getElementById('qm_last_name').value.trim();
        const email     = document.getElementById('qm_email').value.trim();
        const password  = document.getElementById('qm_password').value;
        const roleId    = document.getElementById('qm_role_id').value;

        if (!firstName || !lastName) { showManagerAlert('First and last name are required.', 'error'); return; }
        if (!email)    { showManagerAlert('Email is required.', 'error'); document.getElementById('qm_email').classList.add('border-red-500'); return; }
        if (password.length < 8) { showManagerAlert('Password must be at least 8 characters.', 'error'); return; }
        if (!roleId)   { showManagerAlert('Please select a role.', 'error'); return; }

        const btn     = document.getElementById('quickAddManagerSubmitBtn');
        const spinner = document.getElementById('quickAddManagerSpinner');
        btn.disabled  = true;
        spinner.classList.remove('hidden');

        const formData = new FormData();
        formData.append('first_name', firstName);
        formData.append('last_name', lastName);
        formData.append('email', email);
        formData.append('password', password);
        formData.append('password_confirmation', password);
        formData.append('role_id', roleId);
        formData.append('status', 'active');
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ route('tenant.admin.users.store', ['tenant' => $tenant->slug]) }}', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById('assigned_to');
                const option = new Option(data.display_name, data.user_id, true, true);
                select.add(option);
                closeQuickAddManager();
            } else {
                showManagerAlert(data.message || 'Failed to create user.', 'error');
            }
        })
        .catch(() => showManagerAlert('An unexpected error occurred. Please try again.', 'error'))
        .finally(() => { btn.disabled = false; spinner.classList.add('hidden'); });
    }
</script>
@endpush

@push('scripts')
<script>
    function confirmDelete() {
        if (confirm('Are you sure you want to delete this project? This action cannot be undone.')) {
            document.getElementById('deleteForm').submit();
        }
    }
</script>
@endpush

<!-- Quick Add Manager Modal -->
<template id="quickAddManagerModalTemplate">
</template>
<div id="quickAddManagerModal" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeQuickAddManager()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-violet-100 text-violet-600 mr-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </span>
                    Add New Project Manager
                </h3>
                <button type="button" onclick="closeQuickAddManager()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div id="quickAddManagerAlert" class="hidden rounded-lg p-3 text-sm"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="qm_first_name" class="block text-sm font-medium text-gray-700">First Name <span class="text-red-500">*</span></label>
                        <input type="text" id="qm_first_name" placeholder="John"
                               class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 text-sm">
                    </div>
                    <div>
                        <label for="qm_last_name" class="block text-sm font-medium text-gray-700">Last Name <span class="text-red-500">*</span></label>
                        <input type="text" id="qm_last_name" placeholder="Doe"
                               class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 text-sm">
                    </div>
                </div>
                <div>
                    <label for="qm_email" class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                    <input type="email" id="qm_email" placeholder="manager@example.com"
                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 text-sm">
                </div>
                <div>
                    <label for="qm_password" class="block text-sm font-medium text-gray-700">Password <span class="text-red-500">*</span></label>
                    <div class="relative mt-1">
                        <input type="password" id="qm_password" placeholder="Min. 8 characters"
                               class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 text-sm pr-10">
                        <button type="button" onclick="toggleQmPassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <svg id="qm_eye_open" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="qm_eye_closed" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div>
                    <label for="qm_role_id" class="block text-sm font-medium text-gray-700">Role <span class="text-red-500">*</span></label>
                    <select id="qm_role_id"
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 text-sm">
                        <option value="">— Select Role —</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex items-center justify-end space-x-3 px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                <button type="button" onclick="closeQuickAddManager()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="button" onclick="submitQuickAddManager()" id="quickAddManagerSubmitBtn"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-violet-600 rounded-lg hover:bg-violet-700 transition-colors disabled:opacity-60 disabled:cursor-not-allowed">
                    <svg id="quickAddManagerSpinner" class="hidden w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    Add Manager
                </button>
            </div>
        </div>
    </div>
</div>
