@extends('layouts.tenant')

@section('title', $position->name . ' - Position Details')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 via-blue-600 to-cyan-600 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-3xl font-bold text-white">{{ $position->name }}</h1>
                        @if(!$position->is_active)
                            <span class="px-3 py-1 bg-white/20 text-white rounded-full text-sm">Inactive</span>
                        @endif
                    </div>
                    <p class="text-blue-100 text-lg">{{ $position->code }}</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('tenant.payroll.positions.edit', [$tenant, $position]) }}"
                       class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl border border-white/20">
                        <i class="fas fa-edit mr-2"></i>Edit Position
                    </a>
                    <a href="{{ route('tenant.payroll.positions.index', $tenant) }}"
                       class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl border border-white/20">
                        <i class="fas fa-arrow-left mr-2"></i>Back
                    </a>
                </div>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Position Details Card -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 pb-3 border-b border-gray-200">Position Details</h2>

                    <div class="space-y-4">
                        @if($position->description)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Description</label>
                                <p class="mt-1 text-gray-900">{{ $position->description }}</p>
                            </div>
                        @endif

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Position Level</label>
                                <p class="mt-1 text-gray-900 font-medium">{{ $position->level_name }}</p>
                            </div>

                            @if($position->department)
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Department</label>
                                    <p class="mt-1 text-gray-900 font-medium">{{ $position->department->name }}</p>
                                </div>
                            @endif

                            @if($position->reportsTo)
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Reports To</label>
                                    <a href="{{ route('tenant.payroll.positions.show', [$tenant, $position->reportsTo]) }}"
                                       class="mt-1 text-blue-600 hover:text-blue-700 font-medium block">
                                        {{ $position->reportsTo->name }}
                                    </a>
                                </div>
                            @endif

                            @if($position->min_salary || $position->max_salary)
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Salary Range</label>
                                    <p class="mt-1 text-gray-900 font-medium">{{ $position->salary_range }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Requirements -->
                @if($position->requirements)
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 pb-3 border-b border-gray-200">
                            <i class="fas fa-clipboard-check text-blue-600 mr-2"></i>Requirements
                        </h2>
                        <div class="text-gray-700 whitespace-pre-line">{{ $position->requirements }}</div>
                    </div>
                @endif

                <!-- Responsibilities -->
                @if($position->responsibilities)
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 pb-3 border-b border-gray-200">
                            <i class="fas fa-tasks text-green-600 mr-2"></i>Responsibilities
                        </h2>
                        <div class="text-gray-700 whitespace-pre-line">{{ $position->responsibilities }}</div>
                    </div>
                @endif

                <!-- Employees in this Position -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                    <div class="flex items-center justify-between mb-6 pb-3 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900">
                            <i class="fas fa-users text-purple-600 mr-2"></i>
                            Employees ({{ $position->employees->count() }})
                        </h2>
                        @if($position->employees->count() > 0)
                            <a href="{{ route('tenant.payroll.employees.index', ['tenant' => $tenant, 'position' => $position->id]) }}"
                               class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                View All →
                            </a>
                        @endif
                    </div>

                    @if($position->employees->count() > 0)
                        <div class="space-y-3">
                            @foreach($position->employees->take(10) as $employee)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                                            {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ $employee->first_name }} {{ $employee->last_name }}</h4>
                                            <p class="text-sm text-gray-500">{{ $employee->employee_number }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @if($employee->department)
                                            <p class="text-sm text-gray-600">{{ $employee->department->name }}</p>
                                        @endif
                                        <span class="inline-block px-3 py-1 text-xs rounded-full
                                            {{ $employee->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($employee->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach

                            @if($position->employees->count() > 10)
                                <div class="text-center pt-4">
                                    <a href="{{ route('tenant.payroll.employees.index', ['tenant' => $tenant, 'position' => $position->id]) }}"
                                       class="text-blue-600 hover:text-blue-700 font-medium">
                                        View {{ $position->employees->count() - 10 }} more employees →
                                    </a>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-users text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">No employees assigned to this position yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Statistics Card -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Statistics</h3>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-users text-blue-600"></i>
                                <span class="text-sm font-medium text-gray-700">Total Employees</span>
                            </div>
                            <span class="text-lg font-bold text-blue-600">{{ $position->employees->count() }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-user-check text-green-600"></i>
                                <span class="text-sm font-medium text-gray-700">Active Employees</span>
                            </div>
                            <span class="text-lg font-bold text-green-600">{{ $position->activeEmployees->count() }}</span>
                        </div>

                        @if($position->subordinates->count() > 0)
                            <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-sitemap text-purple-600"></i>
                                    <span class="text-sm font-medium text-gray-700">Subordinate Positions</span>
                                </div>
                                <span class="text-lg font-bold text-purple-600">{{ $position->subordinates->count() }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Subordinate Positions -->
                @if($position->subordinates->count() > 0)
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            <i class="fas fa-sitemap text-purple-600 mr-2"></i>Reports to This Position
                        </h3>

                        <div class="space-y-3">
                            @foreach($position->subordinates as $subordinate)
                                <a href="{{ route('tenant.payroll.positions.show', [$tenant, $subordinate]) }}"
                                   class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <h4 class="font-semibold text-gray-900">{{ $subordinate->name }}</h4>
                                    <p class="text-sm text-gray-500">{{ $subordinate->code }} • Level {{ $subordinate->level }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $subordinate->employees->count() }} employees</p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>

                    <div class="space-y-2">
                        <a href="{{ route('tenant.payroll.positions.edit', [$tenant, $position]) }}"
                           class="block w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-center font-medium transition-colors">
                            <i class="fas fa-edit mr-2"></i>Edit Position
                        </a>

                        <form action="{{ route('tenant.payroll.positions.toggle-status', [$tenant, $position]) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="block w-full px-4 py-3 {{ $position->is_active ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-lg text-center font-medium transition-colors">
                                <i class="fas fa-toggle-{{ $position->is_active ? 'off' : 'on' }} mr-2"></i>
                                {{ $position->is_active ? 'Deactivate' : 'Activate' }} Position
                            </button>
                        </form>

                        @if(!$position->hasEmployees() && $position->subordinates->count() === 0)
                            <button onclick="confirmDelete()"
                                    class="block w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg text-center font-medium transition-colors">
                                <i class="fas fa-trash mr-2"></i>Delete Position
                            </button>

                            <form id="deleteForm" action="{{ route('tenant.payroll.positions.destroy', [$tenant, $position]) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        @else
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-500">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Cannot delete position with employees or subordinates
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Timeline -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Timeline</h3>

                    <div class="space-y-3 text-sm">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-plus-circle text-green-500 mt-1"></i>
                            <div>
                                <p class="font-medium text-gray-900">Position Created</p>
                                <p class="text-gray-500">{{ $position->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        @if($position->updated_at->ne($position->created_at))
                            <div class="flex items-start gap-3">
                                <i class="fas fa-edit text-blue-500 mt-1"></i>
                                <div>
                                    <p class="font-medium text-gray-900">Last Updated</p>
                                    <p class="text-gray-500">{{ $position->updated_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete this position? This action cannot be undone.')) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
@endsection
