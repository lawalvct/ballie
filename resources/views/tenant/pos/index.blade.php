@extends('layouts.tenant')

@section('title', 'Point of Sale - ' . tenant()->name)

@section('page-title', 'Point of Sale')
@section('page-description')
    <span class="hidden md:inline">
        Manage your sales transactions, print receipts, track inventory in real time, and provide a seamless checkout experience for your customers.
    </span>
@endsection
@section('content')
<div x-data="posSystem()"
     x-init="init()"
     x-ref="posRoot"
     :class="{'touch-mode': touchMode, 'dark-mode': darkMode}"
     class="min-h-screen bg-gray-100 dark:bg-gray-900 transition-colors duration-300">

    <!-- Notification -->
    <div x-show="showNotification"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-4"
         class="fixed bottom-4 right-4 z-50 shadow-lg rounded-lg p-4 max-w-md glass-morphism"
         :class="{
             'bg-green-50/90 dark:bg-green-900/50 border-green-200 dark:border-green-700': notificationType === 'success',
             'bg-blue-50/90 dark:bg-blue-900/50 border-blue-200 dark:border-blue-700': notificationType === 'info',
             'bg-yellow-50/90 dark:bg-yellow-900/50 border-yellow-200 dark:border-yellow-700': notificationType === 'warning',
             'bg-red-50/90 dark:bg-red-900/50 border-red-200 dark:border-red-700': notificationType === 'error'
         }">
        <div class="flex items-center">
            <div class="flex-shrink-0 mr-3">
                <template x-if="notificationType === 'success'">
                    <i class="fas fa-check-circle text-green-500 dark:text-green-400 text-xl"></i>
                </template>
                <template x-if="notificationType === 'info'">
                    <i class="fas fa-info-circle text-blue-500 dark:text-blue-400 text-xl"></i>
                </template>
                <template x-if="notificationType === 'warning'">
                    <i class="fas fa-exclamation-triangle text-yellow-500 dark:text-yellow-400 text-xl"></i>
                </template>
                <template x-if="notificationType === 'error'">
                    <i class="fas fa-times-circle text-red-500 dark:text-red-400 text-xl"></i>
                </template>
            </div>
            <div class="flex-1">
                <p class="font-medium"
                   :class="{
                       'text-green-800 dark:text-green-200': notificationType === 'success',
                       'text-blue-800 dark:text-blue-200': notificationType === 'info',
                       'text-yellow-800 dark:text-yellow-200': notificationType === 'warning',
                       'text-red-800 dark:text-red-200': notificationType === 'error'
                   }"
                   x-text="notificationMessage"></p>
            </div>
            <div class="ml-4">
                <button @click="showNotification = false"
                        class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="pos-container">
        @include('tenant.pos.partials.header')

        <!-- Feature Hint Banner (dismissible) -->
        {{-- <div x-data="{ showHint: !localStorage.getItem('pos_hint_dismissed') }"
             x-show="showHint"
             x-transition
             class="mx-4 mt-4 bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 border border-purple-200 dark:border-purple-800 rounded-xl p-4 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-magic text-white text-sm"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-purple-900 dark:text-purple-100 mb-1">✨ Enhanced POS Features</h4>
                        <p class="text-xs text-purple-700 dark:text-purple-300 mb-2">
                            <strong>New:</strong> Click cart quantities for precise input • Press <kbd class="px-1.5 py-0.5 bg-purple-200 dark:bg-purple-800 rounded text-xs">F1</kbd> for shortcuts • Real-time search • Improved checkout
                        </p>
                        <div class="flex flex-wrap gap-2 text-xs">
                            <span class="px-2 py-1 bg-white dark:bg-gray-800 rounded-md text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-700">
                                <i class="fas fa-keyboard text-xs mr-1"></i>Keyboard shortcuts
                            </span>
                            <span class="px-2 py-1 bg-white dark:bg-gray-800 rounded-md text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-700">
                                <i class="fas fa-calculator text-xs mr-1"></i>Quick quantity input
                            </span>
                            <span class="px-2 py-1 bg-white dark:bg-gray-800 rounded-md text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-700">
                                <i class="fas fa-search text-xs mr-1"></i>Live filtering
                            </span>
                        </div>
                    </div>
                </div>
                <button @click="showHint = false; localStorage.setItem('pos_hint_dismissed', 'true')"
                        class="text-purple-400 hover:text-purple-600 dark:hover:text-purple-200 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div> --}}

        @if(!isset($activeSession))
            @include('tenant.pos.partials.no-session')
        @else
            <!-- Main POS Interface -->
            <div class="flex flex-col lg:flex-row overflow-hidden pos-interface relative" style="height: calc(100vh - 140px);">
                <!-- Mobile Cart Toggle Button -->
                <button @click="showCartSidebar = !showCartSidebar"
                        class="fixed bottom-4 left-4 z-30 lg:hidden bg-[var(--color-dark-purple)] dark:bg-[var(--color-purple-accent)] text-white rounded-full w-14 h-14 flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105">
                    <div class="relative">
                        <i class="fas fa-shopping-cart text-lg"></i>
                        <div x-show="cartItems.length > 0"
                             class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center cart-badge">
                            <span x-text="cartItems.length"></span>
                        </div>
                    </div>
                </button>

                <!-- Mobile Cart Backdrop Overlay -->
                <div x-show="showCartSidebar"
                     @click="showCartSidebar = false"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-black/50 dark:bg-gray-900/50 backdrop-blur-sm z-30 lg:hidden"></div>

                <!-- Product Grid -->
                <main class="flex-1 bg-gray-100 dark:bg-gray-900 p-4 md:p-6 overflow-y-auto transition-colors duration-300"
                     :class="{'lg:block': !showCartSidebar}">
                    @include('tenant.pos.partials.product-grid')
                    @includeWhen(isset($recentSales) && $recentSales->count() > 0, 'tenant.pos.partials.recent-sales')
                </main>

                @include('tenant.pos.partials.cart-sidebar')
            </div>

            @include('tenant.pos.partials.payment-modal')
        @endif
    </div>

    <!-- Quantity Input Modal -->
    <div x-show="showQuantityModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @keydown.enter="confirmQuantity()"
         @keydown.escape="showQuantityModal = false"
         class="fixed inset-0 bg-black/50 dark:bg-gray-900/80 flex items-center justify-center z-50 p-4"
         style="display: none;">
        <div class="bg-white dark:bg-gray-800 backdrop-blur-sm rounded-2xl shadow-2xl w-full max-w-sm border border-gray-100 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Enter Quantity</h3>
                <button @click="showQuantityModal = false; quantityInput = ''"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Quantity for: <span class="text-[var(--color-dark-purple)] dark:text-[var(--color-purple-accent)] font-semibold" x-text="quantityModalProduct?.name"></span>
                </label>
                <input type="number"
                       x-model="quantityInput"
                       min="0.01"
                       step="0.01"
                       class="w-full px-4 py-3 text-lg text-center border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[var(--color-dark-purple)] focus:border-[var(--color-dark-purple)] bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                       placeholder="0.00"
                       autofocus>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                    Available: <span x-text="quantityModalProduct?.stock_quantity"></span> units
                </p>
            </div>

            <!-- Number Pad -->
            <div class="grid grid-cols-3 gap-2 mb-4">
                <template x-for="num in [1,2,3,4,5,6,7,8,9]" :key="num">
                    <button @click="quantityInput = (quantityInput || '') + num.toString()"
                            class="py-4 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg text-lg font-semibold text-gray-900 dark:text-white transition-colors duration-200">
                        <span x-text="num"></span>
                    </button>
                </template>
                <button @click="quantityInput = (quantityInput || '') + '.'"
                        class="py-4 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg text-lg font-semibold text-gray-900 dark:text-white transition-colors duration-200">
                    .
                </button>
                <button @click="quantityInput = (quantityInput || '') + '0'"
                        class="py-4 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg text-lg font-semibold text-gray-900 dark:text-white transition-colors duration-200">
                    0
                </button>
                <button @click="quantityInput = quantityInput.slice(0, -1)"
                        class="py-4 bg-red-100 dark:bg-red-900/30 hover:bg-red-200 dark:hover:bg-red-900/50 rounded-lg text-lg font-semibold text-red-600 dark:text-red-400 transition-colors duration-200">
                    <i class="fas fa-backspace"></i>
                </button>
            </div>

            <!-- Actions -->
            <div class="flex gap-3">
                <button @click="showQuantityModal = false; quantityInput = ''"
                        class="flex-1 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-xl font-semibold text-gray-700 dark:text-gray-200 transition-colors duration-200">
                    Cancel
                </button>
                <button @click="confirmQuantity()"
                        class="flex-1 py-3 bg-gradient-to-r from-[var(--color-dark-purple)] to-[var(--color-dark-purple-2)] hover:opacity-90 rounded-xl font-semibold text-white transition-opacity duration-200">
                    Confirm
                </button>
            </div>
        </div>
    </div>

    <!-- Keyboard Shortcuts Guide -->
    <div x-show="showKeyboardShortcuts"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 bg-black/50 dark:bg-gray-900/80 flex items-center justify-center z-50 p-4"
         style="display: none;">
        <div class="bg-white dark:bg-gray-800 backdrop-blur-sm rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto border border-gray-100 dark:border-gray-700 dark:text-white p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-[var(--color-dark-purple)] to-[var(--color-light-purple)] rounded-lg flex items-center justify-center shadow-md">
                        <i class="fas fa-keyboard text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Keyboard Shortcuts</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Quick access to features</p>
                    </div>
                </div>
                <button @click="showKeyboardShortcuts = false"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <span class="font-medium">Toggle Quick Add</span>
                    <span class="shortcut-label">Ctrl+B</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <span class="font-medium">Toggle Dark Mode</span>
                    <span class="shortcut-label">Ctrl+D</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <span class="font-medium">Toggle Fullscreen</span>
                    <span class="shortcut-label">Ctrl+F</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <span class="font-medium">Show Shortcuts</span>
                    <span class="shortcut-label">Ctrl+K / F1</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <span class="font-medium">Proceed to Payment</span>
                    <span class="shortcut-label">Ctrl+P</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <span class="font-medium">Focus Search</span>
                    <span class="shortcut-label">F2</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <span class="font-medium">Toggle View Mode</span>
                    <span class="shortcut-label">F3</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <span class="font-medium">Clear Cart</span>
                    <span class="shortcut-label">F4</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <span class="font-medium">Close Modals / Exit</span>
                    <span class="shortcut-label">Esc</span>
                </div>
            </div>
            <div class="mt-6">
                <button @click="showKeyboardShortcuts = false"
                        class="w-full py-3 px-4 bg-[var(--color-dark-purple)] dark:bg-[var(--color-purple-accent)] text-white rounded-lg hover:opacity-90 transition-opacity duration-200">
                    Got it
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function posSystem() {
    return {
        cartItems: [],
        searchQuery: '',
        selectedCategory: '',
        selectedCustomer: '',
        showPaymentModal: false,
        showRecentSales: false,
        touchMode: false,
        isFullscreen: false,
        darkMode: false,
        quickAddEnabled: true,
        favoriteProducts: [],
        showScanner: false,
        categoryFilter: 'all',
        paymentMethod: 'cash',
        viewMode: 'grid', // grid or list
        showMenuDropdown: false,
        showCartSidebar: (typeof window !== 'undefined' && window.innerWidth >= 1024), // Show by default on desktop, hidden on mobile
        showKeyboardShortcuts: false,
        showQuantityModal: false,
        quantityInput: '',
        quantityModalProduct: null,
        quantityModalCartIndex: null,
        payments: [{
            method_id: '',
            amount: 0,
            reference: ''
        }],
        isProcessing: false,
        showNotification: false,
        notificationMessage: '',
        notificationType: 'info', // 'info', 'success', 'warning', 'error'

        // Helper method to get cash payment method ID
        getCashPaymentMethodId() {
            @if(isset($paymentMethods))
                const methods = @json($paymentMethods);
                const cashMethod = methods.find(method =>
                    method.name.toLowerCase().includes('cash') ||
                    method.name.toLowerCase() === 'cash'
                );
                return cashMethod ? cashMethod.id : (methods.length > 0 ? methods[0].id : '');
            @else
                return 1; // Fallback cash method ID
            @endif
        },

        // Computed properties
        get cartSubtotal() {
            return this.cartItems.reduce((sum, item) => sum + (parseFloat(item.quantity) * parseFloat(item.unit_price)), 0);
        },

        get cartTax() {
            return this.cartItems.reduce((sum, item) => {
                const itemSubtotal = parseFloat(item.quantity) * parseFloat(item.unit_price);
                return sum + (itemSubtotal * (parseFloat(item.tax_rate) || 0) / 100);
            }, 0);
        },

        get cartTotal() {
            return this.cartSubtotal + this.cartTax;
        },

        get totalPaid() {
            return this.payments.reduce((sum, payment) => sum + (parseFloat(payment.amount) || 0), 0);
        },

        get balance() {
            return this.totalPaid - this.cartTotal;
        },

        get change() {
            return Math.max(0, this.balance);
        },

        // Helper methods
        formatMoney(amount) {
            return new Intl.NumberFormat('en-NG', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(amount || 0);
        },

        // Payment methods
        addPayment() {
            this.payments.push({
                method_id: '',
                amount: Math.max(0, this.cartTotal - this.totalPaid),
                reference: ''
            });
        },

        removePayment(index) {
            if (this.payments.length > 1) {
                this.payments.splice(index, 1);
            }
        },

        setQuickAmount(amount) {
            if (this.payments.length > 0) {
                this.payments[0].amount = amount;
            }
        },

        setExactAmount() {
            if (this.payments.length > 0) {
                this.payments[0].amount = this.cartTotal;
            }
        },

        getPaymentMethod(methodId) {
            // Check if payment methods are available from server
            @if(isset($paymentMethods))
                const methods = @json($paymentMethods);
                return methods.find(method => method.id == methodId);
            @else
                // Fallback for demo purposes
                const methods = [
                    { id: 1, name: 'Cash', requires_reference: false },
                    { id: 2, name: 'Card', requires_reference: true },
                    { id: 3, name: 'Transfer', requires_reference: true }
                ];
                return methods.find(method => method.id == methodId);
            @endif
        },

        async completeSale() {
            // Validation
            if (this.cartItems.length === 0) {
                this.showNotification = true;
                this.notificationMessage = 'Cart is empty!';
                this.notificationType = 'error';
                return;
            }

            if (this.totalPaid < this.cartTotal) {
                this.showNotification = true;
                this.notificationMessage = 'Payment amount is insufficient!';
                this.notificationType = 'error';
                return;
            }

            // Validate payment methods
            for (let payment of this.payments) {
                if (!payment.method_id || payment.amount <= 0) {
                    this.showNotification = true;
                    this.notificationMessage = 'Please complete all payment details!';
                    this.notificationType = 'error';
                    return;
                }
            }

            this.isProcessing = true;

            try {
                const response = await fetch('{{ route("tenant.pos.store", ["tenant" => $tenant->slug]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        customer_id: this.selectedCustomer || null,
                        items: this.cartItems.map(item => ({
                            product_id: item.id,
                            quantity: item.quantity,
                            unit_price: item.unit_price,
                            discount_amount: 0,
                        })),
                        payments: this.payments,
                        notes: null
                    })
                });

                const result = await response.json();

                if (result.success) {
                    // Show success message
                    this.notificationMessage = result.message || 'Sale completed successfully!';
                    this.notificationType = 'success';
                    this.showNotification = true;

                    // Broadcast sale completion to customer display
                    this.broadcastSaleComplete(this.cartTotal, result.change_amount || 0);

                    // Show change if any
                    if (result.change_amount > 0) {
                        setTimeout(() => {
                            this.notificationMessage = `Change: ₦${this.formatMoney(result.change_amount)}`;
                            this.notificationType = 'info';
                            this.showNotification = true;
                        }, 2000);
                    }

                    // Close modal and reset
                    this.showPaymentModal = false;
                    this.cartItems = [];
                    this.selectedCustomer = '';
                    this.payments = [{
                        method_id: this.getCashPaymentMethodId(),
                        amount: 0,
                        reference: ''
                    }];

                    // Open receipt in new window
                    if (result.receipt_url) {
                        setTimeout(() => {
                            window.open(result.receipt_url, '_blank');
                        }, 1000);
                    }

                    // Hide notification after 5 seconds
                    setTimeout(() => {
                        this.showNotification = false;
                    }, 5000);
                } else {
                    throw new Error(result.message || 'Sale failed');
                }
            } catch (error) {
                console.error('Sale error:', error);
                this.notificationMessage = error.message || 'Failed to complete sale. Please try again.';
                this.notificationType = 'error';
                this.showNotification = true;

                setTimeout(() => {
                    this.showNotification = false;
                }, 5000);
            } finally {
                this.isProcessing = false;
            }
        },

        // Methods
        addToCart(product) {
            const existingItem = this.cartItems.find(item => item.id === product.id);

            if (existingItem) {
                // Check stock before adding
                if (existingItem.quantity + 1 > existingItem.stock_quantity) {
                    this.notificationMessage = `Only ${existingItem.stock_quantity} units available in stock`;
                    this.notificationType = 'warning';
                    this.showNotification = true;
                    setTimeout(() => this.showNotification = false, 3000);
                    return;
                }
                this.updateQuantity(this.cartItems.indexOf(existingItem), parseFloat(existingItem.quantity) + 1);
            } else {
                // Check if product has stock
                if (product.stock_quantity <= 0) {
                    this.notificationMessage = `${product.name} is out of stock`;
                    this.notificationType = 'error';
                    this.showNotification = true;
                    setTimeout(() => this.showNotification = false, 3000);
                    return;
                }

                this.cartItems.push({
                    id: product.id,
                    name: product.name,
                    sku: product.sku,
                    quantity: 1,
                    unit_price: parseFloat(product.selling_price),
                    tax_rate: parseFloat(product.tax_rate || 0),
                    stock_quantity: product.stock_quantity,
                    lineTotal: parseFloat(product.selling_price)
                });

                this.updateLineTotal(this.cartItems.length - 1);

                // Broadcast to customer display
                this.broadcastCartUpdate();

                // Show notification
                this.notificationMessage = `${product.name} added to cart`;
                this.notificationType = 'success';
                this.showNotification = true;
                setTimeout(() => this.showNotification = false, 2000);
            }

            // Show cart on mobile when adding first item
            if (typeof window !== 'undefined' && window.innerWidth < 1024 && this.cartItems.length === 1) {
                this.showCartSidebar = true;
            }
        },

        removeFromCart(index) {
            this.cartItems.splice(index, 1);
            this.broadcastCartUpdate();
        },

        updateQuantity(index, newQuantity) {
            if (newQuantity <= 0) {
                this.removeFromCart(index);
                return;
            }

            const item = this.cartItems[index];
            if (newQuantity > item.stock_quantity) {
                alert(`Only ${item.stock_quantity} items available in stock`);
                return;
            }

            item.quantity = newQuantity;
            this.updateLineTotal(index);
            this.broadcastCartUpdate();
        },

        updateLineTotal(index) {
            const item = this.cartItems[index];
            const itemSubtotal = parseFloat(item.quantity) * parseFloat(item.unit_price);
            const itemTax = itemSubtotal * (parseFloat(item.tax_rate) || 0) / 100;
            item.lineTotal = itemSubtotal + itemTax;
        },

        clearCart() {
            if (this.cartItems.length === 0) {
                return;
            }

            if (confirm('Are you sure you want to clear the cart? This will remove all items.')) {
                this.cartItems = [];
                this.broadcastCartClear();
                this.notificationMessage = 'Cart cleared';
                this.notificationType = 'info';
                this.showNotification = true;
                setTimeout(() => this.showNotification = false, 2000);
            }
        },

        // Broadcast cart updates to customer display
        broadcastCartUpdate() {
            const cartData = {
                items: this.cartItems.map(item => ({
                    name: item.name,
                    quantity: item.quantity,
                    price: item.unit_price,
                    total: item.quantity * item.unit_price
                })),
                subtotal: this.cartSubtotal,
                tax: this.cartTax,
                discount: 0,
                total: this.cartTotal
            };

            // Broadcast via BroadcastChannel
            if (this.customerDisplayChannel) {
                this.customerDisplayChannel.postMessage({
                    type: 'CART_UPDATE',
                    data: cartData
                });
            }

            // Also save to localStorage as fallback
            localStorage.setItem('pos_customer_cart', JSON.stringify(cartData));
        },

        broadcastCartClear() {
            if (this.customerDisplayChannel) {
                this.customerDisplayChannel.postMessage({
                    type: 'CART_CLEAR'
                });
            }
            localStorage.removeItem('pos_customer_cart');
        },

        broadcastSaleComplete(total, change) {
            if (this.customerDisplayChannel) {
                this.customerDisplayChannel.postMessage({
                    type: 'SALE_COMPLETE',
                    data: { total, change }
                });
            }
        },

        openCustomerDisplay() {
            const url = '{{ route("tenant.pos.customer-display", ["tenant" => $tenant->slug]) }}';
            const features = 'width=1024,height=768,menubar=no,toolbar=no,location=no,status=no';
            window.open(url, 'CustomerDisplay', features);

            this.notificationMessage = 'Customer display opened in new window';
            this.notificationType = 'success';
            this.showNotification = true;
            setTimeout(() => this.showNotification = false, 2000);
        },

        updateCart() {
            // Update cart totals and UI
            this.$nextTick(() => {
                // Force reactivity update
                this.cartItems = [...this.cartItems];
            });
        },

        proceedToPayment() {
            if (this.cartItems.length === 0) {
                this.notificationMessage = 'Cart is empty. Add items before proceeding to payment.';
                this.notificationType = 'warning';
                this.showNotification = true;

                setTimeout(() => {
                    this.showNotification = false;
                }, 3000);
                return;
            }

            // Reset payments array with cash as default and set amount to cart total
            this.payments = [{
                method_id: this.getCashPaymentMethodId(),
                amount: this.cartTotal,
                reference: ''
            }];

            this.showPaymentModal = true;
        },

        async toggleFullscreen() {
            const el = this.$refs.posRoot || document.documentElement;
            try {
                if (!document.fullscreenElement) {
                    if (el.requestFullscreen) await el.requestFullscreen();
                } else {
                    if (document.exitFullscreen) await document.exitFullscreen();
                }
            } catch (e) {
                console.error('Fullscreen toggle failed', e);
            }
        },

        toggleTouchMode() {
            this.touchMode = !this.touchMode;
        },

        toggleDarkMode() {
            this.darkMode = !this.darkMode;
            if (typeof localStorage !== 'undefined') {
                localStorage.setItem('pos_dark_mode', this.darkMode ? 'true' : 'false');
            }
        },

        toggleViewMode() {
            // Ensure viewMode is initialized
            if (!this.viewMode) {
                this.viewMode = 'grid';
            }
            this.viewMode = this.viewMode === 'grid' ? 'list' : 'grid';
            if (typeof localStorage !== 'undefined') {
                localStorage.setItem('pos_view_mode', this.viewMode);
            }
        },

        toggleQuickAdd() {
            this.quickAddEnabled = !this.quickAddEnabled;
        },

        toggleKeyboardShortcuts() {
            this.showKeyboardShortcuts = !this.showKeyboardShortcuts;
        },

        showQuantityModalFor(product, cartIndex = null) {
            this.quantityModalProduct = product;
            this.quantityModalCartIndex = cartIndex;
            this.quantityInput = cartIndex !== null ? this.cartItems[cartIndex].quantity.toString() : '1';
            this.showQuantityModal = true;
        },

        confirmQuantity() {
            const qty = parseFloat(this.quantityInput);

            if (!qty || qty <= 0) {
                this.notificationMessage = 'Please enter a valid quantity';
                this.notificationType = 'error';
                this.showNotification = true;
                setTimeout(() => this.showNotification = false, 3000);
                return;
            }

            if (this.quantityModalCartIndex !== null) {
                // Updating existing cart item
                this.updateQuantity(this.quantityModalCartIndex, qty);
            } else {
                // Adding new item with custom quantity
                const product = this.quantityModalProduct;
                if (qty > product.stock_quantity) {
                    this.notificationMessage = `Only ${product.stock_quantity} units available`;
                    this.notificationType = 'warning';
                    this.showNotification = true;
                    setTimeout(() => this.showNotification = false, 3000);
                    return;
                }

                // Check if product already in cart
                const existingIndex = this.cartItems.findIndex(item => item.id === product.id);
                if (existingIndex !== -1) {
                    this.updateQuantity(existingIndex, qty);
                } else {
                    this.cartItems.push({
                        id: product.id,
                        name: product.name,
                        sku: product.sku,
                        quantity: qty,
                        unit_price: parseFloat(product.selling_price),
                        tax_rate: parseFloat(product.tax_rate || 0),
                        stock_quantity: product.stock_quantity,
                        lineTotal: 0
                    });
                    this.updateLineTotal(this.cartItems.length - 1);
                }
            }

            this.showQuantityModal = false;
            this.quantityInput = '';
            this.quantityModalProduct = null;
            this.quantityModalCartIndex = null;
        },

        toggleScanner() {
            this.showScanner = !this.showScanner;
            // Scanner implementation would go here
        },

        addToFavorites(product) {
            if (!this.favoriteProducts.some(p => p.id === product.id)) {
                this.favoriteProducts.push(product);
                if (typeof localStorage !== 'undefined') {
                    localStorage.setItem('pos_favorites', JSON.stringify(this.favoriteProducts));
                }
            }
        },

        removeFromFavorites(productId) {
            this.favoriteProducts = this.favoriteProducts.filter(p => p.id !== productId);
            if (typeof localStorage !== 'undefined') {
                localStorage.setItem('pos_favorites', JSON.stringify(this.favoriteProducts));
            }
        },

        filterProducts() {
            // This will be reactive - the template will automatically update when searchQuery or selectedCategory changes
            // We're using Alpine.js reactive properties, so filtering happens in the template via x-show or v-if
            // For now, log to confirm it's being called
            console.log('Filtering products with query:', this.searchQuery, 'and category:', this.selectedCategory);
        },

        init() {
            // Initialize BroadcastChannel for customer display
            this.customerDisplayChannel = new BroadcastChannel('pos_customer_display');

            // Initialize critical properties early to prevent undefined errors
            if (!this.viewMode) {
                this.viewMode = 'grid';
            }
            if (!this.cartItems) {
                this.cartItems = [];
            }
            if (!this.favoriteProducts) {
                this.favoriteProducts = [];
            }
            if (typeof this.showCartSidebar === 'undefined') {
                this.showCartSidebar = (typeof window !== 'undefined' && window.innerWidth >= 1024);
            }

            if (typeof document !== 'undefined') {
                document.addEventListener('fullscreenchange', () => {
                    this.isFullscreen = !!document.fullscreenElement;
                });
            }

            // Load preferences early
            if (typeof localStorage !== 'undefined') {
                // Load view mode preference first
                const savedViewMode = localStorage.getItem('pos_view_mode');
                if (savedViewMode) {
                    this.viewMode = savedViewMode;
                } else if (!this.viewMode) {
                    this.viewMode = 'grid';
                }

                // Load dark mode preference
                const savedDarkMode = localStorage.getItem('pos_dark_mode');
                if (savedDarkMode) {
                    this.darkMode = savedDarkMode === 'true';
                } else {
                    // Check if user prefers dark mode
                    if (typeof window !== 'undefined' && window.matchMedia) {
                        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                        this.darkMode = prefersDark;
                    }
                }

                // Load favorite products
                const savedFavorites = localStorage.getItem('pos_favorites');
                if (savedFavorites) {
                    try {
                        this.favoriteProducts = JSON.parse(savedFavorites);
                    } catch (e) {
                        console.error('Failed to parse favorites', e);
                        this.favoriteProducts = [];
                    }
                }
            }

            // Add keyboard shortcuts
            if (typeof window !== 'undefined') {
                window.addEventListener('keydown', (e) => {
                    // Prevent shortcuts when typing in inputs
                    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') {
                        // Allow Escape key even in inputs
                        if (e.key === 'Escape') {
                            e.target.blur();
                            if (this.showPaymentModal) {
                                this.showPaymentModal = false;
                            } else if (this.showQuantityModal) {
                                this.showQuantityModal = false;
                            } else if (this.showKeyboardShortcuts) {
                                this.showKeyboardShortcuts = false;
                            }
                        }
                        return;
                    }

                    if (e.ctrlKey && e.key === 'b') { // Ctrl+B for quick add
                        e.preventDefault();
                        this.toggleQuickAdd();
                    } else if (e.ctrlKey && e.key === 'd') { // Ctrl+D for dark mode
                        e.preventDefault();
                        this.toggleDarkMode();
                    } else if (e.ctrlKey && e.key === 'f') { // Ctrl+F for fullscreen
                        e.preventDefault();
                        this.toggleFullscreen();
                    } else if (e.ctrlKey && e.key === 'k') { // Ctrl+K for keyboard shortcuts
                        e.preventDefault();
                        this.toggleKeyboardShortcuts();
                    } else if (e.ctrlKey && e.key === 'p') { // Ctrl+P for payment (if cart has items)
                        e.preventDefault();
                        if (this.cartItems.length > 0) {
                            this.proceedToPayment();
                        }
                    } else if (e.key === 'F1') { // F1 for help/shortcuts
                        e.preventDefault();
                        this.toggleKeyboardShortcuts();
                    } else if (e.key === 'F2') { // F2 to focus search
                        e.preventDefault();
                        const searchInput = document.querySelector('input[placeholder*="Search"]');
                        if (searchInput) searchInput.focus();
                    } else if (e.key === 'F3') { // F3 to toggle view
                        e.preventDefault();
                        this.toggleViewMode();
                    } else if (e.key === 'F4') { // F4 to clear cart
                        e.preventDefault();
                        this.clearCart();
                    } else if (e.key === 'Escape') {
                        if (this.showPaymentModal) {
                            this.showPaymentModal = false;
                        } else if (this.showQuantityModal) {
                            this.showQuantityModal = false;
                        } else if (this.showKeyboardShortcuts) {
                            this.showKeyboardShortcuts = false;
                        } else if (typeof window !== 'undefined' && window.innerWidth < 1024 && this.showCartSidebar) {
                            this.showCartSidebar = false;
                        }
                    }
                });
            }

            // Handle window resize for cart sidebar
            if (typeof window !== 'undefined') {
                window.addEventListener('resize', () => {
                    if (window.innerWidth >= 1024) {
                        this.showCartSidebar = true;
                    }
                });
            }

            // Show welcome notification on load
            setTimeout(() => {
                this.notificationMessage = 'Welcome to the enhanced POS system!';
                this.notificationType = 'info';
                this.showNotification = false;

                setTimeout(() => {
                    this.showNotification = false;
                }, 3000);
            }, 1000);
        }
    }
}
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Keyboard key styling */
kbd {
    display: inline-block;
    padding: 0.2rem 0.4rem;
    font-size: 0.75rem;
    font-family: monospace;
    line-height: 1;
    border: 1px solid currentColor;
    border-radius: 0.25rem;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Theme colors */
:root {
    --color-dark-purple: #3c2c64;
    --color-dark-purple-2: #2f224d;
    --color-purple-light: #5a4387;
    --color-purple-accent: #8a6dcc;
    --color-purple-muted: rgba(60, 44, 100, 0.1);
}

/* Glass morphism */
.glass-morphism {
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    background-color: rgba(255, 255, 255, 0.6);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.dark-mode .glass-morphism {
    background-color: rgba(30, 30, 40, 0.7);
    border: 1px solid rgba(255, 255, 255, 0.05);
}

.pos-container {
    max-width: 100%;
    overflow-x: hidden;
}

.pos-interface {
    transition: all 0.3s ease;
}

/* Product card styles */
.product-card {
    transition: all 0.2s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px -5px rgba(60, 44, 100, 0.1), 0 8px 10px -6px rgba(60, 44, 100, 0.1);
}

.product-card .card-actions {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.product-card:hover .card-actions {
    opacity: 1;
}

.product-card .price-tag {
    position: absolute;
    top: 0.75rem;
    left: 0;
    background: var(--color-dark-purple);
    color: white;
    padding: 0.25rem 0.75rem;
    border-top-right-radius: 0.5rem;
    border-bottom-right-radius: 0.5rem;
    font-weight: bold;
    font-size: 0.875rem;
    box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
    z-index: 5;
}

.product-card .stock-indicator {
    position: absolute;
    bottom: 0.75rem;
    right: 0.75rem;
    border-radius: 9999px;
    width: 0.75rem;
    height: 0.75rem;
}

/* Button styles */
.btn-primary {
    background-image: linear-gradient(to right, var(--color-dark-purple), var(--color-dark-purple-2));
    color: #fff;
    transition: all 0.2s ease;
}

.btn-primary:hover {
    filter: brightness(1.1);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(60, 44, 100, 0.15);
}

.btn-primary:active {
    transform: translateY(0);
}

.btn-outline {
    border: 1px solid var(--color-dark-purple);
    color: var(--color-dark-purple);
    background: transparent;
    transition: all 0.2s ease;
}

.dark-mode .btn-outline {
    border-color: var(--color-purple-accent);
    color: var(--color-purple-accent);
}

.btn-outline:hover {
    background-color: var(--color-dark-purple);
    color: white;
}

.dark-mode .btn-outline:hover {
    background-color: var(--color-purple-accent);
}

.text-primary { color: var(--color-dark-purple); }
.dark-mode .text-primary { color: var(--color-purple-accent); }
.bg-primary { background-color: var(--color-dark-purple); }
.dark-mode .bg-primary { background-color: var(--color-purple-accent); }
.border-primary { border-color: var(--color-dark-purple); }
.dark-mode .border-primary { border-color: var(--color-purple-accent); }

/* Touch mode */
.touch-mode .touch-grow { transform: scale(1.05); }
.touch-mode .touch-py { padding-top: 1rem; padding-bottom: 1rem; }
.touch-mode .touch-btn { width: 2.75rem; height: 2.75rem; }
.touch-mode .touch-input { font-size: 1rem; padding: 0.75rem 1rem; }
.touch-mode .product-card { padding: 1.25rem; }
.touch-mode .product-card .card-actions { opacity: 1; }

/* Animation utilities */
.animate-fade-in {
    animation: fadeIn 0.3s ease-in;
}

.animate-slide-in-right {
    animation: slideInRight 0.3s ease-out;
}

.animate-slide-in-left {
    animation: slideInLeft 0.3s ease-out;
}

.animate-slide-in-up {
    animation: slideInUp 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideInRight {
    from { transform: translateX(20px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes slideInLeft {
    from { transform: translateX(-20px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes slideInUp {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.animate-pulse {
    animation: pulse 2s infinite;
}

/* Cart indicator badge animation */
@keyframes badgePulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.2); }
}

.cart-badge {
    animation: badgePulse 1.5s infinite;
}

/* List view vs Grid view */
.list-view-item {
    padding: 0 !important;
}

.list-view-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px -5px rgba(60, 44, 100, 0.12), 0 8px 10px -6px rgba(60, 44, 100, 0.12);
}

.list-view-item .price-tag {
    display: none; /* Hide grid-style price tag in list view */
}

/* Enhanced hover effects for list view */
.list-view-item:hover .group-hover\:text-\[var\(--color-dark-purple\)\] {
    color: var(--color-dark-purple) !important;
}

.dark-mode .list-view-item:hover .dark\:group-hover\:text-\[var\(--color-purple-accent\)\] {
    color: var(--color-purple-accent) !important;
}

/* Ensure consistent spacing and alignment */
.list-view-item .line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Better mobile responsiveness for list view */
@media (max-width: 640px) {
    .list-view-item {
        padding: 0.5rem !important;
    }

    .list-view-item .flex {
        gap: 0.75rem;
    }
}

/* Dark mode */
.dark-mode .bg-white { background-color: #1e1e2d !important; }
.dark-mode .bg-gray-50 { background-color: #151521 !important; }
.dark-mode .bg-gray-100 { background-color: #1a1a2a !important; }
.dark-mode .border-gray-200 { border-color: #2a2a3a !important; }
.dark-mode .text-gray-900 { color: #e1e1e6 !important; }
.dark-mode .text-gray-600,
.dark-mode .text-gray-500 { color: #a0a0b0 !important; }

.dark-mode .overflow-y-auto::-webkit-scrollbar-track {
    background: #1a1a2a;
}

.dark-mode .overflow-y-auto::-webkit-scrollbar-thumb {
    background: #3a3a4a;
}

/* Keyboard shortcuts tooltip */
.shortcut-label {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(0,0,0,0.07);
    color: rgba(0,0,0,0.7);
    border-radius: 4px;
    padding: 0.1rem 0.3rem;
    font-size: 0.7rem;
    margin-left: 0.5rem;
    border: 1px solid rgba(0,0,0,0.1);
}

.dark-mode .shortcut-label {
    background-color: rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.7);
    border-color: rgba(255,255,255,0.1);
}
</style>
@endsection
