@extends('layouts.tenant')

@section('title', 'Close Cash Register Session - ' . $tenant->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-gradient-to-r from-red-500 via-red-600 to-red-700 rounded-full flex items-center justify-center mx-auto mb-6 shadow-2xl relative">
                <i class="fas fa-cash-register text-white text-3xl"></i>
                <div class="absolute -top-2 -right-2 w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center shadow-lg">
                    <i class="fas fa-lock text-yellow-800 text-sm"></i>
                </div>
            </div>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent mb-3">
                Close Cash Register Session
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">Count your cash drawer and finalize today's session with confidence</p>

            <!-- Session Status Badge -->
            <div class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-medium mt-4">
                <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                Active Session • {{ $activeSession->opened_at->diffForHumans() }}
            </div>
        </div>

        <!-- Session Summary -->
        <div class="bg-white/70 backdrop-blur-sm rounded-3xl shadow-2xl border border-white/50 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-chart-line text-white"></i>
                    </div>
                    Session Overview
                </h2>
                <p class="text-blue-100 text-sm mt-1">Summary of today's activity</p>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                    <!-- Session Info -->
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-6 border border-gray-200/50 hover:shadow-lg transition-all duration-300 group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-gray-200 rounded-xl flex items-center justify-center group-hover:scale-105 transition-transform duration-300">
                                <i class="fas fa-info-circle text-gray-600 text-xl"></i>
                            </div>
                            <span class="text-xs text-gray-500 bg-gray-200 px-2 py-1 rounded-full">INFO</span>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-3 text-lg">Session Details</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center">
                                <i class="fas fa-desktop text-gray-400 mr-2 w-4"></i>
                                <span class="text-gray-600">Register:</span>
                                <span class="font-semibold ml-1">{{ $activeSession->cashRegister->name }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-clock text-gray-400 mr-2 w-4"></i>
                                <span class="text-gray-600">Started:</span>
                                <span class="font-semibold ml-1">{{ $activeSession->opened_at->format('M d, H:i') }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-hourglass-half text-gray-400 mr-2 w-4"></i>
                                <span class="text-gray-600">Duration:</span>
                                <span class="font-semibold ml-1">{{ $activeSession->opened_at->diffForHumans(null, true) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Opening Balance -->
                    <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-2xl p-6 border border-green-200/50 hover:shadow-lg transition-all duration-300 group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-green-200 rounded-xl flex items-center justify-center group-hover:scale-105 transition-transform duration-300">
                                <i class="fas fa-play text-green-600 text-xl"></i>
                            </div>
                            <span class="text-xs text-green-600 bg-green-200 px-2 py-1 rounded-full font-medium">OPENING</span>
                        </div>
                        <h3 class="font-bold text-green-900 mb-2 text-lg">Starting Balance</h3>
                        <p class="text-3xl font-black text-green-700">₦{{ number_format($activeSession->opening_balance, 0) }}</p>
                        <p class="text-xs text-green-600 mt-1">Initial cash amount</p>
                    </div>

                    <!-- Total Sales -->
                    <div class="bg-gradient-to-br from-blue-50 to-cyan-100 rounded-2xl p-6 border border-blue-200/50 hover:shadow-lg transition-all duration-300 group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-blue-200 rounded-xl flex items-center justify-center group-hover:scale-105 transition-transform duration-300">
                                <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                            </div>
                            <span class="text-xs text-blue-600 bg-blue-200 px-2 py-1 rounded-full font-medium">SALES</span>
                        </div>
                        <h3 class="font-bold text-blue-900 mb-2 text-lg">Total Sales</h3>
                        <p class="text-3xl font-black text-blue-700">₦{{ number_format($activeSession->total_sales, 0) }}</p>
                        <div class="flex items-center justify-between mt-2">
                            <p class="text-xs text-blue-600">{{ $activeSession->sales->count() }} transactions</p>
                            <div class="flex items-center text-xs text-blue-500">
                                <i class="fas fa-trending-up mr-1"></i>
                                Active
                            </div>
                        </div>
                    </div>

                    <!-- Expected Cash -->
                    <div class="bg-gradient-to-br from-purple-50 to-violet-100 rounded-2xl p-6 border border-purple-200/50 hover:shadow-lg transition-all duration-300 group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-purple-200 rounded-xl flex items-center justify-center group-hover:scale-105 transition-transform duration-300">
                                <i class="fas fa-calculator text-purple-600 text-xl"></i>
                            </div>
                            <span class="text-xs text-purple-600 bg-purple-200 px-2 py-1 rounded-full font-medium">EXPECTED</span>
                        </div>
                        <h3 class="font-bold text-purple-900 mb-2 text-lg">Expected Cash</h3>
                        <p class="text-3xl font-black text-purple-700">₦{{ number_format($activeSession->opening_balance + $activeSession->total_cash_sales, 0) }}</p>
                        <p class="text-xs text-purple-600 mt-1">Opening + Cash Sales</p>
                    </div>
                </div>

                <!-- Quick Stats Row -->
                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl p-4 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-indigo-100 text-sm">Cash Sales</p>
                                <p class="text-2xl font-bold">₦{{ number_format($activeSession->total_cash_sales, 0) }}</p>
                            </div>
                            <i class="fas fa-money-bill-wave text-3xl text-indigo-200"></i>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl p-4 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm">Card Sales</p>
                                <p class="text-2xl font-bold">₦{{ number_format($activeSession->total_sales - $activeSession->total_cash_sales, 0) }}</p>
                            </div>
                            <i class="fas fa-credit-card text-3xl text-green-200"></i>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-orange-500 to-red-600 rounded-2xl p-4 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-orange-100 text-sm">Avg. Transaction</p>
                                <p class="text-2xl font-bold">₦{{ $activeSession->sales->count() > 0 ? number_format($activeSession->total_sales / $activeSession->sales->count(), 0) : '0' }}</p>
                            </div>
                            <i class="fas fa-chart-pie text-3xl text-orange-200"></i>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                @if($activeSession->sales->count() > 0)
                    <div class="mt-8">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <i class="fas fa-receipt text-gray-600 mr-3"></i>
                                Recent Transactions
                            </h3>
                            <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                                Last {{ min(10, $activeSession->sales->count()) }} of {{ $activeSession->sales->count() }}
                            </span>
                        </div>
                        <div class="bg-gray-50/50 rounded-2xl p-6 max-h-80 overflow-y-auto">
                            <div class="space-y-3">
                                @foreach($activeSession->sales->take(10) as $sale)
                                    <div class="flex items-center justify-between bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-200 group">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                                                #{{ substr($sale->sale_number, -3) }}
                                            </div>
                                            <div>
                                                <div class="flex items-center space-x-2">
                                                    <span class="font-semibold text-gray-900">{{ $sale->sale_number }}</span>
                                                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">{{ $sale->created_at->format('H:i') }}</span>
                                                </div>
                                                <div class="text-sm text-gray-600 mt-1">
                                                    @if($sale->customer)
                                                        <i class="fas fa-user text-gray-400 mr-1"></i>
                                                        {{ $sale->customer->customer_type === 'individual'
                                                            ? $sale->customer->first_name . ' ' . $sale->customer->last_name
                                                            : $sale->customer->company_name }}
                                                    @else
                                                        <i class="fas fa-user-circle text-gray-400 mr-1"></i>
                                                        Walk-in Customer
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-xl font-bold text-gray-900">₦{{ number_format($sale->total_amount, 0) }}</span>
                                            <div class="flex items-center justify-end mt-1">
                                                <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full font-medium">
                                                    <i class="fas fa-check mr-1"></i>Completed
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Close Session Form -->
        <div class="bg-white/70 backdrop-blur-sm rounded-3xl shadow-2xl border border-white/50 overflow-hidden">
            <div class="bg-gradient-to-r from-red-500 via-red-600 to-rose-700 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-lock text-white"></i>
                    </div>
                    Close Session
                </h2>
                <p class="text-red-100 text-sm mt-1">Final cash count and session closure</p>
            </div>

            <form action="{{ route('tenant.pos.store-close-session', ['tenant' => $tenant->slug]) }}" method="POST" class="p-8">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Actual Cash Count -->
                    <div class="space-y-6">
                        <div>
                            <label for="closing_balance" class="flex items-center text-sm font-bold text-gray-700 mb-3">
                                <i class="fas fa-money-bill-wave text-green-600 mr-2"></i>
                                Actual Cash Count (₦)
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <input type="number"
                                       name="closing_balance"
                                       id="closing_balance"
                                       step="0.01"
                                       min="0"
                                       required
                                       value="{{ old('closing_balance') }}"
                                       class="w-full px-6 py-4 text-2xl font-bold border-2 border-gray-300 rounded-2xl focus:ring-4 focus:ring-red-500/20 focus:border-red-500 bg-gray-50 transition-all duration-200"
                                       placeholder="0.00">
                                <div class="absolute left-4 top-4 text-2xl font-bold text-gray-400">₦</div>
                            </div>
                            @error('closing_balance')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror

                            <!-- Difference Indicator -->
                            <div id="difference-indicator" class="mt-4 hidden">
                                <div id="difference-positive" class="bg-green-50 border border-green-200 rounded-xl p-4 text-green-700 hidden">
                                    <div class="flex items-center">
                                        <i class="fas fa-arrow-up text-green-600 mr-2"></i>
                                        <span class="font-semibold">Overage: ₦</span><span id="difference-amount-positive" class="font-bold"></span>
                                    </div>
                                    <p class="text-sm text-green-600 mt-1">You have more cash than expected</p>
                                </div>
                                <div id="difference-negative" class="bg-red-50 border border-red-200 rounded-xl p-4 text-red-700 hidden">
                                    <div class="flex items-center">
                                        <i class="fas fa-arrow-down text-red-600 mr-2"></i>
                                        <span class="font-semibold">Shortage: ₦</span><span id="difference-amount-negative" class="font-bold"></span>
                                    </div>
                                    <p class="text-sm text-red-600 mt-1">You have less cash than expected</p>
                                </div>
                                <div id="difference-exact" class="bg-green-50 border border-green-200 rounded-xl p-4 text-green-700 hidden">
                                    <div class="flex items-center">
                                        <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                        <span class="font-bold">Perfect balance!</span>
                                    </div>
                                    <p class="text-sm text-green-600 mt-1">Your cash count matches exactly</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Expected vs Actual -->
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-6 border border-gray-200">
                        <h3 class="font-bold text-gray-900 mb-4 text-lg flex items-center">
                            <i class="fas fa-balance-scale text-gray-600 mr-2"></i>
                            Balance Verification
                        </h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-3 border-b border-gray-200">
                                <span class="text-gray-700 font-medium">Expected Cash:</span>
                                <span class="font-bold text-2xl text-purple-700">₦{{ number_format($activeSession->opening_balance + $activeSession->total_cash_sales, 0) }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-200">
                                <span class="text-gray-700 font-medium">Actual Count:</span>
                                <span class="font-bold text-2xl text-blue-700" id="actual-count">₦0</span>
                            </div>
                            <div class="flex justify-between items-center py-3 bg-gray-100 rounded-xl px-4">
                                <span class="font-bold text-gray-900">Difference:</span>
                                <span id="difference-display" class="font-bold text-2xl">₦0</span>
                            </div>
                        </div>

                        <!-- Quick Status -->
                        <div class="mt-6 p-4 bg-white rounded-xl border border-gray-200">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-3" id="status-icon">
                                    <i class="fas fa-hourglass-half text-gray-500 text-2xl"></i>
                                </div>
                                <p class="text-gray-600 font-medium" id="status-text">Enter cash count to verify</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Closing Notes -->
                <div class="mt-8">
                    <label for="closing_notes" class="flex items-center text-sm font-bold text-gray-700 mb-3">
                        <i class="fas fa-sticky-note text-gray-600 mr-2"></i>
                        Closing Notes
                    </label>
                    <textarea name="closing_notes"
                              id="closing_notes"
                              rows="4"
                              class="w-full px-6 py-4 border-2 border-gray-300 rounded-2xl focus:ring-4 focus:ring-red-500/20 focus:border-red-500 bg-gray-50 transition-all duration-200 resize-none"
                              placeholder="Any notes about discrepancies, issues, or observations...">{{ old('closing_notes') }}</textarea>
                    @error('closing_notes')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Cash Counting Helper -->
                <div class="mt-8 bg-gradient-to-br from-blue-50 to-indigo-100 border-2 border-blue-200 rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-blue-900 font-bold text-lg flex items-center">
                            <i class="fas fa-calculator text-blue-700 mr-3"></i>
                            Cash Counting Assistant
                        </h3>
                        <span class="text-xs text-blue-600 bg-blue-200 px-3 py-1 rounded-full font-medium">HELPER</span>
                    </div>
                    <p class="text-blue-700 text-sm mb-6">Count each denomination and let us calculate the total for you</p>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-white rounded-xl p-4 border border-blue-200 hover:shadow-md transition-all duration-200">
                            <label class="block text-blue-800 font-bold mb-2 text-center">₦1000</label>
                            <input type="number" id="notes-1000" min="0" class="w-full px-3 py-2 border-2 border-blue-300 rounded-lg text-center font-bold focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="calculateTotal()" placeholder="0">
                        </div>
                        <div class="bg-white rounded-xl p-4 border border-blue-200 hover:shadow-md transition-all duration-200">
                            <label class="block text-blue-800 font-bold mb-2 text-center">₦500</label>
                            <input type="number" id="notes-500" min="0" class="w-full px-3 py-2 border-2 border-blue-300 rounded-lg text-center font-bold focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="calculateTotal()" placeholder="0">
                        </div>
                        <div class="bg-white rounded-xl p-4 border border-blue-200 hover:shadow-md transition-all duration-200">
                            <label class="block text-blue-800 font-bold mb-2 text-center">₦200</label>
                            <input type="number" id="notes-200" min="0" class="w-full px-3 py-2 border-2 border-blue-300 rounded-lg text-center font-bold focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="calculateTotal()" placeholder="0">
                        </div>
                        <div class="bg-white rounded-xl p-4 border border-blue-200 hover:shadow-md transition-all duration-200">
                            <label class="block text-blue-800 font-bold mb-2 text-center">₦100</label>
                            <input type="number" id="notes-100" min="0" class="w-full px-3 py-2 border-2 border-blue-300 rounded-lg text-center font-bold focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="calculateTotal()" placeholder="0">
                        </div>
                        <div class="bg-white rounded-xl p-4 border border-blue-200 hover:shadow-md transition-all duration-200">
                            <label class="block text-blue-800 font-bold mb-2 text-center">₦50</label>
                            <input type="number" id="notes-50" min="0" class="w-full px-3 py-2 border-2 border-blue-300 rounded-lg text-center font-bold focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="calculateTotal()" placeholder="0">
                        </div>
                        <div class="bg-white rounded-xl p-4 border border-blue-200 hover:shadow-md transition-all duration-200">
                            <label class="block text-blue-800 font-bold mb-2 text-center">₦20</label>
                            <input type="number" id="notes-20" min="0" class="w-full px-3 py-2 border-2 border-blue-300 rounded-lg text-center font-bold focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="calculateTotal()" placeholder="0">
                        </div>
                        <div class="bg-white rounded-xl p-4 border border-blue-200 hover:shadow-md transition-all duration-200">
                            <label class="block text-blue-800 font-bold mb-2 text-center">₦10</label>
                            <input type="number" id="notes-10" min="0" class="w-full px-3 py-2 border-2 border-blue-300 rounded-lg text-center font-bold focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="calculateTotal()" placeholder="0">
                        </div>
                        <div class="bg-white rounded-xl p-4 border border-blue-200 hover:shadow-md transition-all duration-200">
                            <label class="block text-blue-800 font-bold mb-2 text-center">₦5</label>
                            <input type="number" id="notes-5" min="0" class="w-full px-3 py-2 border-2 border-blue-300 rounded-lg text-center font-bold focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="calculateTotal()" placeholder="0">
                        </div>
                    </div>

                    <div class="mt-6 bg-white rounded-xl p-4 border-2 border-blue-300">
                        <div class="flex justify-between items-center">
                            <span class="text-blue-800 font-bold text-lg">Calculated Total:</span>
                            <span id="helper-total" class="text-blue-900 font-black text-3xl">₦0</span>
                        </div>
                        <button type="button" onclick="useHelperTotal()" class="mt-4 w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl font-bold transition-all duration-200 flex items-center justify-center space-x-2 shadow-lg">
                            <i class="fas fa-arrow-up"></i>
                            <span>Use This Amount</span>
                        </button>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-10 space-y-6">
                    <!-- Top navigation link -->
                    <div class="flex items-center justify-center">
                        <a href="{{ route('tenant.pos.index', ['tenant' => $tenant->slug]) }}"
                           class="inline-flex items-center text-gray-600 hover:text-gray-800 font-medium transition-colors duration-200 px-4 py-2 rounded-xl hover:bg-gray-100">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to POS
                        </a>
                    </div>

                    <!-- Main action buttons -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button type="button"
                                onclick="clearForm()"
                                class="flex-1 bg-gradient-to-r from-gray-400 to-gray-500 hover:from-gray-500 hover:to-gray-600 text-white px-8 py-4 rounded-2xl font-bold text-lg transition-all duration-200 flex items-center justify-center space-x-3 shadow-lg hover:shadow-xl border border-gray-300">
                            <i class="fas fa-eraser"></i>
                            <span>Clear Form</span>
                        </button>

                        <button type="submit"
                                class="flex-1 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-8 py-4 rounded-2xl font-bold text-lg transition-all duration-200 flex items-center justify-center space-x-3 shadow-lg hover:shadow-xl border border-red-500 transform hover:scale-105">
                            <i class="fas fa-lock"></i>
                            <span>Close Session</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const expectedBalance = {{ $activeSession->opening_balance + $activeSession->total_cash_sales }};

function calculateTotal() {
    const denominations = [1000, 500, 200, 100, 50, 20, 10, 5];
    let total = 0;

    denominations.forEach(denom => {
        const count = parseInt(document.getElementById(`notes-${denom}`).value) || 0;
        total += count * denom;
    });

    document.getElementById('helper-total').textContent = `₦${total.toFixed(2)}`;
}

function useHelperTotal() {
    const helperTotal = document.getElementById('helper-total').textContent.replace('₦', '');
    document.getElementById('closing_balance').value = parseFloat(helperTotal).toFixed(2);
    updateDifference();
}

function updateDifference() {
    const closingBalance = parseFloat(document.getElementById('closing_balance').value) || 0;
    const difference = closingBalance - expectedBalance;

    document.getElementById('actual-count').textContent = `₦${closingBalance.toFixed(2)}`;
    document.getElementById('difference-display').textContent = `₦${Math.abs(difference).toFixed(2)}`;

    // Show/hide difference indicators
    const indicator = document.getElementById('difference-indicator');
    const positive = document.getElementById('difference-positive');
    const negative = document.getElementById('difference-negative');
    const exact = document.getElementById('difference-exact');

    // Hide all first
    [positive, negative, exact].forEach(el => el.classList.add('hidden'));

    if (Math.abs(difference) < 0.01) {
        exact.classList.remove('hidden');
        document.getElementById('difference-display').className = 'text-green-600 font-bold';
    } else if (difference > 0) {
        positive.classList.remove('hidden');
        document.getElementById('difference-amount-positive').textContent = Math.abs(difference).toFixed(2);
        document.getElementById('difference-display').className = 'text-green-600 font-bold';
    } else {
        negative.classList.remove('hidden');
        document.getElementById('difference-amount-negative').textContent = Math.abs(difference).toFixed(2);
        document.getElementById('difference-display').className = 'text-red-600 font-bold';
    }

    indicator.classList.remove('hidden');
}

function clearForm() {
    document.getElementById('closing_balance').value = '';
    document.getElementById('closing_notes').value = '';

    // Clear cash counting helper
    const denominations = [1000, 500, 200, 100, 50, 20, 10, 5];
    denominations.forEach(denom => {
        document.getElementById(`notes-${denom}`).value = '';
    });

    document.getElementById('helper-total').textContent = '₦0.00';
    document.getElementById('difference-indicator').classList.add('hidden');
    document.getElementById('actual-count').textContent = '₦0.00';
    document.getElementById('difference-display').textContent = '₦0.00';
}

// Add event listener for closing balance input
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('closing_balance').addEventListener('input', updateDifference);
});
</script>
@endsection
