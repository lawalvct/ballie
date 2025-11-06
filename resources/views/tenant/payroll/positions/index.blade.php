@extends('layouts.tenant')

@section('title', 'Positions')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 via-blue-600 to-cyan-600 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Positions</h1>
                    <p class="text-blue-100 text-lg">Manage organizational positions and hierarchies</p>
                </div>
                <button onclick="openCreateModal()"
                        class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl border border-white/20">
                    <i class="fas fa-plus mr-2"></i>Add Position
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

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-lg mb-8">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Filter Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <form method="GET" class="flex items-center gap-4">
                <div class="flex-1">
                    <select name="department_id" onchange="this.form.submit()"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1">
                    <select name="level" onchange="this.form.submit()"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Levels</option>
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ request('level') == $i ? 'selected' : '' }}>
                                Level {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="flex-1">
                    <select name="status" onchange="this.form.submit()"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Positions Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($positions as $position)
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300 {{ !$position->is_active ? 'opacity-60' : '' }}">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="text-xl font-bold text-gray-900">{{ $position->name }}</h3>
                                @if(!$position->is_active)
                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full">Inactive</span>
                                @endif
                            </div>
                            <p class="text-gray-500 text-sm font-medium">{{ $position->code }}</p>
                            @if($position->department)
                                <p class="text-blue-600 text-xs mt-1">{{ $position->department->name }}</p>
                            @endif
                        </div>
                        <div class="relative">
                            <button onclick="toggleDropdown({{ $position->id }})"
                                    class="text-gray-400 hover:text-gray-600 p-2 rounded-lg hover:bg-gray-100">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div id="dropdown-{{ $position->id }}"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10 hidden">
                                <div class="py-1">
                                    <a href="{{ route('tenant.payroll.positions.show', [$tenant, $position]) }}"
                                       class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-eye mr-2"></i>View Details
                                    </a>
                                    <a href="{{ route('tenant.payroll.positions.edit', [$tenant, $position]) }}"
                                       class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-edit mr-2"></i>Edit
                                    </a>
                                    <form action="{{ route('tenant.payroll.positions.toggle-status', [$tenant, $position]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-toggle-{{ $position->is_active ? 'off' : 'on' }} mr-2"></i>
                                            {{ $position->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                    <button onclick="deletePosition({{ $position->id }})"
                                            class="block w-full text-left px-4 py-2 text-red-600 hover:bg-red-50">
                                        <i class="fas fa-trash mr-2"></i>Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($position->description)
                        <p class="text-gray-600 text-sm mb-4">{{ Str::limit($position->description, 80) }}</p>
                    @endif

                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm">
                            <span class="text-gray-500 w-24">Level:</span>
                            <span class="font-medium text-gray-700">{{ $position->level_name }}</span>
                        </div>
                        @if($position->reportsTo)
                            <div class="flex items-center text-sm">
                                <span class="text-gray-500 w-24">Reports to:</span>
                                <span class="font-medium text-gray-700">{{ $position->reportsTo->name }}</span>
                            </div>
                        @endif
                        @if($position->min_salary || $position->max_salary)
                            <div class="flex items-center text-sm">
                                <span class="text-gray-500 w-24">Salary:</span>
                                <span class="font-medium text-gray-700">{{ $position->salary_range }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-users mr-2 text-blue-500"></i>
                            <span>{{ $position->employees_count }} {{ Str::plural('employee', $position->employees_count) }}</span>
                        </div>
                        <a href="{{ route('tenant.payroll.positions.show', [$tenant, $position]) }}"
                           class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                            View Details →
                        </a>
                    </div>
                </div>
            @endforeach

            @if($positions->count() === 0)
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-briefcase text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">No positions found</h3>
                    <p class="text-gray-500 mb-6">Get started by creating your first position.</p>
                    <button onclick="openCreateModal()"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-300">
                        <i class="fas fa-plus mr-2"></i>Add Position
                    </button>
                </div>
            @endif
        </div>

        <!-- Pagination -->
        @if($positions->hasPages())
            <div class="mt-8">
                {{ $positions->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Create Position Modal -->
<div id="positionModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Add Position</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <form action="{{ route('tenant.payroll.positions.create', $tenant) }}" method="GET" class="p-6">
            <div class="text-center">
                <p class="text-gray-600 mb-6">Create a new position with detailed information, salary range, and reporting structure.</p>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-300">
                    Continue to Form
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreateModal() {
    window.location.href = "{{ route('tenant.payroll.positions.create', $tenant) }}";
}

function closeModal() {
    document.getElementById('positionModal').classList.add('hidden');
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

function deletePosition(id) {
    if (confirm('Are you sure you want to delete this position? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/tenant/{{ $tenant->id }}/payroll/positions/${id}`;

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
const modal = document.getElementById('positionModal');
if (modal) {
    modal.addEventListener('click', function(event) {
        if (event.target === this) {
            closeModal();
        }
    });
}
</script>
@endsection
