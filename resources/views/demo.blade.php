@extends('layouts.app')

@section('title', 'Demo - Ballie')
@section('description', 'See Ballie in action with our interactive demo.')

@section('content')
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">See Ballie in Action</h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Watch our demo video to see how Ballie can transform your business operations.
            </p>
        </div>

        <div class="max-w-4xl mx-auto">
            <div class="bg-gray-200 rounded-lg aspect-video flex items-center justify-center">
                <div class="text-center">
                    <svg class="w-20 h-20 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293H15M9 10V9a2 2 0 012-2h2a2 2 0 012 2v1M9 10v5a2 2 0 002 2h2a2 2 0 002-2v-5"></path>
                    </svg>
                    <p class="text-gray-500 text-lg">Demo video coming soon</p>
                    <p class="text-gray-400">In the meantime, start your free trial to explore all features</p>
                </div>
            </div>

            <div class="text-center mt-8">
                <a href="{{ route('register') }}" class="bg-primary-600 text-white px-8 py-3 rounded-lg hover:bg-primary-700 transition-colors font-medium">
                    Start Free Trial
                </a>
            </div>
        </div>
    </div>
</section>
@endsection