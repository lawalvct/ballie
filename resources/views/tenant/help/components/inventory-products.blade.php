'inventory-products': {
    template: `
        <div>
            <h1 class="text-3xl font-bold mb-6">📦 Product Management</h1>

            <div class="bg-blue-50 border-l-4 border-blue-600 p-6 rounded-r-lg mb-8">
                <p class="text-gray-700 leading-relaxed">
                    Manage your products and services inventory. Add, edit, organize, and track all your inventory items in one place.
                </p>
            </div>

            <h2 class="text-2xl font-bold mb-4">Accessing Product Management</h2>
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                <p class="text-gray-700 mb-3">Navigate to <strong>Inventory → Products & Services</strong></p>
                <p class="text-gray-700 mb-3">Or click <strong>"MORE ACTIONS"</strong> from Inventory Dashboard:</p>
                <img src="{{ asset('images/help/inventory_more-actions.png') }}" alt="More Actions" class="w-full rounded-lg shadow-md">
            </div>

            <h2 class="text-2xl font-bold mb-4">Product List View</h2>
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                <img src="{{ asset('images/help/inventory_product-list.png') }}" alt="Product List" class="w-full rounded-lg shadow-md mb-4">
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-3 rounded">
                        <h4 class="font-semibold text-gray-900 mb-2">Key Features</h4>
                        <ul class="text-sm text-gray-700 space-y-1">
                            <li>• Stock Date Filter - View historical data</li>
                            <li>• Valuation Method - Weighted Average/FIFO/LIFO</li>
                            <li>• Search & Filters - Find products quickly</li>
                            <li>• Real-time Stock Status</li>
                        </ul>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <h4 class="font-semibold text-gray-900 mb-2">Dashboard Stats</h4>
                        <ul class="text-sm text-gray-700 space-y-1">
                            <li>• Total Products</li>
                            <li>• Active Products</li>
                            <li>• Low Stock Items</li>
                            <li>• Out of Stock Items</li>
                        </ul>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">Adding New Products</h2>
            <div class="space-y-6 mb-8">
                <div class="border-l-4 border-green-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">1</div>
                        <h3 class="text-xl font-semibold">Click "Add Product"</h3>
                    </div>
                    <p class="text-gray-700">From Products page or Inventory Dashboard</p>
                </div>

                <div class="border-l-4 border-green-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">2</div>
                        <h3 class="text-xl font-semibold">Select Product Type</h3>
                    </div>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-blue-900 mb-2">Item</h4>
                            <p class="text-sm text-blue-800">Physical products with inventory tracking</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-purple-900 mb-2">Service</h4>
                            <p class="text-sm text-purple-800">Non-physical services (no stock tracking)</p>
                        </div>
                    </div>
                </div>

                <div class="border-l-4 border-green-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">3</div>
                        <h3 class="text-xl font-semibold">Fill Product Information</h3>
                    </div>
                    <img src="{{ asset('images/help/inventory_create-form.png') }}" alt="Create Form" class="w-full rounded-lg shadow-md mb-3">
                    <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                        <div><strong>Product Name:</strong> Unique name</div>
                        <div><strong>SKU:</strong> Auto-generated or manual</div>
                        <div><strong>Category:</strong> Product classification</div>
                        <div><strong>Brand:</strong> Manufacturer name</div>
                        <div><strong>Description:</strong> Product details</div>
                    </div>
                </div>

                <div class="border-l-4 border-green-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">4</div>
                        <h3 class="text-xl font-semibold">Set Pricing</h3>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                        <div><strong>Purchase Rate:</strong> Cost price</div>
                        <div><strong>Sales Rate:</strong> Selling price</div>
                        <div><strong>MRP:</strong> Maximum Retail Price</div>
                    </div>
                </div>

                <div class="border-l-4 border-green-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">5</div>
                        <h3 class="text-xl font-semibold">Configure Stock</h3>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                        <div><strong>Opening Stock:</strong> Initial quantity</div>
                        <div><strong>Reorder Level:</strong> Minimum stock alert</div>
                        <div><strong>Maintain Stock:</strong> Enable tracking</div>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">Bulk Import Products</h2>
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                <img src="{{ asset('images/help/inventory_product-upload-modal.png') }}" alt="Import Modal" class="w-full rounded-lg shadow-md mb-4">
                <ol class="list-decimal list-inside space-y-2 text-gray-700">
                    <li>Click <strong>"Upload Products"</strong> button</li>
                    <li>Download Excel template</li>
                    <li>Fill product details in template</li>
                    <li>Upload completed file (XLSX, XLS, CSV - max 10MB)</li>
                    <li>Click <strong>"Import Products"</strong></li>
                </ol>
            </div>

            <div class="bg-gradient-to-r from-purple-50 to-blue-50 p-6 rounded-lg border border-purple-200 mb-8">
                <h3 class="text-xl font-semibold text-purple-900 mb-4">💡 Best Practices</h3>
                <ul class="space-y-2 text-purple-800">
                    <li>✅ Use consistent naming conventions</li>
                    <li>✅ Assign products to appropriate categories</li>
                    <li>✅ Set realistic reorder levels</li>
                    <li>✅ Keep SKUs unique and meaningful</li>
                    <li>✅ Link correct ledger accounts</li>
                    <li>✅ Upload product images</li>
                    <li>✅ Use bulk import for multiple products</li>
                </ul>
            </div>
        </div>
    `
},
