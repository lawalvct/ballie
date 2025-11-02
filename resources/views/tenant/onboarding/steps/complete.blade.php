@extends('layouts.tenant-onboarding')

@section('title', 'Setup Complete - Ballie Setup')

@section('content')
<div class="text-center ">
    <div class="inline-block p-4 bg-green-100 rounded-full mb-6">
        <svg class="w-16 h-16 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
    </div>
    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Setup Complete!</h1>
    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
        Congratulations! Your business is now set up and ready to go. You can now start using Ballie to manage your business.
    </p>
</div>

<!-- Celebration Animation -->
<div class="relative h-64 mb-12">
    <div class="absolute inset-0 flex items-center justify-center">
        <!-- Immediate CSS Animation as fallback -->
        <div id="css-celebration" class="celebration-fallback">
            <div class="confetti-piece confetti-1"></div>
            <div class="confetti-piece confetti-2"></div>
            <div class="confetti-piece confetti-3"></div>
            <div class="confetti-piece confetti-4"></div>
            <div class="confetti-piece confetti-5"></div>
            <div class="confetti-piece confetti-6"></div>
            <div class="celebration-star">
                <svg class="w-12 h-12 text-yellow-400 animate-pulse" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
            </div>
        </div>
        <!-- Lottie Animation Container -->
        <div id="celebration-animation" class="w-full h-full" style="display: none;"></div>
    </div>
</div>

<!-- Quick Start Cards -->
{{-- <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
    <div class="p-6 md:p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Quick Start Actions</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Add Products Card -->
            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Add Your Products</h3>
                <p class="text-gray-600 mb-4">Start by adding your products or services to your inventory.</p>
                <a href="#" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Add Products
                </a>
            </div>

            <!-- Add Customers Card -->
            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Add Your Customers</h3>
                <p class="text-gray-600 mb-4">Add your customers to start creating invoices and tracking sales.</p>
                <a href="{{ route('tenant.crm.customers.create', ['tenant' => tenant()->slug]) }}" class="inline-block bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    Add Customers
                </a>
            </div>

            <!-- Create Invoice Card -->
            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Create Your First Invoice</h3>
                <p class="text-gray-600 mb-4">Start generating professional invoices for your customers.</p>
                <a href="#" class="inline-block bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                    Create Invoice
                </a>
            </div>
        </div>
    </div>
</div> --}}

<!-- Additional Resources -->
{{-- <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
    <div class="p-6 md:p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Additional Resources</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Video Tutorials -->
            <div class="flex">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Video Tutorials</h3>
                    <p class="text-gray-600 mb-3">Watch our video tutorials to learn how to use Ballie effectively.</p>
                    <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">
                        Watch Tutorials →
                    </a>
                </div>
            </div>

            <!-- Help Center -->
            <div class="flex">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Help Center</h3>
                    <p class="text-gray-600 mb-3">Browse our knowledge base for answers to common questions.</p>
                    <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">
                        Visit Help Center →
                    </a>
                </div>
            </div>

            <!-- Contact Support -->
            <div class="flex">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Contact Support</h3>
                    <p class="text-gray-600 mb-3">Need help? Our support team is ready to assist you.</p>
                    <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">
                        Contact Support →
                    </a>
                </div>
            </div>

            <!-- Community Forum -->
            <div class="flex">
                <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Community Forum</h3>
                    <p class="text-gray-600 mb-3">Connect with other Ballie users to share tips and best practices.</p>
                    <a href=:#" class="text-blue-600 hover:text-blue-800 font-medium">
                        Join Community →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div> --}}

<!-- Go to Dashboard Button -->
<div class="text-center mt-12">
    <form method="POST" action="{{ route('tenant.onboarding.complete', ['tenant' => $currentTenant->slug]) }}">
        @csrf
        <button type="submit" class="inline-flex items-center px-8 py-4 bg-brand-blue text-white rounded-lg hover:bg-brand-dark-purple transition-colors font-semibold text-lg">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Go to Dashboard
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/lottie-web@5.7.8/build/player/lottie.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const animationContainer = document.getElementById('celebration-animation');
        const cssBackup = document.getElementById('css-celebration');

        // Show CSS animation immediately
        cssBackup.style.display = 'block';

        // Preload and replace with Lottie animation
        try {
            const anim = lottie.loadAnimation({
                container: animationContainer,
                renderer: 'svg',
                loop: true,
                autoplay: true,
                path: 'https://assets10.lottiefiles.com/packages/lf20_touohxv0.json',
                rendererSettings: {
                    preserveAspectRatio: 'xMidYMid slice'
                }
            });

            // When Lottie animation is ready, hide CSS backup and show Lottie
            anim.addEventListener('DOMLoaded', function() {
                setTimeout(() => {
                    cssBackup.style.display = 'none';
                    animationContainer.style.display = 'block';
                }, 100);
            });

            // Fallback: if Lottie fails to load after 3 seconds, keep CSS animation
            setTimeout(() => {
                if (animationContainer.style.display === 'none') {
                    console.log('Lottie animation timeout, using CSS fallback');
                }
            }, 3000);

        } catch (error) {
            console.log('Lottie animation failed, using CSS fallback');
            // Keep CSS animation visible
        }

        // Track completion in analytics
        if (typeof gtag !== 'undefined') {
            gtag('event', 'onboarding_complete', {
                'event_category': 'onboarding',
                'event_label': 'completed'
            });
        }
    });
</script>

<style>
/* Immediate CSS Celebration Animation */
.celebration-fallback {
    position: relative;
    width: 100%;
    height: 100%;
    display: none;
    overflow: hidden;
}

.celebration-star {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 10;
}

.confetti-piece {
    position: absolute;
    width: 10px;
    height: 10px;
    background: linear-gradient(45deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4, #feca57);
    border-radius: 50%;
    animation: confetti-fall 3s infinite linear;
}

.confetti-1 {
    left: 10%;
    animation-delay: 0s;
    background: #ff6b6b;
}

.confetti-2 {
    left: 20%;
    animation-delay: 0.5s;
    background: #4ecdc4;
}

.confetti-3 {
    left: 40%;
    animation-delay: 1s;
    background: #45b7d1;
}

.confetti-4 {
    left: 60%;
    animation-delay: 1.5s;
    background: #96ceb4;
}

.confetti-5 {
    left: 80%;
    animation-delay: 2s;
    background: #feca57;
}

.confetti-6 {
    left: 90%;
    animation-delay: 2.5s;
    background: #ff9ff3;
}

@keyframes confetti-fall {
    0% {
        top: -10%;
        transform: rotate(0deg);
        opacity: 1;
    }
    100% {
        top: 110%;
        transform: rotate(720deg);
        opacity: 0;
    }
}

/* Enhance existing animations */
@keyframes pulse {
    0%, 100% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.1);
        opacity: 0.8;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Celebration bounce animation for the star */
@keyframes celebration-bounce {
    0%, 20%, 53%, 80%, 100% {
        transform: translate(-50%, -50%) scale(1);
    }
    40%, 43% {
        transform: translate(-50%, -50%) scale(1.2);
    }
}

.celebration-star svg {
    animation: celebration-bounce 2s infinite;
}

/* Rainbow glow effect */
.celebration-star svg {
    filter: drop-shadow(0 0 20px rgba(255, 215, 0, 0.6));
}
</style>
@endpush
