@extends('layouts.tenant')

@section('title', 'Edit Cash Register - ' . $tenant->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Cash Register</h1>
            <p class="mt-2 text-gray-600">Update cash register settings for {{ $cashRegister->name }}</p>
        </div>
        <a href="{{ route('tenant.settings.cash-registers.index', ['tenant' => $tenant->slug]) }}"
           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to List
        </a>
    </div>

    <!-- Alert for Active Sessions -->
    @if($hasActiveSessions)
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg flex items-start">
            <svg class="w-5 h-5 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <div>
                <p class="font-semibold">Active Session Warning</p>
                <p class="text-sm mt-1">This register has active sessions. Some operations may be restricted. Close all sessions before deactivating this register.</p>
            </div>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <form action="{{ route('tenant.settings.cash-registers.update', ['tenant' => $tenant->slug, 'cashRegister' => $cashRegister->id]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-6">
                @include('tenant.settings.cash-registers._form', ['edit' => true])
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                <div>
                    @if($cashRegister->sessions()->count() == 0 && $cashRegister->sales()->count() == 0)
                        <button type="button"
                                onclick="if(confirm('Are you sure you want to delete this cash register? This action cannot be undone.')) { document.getElementById('delete-form').submit(); }"
                                class="px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-red-700">
                            <i class="fas fa-trash mr-2"></i>Delete Register
                        </button>
                    @else
                        <button type="button"
                                disabled
                                class="px-4 py-2 bg-gray-300 border border-transparent rounded-lg font-semibold text-sm text-gray-500 cursor-not-allowed"
                                title="Cannot delete register with sessions or sales">
                            <i class="fas fa-trash mr-2"></i>Delete Register
                        </button>
                    @endif
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('tenant.settings.cash-registers.index', ['tenant' => $tenant->slug]) }}"
                       class="px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <i class="fas fa-save mr-2"></i>Update Cash Register
                    </button>
                </div>
            </div>
        </form>

        <!-- Delete Form (Hidden) -->
        <form id="delete-form"
              action="{{ route('tenant.settings.cash-registers.destroy', ['tenant' => $tenant->slug, 'cashRegister' => $cashRegister->id]) }}"
              method="POST"
              class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>

    <!-- Information Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Register Statistics -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-gray-900 font-semibold mb-4 flex items-center">
                <i class="fas fa-chart-bar mr-2 text-blue-600"></i>
                Register Statistics
            </h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Total Sessions:</span>
                    <span class="text-sm font-medium text-gray-900">{{ $cashRegister->sessions()->count() }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Active Sessions:</span>
                    <span class="text-sm font-medium {{ $hasActiveSessions ? 'text-green-600' : 'text-gray-900' }}">
                        {{ $cashRegister->sessions()->whereNull('closed_at')->count() }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Total Sales:</span>
                    <span class="text-sm font-medium text-gray-900">{{ $cashRegister->sales()->count() }}</span>
                </div>
                <div class="flex items-center justify-between border-t pt-3">
                    <span class="text-sm text-gray-600">Opening Balance:</span>
                    <span class="text-sm font-medium text-gray-900">₦{{ number_format($cashRegister->opening_balance, 2) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Current Balance:</span>
                    <span class="text-sm font-bold text-green-600">₦{{ number_format($cashRegister->current_balance, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Important Notes -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
            <h3 class="text-yellow-900 font-semibold mb-3 flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                Editing Restrictions
            </h3>
            <ul class="text-yellow-800 text-sm space-y-2">
                <li class="flex items-start">
                    <i class="fas fa-lock text-yellow-600 mr-2 mt-0.5"></i>
                    Opening balance cannot be changed after creation
                </li>
                <li class="flex items-start">
                    <i class="fas fa-lock text-yellow-600 mr-2 mt-0.5"></i>
                    Current balance is automatically managed by the system
                </li>
                <li class="flex items-start">
                    <i class="fas fa-lock text-yellow-600 mr-2 mt-0.5"></i>
                    Cannot delete registers with transaction history
                </li>
                <li class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mr-2 mt-0.5"></i>
                    Close all active sessions before deactivating
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
