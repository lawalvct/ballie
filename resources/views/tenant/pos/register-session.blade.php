@extends('layouts.tenant')

@section('title', 'Cash Register Session - ' . $tenant->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-r from-purple-600 to-purple-700 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-cash-register text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Cash Register Session</h1>
            <p class="text-gray-600">Open a cash register session to start selling</p>
        </div>

        <!-- Active Sessions Alert -->
        @if($activeSessions->count() > 0)
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mr-3"></i>
                    <div>
                        <h3 class="text-yellow-800 font-semibold">Active Sessions Found</h3>
                        <p class="text-yellow-700 text-sm">There are currently {{ $activeSessions->count() }} active sessions running.</p>
                    </div>
                </div>
                <div class="mt-4 space-y-2">
                    @foreach($activeSessions as $session)
                        <div class="bg-white rounded-lg p-3 flex items-center justify-between">
                            <div>
                                <span class="font-medium">{{ $session->cashRegister->name }}</span>
                                <span class="text-gray-500 text-sm">- {{ $session->user->name }}</span>
                                <span class="text-gray-400 text-xs ml-2">Started: {{ $session->opened_at->format('M d, Y H:i') }}</span>
                            </div>
                            @if($session->user_id === auth()->id())
                                <a href="{{ route('tenant.pos.index', ['tenant' => $tenant->slug]) }}"
                                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200">
                                    Continue Session
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Open New Session Form -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                <h2 class="text-xl font-semibold text-white flex items-center">
                    <i class="fas fa-plus-circle mr-3"></i>
                    Open New Cash Register Session
                </h2>
            </div>

            <form action="{{ route('tenant.pos.open-session', ['tenant' => $tenant->slug]) }}" method="POST" class="p-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Cash Register Selection -->
                    <div>
                        <label for="cash_register_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Select Cash Register <span class="text-red-500">*</span>
                        </label>
                        <select name="cash_register_id" id="cash_register_id" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white">
                            <option value="">Choose a cash register...</option>
                            @foreach($cashRegisters as $register)
                                <option value="{{ $register->id }}">
                                    {{ $register->name }}
                                    @if($register->location)
                                        - {{ $register->location }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('cash_register_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Opening Balance -->
                    <div>
                        <label for="opening_balance" class="block text-sm font-medium text-gray-700 mb-2">
                            Opening Balance (₦) <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="opening_balance"
                               id="opening_balance"
                               step="0.01"
                               min="0"
                               required
                               value="{{ old('opening_balance', '0.00') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                               placeholder="0.00">
                        @error('opening_balance')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Opening Notes -->
                <div class="mt-6">
                    <label for="opening_notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Opening Notes (Optional)
                    </label>
                    <textarea name="opening_notes"
                              id="opening_notes"
                              rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                              placeholder="Any notes about the opening balance or session...">{{ old('opening_notes') }}</textarea>
                    @error('opening_notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quick Amount Buttons -->
                <div class="mt-6">
                    <p class="text-sm font-medium text-gray-700 mb-3">Quick Amount</p>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach([0, 1000, 5000, 10000] as $amount)
                            <button type="button"
                                    onclick="setOpeningBalance({{ $amount }})"
                                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded-lg font-medium transition-colors duration-200 border border-gray-300">
                                ₦{{ number_format($amount, 2) }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                    <a href="{{ route('tenant.dashboard', ['tenant' => $tenant->slug]) }}"
                       class="text-gray-600 hover:text-gray-800 font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>

                    <div class="flex space-x-3">
                        <button type="button"
                                onclick="document.getElementById('opening_balance').value = ''; document.getElementById('opening_notes').value = '';"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-3 rounded-xl font-semibold transition-colors duration-200">
                            Clear
                        </button>
                        <button type="submit"
                                class="bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white px-8 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center space-x-2">
                            <i class="fas fa-cash-register"></i>
                            <span>Open Session</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Instructions -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-xl p-6">
            <h3 class="text-blue-900 font-semibold mb-3 flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                Important Instructions
            </h3>
            <ul class="text-blue-800 text-sm space-y-2">
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                    Count your cash drawer carefully before entering the opening balance
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                    Make sure to select the correct cash register for your location
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                    You can only have one active session at a time
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                    Remember to close your session at the end of your shift
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
function setOpeningBalance(amount) {
    document.getElementById('opening_balance').value = amount.toFixed(2);
}

// Auto-focus on cash register selection
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('cash_register_id').focus();
});
</script>
@endsection
