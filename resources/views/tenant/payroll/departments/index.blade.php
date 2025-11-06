@extends('layouts.tenant')

@section('title', 'Departments')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 via-pink-600 to-red-600 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Departments</h1>
                    <p class="text-purple-100 text-lg">Manage organizational departments</p>
                </div>
                <button onclick="openCreateModal()"
                        class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl border border-white/20">
                    <i class="fas fa-plus mr-2"></i>Add Department
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

        <!-- Departments Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($departments as $department)
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $department->name }}</h3>
                            <p class="text-gray-500 text-sm font-medium">{{ $department->code }}</p>
                        </div>
                        <div class="relative">
                            <button onclick="toggleDropdown({{ $department->id }})"
                                    class="text-gray-400 hover:text-gray-600 p-2 rounded-lg hover:bg-gray-100">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div id="dropdown-{{ $department->id }}"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10 hidden">
                                <div class="py-1">
                                    <button onclick="editDepartment({{ $department->id }}, '{{ $department->name }}', '{{ $department->code }}', '{{ $department->description }}')"
                                            class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-edit mr-2"></i>Edit
                                    </button>
                                    <button onclick="deleteDepartment({{ $department->id }})"
                                            class="block w-full text-left px-4 py-2 text-red-600 hover:bg-red-50">
                                        <i class="fas fa-trash mr-2"></i>Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($department->description)
                        <p class="text-gray-600 text-sm mb-4">{{ $department->description }}</p>
                    @endif

                    <div class="flex items-center justify-between">
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-users mr-2 text-blue-500"></i>
                            <span>{{ $department->employees_count }} {{ Str::plural('employee', $department->employees_count) }}</span>
                        </div>
                        <a href="{{ route('tenant.payroll.employees.index', ['tenant' => $tenant, 'department' => $department->id]) }}"
                           class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                            View Employees →
                        </a>
                    </div>
                </div>
            @endforeach

            @if($departments->count() === 0)
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-building text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">No departments found</h3>
                    <p class="text-gray-500 mb-6">Get started by creating your first department.</p>
                    <button onclick="openCreateModal()"
                            class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-300">
                        <i class="fas fa-plus mr-2"></i>Add Department
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Create/Edit Department Modal -->
<div id="departmentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 id="modalTitle" class="text-xl font-bold text-gray-900">Add Department</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <form id="departmentForm" action="{{ route('tenant.payroll.departments.store', $tenant) }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" id="departmentId" name="department_id">
            <input type="hidden" id="formMethod" name="_method" value="POST">

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department Name <span class="text-red-500">*</span></label>
                    <input type="text" id="departmentName" name="name" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department Code <span class="text-red-500">*</span></label>
                    <input type="text" id="departmentCode" name="code" required maxlength="10"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Maximum 10 characters</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="departmentDescription" name="description" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent"></textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-8">
                <button type="button" onclick="closeModal()"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-300">
                    Cancel
                </button>
                <button type="submit"
                        class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-300">
                    <span id="submitText">Create Department</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreateModal() {
    document.getElementById('modalTitle').textContent = 'Add Department';
    document.getElementById('submitText').textContent = 'Create Department';
    document.getElementById('departmentForm').action = "{{ route('tenant.payroll.departments.store', $tenant) }}";
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('departmentId').value = '';
    document.getElementById('departmentName').value = '';
    document.getElementById('departmentCode').value = '';
    document.getElementById('departmentDescription').value = '';
    document.getElementById('departmentModal').classList.remove('hidden');
}

function editDepartment(id, name, code, description) {
    document.getElementById('modalTitle').textContent = 'Edit Department';
    document.getElementById('submitText').textContent = 'Update Department';
    document.getElementById('departmentForm').action = `/tenant/{{ $tenant->id }}/payroll/departments/${id}`;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('departmentId').value = id;
    document.getElementById('departmentName').value = name;
    document.getElementById('departmentCode').value = code;
    document.getElementById('departmentDescription').value = description || '';
    document.getElementById('departmentModal').classList.remove('hidden');

    // Close dropdown
    document.getElementById(`dropdown-${id}`).classList.add('hidden');
}

function closeModal() {
    document.getElementById('departmentModal').classList.add('hidden');
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

function deleteDepartment(id) {
    if (confirm('Are you sure you want to delete this department? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/tenant/{{ $tenant->id }}/payroll/departments/${id}`;

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
document.getElementById('departmentModal').addEventListener('click', function(event) {
    if (event.target === this) {
        closeModal();
    }
});
</script>
@endsection
