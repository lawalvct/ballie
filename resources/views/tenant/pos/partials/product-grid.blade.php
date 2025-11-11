<!-- Search and Filters -->
<div class="mb-6 p-4 bg-white/60 dark:bg-gray-800/40 backdrop-blur-sm rounded-2xl border border-gray-200/80 dark:border-gray-700/50 animate-slide-in-up">
    <div class="flex flex-col lg:flex-row lg:items-center gap-3 lg:gap-4">
        <div class="flex-1 relative">
            <div class="relative">
                <input type="text"
                       x-model="searchQuery"
                       @input.debounce.300ms="filterProducts()"
                       placeholder="Search products by name, SKU, or barcode..."
                       class="w-full pl-12 pr-12 py-3 border border-gray-300/80 dark:border-gray-600/60 rounded-xl focus:ring-2 focus:ring-[var(--color-dark-purple)] focus:border-[var(--color-dark-purple)] bg-white/80 dark:bg-gray-700/60 shadow-sm touch-input dark:text-gray-200 dark:placeholder-gray-400 transition-colors duration-300">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400 dark:text-gray-500"></i>
                </div>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                    <button @click="toggleScanner()" class="text-gray-400 dark:text-gray-500 hover:text-[var(--color-dark-purple)] dark:hover:text-[var(--color-purple-accent)] focus:outline-none">
                        <i class="fas fa-barcode"></i>
                    </button>
                </div>
            </div>

            <!-- Barcode Scanner (shows when scanner is active) -->
            <div x-show="showScanner" x-transition class="absolute top-full left-0 right-0 mt-2 p-4 bg-white dark:bg-gray-800 shadow-lg rounded-lg border border-gray-200 dark:border-gray-700 z-10" style="display: none;">
                <div class="text-center space-y-4">
                    <div class="w-full h-12 bg-gray-100 dark:bg-gray-700 rounded flex items-center justify-center border-2 border-dashed border-gray-300 dark:border-gray-600">
                        <span class="text-gray-500 dark:text-gray-400 text-sm">Scan barcode...</span>
                    </div>
                    <button @click="toggleScanner()" class="px-3 py-2 rounded bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm">
                        <i class="fas fa-times mr-1"></i> Close Scanner
                    </button>
                </div>
            </div>
        </div>

        <div class="flex gap-2 flex-wrap sm:flex-nowrap">
            @if(isset($categories) && $categories->count() > 0)
                <select x-model="selectedCategory"
                        @change="filterProducts()"
                        class="px-4 py-3 border border-gray-300/80 dark:border-gray-600/60 rounded-xl focus:ring-2 focus:ring-[var(--color-dark-purple)] focus:border-[var(--color-dark-purple)] bg-white/80 dark:bg-gray-700/60 shadow-sm text-gray-700 dark:text-gray-200 transition-colors duration-300">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        @if($category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endif
                    @endforeach
                </select>
            @endif

            <button @click="toggleQuickAdd()"
                    :class="quickAddEnabled ? 'bg-primary text-white shadow-lg ring-2 ring-purple-300 dark:ring-purple-600' : 'btn-outline'"
                    class="px-3 py-3 rounded-xl flex items-center gap-2 transition-all duration-200 relative">
                <i class="fas fa-bolt" :class="quickAddEnabled ? 'animate-pulse' : ''"></i>
                <span class="hidden md:inline">Quick Add</span>
                <span class="shortcut-label hidden md:inline">Ctrl+B</span>
                <span x-show="quickAddEnabled" class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 rounded-full animate-pulse"></span>
            </button>

            @if(isset($recentSales) && $recentSales->count() > 0)
                <button @click="showRecentSales = !showRecentSales"
                        class="px-3 py-3 rounded-xl transition-all duration-200 flex items-center gap-2 btn-primary">
                    <i class="fas fa-history"></i>
                    <span class="hidden md:inline">Recent</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Favorite Products Quick Bar (if any) -->
    <div x-show="favoriteProducts.length > 0" class="mt-4 overflow-x-auto" style="display: none;">
        <div class="flex gap-2 pb-2">
            <template x-for="(product, index) in favoriteProducts" :key="index">
                <div @click="addToCart(product)" class="flex-shrink-0 px-3 py-2 bg-white/80 dark:bg-gray-700/50 hover:bg-white dark:hover:bg-gray-600 rounded-lg shadow-sm border border-gray-200/80 dark:border-gray-600/50 cursor-pointer transition-all duration-200 group">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 flex items-center justify-center bg-[var(--color-dark-purple)] dark:bg-[var(--color-purple-accent)] rounded-full text-white text-xs">
                            <i class="fas fa-star"></i>
                        </span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-[var(--color-dark-purple)] dark:group-hover:text-[var(--color-purple-accent)]" x-text="product.name.substring(0, 15) + (product.name.length > 15 ? '...' : '')"></span>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<!-- Products Grid/List -->
<div :class="(viewMode || 'grid') === 'grid' ? 'grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-4' : 'space-y-2'"
     class="animate-fade-in"
     x-show="!showRecentSales">
    @if(isset($products) && $products->count() > 0)
        @foreach($products as $product)
        <div @click="quickAddEnabled ? addToCart({{ $product->toJson() }}) : null"
             x-show="
                (searchQuery === '' ||
                 '{{ strtolower($product->name) }}'.includes(searchQuery.toLowerCase()) ||
                 '{{ strtolower($product->sku) }}'.includes(searchQuery.toLowerCase())) &&
                (selectedCategory === '' || selectedCategory == '{{ $product->category_id ?? '' }}')"
             class="product-card bg-white/80 dark:bg-gray-800/50 backdrop-blur-sm rounded-xl shadow-lg border border-gray-200/80 dark:border-gray-700/50 hover:border-[var(--color-dark-purple)] dark:hover:border-[var(--color-purple-accent)] transition-all duration-200 transform hover:-translate-y-1 touch-grow group relative overflow-hidden"
             :class="(viewMode || 'grid') === 'grid' ? 'p-4' : 'list-view-item'">

            <!-- Grid View Layout -->
            <template x-if="(viewMode || 'grid') === 'grid'">
                <div>
                    <!-- Price Tag for Grid -->
                    <div class="price-tag">₦{{ number_format($product->selling_price, 0) }}</div>

                    <!-- Product Image for Grid -->
                    <div>
                        @if($product->image_path)
                            <img src="{{ Storage::url($product->image_path) }}"
                                 alt="{{ $product->name }}"
                                 class="w-full h-32 object-cover rounded-lg mb-3">
                        @else
                            <div class="w-full h-32 bg-gradient-to-br from-[var(--color-purple-muted)] to-[var(--color-purple-light)] rounded-lg mb-3 flex items-center justify-center">
                                <i class="fas fa-box text-[var(--color-dark-purple)] dark:text-white text-2xl"></i>
                            </div>
                        @endif
                    </div>

                    <!-- Product Info for Grid -->
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-sm mb-1 line-clamp-2 group-hover:text-[var(--color-dark-purple)] dark:group-hover:text-[var(--color-purple-accent)]">{{ $product->name }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ $product->sku }}</p>

                        <div class="flex items-center justify-between mt-2">
                            <div class="flex items-center gap-2">
                                <span class="stock-indicator {{ $product->stock_quantity > 10 ? 'bg-green-500' : ($product->stock_quantity > 0 ? 'bg-yellow-500' : 'bg-red-500') }}"></span>
                                <span class="text-xs {{ $product->stock_quantity > 10 ? 'text-green-600 dark:text-green-400' : ($product->stock_quantity > 0 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">
                                    {{ $product->stock_quantity }} left
                                </span>
                            </div>
                        </div>

                        <!-- Action Buttons for Grid -->
                        <div class="card-actions">
                            <div class="flex gap-1">
                                <button @click.stop="addToFavorites({{ $product->toJson() }})" class="w-7 h-7 rounded-full bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:text-[var(--color-dark-purple)] dark:hover:text-[var(--color-purple-accent)] flex items-center justify-center shadow-sm border border-gray-200 dark:border-gray-700">
                                    <i class="fas fa-star text-xs"></i>
                                </button>
                                <button @click.stop="addToCart({{ $product->toJson() }})" class="w-7 h-7 rounded-full bg-[var(--color-dark-purple)] dark:bg-[var(--color-purple-accent)] text-white flex items-center justify-center shadow-sm">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- List View Layout -->
            <template x-if="(viewMode || 'grid') === 'list'">
                <div class="flex items-center p-3 gap-4">
                    <!-- Product Image for List -->
                    <div class="flex-shrink-0">
                        @if($product->image_path)
                            <img src="{{ Storage::url($product->image_path) }}"
                                 alt="{{ $product->name }}"
                                 class="w-16 h-16 object-cover rounded-lg shadow-sm">
                        @else
                            <div class="w-16 h-16 bg-gradient-to-br from-[var(--color-purple-muted)] to-[var(--color-purple-light)] rounded-lg flex items-center justify-center shadow-sm">
                                <i class="fas fa-box text-[var(--color-dark-purple)] dark:text-white text-lg"></i>
                            </div>
                        @endif
                    </div>

                    <!-- Product Details for List -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-sm mb-1 line-clamp-1 group-hover:text-[var(--color-dark-purple)] dark:group-hover:text-[var(--color-purple-accent)] transition-colors duration-200">
                                    {{ $product->name }}
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">SKU: {{ $product->sku }}</p>

                                <!-- Stock and Category Info -->
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full {{ $product->stock_quantity > 10 ? 'bg-green-500' : ($product->stock_quantity > 0 ? 'bg-yellow-500' : 'bg-red-500') }}"></span>
                                        <span class="text-xs {{ $product->stock_quantity > 10 ? 'text-green-600 dark:text-green-400' : ($product->stock_quantity > 0 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">
                                            {{ $product->stock_quantity }} in stock
                                        </span>
                                    </div>
                                    @if($product->category)
                                        <div class="flex items-center gap-1">
                                            <i class="fas fa-tag text-gray-400 text-xs"></i>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $product->category->name }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Product Description (if available) -->
                                @if($product->description)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1 mb-2">{{ $product->description }}</p>
                                @endif
                            </div>

                            <!-- Price and Actions for List -->
                            <div class="flex-shrink-0 text-right ml-4">
                                <div class="text-lg font-bold text-[var(--color-dark-purple)] dark:text-[var(--color-purple-accent)] mb-2">
                                    ₦{{ number_format($product->selling_price, 0) }}
                                </div>
                                @if($product->compare_price && $product->compare_price > $product->selling_price)
                                    <div class="text-xs text-gray-400 line-through mb-2">
                                        ₦{{ number_format($product->compare_price, 0) }}
                                    </div>
                                @endif

                                <!-- Action Buttons for List -->
                                <div class="flex items-center gap-2 justify-end">
                                    <button @click.stop="addToFavorites({{ $product->toJson() }})"
                                            class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:text-[var(--color-dark-purple)] dark:hover:text-[var(--color-purple-accent)] hover:bg-gray-200 dark:hover:bg-gray-600 flex items-center justify-center transition-all duration-200 shadow-sm">
                                        <i class="fas fa-star text-xs"></i>
                                    </button>
                                    <button @click.stop="addToCart({{ $product->toJson() }})"
                                            class="w-8 h-8 rounded-lg bg-[var(--color-dark-purple)] dark:bg-[var(--color-purple-accent)] text-white hover:bg-[var(--color-purple-light)] dark:hover:bg-[var(--color-purple-accent)]/80 flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        @endforeach
    @else
        <div class="col-span-full text-center py-12 bg-white/80 dark:bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-gray-200/80 dark:border-gray-700/50">
            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-box text-gray-400 dark:text-gray-500 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No Products Available</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">Add products to your inventory to start selling</p>
            <a href="{{ route('tenant.inventory.products.create', ['tenant' => $tenant->slug]) }}"
               class="px-4 py-2 rounded-lg btn-primary">
                Add Products
            </a>
        </div>
    @endif
</div>
