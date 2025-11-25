@extends('layouts.tenant')

@section('title', 'Employee Details')
@section('page-title', 'Employee Details')
@section('page-description', 'Employee Details and Information')
@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header with Employee Info -->
    <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-6">
                    <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center overflow-hidden">
                        @if($employee->avatar)
                            <img src="{{ asset($employee->avatar) }}"
                                 alt="{{ $employee->first_name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <i class="fas fa-user-circle text-3xl text-white"></i>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-1">
                            {{ $employee->first_name }} {{ $employee->last_name }}
                        </h1>
                        <p class="text-blue-100 text-lg">{{ $employee->job_title }}</p>
                        <p class="text-blue-200 text-sm">
                            <i class="fas fa-id-card mr-1"></i>{{ $employee->employee_number }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium
                        {{ $employee->status === 'active' ? 'bg-green-500/20 text-green-100' : 'bg-red-500/20 text-red-100' }}">
                        <span class="w-2 h-2 rounded-full mr-2
                            {{ $employee->status === 'active' ? 'bg-green-400' : 'bg-red-400' }}"></span>
                        {{ ucfirst($employee->status) }}
                    </span>
                    <a href="{{ route('tenant.payroll.employees.index', ['tenant' => $tenant->slug]) }}"
                       class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl border border-white/20">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Employees
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Personal Information -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Personal Details Card -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-user mr-2 text-blue-500"></i>
                        Personal Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                            <p class="text-gray-900">{{ $employee->email ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Phone</label>
                            <p class="text-gray-900">{{ $employee->phone ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Date of Birth</label>
                            <p class="text-gray-900">
                                {{ $employee->date_of_birth ? $employee->date_of_birth->format('M d, Y') : 'Not provided' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Gender</label>
                            <p class="text-gray-900">{{ $employee->gender ? ucfirst($employee->gender) : 'Not specified' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Address</label>
                            <p class="text-gray-900">{{ $employee->address ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Employment Details Card -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-briefcase mr-2 text-green-500"></i>
                        Employment Details
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Department</label>
                            <p class="text-gray-900">{{ $employee->department->name ?? 'Not assigned' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Position</label>
                            <p class="text-gray-900">
                                @if($employee->position)
                                    {{ $employee->position->name }}
                                    <span class="text-gray-500 text-sm">({{ $employee->position->code }})</span>
                                @else
                                    Not assigned
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Job Title</label>
                            <p class="text-gray-900">{{ $employee->job_title ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Employment Type</label>
                            <p class="text-gray-900">{{ $employee->employment_type ? ucfirst($employee->employment_type) : 'Not specified' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Hire Date</label>
                            <p class="text-gray-900">{{ $employee->hire_date->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Pay Frequency</label>
                            <p class="text-gray-900">{{ $employee->pay_frequency ? ucfirst($employee->pay_frequency) : 'Not set' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Bank Information Card -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-university mr-2 text-purple-500"></i>
                        Bank Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Bank Name</label>
                            <p class="text-gray-900">{{ $employee->bank_name ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Account Number</label>
                            <p class="text-gray-900">{{ $employee->account_number ?? 'Not provided' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Account Name</label>
                            <p class="text-gray-900">{{ $employee->account_name ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Employee Documents -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-file-alt mr-2 text-indigo-500"></i>
                            Employee Documents
                        </h3>
                        <button onclick="document.getElementById('uploadModal').classList.remove('hidden')"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            <i class="fas fa-upload mr-2"></i>Upload Document
                        </button>
                    </div>

                    @if($employee->documents && $employee->documents->count() > 0)
                        <div class="space-y-3">
                            @foreach($employee->documents as $document)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-file-pdf text-indigo-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $document->document_name }}</p>
                                            <p class="text-xs text-gray-500">{{ $document->document_type }} • {{ $document->created_at->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('tenant.payroll.employees.documents.download', ['tenant' => $tenant->slug, 'employee' => $employee->id, 'document' => $document->id]) }}"
                                           class="text-blue-600 hover:text-blue-800 p-2">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <form action="{{ route('tenant.payroll.employees.documents.delete', ['tenant' => $tenant->slug, 'employee' => $employee->id, 'document' => $document->id]) }}"
                                              method="POST" onsubmit="return confirm('Are you sure you want to delete this document?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 p-2">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No documents uploaded yet.</p>
                    @endif
                </div>

                <!-- Recent Payroll Activity -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-history mr-2 text-orange-500"></i>
                        Recent Payroll Activity
                    </h3>

                    @if($employee->payrollRuns && $employee->payrollRuns->count() > 0)
                        <div class="space-y-4">
                            @foreach($employee->payrollRuns as $payrollRun)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900">
                                            {{ $payrollRun->payrollPeriod->name ?? 'Payroll Run' }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $payrollRun->created_at->format('M d, Y') }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-emerald-600">
                                            ₦{{ number_format($payrollRun->net_salary ?? 0, 2) }}
                                        </p>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            {{ $payrollRun->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($payrollRun->payment_status ?? 'pending') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No payroll activity yet.</p>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Current Salary Card -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-money-bill-wave mr-2 text-green-500"></i>
                        Current Salary
                    </h3>

                    @if($employee->currentSalary)
                        <div class="space-y-4">
                            <div class="text-center">
                                <p class="text-3xl font-bold text-emerald-600">
                                    ₦{{ number_format($employee->currentSalary->basic_salary, 2) }}
                                </p>
                                <p class="text-sm text-gray-500">Basic Salary</p>
                            </div>

                            @if($employee->currentSalary->salaryComponents && $employee->currentSalary->salaryComponents->count() > 0)
                                <div class="border-t pt-4">
                                    <h4 class="font-medium text-gray-900 mb-2">Salary Components</h4>
                                    <div class="space-y-2">
                                        @foreach($employee->currentSalary->salaryComponents as $component)
                                            <div class="flex justify-between items-center text-sm">
                                                <span class="text-gray-600">{{ $component->salaryComponent->name }}</span>
                                                <span class="font-medium text-gray-900">
                                                    @if($component->amount)
                                                        ₦{{ number_format($component->amount, 2) }}
                                                    @else
                                                        {{ $component->percentage }}%
                                                    @endif
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No salary information set</p>
                    @endif
                </div>

                <!-- Portal Access -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-key mr-2 text-indigo-500"></i>
                        Portal Access
                    </h3>

                    <div class="space-y-4">
                        <!-- Portal Link Display -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Employee Portal Link</label>
                            <div class="relative">
                                <input type="text" readonly
                                       id="portalLink"
                                       value="{{ $employee->portal_link }}"
                                       class="w-full px-3 py-2 pr-20 border border-gray-300 rounded-lg bg-gray-50 text-sm font-mono">
                                <button onclick="copyPortalLink()"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition-colors">
                                    <i class="fas fa-copy mr-1"></i>Copy
                                </button>
                            </div>
                        </div>

                        <!-- Token Status -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Token Status</p>
                                <p class="text-xs text-gray-500">
                                    @if($employee->hasValidPortalToken())
                                        Expires: {{ $employee->portal_token_expires_at->format('M d, Y') }}
                                    @else
                                        <span class="text-red-600">Expired or Invalid</span>
                                    @endif
                                </p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                {{ $employee->hasValidPortalToken() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                @if($employee->hasValidPortalToken())
                                    <i class="fas fa-check-circle mr-1"></i>Active
                                @else
                                    <i class="fas fa-times-circle mr-1"></i>Inactive
                                @endif
                            </span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-2">
                            <button onclick="sharePortalLink()"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors text-sm">
                                <i class="fas fa-share-alt mr-2"></i>Share via Email/WhatsApp
                            </button>
                            <button onclick="resetPortalToken()"
                                    class="w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg font-medium transition-colors text-sm">
                                <i class="fas fa-sync-alt mr-2"></i>Reset Portal Link
                            </button>
                        </div>

                        <!-- Help Text -->
                        <div class="p-3 bg-blue-50 rounded-lg">
                            <p class="text-xs text-blue-800">
                                <i class="fas fa-info-circle mr-1"></i>
                                Share this link with the employee to access their self-service portal for attendance, payslips, and more.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>

                    <div class="space-y-3">
                        <a href="{{ route('tenant.payroll.employees.edit', ['tenant' => $tenant->slug, 'employee' => $employee->id]) }}"
                           class="block w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-medium transition-colors duration-300 text-center">
                            <i class="fas fa-edit mr-2"></i>Edit Employee
                        </a>
                        <a href="{{ route('tenant.payroll.employees.payslip', ['tenant' => $tenant->slug, 'employee' => $employee->id]) }}"
                           class="block w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg font-medium transition-colors duration-300 text-center">
                            <i class="fas fa-money-check-alt mr-2"></i>Generate Payslip
                        </a>
                        <a href="{{ route('tenant.payroll.employees.edit-salary', ['tenant' => $tenant->slug, 'employee' => $employee->id]) }}"
                           class="block w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-lg font-medium transition-colors duration-300 text-center">
                            <i class="fas fa-calculator mr-2"></i>Update Salary
                        </a>
                        <a href="#" class="block w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-3 rounded-lg font-medium transition-colors duration-300 text-center">
                            <i class="fas fa-hand-holding-usd mr-2"></i>Manage Loans
                        </a>
                    </div>
                </div>

                <!-- Employment Summary -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Employment Summary</h3>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Years of Service</span>
                            <span class="font-medium">
                                {{ $employee->hire_date->diffInYears(now()) }} years
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Payslips</span>
                            <span class="font-medium">{{ $employee->payrollRuns->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Active Loans</span>
                            <span class="font-medium">{{ $employee->loans ? $employee->loans->where('status', 'active')->count() : 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Document Modal -->
<div id="uploadModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Upload Document</h3>
                <button onclick="document.getElementById('uploadModal').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <form action="{{ route('tenant.payroll.employees.documents.upload', ['tenant' => $tenant->slug, 'employee' => $employee->id]) }}"
              method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Document Type</label>
                <select name="document_type" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">Select document type</option>
                    <option value="International Passport">International Passport</option>
                    <option value="CV/Resume">CV/Resume</option>
                    <option value="Certificate">Certificate</option>
                    <option value="ID Card">ID Card</option>
                    <option value="Contract">Contract</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Document Name</label>
                <input type="text" name="document_name" required
                       placeholder="e.g., John Doe Passport"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select File</label>
                <input type="file" name="file" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">Allowed: PDF, JPG, PNG, DOC, DOCX (Max: 5MB)</p>
            </div>

            <div class="flex space-x-3 pt-4">
                <button type="button"
                        onclick="document.getElementById('uploadModal').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-upload mr-2"></i>Upload
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Share Options Modal -->
<div id="shareModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Share Portal Link</h3>
                <button onclick="document.getElementById('shareModal').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <div class="p-6 space-y-4">
            <a href="mailto:{{ $employee->email }}?subject=Your Employee Portal Access&body=Hello {{ $employee->first_name }},%0D%0A%0D%0AYou can access your employee portal using this link:%0D%0A{{ $employee->portal_link }}%0D%0A%0D%0AThis link is valid until {{ $employee->portal_token_expires_at?->format('M d, Y') }}."
               class="block w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-medium transition-colors text-center">
                <i class="fas fa-envelope mr-2"></i>Share via Email
            </a>

            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $employee->phone) }}?text=Hello%20{{ $employee->first_name }},%20you%20can%20access%20your%20employee%20portal%20here:%20{{ urlencode($employee->portal_link) }}"
               target="_blank"
               class="block w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg font-medium transition-colors text-center">
                <i class="fab fa-whatsapp mr-2"></i>Share via WhatsApp
            </a>

            <button onclick="copyPortalLink(); document.getElementById('shareModal').classList.add('hidden')"
                    class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-3 rounded-lg font-medium transition-colors">
                <i class="fas fa-copy mr-2"></i>Copy Link
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyPortalLink() {
    const linkInput = document.getElementById('portalLink');
    const btn = event ? event.target.closest('button') : null;

    // Select the text
    linkInput.select();
    linkInput.setSelectionRange(0, 99999); // For mobile devices

    // Try modern clipboard API first, fallback to document.execCommand
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(linkInput.value).then(function() {
            showCopySuccess(btn);
        }).catch(function(err) {
            // Fallback to execCommand
            fallbackCopy(linkInput, btn);
        });
    } else {
        // Use fallback for older browsers or non-secure contexts
        fallbackCopy(linkInput, btn);
    }
}

function fallbackCopy(inputElement, btn) {
    try {
        inputElement.select();
        const successful = document.execCommand('copy');
        if (successful) {
            showCopySuccess(btn);
        } else {
            alert('Failed to copy. Please manually select and copy the link.');
        }
    } catch (err) {
        alert('Failed to copy. Please manually select and copy the link.');
    }
}

function showCopySuccess(btn) {
    if (!btn) return;

    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check mr-1"></i>Copied!';
    btn.classList.add('bg-green-600');
    btn.classList.remove('bg-blue-600');

    setTimeout(() => {
        btn.innerHTML = originalHTML;
        btn.classList.remove('bg-green-600');
        btn.classList.add('bg-blue-600');
    }, 2000);
}

function sharePortalLink() {
    document.getElementById('shareModal').classList.remove('hidden');
}

function resetPortalToken() {
    if (!confirm('Are you sure you want to reset the portal link? The old link will stop working and the employee will need to use the new link.')) {
        return;
    }

    const btn = event.target;
    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Resetting...';

    fetch('{{ route('tenant.payroll.employees.reset-portal-token', ['tenant' => $tenant->slug, 'employee' => $employee->id]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the link input with new token
            document.getElementById('portalLink').value = data.portal_link;

            // Show success message
            alert('Portal link has been reset successfully! The new link is valid until ' + data.expires_at);

            // Reload page to update token status
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to reset portal link'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while resetting the portal link. Please try again.');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    });
}
</script>
@endpush

@endsection
