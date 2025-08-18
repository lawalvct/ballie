<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <title>@yield('title', 'Setup - Ballie')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:400,500,600,700" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {


                        'brand-gold': '#d1b05e',
                        'brand-blue': '#2b6399',
                        'brand-dark-purple': '#3c2c64',
                        'brand-teal': '#69a2a4',
                        'brand-purple': '#85729d',
                        'brand-light-blue': '#7b87b8',
                        'brand-deep-purple': '#4a3570',
                        'brand-lavender': '#a48cb4',
                        'brand-violet': '#614c80',
                        'brand-green': '#249484',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #2b6399 0%, #3c2c64 50%, #4a3570 100%);
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->

        <header class="gradient-bg text-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <!-- Logo -->
                    <div class="flex items-center">


                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                            <span class="text-white font-bold text-xl">B</span>
                        </div>

                        <div>
                            <h1 class="text-xl font-bold">Ballie Setup</h1>
                            <p class="text-sm opacity-80">{{ $tenant->name ?? 'Your Business' }}</p>
                        </div>
                    </div>



                    <!-- User Info -->
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <div class="text-sm font-medium">{{ auth()->user()->name }}</div>
                            <div class="text-xs opacity-80">{{ auth()->user()->email }}</div>
                        </div>
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->

        <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        © {{ date('Y') }} Ballie. All rights reserved.
                    </div>
                    <div class="flex space-x-6">
                        <a href="#" class="text-sm text-gray-500 hover:text-brand-blue">Help</a>
                        <a href="#" class="text-sm text-gray-500 hover:text-brand-blue">Support</a>
                        <a href="#" class="text-sm text-gray-500 hover:text-brand-blue">Privacy</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>