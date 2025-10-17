<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Affiliate Dashboard - Ballie')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:400,500,600,700" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            gold: '#d1b05e',
                            blue: '#2b6399',
                            'dark-purple': '#3c2c64',
                            teal: '#69a2a4',
                            purple: '#85729d',
                            'light-blue': '#7b87b8',
                            'deep-purple': '#4a3570',
                            lavender: '#a48cb4',
                            violet: '#614c80',
                            green: '#249484',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="font-sans antialiased" x-data="{ mobileOpen: false }">
    <div class="min-h-screen bg-gray-50">
        <!-- Affiliate Navigation -->
        @include('affiliate.partials.navigation')

        <!-- Page Content -->
        <main>
            @yield('affiliate-content')
        </main>
    </div>
</body>
</html>