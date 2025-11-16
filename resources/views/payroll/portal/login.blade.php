<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Employee Portal Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Logo/Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                <i class="fas fa-user-circle text-3xl text-white"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                Employee Portal
            </h1>
            <p class="text-gray-600">
                Welcome, {{ $employee->first_name }}
            </p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <!-- Info Banner -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-3"></i>
                    <div class="flex-1">
                        <h3 class="font-semibold text-blue-900 text-sm mb-1">Secure Login Required</h3>
                        <p class="text-blue-800 text-xs">
                            Please enter your Employee ID and Date of Birth to access your portal.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Error Messages -->
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle text-red-600 mt-0.5 mr-3"></i>
                        <div class="flex-1">
                            <h3 class="font-semibold text-red-900 text-sm mb-1">Login Failed</h3>
                            <ul class="text-red-800 text-xs space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('payroll.portal.login', ['tenant' => $tenant, 'token' => $token]) }}" class="space-y-6">
                @csrf

                <!-- Employee ID -->
                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-id-card mr-1 text-gray-400"></i>
                        Employee ID
                    </label>
                    <input type="text"
                           id="employee_id"
                           name="employee_id"
                           required
                           placeholder="Enter your employee ID"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                           value="{{ old('employee_id') }}">
                    <p class="text-xs text-gray-500 mt-1">
                        Example: {{ $employee->employee_number }}
                    </p>
                </div>

                <!-- Date of Birth -->
                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-1 text-gray-400"></i>
                        Date of Birth
                    </label>
                    <input type="date"
                           id="date_of_birth"
                           name="date_of_birth"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                           value="{{ old('date_of_birth') }}">
                    <p class="text-xs text-gray-500 mt-1">
                        Format: YYYY-MM-DD
                    </p>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-300 shadow-lg hover:shadow-xl">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Access Portal
                </button>
            </form>

            <!-- Security Notice -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex items-start text-xs text-gray-500">
                    <i class="fas fa-shield-alt mr-2 mt-0.5 text-green-600"></i>
                    <p>
                        Your data is protected with industry-standard encryption.
                        Never share your login credentials with anyone.
                    </p>
                </div>
            </div>
        </div>

        <!-- Token Expiry Notice -->
        <div class="mt-6 bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center text-sm">
                    <i class="fas fa-clock mr-2 text-amber-600"></i>
                    <span class="text-gray-700">Portal link expires:</span>
                </div>
                <span class="font-semibold text-gray-900">
                    {{ $employee->portal_token_expires_at ? $employee->portal_token_expires_at->format('M d, Y') : 'N/A' }}
                </span>
            </div>
        </div>

        <!-- Help Section -->
        <div class="text-center mt-8">
            <p class="text-sm text-gray-600">
                <i class="fas fa-question-circle mr-1"></i>
                Need help? Contact your HR department
            </p>
        </div>
    </div>

    <!-- Add subtle animation -->
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .bg-white {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</body>
</html>
