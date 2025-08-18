@extends('layouts.app')

@section('title', 'Create Your Account - Ballie')
@section('description', 'Join thousands of Nigerian businesses using Ballie to manage their operations.')

@section('content')
<style>
    :root {
        --color-gold: #d1b05e;
        --color-blue: #2b6399;
        --color-dark-purple: #3c2c64;
        --color-teal: #69a2a4;
        --color-purple: #85729d;
        --color-light-blue: #7b87b8;
        --color-deep-purple: #4a3570;
        --color-lavender: #a48cb4;
        --color-violet: #614c80;
        --color-green: #249484;
    }

    .gradient-bg {
        background: linear-gradient(135deg, var(--color-blue) 0%, var(--color-dark-purple) 50%, var(--color-deep-purple) 100%);
    }

    .social-btn {
        transition: all 0.3s ease;
        border: 2px solid #e5e7eb;
    }

    .social-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .business-type-card {
        transition: all 0.3s ease;
        cursor: pointer;
        border: 2px solid #e5e7eb;
    }

    .business-type-card:hover {
        border-color: var(--color-gold);
        transform: translateY(-2px);
    }

    .business-type-card.selected {
        border-color: var(--color-gold);
        background-color: rgba(209, 176, 94, 0.1);
    }

    .step-indicator {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 2rem;
    }

    .step {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin: 0 10px;
        position: relative;
    }

    .step.active {
        background-color: var(--color-gold);
        color: #000;
    }

    .step.completed {
        background-color: var(--color-green);
        color: white;
    }

    .step.inactive {
        background-color: #e5e7eb;
        color: #9ca3af;
    }

    .step-line {
        width: 60px;
        height: 2px;
        background-color: #e5e7eb;
    }

    .step-line.completed {
        background-color: var(--color-green);
    }
</style>

<div class="min-h-screen gradient-bg py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-white font-bold text-2xl">B'</span>
            </div>
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Join Ballie Today</h1>
            <p class="text-gray-200">Start your 30-day free trial and transform your business</p>
        </div>

        <!-- Step Indicator -->
        <div class="step-indicator">
            <div class="step active" id="step-1">1</div>
            <div class="step-line" id="line-1"></div>
            <div class="step inactive" id="step-2">2</div>
            <div class="step-line" id="line-2"></div>
            <div class="step inactive" id="step-3">3</div>
        </div>

        <!-- Registration Form -->
        <div class="bg-white rounded-2xl shadow-2xl p-8 md:p-12">
            <form id="registration-form" method="POST" action="{{ route('register') }}">
                @csrf
                <input type="hidden" name="selected_plan" value="{{ request('plan') }}">

                <!-- Step 1: Business Type -->
                <div class="step-content" id="step-content-1">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">What type of business do you run?</h2>
                        <p class="text-gray-600">This helps us customize your experience</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                        <div class="business-type-card p-6 rounded-xl text-center" data-type="retail">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" style="background-color: var(--color-teal);">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-2">Retail & E-commerce</h3>
                            <p class="text-sm text-gray-600">Selling products online or in-store</p>
                        </div>

                        <div class="business-type-card p-6 rounded-xl text-center" data-type="service">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" style="background-color: var(--color-purple);">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-2">Service Business</h3>
                            <p class="text-sm text-gray-600">Consulting, agency, or professional services</p>
                        </div>

                        <div class="business-type-card p-6 rounded-xl text-center" data-type="restaurant">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" style="background-color: var(--color-green);">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-2">Restaurant & Food</h3>
                            <p class="text-sm text-gray-600">Restaurant, catering, or food business</p>
                        </div>

                        <div class="business-type-card p-6 rounded-xl text-center" data-type="manufacturing">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" style="background-color: var(--color-blue);">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-2">Manufacturing</h3>
                            <p class="text-sm text-gray-600">Production, assembly, or manufacturing</p>
                        </div>

                        <div class="business-type-card p-6 rounded-xl text-center" data-type="wholesale">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" style="background-color: var(--color-light-blue);">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-2">Wholesale & Distribution</h3>
                            <p class="text-sm text-gray-600">Bulk sales and distribution</p>
                        </div>

                        <div class="business-type-card p-6 rounded-xl text-center" data-type="other">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" style="background-color: var(--color-violet);">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-2">Other</h3>
                            <p class="text-sm text-gray-600">Something else or mixed business</p>
                        </div>
                    </div>

                    <input type="hidden" name="business_type" id="business_type" required>

                    <div class="text-center">
                        <button type="button" id="next-step-1" class="px-8 py-3 rounded-lg font-semibold text-white transition-all duration-300" style="background-color: var(--color-gold);" disabled>
                            Continue
                        </button>
                    </div>
                </div>

                <!-- Step 2: Account Details -->
                <div class="step-content hidden" id="step-content-2">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Create Your Account</h2>
                        <p class="text-gray-600">Enter your business and personal details</p>
                    </div>

                    <!-- Social Login Options -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                        <a href="{{ route('auth.google') }}" class="social-btn flex items-center justify-center px-4 py-3 rounded-lg bg-white hover:bg-gray-50">
                            <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            Continue with Google
                        </a>
                        <a href="{{ route('auth.facebook') }}" class="social-btn flex items-center justify-center px-4 py-3 rounded-lg bg-white hover:bg-gray-50">
                            <svg class="w-5 h-5 mr-3" fill="#1877F2" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            Continue with Facebook
                        </a>
                    </div>

                    <div class="relative mb-8">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">Or continue with email</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="business_name" class="block text-sm font-medium text-gray-700 mb-2">Business Name</label>
                            <input type="text" id="business_name" name="business_name" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Your Business Name">
                            @error('business_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Your Full Name</label>
                            <input type="text" id="name" name="name" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="John Doe">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" id="email" name="email" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="john@business.com">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" id="phone" name="phone"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="+234 800 000 0000">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <input type="password" id="password" name="password" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="••••••••">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <button type="button" id="back-step-2" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Back
                        </button>
                        <button type="button" id="next-step-2" class="px-8 py-3 rounded-lg font-semibold text-white transition-all duration-300" style="background-color: var(--color-gold);">
                            Continue
                        </button>
                    </div>
                </div>

                <!-- Step 3: Plan Selection -->
                <div class="step-content hidden" id="step-content-3">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Choose Your Plan</h2>
                        <p class="text-gray-600">Start with a 30-day free trial, no credit card required</p>
                    </div>

                    @if($plans ?? false)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        @foreach($plans as $plan)
                        <div class="plan-card border-2 border-gray-200 rounded-xl p-6 cursor-pointer transition-all duration-300 hover:border-yellow-400 {{ $plan->is_popular ? 'border-yellow-400 bg-yellow-50' : '' }}"
                             data-plan="{{ $plan->slug }}">
                            @if($plan->is_popular)
                                <div class="text-center mb-4">
                                    <span class="inline-block px-3 py-1 text-xs font-semibold text-yellow-800 bg-yellow-200 rounded-full">
                                        Most Popular
                                    </span>
                                </div>
                            @endif

                            <div class="text-center">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $plan->name }}</h3>
                                <div class="mb-4">
                                    <span class="text-3xl font-bold" style="color: var(--color-blue);">{{ $plan->formatted_monthly_price }}</span>
                                    <span class="text-gray-500">/month</span>
                                </div>
                                <p class="text-gray-600 mb-6">{{ $plan->description }}</p>

                                <div class="space-y-3 text-left">
                                    @foreach(array_slice($plan->features, 0, 5) as $feature)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-3 flex-shrink-0" style="color: var(--color-green);" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm text-gray-700">{{ $feature }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <input type="hidden" name="plan" id="selected_plan" value="{{ request('plan') }}">

                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="terms" required class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">
                                I agree to the <a href="#" class="text-blue-600 hover:underline">Terms of Service</a> and
                                <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>
                            </span>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <button type="button" id="back-step-3" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Back
                        </button>
                        <button type="submit" class="px-8 py-3 rounded-lg font-semibold text-white transition-all duration-300" style="background-color: var(--color-gold);">
                            Start Free Trial
                        </button>
                    </div>
                </div>
            </form>

            <!-- Login Link -->
            <div class="text-center mt-8 pt-6 border-t border-gray-200">
                <p class="text-gray-600">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-semibold hover:underline" style="color: var(--color-blue);">Sign in</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const totalSteps = 3;

    // Business type selection
    const businessTypeCards = document.querySelectorAll('.business-type-card');
    const businessTypeInput = document.getElementById('business_type');
    const nextStep1Btn = document.getElementById('next-step-1');

    businessTypeCards.forEach(card => {
        card.addEventListener('click', function() {
            businessTypeCards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            businessTypeInput.value = this.dataset.type;
            nextStep1Btn.disabled = false;
            nextStep1Btn.style.opacity = '1';
        });
    });

    // Plan selection
    const planCards = document.querySelectorAll('.plan-card');
    const selectedPlanInput = document.getElementById('selected_plan');

    planCards.forEach(card => {
        card.addEventListener('click', function() {
            planCards.forEach(c => {
                c.classList.remove('border-yellow-400', 'bg-yellow-50');
                c.classList.add('border-gray-200');
            });
            this.classList.remove('border-gray-200');
            this.classList.add('border-yellow-400', 'bg-yellow-50');
            selectedPlanInput.value = this.dataset.plan;
        });
    });

    // Step navigation
    function showStep(step) {
        // Hide all steps
        for (let i = 1; i <= totalSteps; i++) {
            document.getElementById(`step-content-${i}`).classList.add('hidden');
            const stepIndicator = document.getElementById(`step-${i}`);
            stepIndicator.classList.remove('active', 'completed');
            stepIndicator.classList.add('inactive');
        }

        // Show current step
        document.getElementById(`step-content-${step}`).classList.remove('hidden');
        const currentStepIndicator = document.getElementById(`step-${step}`);
        currentStepIndicator.classList.remove('inactive');
        currentStepIndicator.classList.add('active');

        // Mark previous steps as completed
        for (let i = 1; i < step; i++) {
            const stepIndicator = document.getElementById(`step-${i}`);
            stepIndicator.classList.remove('inactive', 'active');
            stepIndicator.classList.add('completed');

            const line = document.getElementById(`line-${i}`);
            if (line) line.classList.add('completed');
        }

        currentStep = step;
    }

    // Next/Back button handlers
    document.getElementById('next-step-1').addEventListener('click', () => showStep(2));
    document.getElementById('next-step-2').addEventListener('click', () => showStep(3));
    document.getElementById('back-step-2').addEventListener('click', () => showStep(1));
    document.getElementById('back-step-3').addEventListener('click', () => showStep(2));

    // Initialize with selected plan if coming from pricing page
    const urlPlan = '{{ request("plan") }}';
    if (urlPlan) {
        const planCard = document.querySelector(`[data-plan="${urlPlan}"]`);
        if (planCard) {
            planCard.click();
        }
    }
});
</script>
@endsection
