<!-- Cart Sidebar -->
<aside class="w-full lg:w-80 xl:w-96 bg-white/90 dark:bg-gray-800/90 backdrop-blur-md border-l border-gray-200/80 dark:border-gray-700/50 flex flex-col animate-slide-in-right fixed inset-y-0 right-0 z-40 lg:relative transform transition-all duration-300 ease-in-out shadow-2xl"
     :class="{'translate-x-0': showCartSidebar, 'translate-x-full lg:translate-x-0': !showCartSidebar}"
     style="height: calc(100vh - 60px); top: 10px;">

    <!-- Close Button (Mobile) -->
    <button @click="showCartSidebar = false"
            class="absolute top-4 left-4 lg:hidden bg-black/20 text-white rounded-full w-10 h-10 flex items-center justify-center shadow-lg hover:bg-black/30 transition-colors duration-200 z-50">
        <i class="fas fa-times text-lg"></i>
    </button>

    <!-- Cart Header -->
    <div class="p-4 md:p-5 border-b border-gray-200/80 dark:border-gray-700/50 bg-gradient-to-r from-[var(--color-dark-purple)] to-[var(--color-dark-purple-2)] text-white sticky top-0 z-10 shadow-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-shopping-cart text-lg"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold">Cart</h2>
                    <p class="text-white/70 text-xs" x-show="cartItems.length > 0" x-text="cartItems.length + ' item' + (cartItems.length !== 1 ? 's' : '')"></p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button @click="clearCart()"
                        x-show="cartItems.length > 0"
                        class="text-white/80 hover:text-white text-sm transition-colors duration-200 hover:bg-white/10 px-2 py-1 rounded-lg">
                    <i class="fas fa-trash mr-1"></i>
                    Clear
                </button>
            </div>
        </div>
        <div class="mt-3 text-white/90 text-sm flex justify-between items-center" x-show="cartItems.length > 0">
            <span class="font-medium">Total:</span>
            <span class="font-bold text-lg" x-text="'₦' + formatMoney(cartTotal)"></span>
        </div>
    </div>

    <!-- Cart Items -->
    <div class="flex-1 overflow-y-auto px-2 py-2" x-data="{ showAllItems: false, maxVisibleItems: 5 }" style="max-height: calc(100vh - 400px);">
        <!-- Empty Cart State -->
        <div x-show="cartItems.length === 0" class="text-center py-16 px-4">
            <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700/50 dark:to-gray-600/50 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-shopping-cart text-gray-400 dark:text-gray-500 text-2xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-3">Your Cart is Empty</h3>
            <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">Select products from the catalog to add them to your cart and begin checkout.</p>
        </div>

        <!-- Items Display -->
        <div x-show="cartItems.length > 0" class="space-y-2">
            <!-- Show/Hide Toggle for many items -->
            <div x-show="cartItems.length > maxVisibleItems" class="text-center pb-2 sticky top-0 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm z-10">
                <button @click="showAllItems = !showAllItems"
                        class="text-sm text-[var(--color-dark-purple)] dark:text-[var(--color-purple-accent)] hover:underline font-medium py-2 px-4 rounded-lg bg-white/80 dark:bg-gray-700/80 border border-gray-200/50 dark:border-gray-600/50">
                    <span x-show="!showAllItems">
                        <i class="fas fa-chevron-down mr-1"></i>
                        Show all <span x-text="cartItems.length"></span> items
                    </span>
                    <span x-show="showAllItems">
                        <i class="fas fa-chevron-up mr-1"></i>
                        Show latest <span x-text="maxVisibleItems"></span> items
                    </span>
                </button>
            </div>

            <!-- Scrollable Cart Items List -->
            <div class="space-y-3 pb-4">
                <template x-for="(item, index) in (showAllItems ? cartItems.slice().reverse() : cartItems.slice().reverse().slice(0, maxVisibleItems))" :key="item.id + '_' + index">
                    <div class="bg-white/90 dark:bg-gray-700/70 backdrop-blur-sm rounded-xl p-3 border border-gray-200/80 dark:border-gray-600/50 hover:border-[var(--color-dark-purple)]/50 dark:hover:border-[var(--color-purple-accent)]/50 transition-all duration-200 group shadow-sm hover:shadow-md mb-3">
                        <!-- Item Header -->
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1 min-w-0 pr-3">
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100 text-sm leading-tight mb-1 line-clamp-2" x-text="item.name"></h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1" x-text="item.sku"></p>
                                <p class="text-xs text-[var(--color-dark-purple)] dark:text-[var(--color-purple-accent)] font-medium">
                                    ₦<span x-text="formatMoney(item.unit_price)"></span> each
                                </p>
                            </div>
                            <button @click="removeFromCart(cartItems.findIndex(cartItem => cartItem.id === item.id))"
                                    class="text-red-500 hover:text-red-700 dark:hover:text-red-400 p-1.5 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all duration-200 shrink-0">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>

                        <!-- Quantity Controls -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-800/50 rounded-lg p-1">
                                <button @click="updateQuantity(cartItems.findIndex(cartItem => cartItem.id === item.id), item.quantity - 1)"
                                        class="w-7 h-7 bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-md flex items-center justify-center transition-colors duration-200 shadow-sm">
                                    <i class="fas fa-minus text-xs text-gray-600 dark:text-gray-300"></i>
                                </button>
                                <button @click="showQuantityModalFor(item, cartItems.findIndex(cartItem => cartItem.id === item.id))"
                                        class="min-w-[2.5rem] px-2 text-center font-semibold text-sm text-gray-900 dark:text-gray-100 hover:text-[var(--color-dark-purple)] dark:hover:text-[var(--color-purple-accent)] transition-colors duration-200"
                                        x-text="item.quantity"></button>
                                <button @click="updateQuantity(cartItems.findIndex(cartItem => cartItem.id === item.id), parseFloat(item.quantity) + 1)"
                                        class="w-7 h-7 bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-md flex items-center justify-center transition-colors duration-200 shadow-sm">
                                    <i class="fas fa-plus text-xs text-gray-600 dark:text-gray-300"></i>
                                </button>
                            </div>
                            <div class="text-right">
                                <div class="text-base font-bold text-[var(--color-dark-purple)] dark:text-[var(--color-purple-accent)]">
                                    ₦<span x-text="formatMoney(item.lineTotal)"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>    <!-- Totals -->
    <div class="border-t border-gray-200/80 dark:border-gray-700/50 p-3 md:p-4 space-y-3 bg-white/50 dark:bg-gray-800/50 transition-colors duration-300">
        <div class="flex justify-between text-sm">
            <span class="text-gray-700 dark:text-gray-300">Subtotal:</span>
            <span class="text-gray-900 dark:text-gray-100 font-medium" x-text="'₦' + formatMoney(cartSubtotal)"></span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-gray-700 dark:text-gray-300">Tax:</span>
            <span class="text-gray-900 dark:text-gray-100 font-medium" x-text="'₦' + formatMoney(cartTax)"></span>
        </div>
        <div class="flex justify-between text-lg font-bold border-t border-gray-200/80 dark:border-gray-700/50 pt-3 text-[var(--color-dark-purple)] dark:text-[var(--color-purple-accent)]">
            <span>Total:</span>
            <span x-text="'₦' + formatMoney(cartTotal)"></span>
        </div>
    </div>

    <!-- Customer and Checkout -->
    <div x-show="cartItems.length > 0" class="border-t border-gray-200/80 dark:border-gray-700/50 p-4 md:p-5 bg-gradient-to-t from-gray-50/90 to-white/90 dark:from-gray-800/90 dark:to-gray-700/90 backdrop-blur-md sticky bottom-0 shadow-2xl space-y-4">
        <!-- Customer Selection -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                <i class="fas fa-user mr-2"></i>Customer
            </label>
            <select x-model="selectedCustomer"
                    class="w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[var(--color-dark-purple)] dark:focus:ring-[var(--color-purple-accent)] focus:border-[var(--color-dark-purple)] dark:focus:border-[var(--color-purple-accent)] bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300">
                <option value="">Walk-in Customer</option>
                @if(isset($customers))
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">
                            @if($customer->customer_type === 'individual')
                                {{ $customer->first_name }} {{ $customer->last_name }}
                            @else
                                {{ $customer->company_name }}
                            @endif
                        </option>
                    @endforeach
                @endif
            </select>
        </div>

        <!-- Payment Method Selection -->
        {{-- <div class="space-y-3">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                <i class="fas fa-credit-card mr-2"></i>Payment Method
            </label>
            <div class="grid grid-cols-3 gap-2">
                <button @click="paymentMethod = 'cash'"
                        :class="paymentMethod === 'cash' ? 'bg-[var(--color-dark-purple)] dark:bg-[var(--color-purple-accent)] text-white shadow-lg scale-105' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                        class="py-3 px-3 rounded-xl transition-all duration-200 text-center text-sm font-medium flex flex-col items-center justify-center gap-1">
                    <i class="fas fa-money-bill-wave text-lg"></i>
                    <span>Cash</span>
                </button>
                <button @click="paymentMethod = 'card'"
                        :class="paymentMethod === 'card' ? 'bg-[var(--color-dark-purple)] dark:bg-[var(--color-purple-accent)] text-white shadow-lg scale-105' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                        class="py-3 px-3 rounded-xl transition-all duration-200 text-center text-sm font-medium flex flex-col items-center justify-center gap-1">
                    <i class="fas fa-credit-card text-lg"></i>
                    <span>Card</span>
                </button>
                <button @click="paymentMethod = 'transfer'"
                        :class="paymentMethod === 'transfer' ? 'bg-[var(--color-dark-purple)] dark:bg-[var(--color-purple-accent)] text-white shadow-lg scale-105' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                        class="py-3 px-3 rounded-xl transition-all duration-200 text-center text-sm font-medium flex flex-col items-center justify-center gap-1">
                    <i class="fas fa-exchange-alt text-lg"></i>
                    <span>Transfer</span>
                </button>
            </div>
        </div> --}}

        <!-- Proceed to Payment Button -->
        <div>
            <button @click="proceedToPayment()"
                    :disabled="cartItems.length === 0"
                    :class="cartItems.length === 0 ? 'bg-gray-300 dark:bg-gray-700 cursor-not-allowed' : 'bg-gradient-to-r from-[var(--color-dark-purple)] to-[var(--color-dark-purple-2)] hover:from-[var(--color-purple-accent)] hover:to-[var(--color-dark-purple)] transform hover:scale-[1.02] hover:shadow-2xl active:scale-[0.98]'"
                    class="w-full py-4 px-4 rounded-xl font-bold text-white transition-all duration-300 flex items-center justify-center gap-3 shadow-lg group">
                <i class="fas fa-credit-card text-lg group-hover:scale-110 transition-transform duration-200"></i>
                <span class="text-lg">Proceed to Payment</span>
                <i class="fas fa-arrow-right text-sm group-hover:translate-x-1 transition-transform duration-200"></i>
            </button>
        </div>
    </div>
</aside>
