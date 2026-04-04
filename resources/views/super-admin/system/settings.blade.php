@extends('layouts.super-admin')

@section('title', 'System Settings')
@section('page-title', 'System Settings')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">

    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center space-x-3">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-sm text-green-700">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Tabs --}}
    @php
        $currentTab = request('tab', 'general');
        $tabs = [
            'general' => ['label' => 'General', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'],
            'registration' => ['label' => 'Registration', 'icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z'],
            'payment' => ['label' => 'Payment Gateways', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
            'maintenance' => ['label' => 'Maintenance', 'icon' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4'],
            'security' => ['label' => 'Security', 'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
        ];
    @endphp

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        {{-- Tab Navigation --}}
        <div class="border-b border-gray-200 bg-gray-50">
            <nav class="flex overflow-x-auto px-6" aria-label="Tabs">
                @foreach($tabs as $tabKey => $tab)
                    <a href="{{ route('super-admin.system.settings', ['tab' => $tabKey]) }}"
                       class="flex items-center px-4 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-colors duration-200
                              {{ $currentTab === $tabKey
                                  ? 'border-blue-500 text-blue-600'
                                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"/>
                        </svg>
                        {{ $tab['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>

        {{-- Tab Content --}}
        <form action="{{ route('super-admin.system.settings.update') }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="tab" value="{{ $currentTab }}">

            <div class="p-6 lg:p-8">
                @include("super-admin.system.tabs.{$currentTab}", ['settings' => $settings[$currentTab] ?? []])
            </div>

            {{-- Save Button --}}
            <div class="px-6 lg:px-8 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-3">
                <a href="{{ route('super-admin.system.settings', ['tab' => $currentTab]) }}"
                   class="px-6 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                    Reset
                </a>
                <button type="submit"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 rounded-lg text-sm font-medium text-white shadow-sm transition-colors duration-200">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
