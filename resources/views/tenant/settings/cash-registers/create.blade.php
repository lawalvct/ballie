@extends('layouts.tenant')

@section('title', 'Create Cash Register - ' . $tenant->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Create Cash Register</h1>
            <p class="mt-2 text-gray-600">Set up a new cash register for your POS system</p>
        </div>
        <a href="{{ route('tenant.settings.cash-registers.index', ['tenant' => $tenant->slug]) }}"
           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to List
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <form action="{{ route('tenant.settings.cash-registers.store', ['tenant' => $tenant->slug]) }}" method="POST">
            @csrf

            <div class="p-6 space-y-6">
                @include('tenant.settings.cash-registers._form')
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                <a href="{{ route('tenant.settings.cash-registers.index', ['tenant' => $tenant->slug]) }}"
                   class="px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fas fa-save mr-2"></i>Create Cash Register
                </button>
            </div>
        </form>
    </div>

    <!-- Help Card -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
        <h3 class="text-blue-900 font-semibold mb-3 flex items-center">
            <i class="fas fa-lightbulb mr-2"></i>
            Tips for Setting Up Cash Registers
        </h3>
        <ul class="text-blue-800 text-sm space-y-2">
            <li class="flex items-start">
                <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                Use descriptive names like "Counter 1", "Drive-through", "Express Lane"
            </li>
            <li class="flex items-start">
                <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                Specify location to help users identify the correct register quickly
            </li>
            <li class="flex items-start">
                <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                Opening balance is the initial cash float you want to start with (usually 0)
            </li>
            <li class="flex items-start">
                <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                You can activate/deactivate registers later based on your operational needs
            </li>
        </ul>
    </div>
</div>
@endsection
