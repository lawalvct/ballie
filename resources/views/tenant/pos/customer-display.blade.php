<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Customer Display - {{ $tenant->name }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --color-gold: #d1b05e;
            --color-blue: #2b6399;
            --color-dark-purple: #3c2c64;
            --color-teal: #69a2a4;
            --color-purple: #85729d;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            overflow: hidden;
        }

        .customer-display {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .company-header {
            background: linear-gradient(135deg, var(--color-dark-purple) 0%, #2c1e4a 100%);
            padding: 1rem 1.5rem;
            text-align: center;
            border-bottom: 3px solid var(--color-gold);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .company-logo {
            font-size: 1.75rem;
            color: var(--color-gold);
            margin-bottom: 0.25rem;
        }

        .company-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        .welcome-text {
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.85);
            margin-top: 0.25rem;
        }

        .content-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 1.5rem;
            overflow: hidden;
        }

        .cart-items {
            flex: 1;
            background: white;
            border-radius: 1rem;
            padding: 1rem;
            overflow-y: auto;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
            margin-bottom: 1.5rem;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f3f4f6;
            animation: slideIn 0.3s ease-out;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .item-info {
            flex: 1;
        }

        .item-name {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.125rem;
        }

        .item-quantity {
            font-size: 0.875rem;
            color: #6b7280;
        }

        .item-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--color-dark-purple);
        }

        .totals-section {
            background: white;
            border-radius: 1rem;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .total-row:last-child {
            border-bottom: none;
            padding-top: 0.75rem;
            margin-top: 0.5rem;
            border-top: 2px solid var(--color-gold);
        }

        .total-label {
            font-size: 1rem;
            color: #6b7280;
        }

        .total-value {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1f2937;
        }

        .grand-total-label {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--color-dark-purple);
        }

        .grand-total-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--color-gold);
        }

        .empty-cart {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #9ca3af;
        }

        .empty-cart-icon {
            font-size: 5rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        .empty-cart-text {
            font-size: 1.25rem;
            font-weight: 500;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body>
    <div class="customer-display">
        <!-- Header -->
        <div class="company-header">
            <div class="company-logo">
                <i class="fas fa-store"></i>
            </div>
            <h1 class="company-name">{{ $tenant->name }}</h1>
            <p class="welcome-text">Welcome! Your items will appear here</p>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Cart Items -->
            <div class="cart-items scrollbar-hide" id="cartItemsDisplay">
                <div class="empty-cart" id="emptyCartMessage">
                    <div class="empty-cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <p class="empty-cart-text">Waiting for items...</p>
                </div>
            </div>

            <!-- Totals Section -->
            <div class="totals-section">
                <div class="total-row">
                    <span class="total-label">Subtotal:</span>
                    <span class="total-value" id="subtotalDisplay">₦0.00</span>
                </div>
                <div class="total-row">
                    <span class="total-label">Tax:</span>
                    <span class="total-value" id="taxDisplay">₦0.00</span>
                </div>
                <div class="total-row">
                    <span class="total-label">Discount:</span>
                    <span class="total-value" id="discountDisplay">₦0.00</span>
                </div>
                <div class="total-row">
                    <span class="grand-total-label">Total:</span>
                    <span class="grand-total-value pulse" id="totalDisplay">₦0.00</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // BroadcastChannel for cross-window communication
        const channel = new BroadcastChannel('pos_customer_display');

        // Listen for cart updates from POS
        channel.onmessage = (event) => {
            const { type, data } = event.data;

            if (type === 'CART_UPDATE') {
                updateDisplay(data);
            } else if (type === 'CART_CLEAR') {
                clearDisplay();
            } else if (type === 'SALE_COMPLETE') {
                showCompletionMessage(data);
            }
        };

        // Also listen to localStorage changes (fallback)
        window.addEventListener('storage', (e) => {
            if (e.key === 'pos_customer_cart') {
                const cartData = JSON.parse(e.newValue || '{"items": [], "subtotal": 0, "tax": 0, "discount": 0, "total": 0}');
                updateDisplay(cartData);
            }
        });

        // Initial load from localStorage
        window.addEventListener('load', () => {
            const savedCart = localStorage.getItem('pos_customer_cart');
            if (savedCart) {
                const cartData = JSON.parse(savedCart);
                updateDisplay(cartData);
            }
        });

        function updateDisplay(data) {
            const { items, subtotal, tax, discount, total } = data;

            // Update items
            const cartItemsContainer = document.getElementById('cartItemsDisplay');

            if (items && items.length > 0) {
                let itemsHtml = '';
                items.forEach((item, index) => {
                    itemsHtml += `
                        <div class="cart-item" style="animation-delay: ${index * 0.1}s">
                            <div class="item-info">
                                <div class="item-name">${escapeHtml(item.name)}</div>
                                <div class="item-quantity">Qty: ${item.quantity} × ₦${formatNumber(item.price)}</div>
                            </div>
                            <div class="item-price">₦${formatNumber(item.total)}</div>
                        </div>
                    `;
                });

                cartItemsContainer.innerHTML = itemsHtml;

                // Auto-scroll to bottom to show last item
                setTimeout(() => {
                    cartItemsContainer.scrollTop = cartItemsContainer.scrollHeight;
                }, 100);
            } else {
                // Show empty message
                cartItemsContainer.innerHTML = `
                    <div class="empty-cart" id="emptyCartMessage">
                        <div class="empty-cart-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <p class="empty-cart-text">Waiting for items...</p>
                    </div>
                `;
            }

            // Update totals
            document.getElementById('subtotalDisplay').textContent = '₦' + formatNumber(subtotal || 0);
            document.getElementById('taxDisplay').textContent = '₦' + formatNumber(tax || 0);
            document.getElementById('discountDisplay').textContent = '₦' + formatNumber(discount || 0);
            document.getElementById('totalDisplay').textContent = '₦' + formatNumber(total || 0);
        }

        function clearDisplay() {
            const cartItemsContainer = document.getElementById('cartItemsDisplay');

            cartItemsContainer.innerHTML = `
                <div class="empty-cart" id="emptyCartMessage">
                    <div class="empty-cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <p class="empty-cart-text">Waiting for items...</p>
                </div>
            `;

            document.getElementById('subtotalDisplay').textContent = '₦0.00';
            document.getElementById('taxDisplay').textContent = '₦0.00';
            document.getElementById('discountDisplay').textContent = '₦0.00';
            document.getElementById('totalDisplay').textContent = '₦0.00';
        }

        function showCompletionMessage(data) {
            const cartItemsContainer = document.getElementById('cartItemsDisplay');

            cartItemsContainer.innerHTML = `
                <div class="empty-cart">
                    <div class="empty-cart-icon" style="color: #10b981;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <p class="empty-cart-text" style="color: #10b981;">Thank You!</p>
                    <p class="empty-cart-text" style="font-size: 1.5rem; margin-top: 1rem;">
                        Total: ₦${formatNumber(data.total)}
                    </p>
                    ${data.change > 0 ? `
                        <p class="empty-cart-text" style="font-size: 1.25rem; margin-top: 0.5rem;">
                            Change: ₦${formatNumber(data.change)}
                        </p>
                    ` : ''}
                </div>
            `;

            // Clear after 5 seconds
            setTimeout(() => {
                clearDisplay();
                localStorage.removeItem('pos_customer_cart');
            }, 5000);
        }

        function formatNumber(num) {
            return parseFloat(num || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Prevent closing
        window.addEventListener('beforeunload', (e) => {
            e.preventDefault();
            e.returnValue = '';
        });
    </script>
</body>
</html>
