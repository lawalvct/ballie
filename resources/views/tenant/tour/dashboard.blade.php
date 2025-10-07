@extends('layouts.tenant')

@section('title', 'Welcome to Ballie - Dashboard Tour')

@push('styles')
<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes pulse-slow {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.8;
        }
    }

    .animate-fade-in-up {
        animation: fadeInUp 0.6s ease-out forwards;
    }

    .animate-slide-in-right {
        animation: slideInRight 0.6s ease-out forwards;
    }

    .feature-card {
        opacity: 0;
        animation: fadeInUp 0.6s ease-out forwards;
    }

    .feature-card:nth-child(1) { animation-delay: 0.1s; }
    .feature-card:nth-child(2) { animation-delay: 0.2s; }
    .feature-card:nth-child(3) { animation-delay: 0.3s; }
    .feature-card:nth-child(4) { animation-delay: 0.4s; }

    .video-container {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 aspect ratio */
        height: 0;
        overflow: hidden;
    }

    .video-container video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <!-- Enhanced Progress Bar -->
    <div class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-sm shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-800">Getting Started Tour</h3>
                        <p class="text-xs text-gray-500">Learn Ballie in just a few minutes</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm font-semibold text-gray-700 bg-gray-100 px-3 py-1 rounded-full">
                        Step {{ $currentStep }} of {{ $totalSteps }}
                    </span>
                    <button onclick="window.location.href='{{ route('tenant.tour.skip', ['tenant' => $tenant->slug]) }}'"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-600 h-2.5 rounded-full transition-all duration-700 ease-out shadow-lg relative"
                     style="width: {{ ($currentStep / $totalSteps) * 100 }}%">
                    <div class="absolute inset-0 bg-white/30 animate-pulse"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tour Content -->
    <div class="pt-32 pb-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">
            <!-- Welcome Card -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden mb-8 animate-fade-in-up">
                <!-- Header Section -->
                <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-700 px-8 py-16 text-white text-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-black/10"></div>
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PHBhdGggZD0iTTM2IDE0YzMuMzE0IDAgNiAyLjY4NiA2IDZzLTIuNjg2IDYtNiA2LTYtMi42ODYtNi02IDIuNjg2LTYgNi02ek0yNCA0NGMzLjMxNCAwIDYgMi42ODYgNiA2cy0yLjY4NiA2LTYgNi02LTIuNjg2LTYtNiAyLjY4Ni02IDYtNnoiLz48L2c+PC9nPjwvc3ZnPg==')] opacity-20"></div>

                    <div class="relative z-10">
                        <div class="w-24 h-24 bg-white rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-2xl transform hover:rotate-6 transition-transform duration-300">
                            <svg class="w-14 h-14 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h1 class="text-5xl font-extrabold mb-4 tracking-tight">Welcome to Your Dashboard! 🎉</h1>
                        <p class="text-xl text-blue-100 mb-2 font-medium">Your command center for business insights</p>
                        <p class="text-blue-200 text-sm max-w-2xl mx-auto">Get real-time analytics, track performance, and make data-driven decisions all from one place</p>
                    </div>
                </div>

                <!-- Content Section -->
                <div class="p-8 lg:p-12">
                    <!-- Video Demo Section -->
                    <div class="mb-12">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                                <svg class="w-7 h-7 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                Watch Dashboard Overview
                            </h2>
                            <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full font-medium">2 min watch</span>
                        </div>

                        <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl overflow-hidden shadow-2xl border-4 border-gray-700 relative group">
                            <div class="video-container">
                                <video id="dashboardVideo" class="w-full" controls controlsList="nodownload" preload="metadata">
                                    <source src="{{ asset('demos/dashboard_video.mp4') }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                            <!-- Video Overlay when not playing -->
                            <div id="videoOverlay" class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                                <div class="text-white text-center">
                                    <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium">Click to play</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center justify-center mt-5 gap-3">
                            <button id="playBtn" onclick="playVideo()"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-indigo-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Play Demo
                            </button>
                            <button id="pauseBtn" onclick="pauseVideo()" style="display: none;"
                                    class="inline-flex items-center px-6 py-3 bg-gray-600 text-white font-semibold rounded-xl hover:bg-gray-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Pause
                            </button>
                            <button onclick="restartVideo()"
                                    class="inline-flex items-center px-6 py-3 bg-white text-gray-700 font-semibold rounded-xl hover:bg-gray-50 border-2 border-gray-300 hover:border-gray-400 transform hover:scale-105 transition-all duration-200 shadow-md">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Restart
                            </button>
                            <div class="flex items-center space-x-2 px-4 py-2 bg-gray-100 rounded-xl">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span id="videoTimer" class="text-sm font-medium text-gray-700">0:00 / 0:00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Key Features Grid -->
                    <div class="mb-12">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                            <svg class="w-7 h-7 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                            </svg>
                            Dashboard Key Features
                        </h2>

                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- Feature 1 -->
                            <div class="feature-card bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-2xl border-2 border-blue-200 hover:border-blue-400 hover:shadow-xl transition-all duration-300 cursor-pointer">
                                <div class="flex items-start space-x-4">
                                    <div class="w-14 h-14 bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-bold text-gray-800 mb-2">Revenue & Sales Tracking</h3>
                                        <p class="text-sm text-gray-600 leading-relaxed">Monitor total revenue, monthly sales trends, and growth percentages in real-time with interactive charts.</p>
                                        <div class="mt-3 flex items-center text-blue-600 text-sm font-semibold">
                                            <span>Learn more</span>
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Feature 2 -->
                            <div class="feature-card bg-gradient-to-br from-green-50 to-emerald-50 p-6 rounded-2xl border-2 border-green-200 hover:border-green-400 hover:shadow-xl transition-all duration-300 cursor-pointer">
                                <div class="flex items-start space-x-4">
                                    <div class="w-14 h-14 bg-gradient-to-br from-green-600 to-green-700 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-bold text-gray-800 mb-2">Smart Inventory Alerts</h3>
                                        <p class="text-sm text-gray-600 leading-relaxed">Get instant notifications for low stock, out-of-stock products, and automated reorder suggestions.</p>
                                        <div class="mt-3 flex items-center text-green-600 text-sm font-semibold">
                                            <span>Learn more</span>
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Feature 3 -->
                            <div class="feature-card bg-gradient-to-br from-purple-50 to-pink-50 p-6 rounded-2xl border-2 border-purple-200 hover:border-purple-400 hover:shadow-xl transition-all duration-300 cursor-pointer">
                                <div class="flex items-start space-x-4">
                                    <div class="w-14 h-14 bg-gradient-to-br from-purple-600 to-purple-700 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-bold text-gray-800 mb-2">Visual Analytics</h3>
                                        <p class="text-sm text-gray-600 leading-relaxed">Beautiful interactive charts showing revenue vs expenses, sales by category, and performance metrics.</p>
                                        <div class="mt-3 flex items-center text-purple-600 text-sm font-semibold">
                                            <span>Learn more</span>
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Feature 4 -->
                            <div class="feature-card bg-gradient-to-br from-orange-50 to-red-50 p-6 rounded-2xl border-2 border-orange-200 hover:border-orange-400 hover:shadow-xl transition-all duration-300 cursor-pointer">
                                <div class="flex items-start space-x-4">
                                    <div class="w-14 h-14 bg-gradient-to-br from-orange-600 to-orange-700 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-bold text-gray-800 mb-2">Real-Time Activity Feed</h3>
                                        <p class="text-sm text-gray-600 leading-relaxed">Stay updated with recent transactions, new customers, stock movements, and important business events.</p>
                                        <div class="mt-3 flex items-center text-orange-600 text-sm font-semibold">
                                            <span>Learn more</span>
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pro Tips Section -->
                    <div class="bg-gradient-to-r from-yellow-50 via-amber-50 to-orange-50 border-l-4 border-yellow-500 p-6 rounded-r-2xl mb-8 shadow-lg">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-yellow-500 rounded-xl flex items-center justify-center shadow-md">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 flex-1">
                                <h4 class="text-lg font-bold text-yellow-900 mb-3 flex items-center">
                                    💡 Pro Tips for Maximum Productivity
                                </h4>
                                <ul class="space-y-3">
                                    <li class="flex items-start text-sm text-yellow-800">
                                        <svg class="w-5 h-5 mr-2 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span><strong>Interactive Charts:</strong> Click on any chart to see detailed breakdowns and drill-down analytics</span>
                                    </li>
                                    <li class="flex items-start text-sm text-yellow-800">
                                        <svg class="w-5 h-5 mr-2 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span><strong>Date Filters:</strong> Use date range filters to view data for specific time periods (daily, weekly, monthly)</span>
                                    </li>
                                    <li class="flex items-start text-sm text-yellow-800">
                                        <svg class="w-5 h-5 mr-2 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span><strong>Live Updates:</strong> Top products and customers update in real-time as transactions occur</span>
                                    </li>
                                    <li class="flex items-start text-sm text-yellow-800">
                                        <svg class="w-5 h-5 mr-2 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span><strong>Quick Actions:</strong> Use the quick action buttons to jump directly to common tasks</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats Preview -->
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Dashboard Metrics You'll See</h2>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="bg-white p-5 rounded-xl shadow-md border-2 border-gray-100 hover:border-blue-300 hover:shadow-lg transition-all duration-300">
                                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Total Revenue</p>
                                <p class="text-lg font-bold text-gray-900">₦2.5M</p>
                            </div>
                            <div class="bg-white p-5 rounded-xl shadow-md border-2 border-gray-100 hover:border-green-300 hover:shadow-lg transition-all duration-300">
                                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                </div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Total Sales</p>
                                <p class="text-lg font-bold text-gray-900">1,234</p>
                            </div>
                            <div class="bg-white p-5 rounded-xl shadow-md border-2 border-gray-100 hover:border-purple-300 hover:shadow-lg transition-all duration-300">
                                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Customers</p>
                                <p class="text-lg font-bold text-gray-900">567</p>
                            </div>
                            <div class="bg-white p-5 rounded-xl shadow-md border-2 border-gray-100 hover:border-orange-300 hover:shadow-lg transition-all duration-300">
                                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Products</p>
                                <p class="text-lg font-bold text-gray-900">89</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Navigation -->
            <div class="bg-white rounded-2xl shadow-2xl p-6 border-2 border-gray-100 animate-slide-in-right">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <form action="{{ route('tenant.tour.skip', ['tenant' => $tenant->slug]) }}" method="POST" class="w-full md:w-auto">
                        @csrf
                        <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center px-6 py-3 text-gray-600 hover:text-gray-800 font-semibold rounded-xl hover:bg-gray-100 transition-all duration-200 group">
                            <svg class="w-5 h-5 mr-2 group-hover:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Skip Tour
                        </button>
                    </form>
                    <div class="flex items-center space-x-4 w-full md:w-auto">
                        <div class="flex items-center space-x-2 bg-gray-100 px-4 py-2 rounded-xl">
                            <div class="flex space-x-1">
                                @for($i = 1; $i <= $totalSteps; $i++)
                                    <div class="w-2 h-2 rounded-full {{ $i <= $currentStep ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                                @endfor
                            </div>
                            <span class="text-sm font-semibold text-gray-700 ml-2">{{ $currentStep }}/{{ $totalSteps }}</span>
                        </div>
                        <a href="{{ route('tenant.tour.customers', ['tenant' => $tenant->slug]) }}"
                           class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold rounded-xl hover:from-blue-700 hover:to-indigo-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-2xl group">
                            <span>Next: Customers</span>
                            <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const video = document.getElementById('dashboardVideo');
    const playBtn = document.getElementById('playBtn');
    const pauseBtn = document.getElementById('pauseBtn');
    const videoTimer = document.getElementById('videoTimer');
    const videoOverlay = document.getElementById('videoOverlay');

    function playVideo() {
        video.play();
        playBtn.style.display = 'none';
        pauseBtn.style.display = 'inline-flex';
    }

    function pauseVideo() {
        video.pause();
        playBtn.style.display = 'inline-flex';
        pauseBtn.style.display = 'none';
    }

    function restartVideo() {
        video.currentTime = 0;
        video.play();
        playBtn.style.display = 'none';
        pauseBtn.style.display = 'inline-flex';
    }

    function formatTime(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${mins}:${secs.toString().padStart(2, '0')}`;
    }

    // Update timer
    video.addEventListener('timeupdate', () => {
        const current = formatTime(video.currentTime);
        const duration = formatTime(video.duration);
        videoTimer.textContent = `${current} / ${duration}`;
    });

    // Update button states on video play/pause
    video.addEventListener('play', () => {
        playBtn.style.display = 'none';
        pauseBtn.style.display = 'inline-flex';
        videoOverlay.style.opacity = '0';
    });

    video.addEventListener('pause', () => {
        playBtn.style.display = 'inline-flex';
        pauseBtn.style.display = 'none';
    });

    // Show duration when metadata is loaded
    video.addEventListener('loadedmetadata', () => {
        const duration = formatTime(video.duration);
        videoTimer.textContent = `0:00 / ${duration}`;
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        if (e.code === 'Space' && e.target.tagName !== 'BUTTON' && e.target.tagName !== 'A') {
            e.preventDefault();
            if (video.paused) {
                playVideo();
            } else {
                pauseVideo();
            }
        }
    });
</script>
@endpush
