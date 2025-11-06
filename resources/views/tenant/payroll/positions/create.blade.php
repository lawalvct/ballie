@extends('layouts.tenant')

@section('title', 'Create Position')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 via-blue-600 to-cyan-600 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Create Position</h1>
                    <p class="text-blue-100 text-lg">Add a new position to your organization</p>
                </div>
                <a href="{{ route('tenant.payroll.positions.index', $tenant) }}"
                   class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl border border-white/20">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Positions
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-lg mb-8">
                    <div class="font-medium mb-2">Please correct the following errors:</div>
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('tenant.payroll.positions.store', $tenant) }}" method="POST">
                @csrf

                <!-- Basic Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">Basic Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Position Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="e.g., Senior Software Engineer">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Position Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="code" value="{{ old('code') }}" required maxlength="50"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="e.g., SSE-001">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" rows="3"
                                      class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Brief description of the position">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Organizational Structure -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">Organizational Structure</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                            <select name="department_id"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">-- No Department --</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Position Level <span class="text-red-500">*</span>
                            </label>
                            <select name="level" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">-- Select Level --</option>
                                <option value="1" {{ old('level') == '1' ? 'selected' : '' }}>1 - Entry Level</option>
                                <option value="2" {{ old('level') == '2' ? 'selected' : '' }}>2 - Junior</option>
                                <option value="3" {{ old('level') == '3' ? 'selected' : '' }}>3 - Mid-Level</option>
                                <option value="4" {{ old('level') == '4' ? 'selected' : '' }}>4 - Senior</option>
                                <option value="5" {{ old('level') == '5' ? 'selected' : '' }}>5 - Lead</option>
                                <option value="6" {{ old('level') == '6' ? 'selected' : '' }}>6 - Manager</option>
                                <option value="7" {{ old('level') == '7' ? 'selected' : '' }}>7 - Senior Manager</option>
                                <option value="8" {{ old('level') == '8' ? 'selected' : '' }}>8 - Director</option>
                                <option value="9" {{ old('level') == '9' ? 'selected' : '' }}>9 - Senior Director</option>
                                <option value="10" {{ old('level') == '10' ? 'selected' : '' }}>10 - Executive</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reports To Position</label>
                            <select name="reports_to_position_id"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">-- No Reporting Position --</option>
                                @foreach($parentPositions as $parentPosition)
                                    <option value="{{ $parentPosition->id }}" {{ old('reports_to_position_id') == $parentPosition->id ? 'selected' : '' }}>
                                        {{ $parentPosition->name }} ({{ $parentPosition->level_name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Salary Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">Salary Range</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Salary</label>
                            <input type="number" name="min_salary" value="{{ old('min_salary') }}" step="0.01" min="0"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="0.00">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Salary</label>
                            <input type="number" name="max_salary" value="{{ old('max_salary') }}" step="0.01" min="0"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="0.00">
                        </div>
                    </div>
                </div>

                <!-- Requirements & Responsibilities -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">Details</h3>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Requirements</label>
                            <textarea name="requirements" rows="4"
                                      class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="List the key requirements for this position">{{ old('requirements') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Responsibilities</label>
                            <textarea name="responsibilities" rows="4"
                                      class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="List the key responsibilities for this position">{{ old('responsibilities') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="mb-8">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm font-medium text-gray-700">Position is active</span>
                    </label>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('tenant.payroll.positions.index', $tenant) }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-medium transition-colors duration-300">
                        Cancel
                    </a>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-medium transition-colors duration-300">
                        <i class="fas fa-save mr-2"></i>Create Position
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
