@extends('layouts.tenant')

@section('title', 'Salary Components')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 via-blue-600 to-cyan-600 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Salary Components</h1>
                    <p class="text-indigo-100 text-lg">Manage allowances and deductions</p>
                </div>
                <button onclick="openCreateModal()"
                        class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl border border-white/20">
                    <i class="fas fa-plus mr-2"></i>Add Component
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-lg mb-8">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <!-- Components Tabs -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 mb-8">
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                                        <button onclick="showTab('earnings')"
                            class="tab-button px-8 py-4 rounded-t-lg font-semibold text-gray-600 border-b-2 border-transparent transition-all duration-300 hover:text-indigo-600 hover:border-indigo-600"
                            id="earnings-tab">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Earnings
                        <span class="ml-2 bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full">{{ $components->where('type', 'earning')->count() }}</span>
                    </button>
                    <button onclick="showTab('deductions')"
                            class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 transition-colors duration-200"
                            id="deductions-tab">
                        <i class="fas fa-minus-circle mr-2 text-red-500"></i>
                        Deductions
                    </button>
                </nav>
            </div>

            <!-- Earnings Tab Content -->
            <div id="earnings-content" class="tab-content p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach($components->where('type', 'earning') as $component)
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl border border-green-200 p-6 hover:shadow-lg transition-all duration-300">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $component->name }}</h3>
                                    <p class="text-green-600 font-medium text-sm">{{ $component->code }}</p>
                                </div>
                                <div class="relative">
                                    <button onclick="toggleDropdown({{ $component->id }})"
                                            class="text-gray-400 hover:text-gray-600 p-2 rounded-lg hover:bg-white/50">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div id="dropdown-{{ $component->id }}"
                                         class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10 hidden">
                                        <div class="py-1">
                                            <button onclick="editComponent({{ $component->id }}, '{{ $component->name }}', '{{ $component->code }}', '{{ $component->type }}', '{{ $component->calculation_type }}', '{{ $component->description }}', {{ $component->is_taxable ? 'true' : 'false' }}, {{ $component->is_pensionable ? 'true' : 'false' }})"
                                                    class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-edit mr-2"></i>Edit
                                            </button>
                                            <button onclick="deleteComponent({{ $component->id }})"
                                                    class="block w-full text-left px-4 py-2 text-red-600 hover:bg-red-50">
                                                <i class="fas fa-trash mr-2"></i>Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($component->description)
                                <p class="text-gray-600 text-sm mb-4">{{ $component->description }}</p>
                            @endif

                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">Type:</span>
                                    <span class="font-medium capitalize">{{ $component->calculation_type }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">Taxable:</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $component->is_taxable ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $component->is_taxable ? 'Yes' : 'No' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">Pensionable:</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $component->is_pensionable ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $component->is_pensionable ? 'Yes' : 'No' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if($components->where('type', 'allowance')->count() === 0)
                                            @if($components->where('type', 'earning')->count() === 0)
                        <div class="col-span-2 flex flex-col items-center justify-center py-16">
                            <div class="text-gray-400 mb-4">
                                <i class="fas fa-coins text-6xl"></i>
                            </div>
                            <h3 class="text-xl font-medium text-gray-900 mb-2">No earnings found</h3>
                            <p class="text-gray-500 mb-6">Create your first earning component.</p>
                            <button onclick="openCreateModal('earning')"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-300">
                                <i class="fas fa-plus mr-2"></i>Add Earning
                            </button>
                        </div>
                    @endif
                    @endif
                </div>
            </div>

            <!-- Deductions Tab Content -->
            <div id="deductions-content" class="tab-content p-6 hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($components->where('type', 'deduction') as $component)
                        <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-xl border border-red-200 p-6 hover:shadow-lg transition-all duration-300">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $component->name }}</h3>
                                    <p class="text-red-600 font-medium text-sm">{{ $component->code }}</p>
                                </div>
                                <div class="relative">
                                    <button onclick="toggleDropdown({{ $component->id }})"
                                            class="text-gray-400 hover:text-gray-600 p-2 rounded-lg hover:bg-white/50">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div id="dropdown-{{ $component->id }}"
                                         class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10 hidden">
                                        <div class="py-1">
                                            <button onclick="editComponent({{ $component->id }}, '{{ $component->name }}', '{{ $component->code }}', '{{ $component->type }}', '{{ $component->calculation_type }}', '{{ $component->description }}', {{ $component->is_taxable ? 'true' : 'false' }}, {{ $component->is_pensionable ? 'true' : 'false' }})"
                                                    class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-edit mr-2"></i>Edit
                                            </button>
                                            <button onclick="deleteComponent({{ $component->id }})"
                                                    class="block w-full text-left px-4 py-2 text-red-600 hover:bg-red-50">
                                                <i class="fas fa-trash mr-2"></i>Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($component->description)
                                <p class="text-gray-600 text-sm mb-4">{{ $component->description }}</p>
                            @endif

                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">Type:</span>
                                    <span class="font-medium capitalize">{{ $component->calculation_type }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">Taxable:</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $component->is_taxable ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $component->is_taxable ? 'Yes' : 'No' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">Pensionable:</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $component->is_pensionable ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $component->is_pensionable ? 'Yes' : 'No' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if($components->where('type', 'deduction')->count() === 0)
                        <div class="col-span-full text-center py-12">
                            <i class="fas fa-minus-circle text-6xl text-red-300 mb-4"></i>
                            <h3 class="text-xl font-medium text-gray-900 mb-2">No deductions found</h3>
                            <p class="text-gray-500 mb-6">Create your first deduction component.</p>
                            <button onclick="openCreateModal('deduction')"
                                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-300">
                                <i class="fas fa-plus mr-2"></i>Add Deduction
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Component Modal -->
<div id="componentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 id="modalTitle" class="text-xl font-bold text-gray-900">Add Component</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <form id="componentForm" action="{{ route('tenant.payroll.components.store', $tenant) }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" id="componentId" name="component_id">
            <input type="hidden" id="formMethod" name="_method" value="POST">

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Component Name <span class="text-red-500">*</span></label>
                    <input type="text" id="componentName" name="name" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Component Code <span class="text-red-500">*</span></label>
                    <input type="text" id="componentCode" name="code" required maxlength="10"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Maximum 10 characters, used for payslips</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type <span class="text-red-500">*</span></label>
                        <select id="componentType" name="type" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="earning">Earning/Allowance</option>
                            <option value="deduction">Deduction</option>
                            <option value="employer_contribution">Employer Contribution</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Calculation <span class="text-red-500">*</span></label>
                        <select id="calculationType" name="calculation_type" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="fixed">Fixed Amount</option>
                            <option value="percentage">Percentage</option>
                            <option value="variable">Variable</option>
                            <option value="computed">Computed</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex items-center">
                        <input type="checkbox" id="isTaxable" name="is_taxable" value="1"
                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="isTaxable" class="ml-2 text-sm font-medium text-gray-700">Is Taxable</label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="isPensionable" name="is_pensionable" value="1"
                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="isPensionable" class="ml-2 text-sm font-medium text-gray-700">Is Pensionable</label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="componentDescription" name="description" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-8">
                <button type="button" onclick="closeModal()"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-300">
                    Cancel
                </button>
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-300">
                    <span id="submitText">Create Component</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Tab functionality
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Remove active styles from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-indigo-500', 'text-indigo-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });

    // Show selected tab content
    document.getElementById(tabName + '-content').classList.remove('hidden');

    // Add active styles to selected tab
    const activeTab = document.getElementById(tabName + '-tab');
    activeTab.classList.add('border-indigo-500', 'text-indigo-600');
    activeTab.classList.remove('border-transparent', 'text-gray-500');
}

// Initialize with earnings tab active
document.addEventListener('DOMContentLoaded', () => {
    showTab('earnings');
});

function openCreateModal(type = 'earning') {
    document.getElementById('modalTitle').textContent = 'Add Component';
    document.getElementById('submitText').textContent = 'Create Component';
    document.getElementById('componentForm').action = "{{ route('tenant.payroll.components.store', $tenant) }}";
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('componentId').value = '';
    document.getElementById('componentName').value = '';
    document.getElementById('componentCode').value = '';
    document.getElementById('componentType').value = type;
    document.getElementById('calculationType').value = 'fixed';
    document.getElementById('componentDescription').value = '';
    document.getElementById('isTaxable').checked = false;
    document.getElementById('isPensionable').checked = false;
    document.getElementById('componentModal').classList.remove('hidden');
}

function editComponent(id, name, code, type, calculationType, description, isTaxable, isPensionable) {
    document.getElementById('modalTitle').textContent = 'Edit Component';
    document.getElementById('submitText').textContent = 'Update Component';
    document.getElementById('componentForm').action = "{{ route('tenant.payroll.components.index', $tenant) }}/" + id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('componentId').value = id;
    document.getElementById('componentName').value = name;
    document.getElementById('componentCode').value = code;
    document.getElementById('componentType').value = type;
    document.getElementById('calculationType').value = calculationType;
    document.getElementById('componentDescription').value = description || '';
    document.getElementById('isTaxable').checked = isTaxable;
    document.getElementById('isPensionable').checked = isPensionable;
    document.getElementById('componentModal').classList.remove('hidden');

    // Close dropdown
    document.getElementById(`dropdown-${id}`).classList.add('hidden');
}

function closeModal() {
    document.getElementById('componentModal').classList.add('hidden');
}

function toggleDropdown(id) {
    const dropdown = document.getElementById(`dropdown-${id}`);
    const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');

    // Close all other dropdowns
    allDropdowns.forEach(d => {
        if (d.id !== `dropdown-${id}`) {
            d.classList.add('hidden');
        }
    });

    // Toggle current dropdown
    dropdown.classList.toggle('hidden');
}

function deleteComponent(id) {
    if (confirm('Are you sure you want to delete this component? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('tenant.payroll.components.index', $tenant) }}/" + id;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';

        form.appendChild(csrfToken);
        form.appendChild(methodInput);
        document.body.appendChild(form);
        form.submit();
    }

    // Close dropdown
    document.getElementById(`dropdown-${id}`).classList.add('hidden');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick^="toggleDropdown"]')) {
        const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
        allDropdowns.forEach(d => d.classList.add('hidden'));
    }
});

// Close modal when clicking outside
document.getElementById('componentModal').addEventListener('click', function(event) {
    if (event.target === this) {
        closeModal();
    }
});
</script>
@endsection
