<div x-show="showPaymentModal"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 scale-95"
     x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95"
     class="fixed inset-0 bg-black/50 dark:bg-gray-900/80 flex items-center justify-center z-50 p-4"
     style="display: none;">
    <div class="bg-white/80 dark:bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto border border-gray-200/80 dark:border-gray-700/50 dark:text-white">
        <!-- Payment Modal Content -->
        <div class="p-6">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-[var(--color-dark-purple)] to-[var(--color-purple-accent)] rounded-lg flex items-center justify-center shadow-md">
                        <i class="fas fa-credit-card text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Complete the transaction</p>
                    </div>
                </div>
                <button @click="showPaymentModal = false"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Order Summary -->
            <div class="bg-gray-50/50 dark:bg-gray-700/30 rounded-xl p-4 mb-6 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/30">
                <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Order Summary</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="dark:text-gray-300">Subtotal:</span>
                        <span x-text="'₦' + formatMoney(cartSubtotal)" class="dark:text-white font-medium"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="dark:text-gray-300">Tax:</span>
                        <span x-text="'₦' + formatMoney(cartTax)" class="dark:text-white font-medium"></span>
                    </div>
                    <div class="flex justify-between font-bold text-lg border-t dark:border-gray-600/50 pt-2 text-[var(--color-dark-purple)] dark:text-[var(--color-purple-accent)]">
                        <span>Total:</span>
                        <span x-text="'₦' + formatMoney(cartTotal)"></span>
                    </div>
                </div>
            </div>

    <!-- Payment Methods -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Payment Methods</h4>
        <template x-for="(payment, index) in payments" :key="index">
            <div class="bg-white/50 dark:bg-gray-700/30 border border-gray-200/80 dark:border-gray-600/50 rounded-lg p-4 mb-3 backdrop-blur-sm transition-all duration-300 hover:shadow-md">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Method</label>
                        <select x-model="payment.method_id"
                                class="w-full px-3 py-2 border border-gray-300/80 dark:border-gray-600/60 bg-white/80 dark:bg-gray-800/60 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-[var(--color-purple-accent)] focus:border-[var(--color-purple-accent)] transition-colors duration-200">
                            <option value="">Select Method</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method->id }}">{{ $method->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Amount</label>
                        <input type="number"
                               x-model="payment.amount"
                               class="w-full px-3 py-2 border border-gray-300/80 dark:border-gray-600/60 bg-white/80 dark:bg-gray-800/60 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-[var(--color-purple-accent)] focus:border-[var(--color-purple-accent)]"
                               min="0"
                               step="0.01"
                               placeholder="0.00">

                        <!-- Quick Amount Buttons for Cash Payment -->
                        {{-- <div x-show="getPaymentMethod(payment.method_id)?.name?.toLowerCase().includes('cash')" class="mt-3">
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Quick Amounts</label>
                            <div class="grid grid-cols-2 gap-4">
                                <button type="button" @click="payment.amount = cartTotal"
                                        class="px-3 py-2 text-xs bg-[var(--color-dark-purple)] dark:bg-[var(--color-purple-accent)] text-white rounded-lg hover:opacity-90 transition-opacity duration-200 font-medium">
                                    <div class="flex flex-col items-center">
                                        <span class="text-xs opacity-80">Exact</span>
                                        <span>₦<span x-text="formatMoney(cartTotal)"></span></span>
                                    </div>
                                </button>
                                <button type="button" @click="payment.amount = Math.ceil(cartTotal / 1000) * 1000"
                                        class="px-3 py-2 text-xs bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200 font-medium">
                                    <div class="flex flex-col items-center">
                                        <span class="text-xs opacity-80">Round Up</span>
                                        <span>₦<span x-text="formatMoney(Math.ceil(cartTotal / 1000) * 1000)"></span></span>
                                    </div>
                                </button>
                                <button type="button" @click="payment.amount = cartTotal + 500"
                                        class="px-3 py-2 text-xs bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200 font-medium">
                                    <div class="flex flex-col items-center">
                                        <span class="text-xs opacity-80">+ Tip</span>
                                        <span>₦<span x-text="formatMoney(cartTotal + 500)"></span></span>
                                    </div>
                                </button>
                                <button type="button" @click="payment.amount = cartTotal + 1000"
                                        class="px-3 py-2 text-xs bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-200 font-medium">
                                    <div class="flex flex-col items-center">
                                        <span class="text-xs opacity-80">+ Extra</span>
                                        <span>₦<span x-text="formatMoney(cartTotal + 1000)"></span></span>
                                    </div>
                                </button>
                            </div>
                        </div> --}}
                    </div>
                </div>
                <div class="mt-3" x-show="payment.method_id && getPaymentMethod(payment.method_id)?.requires_reference">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reference Number</label>
                    <input type="text"
                           x-model="payment.reference"
                           class="w-full px-3 py-2 border border-gray-300/80 dark:border-gray-600/60 bg-white/80 dark:bg-gray-800/60 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-[var(--color-purple-accent)] focus:border-[var(--color-purple-accent)]"
                           placeholder="Enter reference number">
                </div>
                <div class="mt-3 flex justify-end" x-show="payments.length > 1">
                    <button @click="removePayment(index)"
                            class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 text-sm transition-colors duration-200">
                        <i class="fas fa-trash mr-1"></i> Remove
                    </button>
                </div>
            </div>
        </template>

        <button @click="addPayment()"
                class="text-[var(--color-dark-purple)] dark:text-[var(--color-purple-accent)] hover:text-[var(--color-purple-accent)] dark:hover:text-white text-sm font-medium transition-colors duration-200">
            <i class="fas fa-plus mr-1"></i> Add Another Payment Method
        </button>
    </div>

    <!-- Payment Summary -->
    <div class="bg-blue-50/50 dark:bg-blue-900/30 rounded-lg p-4 mb-6 backdrop-blur-sm border border-blue-200/50 dark:border-blue-800/30">
        <div class="flex justify-between items-center">
            <div>
                <span class="text-sm text-gray-600 dark:text-gray-300">Total Paid:</span>
                <span class="ml-2 font-semibold text-blue-600 dark:text-blue-400" x-text="'₦' + formatMoney(totalPaid)"></span>
            </div>
            <div>
                <span class="text-sm text-gray-600 dark:text-gray-300">Balance:</span>
                <span class="ml-2 font-semibold"
                      :class="balance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'"
                      x-text="'₦' + formatMoney(balance)"></span>
            </div>
        </div>
        <div class="mt-2" x-show="change > 0">
            <span class="text-sm text-gray-600 dark:text-gray-300">Change Due:</span>
            <span class="ml-2 font-bold text-green-600 dark:text-green-400 text-lg" x-text="'₦' + formatMoney(change)"></span>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex space-x-3">
        <button @click="showPaymentModal = false"
                class="flex-1 bg-gray-300/50 dark:bg-gray-700/50 hover:bg-gray-400/50 dark:hover:bg-gray-600/50 text-gray-700 dark:text-white py-3 px-4 rounded-xl font-semibold transition-colors duration-200 border border-gray-300/80 dark:border-gray-600/60">
            Cancel
        </button>
        <button @click="completeSale()"
                :disabled="isProcessing || balance < 0 || totalPaid === 0"
                :class="(isProcessing || balance < 0 || totalPaid === 0) ? 'bg-gray-400/80 dark:bg-gray-700/80 cursor-not-allowed' : 'bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800'"
                class="flex-1 text-white py-3 px-4 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center space-x-2 shadow-lg shadow-green-500/20">
            <i x-show="!isProcessing" class="fas fa-check"></i>
            <i x-show="isProcessing" class="fas fa-spinner fa-spin"></i>
            <span x-text="isProcessing ? 'Processing...' : 'Complete Sale'"></span>
        </button>
    </div>

    <!-- Quick Amount Buttons -->
    <div class="mt-4">
        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quick Amount</p>
        <div class="grid grid-cols-4 gap-2">
            <template x-for="amount in [500, 1000, 2000, 5000]" :key="amount">
                <button @click="setQuickAmount(amount)"
                        class="bg-gray-100/80 dark:bg-gray-700/60 hover:bg-gray-200/80 dark:hover:bg-gray-600/60 text-gray-700 dark:text-gray-200 py-2 px-3 rounded-lg text-sm font-medium transition-colors duration-200 border border-gray-200/80 dark:border-gray-600/50"
                        x-text="'₦' + amount"></button>
            </template>
        </div>
        <button @click="setExactAmount()"
                class="w-full mt-2 bg-[var(--color-purple-accent)]/10 dark:bg-[var(--color-purple-accent)]/20 hover:bg-[var(--color-purple-accent)]/20 dark:hover:bg-[var(--color-purple-accent)]/30 text-[var(--color-dark-purple)] dark:text-[var(--color-purple-accent)] py-2 px-3 rounded-lg text-sm font-medium transition-colors duration-200 border border-purple-200/50 dark:border-purple-800/30">
            Exact Amount
        </button>
    </div>
        </div>
    </div>
</div>
