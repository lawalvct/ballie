@extends('layouts.tenant-onboarding')

@section('title', 'Welcome to Ballie - Setup Your Business')

@section('content')
<div class="text-center mb-8">
    <div class="w-20 h-20 bg-gradient-to-br from-brand-blue to-brand-deep-purple rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0h2M7 7h10M7 11h10M7 15h10"></path>
        </svg>
    </div>
    <h1 class="text-3xl font-bold text-gray-900 mb-4">Welcome to Ballie!</h1>
    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
        Let's get your business set up in just a few minutes. We'll help you configure everything you need to start managing your business efficiently.
    </p>
</div>

<!-- Progress Steps -->
<div class="mb-12">
    <div class="flex items-center justify-center space-x-8">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-brand-blue text-white rounded-full flex items-center justify-center font-semibold">1</div>
            <span class="ml-3 text-sm font-medium text-brand-blue">Company Info</span>
        </div>
        <div class="w-16 h-1 bg-gray-200 rounded"></div>
        <div class="flex items-center">
            <div class="w-10 h-10 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center font-semibold">2</div>
            <span class="ml-3 text-sm font-medium text-gray-500">Preferences</span>
        </div>
        <div class="w-16 h-1 bg-gray-200 rounded"></div>
        <div class="flex items-center">
            <div class="w-10 h-10 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center font-semibold">3</div>
            <span class="ml-3 text-sm font-medium text-gray-500">Team Setup</span>
        </div>
        <div class="w-16 h-1 bg-gray-200 rounded"></div>
        <div class="flex items-center">
            <div class="w-10 h-10 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center font-semibold">4</div>
            <span class="ml-3 text-sm font-medium text-gray-500">Complete</span>
        </div>
    </div>
</div>

<!-- Onboarding Options -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
    <!-- Guided Setup -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 hover:shadow-md transition-shadow">
        <div class="w-16 h-16 bg-gradient-to-br from-brand-gold to-brand-teal rounded-lg flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-3">Guided Setup</h3>
        <p class="text-gray-600 mb-6">We'll walk you through each step to configure your business settings, preferences, and team members.</p>
        <ul class="text-sm text-gray-500 space-y-2 mb-6">
            <li class="flex items-center">
                <svg class="w-4 h-4 text-brand-green mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Complete business profile
            </li>
            <li class="flex items-center">
                <svg class="w-4 h-4 text-brand-green mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Configure currency & preferences
            </li>
            <li class="flex items-center">
                <svg class="w-4 h-4 text-brand-green mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Invite team members
            </li>
        </ul>
        <a href="{{ route('tenant.onboarding.step', ['tenant' => $tenant->slug, 'step' => 'company']) }}"
           class="w-full bg-brand-blue text-white px-6 py-3 rounded-lg hover:bg-brand-dark-purple transition-colors font-medium text-center block">
            Start Guided Setup
        </a>
    </div>

    <!-- Quick Start -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 hover:shadow-md transition-shadow">
        <div class="w-16 h-16 bg-gradient-to-br from-brand-purple to-brand-violet rounded-lg flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-3">Quick Start</h3>
        <p class="text-gray-600 mb-6">Skip the setup and jump straight to your dashboard. We'll use sensible defaults that you can customize later.</p>
        <ul class="text-sm text-gray-500 space-y-2 mb-6">
            <li class="flex items-center">
                <svg class="w-4 h-4 text-brand-teal mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Default Nigerian settings
            </li>
            <li class="flex items-center">
                <svg class="w-4 h-4 text-brand-teal mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Naira currency (₦)
            </li>
            <li class="flex items-center">
                <svg class="w-4 h-4 text-brand-teal mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Customize anytime in settings
            </li>
        </ul>
        <form method="POST" action="{{ route('tenant.onboarding.complete', ['tenant' => $tenant->slug]) }}">
            @csrf
            <input type="hidden" name="skip_setup" value="1">
            <button type="submit"
                    class="w-full bg-brand-teal text-white px-6 py-3 rounded-lg hover:bg-brand-green transition-colors font-medium">
                Skip Setup & Go to Dashboard
            </button>
        </form>
    </div>
</div>

<!-- Help Section -->
<div class="bg-gradient-to-r from-brand-gold to-brand-teal bg-opacity-10 rounded-xl p-6 text-center">
    <h3 class="text-lg font-semibold text-gray-900 mb-2">Need Help?</h3>
    <p class="text-gray-600 mb-4">Our support team is here to help you get started with Ballie.</p>
    <div class="flex justify-center space-x-4">
        <a href="#" class="text-brand-blue hover:text-brand-dark-purple font-medium">📚 Documentation</a>
        <a href="#" class="text-brand-blue hover:text-brand-dark-purple font-medium">💬 Live Chat</a>
        <a href="#" class="text-brand-blue hover:text-brand-dark-purple font-medium">📧 Email Support</a>
    </div>
</div>
@endsection