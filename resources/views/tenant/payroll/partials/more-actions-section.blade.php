<!-- More Actions Expandable Section -->
<div x-show="moreActionsExpanded"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-95"
     class="bg-gradient-to-br from-purple-900 via-gray-800 to-gray-900 rounded-2xl p-8 shadow-2xl border border-gray-700"
     style="display: none;">

    <!-- Section Header -->
    <div class="flex items-center justify-between mb-8">
        <h3 class="text-2xl font-bold text-white flex items-center">
            <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            All Payroll Actions
        </h3>
        <button @click="moreActionsExpanded = false"
                class="text-gray-400 hover:text-white transition-colors duration-200 p-2 rounded-lg hover:bg-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Employee Management Section -->
    <div class="mb-8">
        <h4 class="text-xl font-semibold text-white mb-6 flex items-center">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            Employee Management
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Add Employee Card -->
            <a href="{{ route('tenant.payroll.employees.create', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-blue-600 to-blue-800 hover:from-blue-500 hover:to-blue-700 border border-blue-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-blue-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-blue-200 transition-colors duration-300">Add Employee</h5>
                        <p class="text-xs text-blue-200">Create new employee</p>
                    </div>
                </div>
                <p class="text-xs text-blue-200">Create new employee records with salary details.</p>
            </a>

            <!-- View Employees Card -->
            <a href="{{ route('tenant.payroll.employees.index', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-green-600 to-green-800 hover:from-green-500 hover:to-green-700 border border-green-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-green-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-green-200 transition-colors duration-300">All Employees</h5>
                        <p class="text-xs text-green-200">Manage employees</p>
                    </div>
                </div>
                <p class="text-xs text-green-200">View and manage all employee records and details.</p>
            </a>

            <!-- Manage Departments Card -->
            <a href="{{ route('tenant.payroll.departments.index', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-purple-600 to-purple-800 hover:from-purple-500 hover:to-purple-700 border border-purple-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-purple-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-purple-200 transition-colors duration-300">Departments</h5>
                        <p class="text-xs text-purple-200">Manage departments</p>
                    </div>
                </div>
                <p class="text-xs text-purple-200">Organize employees into departments and teams.</p>
            </a>

            <!-- Position Management Card -->
            <a href="{{ route('tenant.payroll.positions.index', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-indigo-600 to-indigo-800 hover:from-indigo-500 hover:to-indigo-700 border border-indigo-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-indigo-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-indigo-200 transition-colors duration-300">Positions</h5>
                        <p class="text-xs text-indigo-200">Manage positions</p>
                    </div>
                </div>
                <p class="text-xs text-indigo-200">Define job positions and hierarchies.</p>
            </a>

            <!-- Salary Components Card -->
            <a href="{{ route('tenant.payroll.components.index', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-pink-600 to-pink-800 hover:from-pink-500 hover:to-pink-700 border border-pink-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-pink-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-pink-200 transition-colors duration-300">Salary Components</h5>
                        <p class="text-xs text-pink-200">Manage components</p>
                    </div>
                </div>
                <p class="text-xs text-pink-200">Configure allowances, deductions, and benefits.</p>
            </a>

            <!-- Shift Management Card -->
            <a href="{{ route('tenant.payroll.shifts.index', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-emerald-600 to-emerald-800 hover:from-emerald-500 hover:to-emerald-700 border border-emerald-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-emerald-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-emerald-200 transition-colors duration-300">Shift Management</h5>
                        <p class="text-xs text-emerald-200">Work schedules</p>
                    </div>
                </div>
                <p class="text-xs text-emerald-200">Create and manage employee shift schedules.</p>
            </a>
        </div>
    </div>

    <!-- Payroll Processing Section -->
    <div class="mb-8">
        <h4 class="text-xl font-semibold text-white mb-6 flex items-center">
            <div class="w-8 h-8 bg-orange-600 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
            </div>
            Payroll Processing
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Process Payroll Card -->
            <a href="{{ route('tenant.payroll.processing.create', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-orange-600 to-orange-800 hover:from-orange-500 hover:to-orange-700 border border-orange-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-orange-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-orange-200 transition-colors duration-300">Process Payroll</h5>
                        <p class="text-xs text-orange-200">Run new payroll</p>
                    </div>
                </div>
                <p class="text-xs text-orange-200">Calculate and process current period payroll.</p>
            </a>

            <!-- Payroll History Card -->
            <a href="{{ route('tenant.payroll.processing.index', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-teal-600 to-teal-800 hover:from-teal-500 hover:to-teal-700 border border-teal-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-teal-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-teal-200 transition-colors duration-300">Payroll History</h5>
                        <p class="text-xs text-teal-200">View past payrolls</p>
                    </div>
                </div>
                <p class="text-xs text-teal-200">Review completed and pending payroll runs.</p>
            </a>

            <!-- Attendance Card -->
            <a href="{{ route('tenant.payroll.attendance.index', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-indigo-600 to-indigo-800 hover:from-indigo-500 hover:to-indigo-700 border border-indigo-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-indigo-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-indigo-200 transition-colors duration-300">Attendance</h5>
                        <p class="text-xs text-indigo-200">Track attendance</p>
                    </div>
                </div>
                <p class="text-xs text-indigo-200">Manage employee attendance and time tracking.</p>
            </a>

            <!-- Overtime Card -->
            <a href="{{ route('tenant.payroll.overtime.index', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-cyan-600 to-cyan-800 hover:from-cyan-500 hover:to-cyan-700 border border-cyan-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-cyan-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-cyan-200 transition-colors duration-300">Overtime</h5>
                        <p class="text-xs text-cyan-200">Extra hours</p>
                    </div>
                </div>
                <p class="text-xs text-cyan-200">Manage overtime hours and calculations.</p>
            </a>

            <!-- Salary Advance Card (NEW) -->
            <a href="{{ route('tenant.payroll.salary-advance.create', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-rose-600 to-rose-800 hover:from-rose-500 hover:to-rose-700 border border-rose-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-rose-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-rose-200 transition-colors duration-300">Salary Advance</h5>
                        <p class="text-xs text-rose-200">IOU / Staff Loans</p>
                    </div>
                </div>
                <p class="text-xs text-rose-200">Issue salary advances and manage loan repayments.</p>
            </a>

            <!-- Employee Announcements Card (NEW) -->
            <a href="{{ route('tenant.payroll.announcements.index', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-amber-600 to-amber-800 hover:from-amber-500 hover:to-amber-700 border border-amber-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-amber-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-amber-200 transition-colors duration-300">Announcements</h5>
                        <p class="text-xs text-amber-200">Email & SMS</p>
                    </div>
                </div>
                <p class="text-xs text-amber-200">Send notifications to employees via email or SMS.</p>
            </a>
        </div>
    </div>

    <!-- Payroll Reports Section -->
    <div class="mb-8">
        <h4 class="text-xl font-semibold text-white mb-6 flex items-center">
            <div class="w-8 h-8 bg-yellow-600 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            Payroll Reports
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Tax Reports Card -->
            <a href="{{ route('tenant.payroll.reports.tax-report', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-yellow-600 to-yellow-800 hover:from-yellow-500 hover:to-yellow-700 border border-yellow-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-yellow-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-yellow-200 transition-colors duration-300">Tax Reports</h5>
                        <p class="text-xs text-yellow-200">Tax compliance</p>
                    </div>
                </div>
                <p class="text-xs text-yellow-200">Generate comprehensive tax reports and analytics.</p>
            </a>

            <!-- Payslips Card -->
            <a href="{{ route('tenant.payroll.processing.index', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-red-600 to-red-800 hover:from-red-500 hover:to-red-700 border border-red-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-red-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-red-200 transition-colors duration-300">Payslips</h5>
                        <p class="text-xs text-red-200">Salary statements</p>
                    </div>
                </div>
                <p class="text-xs text-red-200">Generate and view employee payslips.</p>
            </a>

            <!-- Bank Files Card -->
            <a href="{{ route('tenant.payroll.reports.bank-schedule', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-emerald-600 to-emerald-800 hover:from-emerald-500 hover:to-emerald-700 border border-emerald-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-emerald-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-emerald-200 transition-colors duration-300">Bank Files</h5>
                        <p class="text-xs text-emerald-200">Banking integration</p>
                    </div>
                </div>
                <p class="text-xs text-emerald-200">Export bank transfer files for payroll.</p>
            </a>

            <!-- Analytics Card -->
            <a href="{{ route('tenant.payroll.reports.summary', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-violet-600 to-violet-800 hover:from-violet-500 hover:to-violet-700 border border-violet-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-violet-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-violet-200 transition-colors duration-300">Analytics</h5>
                        <p class="text-xs text-violet-200">Insights & trends</p>
                    </div>
                </div>
                <p class="text-xs text-violet-200">View payroll analytics and cost trends.</p>
            </a>
        </div>
    </div>

    <!-- Settings Section -->
    <div>
        <h4 class="text-xl font-semibold text-white mb-6 flex items-center">
            <div class="w-8 h-8 bg-gray-600 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            Settings
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Payroll Settings Card -->
            <a href="{{ route('tenant.payroll.settings', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-gray-600 to-gray-800 hover:from-gray-500 hover:to-gray-700 border border-gray-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-gray-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-gray-200 transition-colors duration-300">Payroll Settings</h5>
                        <p class="text-xs text-gray-200">Configure payroll</p>
                    </div>
                </div>
                <p class="text-xs text-gray-200">Configure employee number format and other settings.</p>
            </a>
        </div>
    </div>
</div>
