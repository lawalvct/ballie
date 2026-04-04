@extends('layouts.app')

@section('title', 'Under Maintenance - Ballie')

@section('content')
<style>
    .gradient-bg {
        background: linear-gradient(135deg, #2b6399 0%, #3c2c64 50%, #4a3570 100%);
    }
</style>

<div class="gradient-bg min-h-screen flex flex-col items-center justify-center px-4 py-16">
    <div class="max-w-2xl w-full bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="p-8 md:p-12">
            <div class="text-center mb-8">
                <svg class="w-20 h-20 mx-auto text-amber-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                </svg>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Under Maintenance</h1>
                <p class="text-gray-600 text-lg">{{ $message }}</p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-500">We apologize for the inconvenience. Please try again later.</p>
            </div>
        </div>
    </div>
</div>
@endsection
