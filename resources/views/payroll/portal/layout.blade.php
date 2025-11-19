<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Employee Portal') - {{ $employee->first_name }} {{ $employee->last_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
</head>
<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-indigo-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-circle text-2xl text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            @yield('page-title', 'Welcome, ' . $employee->first_name . '!')
                        </h1>
                        <p class="text-sm text-gray-600">{{ $employee->employee_number }} • {{ $employee->department->name ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('payroll.portal.dashboard', ['tenant' => $tenant, 'token' => $token]) }}"
                       class="text-gray-600 hover:text-indigo-600 transition-colors"
                       title="Dashboard">
                        <i class="fas fa-home text-xl"></i>
                    </a>
                    <a href="{{ route('payroll.portal.profile', ['tenant' => $tenant, 'token' => $token]) }}"
                       class="text-gray-600 hover:text-indigo-600 transition-colors"
                       title="Profile">
                        <i class="fas fa-user-cog text-xl"></i>
                    </a>
                    <form action="{{ route('payroll.portal.logout', ['tenant' => $tenant, 'token' => $token]) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-red-600 transition-colors" title="Logout">
                            <i class="fas fa-sign-out-alt text-xl"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation Menu -->
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex space-x-8 overflow-x-auto">
                <a href="{{ route('payroll.portal.dashboard', ['tenant' => $tenant, 'token' => $token]) }}"
                   class="inline-flex items-center px-1 pt-4 pb-3 border-b-2 {{ request()->routeIs('payroll.portal.dashboard') ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium transition-colors">
                    <i class="fas fa-home mr-2"></i>
                    Dashboard
                </a>
                <a href="{{ route('payroll.portal.payslips', ['tenant' => $tenant, 'token' => $token]) }}"
                   class="inline-flex items-center px-1 pt-4 pb-3 border-b-2 {{ request()->routeIs('payroll.portal.payslips*') ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium transition-colors">
                    <i class="fas fa-file-invoice-dollar mr-2"></i>
                    Payslips
                </a>
                <a href="{{ route('payroll.portal.attendance', ['tenant' => $tenant, 'token' => $token]) }}"
                   class="inline-flex items-center px-1 pt-4 pb-3 border-b-2 {{ request()->routeIs('payroll.portal.attendance') ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium transition-colors">
                    <i class="fas fa-calendar-check mr-2"></i>
                    Attendance
                </a>
                <a href="{{ route('payroll.portal.profile', ['tenant' => $tenant, 'token' => $token]) }}"
                   class="inline-flex items-center px-1 pt-4 pb-3 border-b-2 {{ request()->routeIs('payroll.portal.profile') ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium transition-colors">
                    <i class="fas fa-user mr-2"></i>
                    Profile
                </a>
                <a href="{{ route('payroll.portal.loans', ['tenant' => $tenant, 'token' => $token]) }}"
                   class="inline-flex items-center px-1 pt-4 pb-3 border-b-2 {{ request()->routeIs('payroll.portal.loans') ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium transition-colors">
                    <i class="fas fa-hand-holding-usd mr-2"></i>
                    Loans
                </a>
                <a href="{{ route('payroll.portal.tax-certificate', ['tenant' => $tenant, 'token' => $token]) }}"
                   class="inline-flex items-center px-1 pt-4 pb-3 border-b-2 {{ request()->routeIs('payroll.portal.tax-certificate') ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium transition-colors">
                    <i class="fas fa-file-alt mr-2"></i>
                    Tax Certificate
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="text-center text-sm text-gray-600">
                <p>© {{ date('Y') }} Employee Self-Service Portal. All rights reserved.</p>
                <p class="mt-1">
                    <i class="fas fa-shield-alt text-green-600"></i>
                    Your data is secure and encrypted
                </p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
