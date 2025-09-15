<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invitation Not Found - {{ config('app.name', 'Ballie') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="mx-auto h-20 w-20 bg-white rounded-full flex items-center justify-center shadow-lg">
                    <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="mt-6 text-3xl font-bold text-white">
                    Invitation Not Available
                </h2>
            </div>

            <!-- Main Card -->
            <div class="bg-white shadow-2xl rounded-2xl overflow-hidden">
                <div class="px-8 py-12 text-center">
                    <div class="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>

                    <h3 class="text-xl font-semibold text-gray-900 mb-4">
                        Sorry, this invitation is no longer valid
                    </h3>

                    <div class="space-y-3 text-gray-600 mb-8">
                        <p>This could happen for several reasons:</p>
                        <ul class="text-left space-y-2 max-w-sm mx-auto">
                            <li class="flex items-start">
                                <span class="flex-shrink-0 w-1.5 h-1.5 bg-gray-400 rounded-full mt-2 mr-3"></span>
                                The invitation has expired (invitations are valid for 7 days)
                            </li>
                            <li class="flex items-start">
                                <span class="flex-shrink-0 w-1.5 h-1.5 bg-gray-400 rounded-full mt-2 mr-3"></span>
                                The invitation has already been accepted
                            </li>
                            <li class="flex items-start">
                                <span class="flex-shrink-0 w-1.5 h-1.5 bg-gray-400 rounded-full mt-2 mr-3"></span>
                                The invitation link is invalid or corrupted
                            </li>
                            <li class="flex items-start">
                                <span class="flex-shrink-0 w-1.5 h-1.5 bg-gray-400 rounded-full mt-2 mr-3"></span>
                                The invitation has been cancelled by the sender
                            </li>
                        </ul>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3 text-left">
                                <h4 class="text-sm font-medium text-blue-800">What can you do?</h4>
                                <p class="mt-1 text-sm text-blue-700">
                                    Contact the person who sent you this invitation and ask them to send a new one.
                                    They can do this from their Ballie dashboard.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <a href="{{ url('/') }}"
                           class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            Go to Ballie Home
                        </a>

                        <div class="text-sm text-gray-500">
                            <p>
                                Already have an account?
                                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-500 underline">
                                    Sign in here
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help Section -->
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6 text-center">
                <h4 class="text-lg font-semibold text-white mb-2">Need Help?</h4>
                <p class="text-indigo-100 text-sm mb-4">
                    If you believe this is an error or need assistance, please contact our support team.
                </p>
                <a href="mailto:{{ config('mail.from.address') }}"
                   class="inline-flex items-center text-white hover:text-indigo-200 underline text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    {{ config('mail.from.address') }}
                </a>
            </div>

            <!-- Footer -->
            <p class="text-center text-sm text-indigo-100">
                © {{ date('Y') }} {{ config('app.name', 'Ballie') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
