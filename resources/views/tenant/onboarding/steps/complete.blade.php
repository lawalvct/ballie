@extends('layouts.tenant-onboarding')

@section('title', 'Setup Complete - Ballie Setup')

@section('content')
<!-- Progress Steps -->
<div class="mb-10">
    <div class="flex items-center justify-center">
        <div class="flex items-center space-x-3 md:space-x-6 overflow-x-auto pb-2">
            @foreach (['Company Info', 'Preferences', 'Accounts', 'Complete'] as $i => $label)
            @if ($i > 0)
            <div class="w-6 md:w-12 h-0.5 bg-green-400 rounded hidden sm:block"></div>
            @endif
            <div class="flex items-center flex-shrink-0">
                <div class="w-8 h-8 {{ $i < 3 ? 'bg-green-500' : 'bg-brand-blue' }} text-white rounded-full flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span class="ml-2 text-xs font-medium {{ $i < 3 ? 'text-green-600' : 'text-brand-blue' }} whitespace-nowrap hidden sm:inline">{{ $label }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Hero Illustration + Message -->
<div class="flex flex-col items-center text-center mb-8">
    <!-- Animated Accounting Illustration -->
    <div class="complete-hero relative w-56 h-56 md:w-64 md:h-64 mb-8">
        <svg viewBox="0 0 240 240" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
            <!-- Background circle -->
            <circle cx="120" cy="120" r="110" fill="#EEF2FF" class="hero-bg"/>
            <circle cx="120" cy="120" r="96" fill="#E0E7FF" opacity="0.5"/>

            <!-- Ledger / Book -->
            <rect x="60" y="65" width="90" height="115" rx="6" fill="white" stroke="#2b6399" stroke-width="2.5" class="hero-book"/>
            <rect x="60" y="65" width="16" height="115" rx="3" fill="#2b6399" class="hero-spine"/>
            <line x1="84" y1="90" x2="140" y2="90" stroke="#CBD5E1" stroke-width="2" stroke-linecap="round" class="hero-line hero-line-1"/>
            <line x1="84" y1="104" x2="135" y2="104" stroke="#CBD5E1" stroke-width="2" stroke-linecap="round" class="hero-line hero-line-2"/>
            <line x1="84" y1="118" x2="130" y2="118" stroke="#CBD5E1" stroke-width="2" stroke-linecap="round" class="hero-line hero-line-3"/>
            <line x1="84" y1="132" x2="138" y2="132" stroke="#CBD5E1" stroke-width="2" stroke-linecap="round" class="hero-line hero-line-4"/>
            <line x1="84" y1="146" x2="126" y2="146" stroke="#CBD5E1" stroke-width="2" stroke-linecap="round" class="hero-line hero-line-5"/>

            <!-- Chart bars on page -->
            <rect x="90" y="152" width="8" height="18" rx="2" fill="#69a2a4" opacity="0.7" class="hero-bar hero-bar-1"/>
            <rect x="102" y="145" width="8" height="25" rx="2" fill="#2b6399" opacity="0.8" class="hero-bar hero-bar-2"/>
            <rect x="114" y="150" width="8" height="20" rx="2" fill="#85729d" opacity="0.7" class="hero-bar hero-bar-3"/>
            <rect x="126" y="142" width="8" height="28" rx="2" fill="#249484" opacity="0.8" class="hero-bar hero-bar-4"/>

            <!-- Calculator floating element -->
            <g class="hero-calc" transform="translate(155, 50)">
                <rect width="42" height="54" rx="5" fill="white" stroke="#3c2c64" stroke-width="1.5" filter="url(#shadow1)"/>
                <rect x="6" y="6" width="30" height="12" rx="2" fill="#E0E7FF"/>
                <text x="28" y="15" font-size="8" font-weight="600" fill="#2b6399" text-anchor="end">1,250</text>
                <rect x="6" y="22" width="8" height="7" rx="1.5" fill="#CBD5E1"/>
                <rect x="17" y="22" width="8" height="7" rx="1.5" fill="#CBD5E1"/>
                <rect x="28" y="22" width="8" height="7" rx="1.5" fill="#CBD5E1"/>
                <rect x="6" y="32" width="8" height="7" rx="1.5" fill="#CBD5E1"/>
                <rect x="17" y="32" width="8" height="7" rx="1.5" fill="#CBD5E1"/>
                <rect x="28" y="32" width="8" height="7" rx="1.5" fill="#2b6399"/>
                <rect x="6" y="42" width="8" height="7" rx="1.5" fill="#CBD5E1"/>
                <rect x="17" y="42" width="19" height="7" rx="1.5" fill="#249484"/>
            </g>

            <!-- Coin floating element -->
            <g class="hero-coin" transform="translate(35, 45)">
                <circle cx="16" cy="16" r="16" fill="#d1b05e" filter="url(#shadow1)"/>
                <circle cx="16" cy="16" r="12" fill="none" stroke="#c9a84e" stroke-width="1.5"/>
                <text x="16" y="20" font-size="13" font-weight="700" fill="white" text-anchor="middle">₦</text>
            </g>

            <!-- Pie chart floating element -->
            <g class="hero-pie" transform="translate(160, 130)">
                <circle cx="22" cy="22" r="22" fill="white" filter="url(#shadow1)"/>
                <circle cx="22" cy="22" r="18" fill="none" stroke="#E0E7FF" stroke-width="6"/>
                <circle cx="22" cy="22" r="18" fill="none" stroke="#2b6399" stroke-width="6" stroke-dasharray="40 73.13" stroke-dashoffset="0" stroke-linecap="round"/>
                <circle cx="22" cy="22" r="18" fill="none" stroke="#249484" stroke-width="6" stroke-dasharray="25 88.13" stroke-dashoffset="-40" stroke-linecap="round"/>
                <circle cx="22" cy="22" r="18" fill="none" stroke="#d1b05e" stroke-width="6" stroke-dasharray="20 93.13" stroke-dashoffset="-65" stroke-linecap="round"/>
            </g>

            <!-- Success checkmark badge -->
            <g class="hero-badge" transform="translate(118, 35)">
                <circle cx="18" cy="18" r="18" fill="#10B981" filter="url(#shadow1)"/>
                <circle cx="18" cy="18" r="14" fill="none" stroke="white" stroke-width="1.5" opacity="0.4"/>
                <path d="M11 18l4 4 8-8" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none" class="hero-check"/>
            </g>

            <!-- Sparkle decorations -->
            <g class="hero-sparkle sparkle-1">
                <path d="M44 100l2-6 2 6 6 2-6 2-2 6-2-6-6-2z" fill="#d1b05e" opacity="0.7"/>
            </g>
            <g class="hero-sparkle sparkle-2">
                <path d="M185 95l1.5-4.5 1.5 4.5 4.5 1.5-4.5 1.5-1.5 4.5-1.5-4.5-4.5-1.5z" fill="#85729d" opacity="0.6"/>
            </g>
            <g class="hero-sparkle sparkle-3">
                <path d="M50 165l1.5-4.5 1.5 4.5 4.5 1.5-4.5 1.5-1.5 4.5-1.5-4.5-4.5-1.5z" fill="#249484" opacity="0.6"/>
            </g>

            <defs>
                <filter id="shadow1" x="-4" y="-2" width="calc(100% + 8px)" height="calc(100% + 10px)">
                    <feDropShadow dx="0" dy="2" stdDeviation="3" flood-opacity="0.1"/>
                </filter>
            </defs>
        </svg>
    </div>

    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">You're All Set!</h1>
    <p class="text-lg text-gray-500 max-w-lg mx-auto leading-relaxed">
        Your accounts are configured and ready. Start managing invoices, tracking expenses, and running reports.
    </p>
</div>

<!-- What's Ready Summary -->
<div class="max-w-2xl mx-auto mb-10">
    <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100 shadow-sm">
        <div class="px-6 py-4 flex items-center gap-4">
            <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900">Chart of Accounts</p>
                <p class="text-xs text-gray-500">Ledger accounts and account groups configured</p>
            </div>
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        </div>
        <div class="px-6 py-4 flex items-center gap-4">
            <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900">Financial Year & Currency</p>
                <p class="text-xs text-gray-500">Reporting period and currency preferences set</p>
            </div>
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        </div>
        <div class="px-6 py-4 flex items-center gap-4">
            <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-brand-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900">Invoicing & Vouchers</p>
                <p class="text-xs text-gray-500">Invoice templates and voucher types ready</p>
            </div>
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        </div>
        <div class="px-6 py-4 flex items-center gap-4">
            <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900">Reports & Dashboard</p>
                <p class="text-xs text-gray-500">P&L, Balance Sheet, Trial Balance available</p>
            </div>
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        </div>
    </div>
</div>

<!-- Go to Dashboard Button -->
<div class="text-center mb-4">
    <form method="POST" action="{{ route('tenant.onboarding.complete', ['tenant' => $currentTenant->slug]) }}" x-data="{ loading: false }" @submit="loading = true">
        @csrf
        <button type="submit"
                :disabled="loading"
                :class="loading ? 'opacity-75 cursor-not-allowed' : 'hover:bg-brand-dark-purple hover:shadow-lg'"
                class="inline-flex items-center px-10 py-4 bg-brand-blue text-white rounded-xl transition-all font-semibold text-lg shadow-md">
            <svg x-show="loading" class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span x-text="loading ? 'Preparing your dashboard...' : 'Open Dashboard'"></span>
            <svg x-show="!loading" class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
        </button>
    </form>
</div>
@endsection

@push('scripts')
<style>
/* Hero entrance animations */
.complete-hero { animation: heroFadeUp 0.6s ease-out both; }
@keyframes heroFadeUp {
    from { opacity: 0; transform: translateY(20px) scale(0.95); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}

/* Floating animations for accounting elements */
.hero-calc {
    animation: floatCalc 4s ease-in-out infinite;
    transform-origin: center;
}
@keyframes floatCalc {
    0%, 100% { transform: translate(155px, 50px); }
    50%      { transform: translate(155px, 42px); }
}

.hero-coin {
    animation: floatCoin 3.5s ease-in-out infinite 0.5s;
    transform-origin: center;
}
@keyframes floatCoin {
    0%, 100% { transform: translate(35px, 45px); }
    50%      { transform: translate(35px, 38px); }
}

.hero-pie {
    animation: floatPie 4.5s ease-in-out infinite 1s;
    transform-origin: center;
}
@keyframes floatPie {
    0%, 100% { transform: translate(160px, 130px); }
    50%      { transform: translate(160px, 123px); }
}

/* Badge pop-in */
.hero-badge {
    animation: badgePop 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) 0.4s both;
}
@keyframes badgePop {
    from { transform: translate(118px, 35px) scale(0); opacity: 0; }
    to   { transform: translate(118px, 35px) scale(1); opacity: 1; }
}

/* Checkmark draw */
.hero-check {
    stroke-dasharray: 24;
    stroke-dashoffset: 24;
    animation: drawCheck 0.4s ease-out 0.8s forwards;
}
@keyframes drawCheck {
    to { stroke-dashoffset: 0; }
}

/* Ledger lines slide in */
.hero-line { opacity: 0; animation: lineSlide 0.3s ease-out forwards; }
.hero-line-1 { animation-delay: 0.3s; }
.hero-line-2 { animation-delay: 0.4s; }
.hero-line-3 { animation-delay: 0.5s; }
.hero-line-4 { animation-delay: 0.6s; }
.hero-line-5 { animation-delay: 0.7s; }
@keyframes lineSlide {
    from { opacity: 0; transform: translateX(-8px); }
    to   { opacity: 1; transform: translateX(0); }
}

/* Bar chart grow */
.hero-bar { transform-origin: bottom; animation: barGrow 0.4s ease-out forwards; opacity: 0; }
.hero-bar-1 { animation-delay: 0.6s; }
.hero-bar-2 { animation-delay: 0.7s; }
.hero-bar-3 { animation-delay: 0.8s; }
.hero-bar-4 { animation-delay: 0.9s; }
@keyframes barGrow {
    from { transform: scaleY(0); opacity: 0; }
    to   { transform: scaleY(1); opacity: 1; }
}

/* Sparkle twinkle */
.hero-sparkle { animation: twinkle 2s ease-in-out infinite; }
.sparkle-1 { animation-delay: 0s; }
.sparkle-2 { animation-delay: 0.7s; }
.sparkle-3 { animation-delay: 1.4s; }
@keyframes twinkle {
    0%, 100% { opacity: 0.3; transform: scale(0.8); }
    50%      { opacity: 0.8; transform: scale(1.1); }
}
</style>
@endpush
