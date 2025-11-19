@extends('payroll.portal.layout')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="space-y-6">
    <!-- Personal Information -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-user mr-2"></i>
                Personal Information
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Full Name</label>
                    <p class="text-base font-semibold text-gray-900">{{ $employee->first_name }} {{ $employee->last_name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Employee Number</label>
                    <p class="text-base font-semibold text-gray-900">{{ $employee->employee_number }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
                    <p class="text-base text-gray-900">{{ $employee->email ?? 'Not provided' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Phone</label>
                    <p class="text-base text-gray-900">{{ $employee->phone ?? 'Not provided' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Date of Birth</label>
                    <p class="text-base text-gray-900">
                        {{ $employee->date_of_birth ? $employee->date_of_birth->format('F d, Y') : 'Not provided' }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Gender</label>
                    <p class="text-base text-gray-900">{{ $employee->gender ? ucfirst($employee->gender) : 'Not specified' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Marital Status</label>
                    <p class="text-base text-gray-900">{{ $employee->marital_status ? ucfirst($employee->marital_status) : 'Not specified' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">State of Origin</label>
                    <p class="text-base text-gray-900">{{ $employee->state_of_origin ?? 'Not provided' }}</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Address</label>
                    <p class="text-base text-gray-900">{{ $employee->address ?? 'Not provided' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Employment Information -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-teal-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-briefcase mr-2"></i>
                Employment Information
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Job Title</label>
                    <p class="text-base font-semibold text-gray-900">{{ $employee->job_title }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Department</label>
                    <p class="text-base text-gray-900">{{ $employee->department->name ?? 'Not assigned' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Position</label>
                    <p class="text-base text-gray-900">{{ $employee->position->name ?? 'Not assigned' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Employment Type</label>
                    <p class="text-base text-gray-900">{{ $employee->employment_type ? ucfirst($employee->employment_type) : 'Not specified' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Hire Date</label>
                    <p class="text-base text-gray-900">{{ $employee->hire_date ? $employee->hire_date->format('F d, Y') : 'Not provided' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Pay Frequency</label>
                    <p class="text-base text-gray-900">{{ $employee->pay_frequency ? ucfirst($employee->pay_frequency) : 'Not set' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Status</label>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        {{ $employee->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $employee->status === 'suspended' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $employee->status === 'terminated' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ ucfirst($employee->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bank Information -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-university mr-2"></i>
                Bank Information
            </h2>
        </div>
        <div class="p-6">
            @if($employee->bank_name || $employee->account_number)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Bank Name</label>
                        <p class="text-base text-gray-900">{{ $employee->bank_name ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Account Number</label>
                        <p class="text-base font-mono text-gray-900">{{ $employee->account_number ?? 'Not provided' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Account Name</label>
                        <p class="text-base text-gray-900">{{ $employee->account_name ?? 'Not provided' }}</p>
                    </div>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-university text-4xl mb-3 text-gray-400"></i>
                    <p>No bank information provided</p>
                    <p class="text-sm mt-2">Contact HR to update your bank details</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Update Profile Form -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-edit mr-2"></i>
                Update Contact Information
            </h2>
        </div>
        <div class="p-6">
            <form method="POST" action="{{ route('payroll.portal.profile.update', ['tenant' => $tenant, 'token' => $token]) }}">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number
                        </label>
                        <input type="tel"
                               name="phone"
                               id="phone"
                               value="{{ old('phone', $employee->phone) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Enter your phone number">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            Address
                        </label>
                        <textarea name="address"
                                  id="address"
                                  rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Enter your address">{{ old('address', $employee->address) }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <h3 class="text-base font-semibold text-gray-900 mb-4">Emergency Contact</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Emergency Contact Name
                                </label>
                                <input type="text"
                                       name="emergency_contact_name"
                                       id="emergency_contact_name"
                                       value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="Contact person name">
                                @error('emergency_contact_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Emergency Contact Phone
                                </label>
                                <input type="tel"
                                       name="emergency_contact_phone"
                                       id="emergency_contact_phone"
                                       value="{{ old('emergency_contact_phone', $employee->emergency_contact_phone) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="Contact phone number">
                                @error('emergency_contact_phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-4 border-t border-gray-200">
                        <button type="submit"
                                class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                            <i class="fas fa-save mr-2"></i>
                            Update Profile
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tax & Pension Information (Read-only) -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-orange-600 to-red-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-file-invoice-dollar mr-2"></i>
                Tax & Pension Information
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Tax Identification Number (TIN)</label>
                    <p class="text-base font-mono text-gray-900">{{ $employee->tin ?? 'Not provided' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Annual Tax Relief</label>
                    <p class="text-base text-gray-900">₦{{ number_format($employee->annual_relief ?? 200000, 2) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Pension PIN</label>
                    <p class="text-base font-mono text-gray-900">{{ $employee->pension_pin ?? 'Not provided' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Pension Fund Administrator</label>
                    <p class="text-base text-gray-900">{{ $employee->pfa_name ?? 'Not provided' }}</p>
                </div>
            </div>
            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm text-yellow-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    To update tax and pension information, please contact the HR department.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
