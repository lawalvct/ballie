'inventory-categories': {
    template: `
        <div>
            <h1 class="text-3xl font-bold mb-6">📊 Categories & Units Management</h1>

            <div class="bg-blue-50 border-l-4 border-blue-600 p-6 rounded-r-lg mb-8">
                <p class="text-gray-700 leading-relaxed">
                    Learn how to organize your products using categories and define measurement units for accurate inventory tracking. This guide covers creating categories, building hierarchies, managing units, and setting up conversion factors.
                </p>
            </div>

            <!-- Quick Navigation -->
            <div class="bg-gradient-to-r from-purple-50 to-blue-50 border border-purple-200 rounded-lg p-6 mb-8">
                <h3 class="text-xl font-semibold text-purple-900 mb-4">📋 In This Guide</h3>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold text-purple-800 mb-2 flex items-center">
                            <span class="mr-2">📁</span> Product Categories
                        </h4>
                        <ul class="text-sm text-purple-700 space-y-1 ml-6">
                            <li>• Creating and managing categories</li>
                            <li>• Building hierarchies (parent-child)</li>
                            <li>• Organizing with subcategories</li>
                            <li>• Viewing, editing, and deleting</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-purple-800 mb-2 flex items-center">
                            <span class="mr-2">📏</span> Units of Measurement
                        </h4>
                        <ul class="text-sm text-purple-700 space-y-1 ml-6">
                            <li>• Base vs Derived units explained</li>
                            <li>• Creating units step-by-step</li>
                            <li>• Setting conversion factors</li>
                            <li>• Managing and using units</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- ==================== PART 1: PRODUCT CATEGORIES ==================== -->
            <div class="border-t-4 border-blue-500 pt-8 mt-8">
                <h1 class="text-3xl font-bold mb-6 text-blue-900 flex items-center">
                    <span class="mr-3">📁</span> Part 1: Product Categories
                </h1>

                <h2 class="text-2xl font-bold mb-4">Access Categories</h2>
                <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                    <p class="text-gray-700 mb-3">Navigate to <strong>Inventory → Categories</strong> from the main sidebar menu</p>
                    <p class="text-gray-700">You'll see the Categories page with a table listing all your product categories</p>
                </div>

                <h2 class="text-2xl font-bold mb-4">Dashboard Statistics</h2>
                    <img src="{{ asset('images/help/inventory_category-list.png') }}" alt="Inventory Category Dashboard" class="w-full rounded-lg shadow-md mb-3">
                <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                    <p class="text-gray-700 mb-4">Four key metrics are displayed at the top:</p>
                    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                            <div class="text-xl font-bold text-blue-600 mb-1">Total Categories</div>
                            <p class="text-sm text-gray-700">All categories (active + inactive)</p>
                        </div>
                        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded">
                            <div class="text-xl font-bold text-green-600 mb-1">Active Categories</div>
                            <p class="text-sm text-gray-700">Available for products</p>
                        </div>
                        <div class="bg-purple-50 border-l-4 border-purple-500 p-4 rounded">
                            <div class="text-xl font-bold text-purple-600 mb-1">Root Categories</div>
                            <p class="text-sm text-gray-700">Top-level parents</p>
                        </div>
                        <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded">
                            <div class="text-xl font-bold text-orange-600 mb-1">With Products</div>
                            <p class="text-sm text-gray-700">Have products assigned</p>
                        </div>
                    </div>
                </div>

                <h2 class="text-2xl font-bold mb-4">How to Create a Category</h2>
                <div class="space-y-6 mb-8">
                    <div class="border-l-4 border-green-500 pl-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">1</div>
                            <h3 class="text-xl font-semibold">Click "Add Category"</h3>
                        </div>
                        <p class="text-gray-700">Click the green <strong>"+ Add Category"</strong> button at the top of the page</p>
                    </div>

                    <div class="border-l-4 border-green-500 pl-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">2</div>
                            <h3 class="text-xl font-semibold">Fill Category Form</h3>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg space-y-3">
                            <div class="border-b border-gray-200 pb-3">
                                <strong class="text-gray-900">Category Name</strong> <span class="text-red-500">*</span>
                                <p class="text-sm text-gray-600 mt-1">Enter a unique, descriptive name</p>
                                <p class="text-sm text-blue-600 mt-1">Examples: "Raw Materials", "Finished Goods", "Electronics", "Office Supplies"</p>
                            </div>
                            <div class="border-b border-gray-200 pb-3">
                                <strong class="text-gray-900">Slug</strong>
                                <p class="text-sm text-gray-600 mt-1">Auto-generated URL-friendly version (e.g., "raw-materials")</p>
                                <p class="text-xs text-gray-500 mt-1">💡 Leave blank to auto-generate</p>
                            </div>
                            <div class="border-b border-gray-200 pb-3">
                                <strong class="text-gray-900">Parent Category</strong>
                                <p class="text-sm text-gray-600 mt-1">Select parent for subcategory, or leave as "Root Category" for top-level</p>
                            </div>
                            <div class="pb-3">
                                <strong class="text-gray-900">Description</strong>
                                <p class="text-sm text-gray-600 mt-1">Optional description of what products belong here</p>
                            </div>
                        </div>
                    </div>

                    <div class="border-l-4 border-green-500 pl-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">3</div>
                            <h3 class="text-xl font-semibold">Upload Image (Optional)</h3>
                        </div>
                        <p class="text-gray-700 mb-3">Click the upload area or drag an image file (PNG, JPG up to 2MB)</p>
                        <p class="text-sm text-blue-600">💡 Images help visually identify categories in lists and reports</p>
                    </div>

                    <div class="border-l-4 border-green-500 pl-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">4</div>
                            <h3 class="text-xl font-semibold">Set Sort Order & Status</h3>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg space-y-3">
                            <div>
                                <strong class="text-gray-900">Sort Order</strong>
                                <p class="text-sm text-gray-600 mt-1">Enter number (lower = appears first). Use 10, 20, 30 for flexibility</p>
                            </div>
                            <div>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" checked class="rounded border-gray-300">
                                    <span class="text-gray-700"><strong>Active</strong> - Makes category available for product assignment</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="border-l-4 border-green-500 pl-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">5</div>
                            <h3 class="text-xl font-semibold">Save Category</h3>
                        </div>
                        <button class="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold text-lg">
                            ✓ Create Category
                        </button>
                            <img src="{{ asset('images/help/inventory_category-create.png') }}" alt="Create Inventory Category" class="w-full rounded-lg shadow-md mb-3">
                    </div>
                </div>

                <h2 class="text-2xl font-bold mb-4">Managing Categories</h2>
                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white border-2 border-blue-200 rounded-lg p-5">
                        <h4 class="font-semibold text-gray-900 mb-2 flex items-center text-lg">
                            <span class="text-blue-600 mr-2 text-2xl">👁️</span> View
                        </h4>
                        <p class="text-sm text-gray-700">Click eye icon to see category details, products in category, and subcategories</p>
                    </div>
                    <div class="bg-white border-2 border-blue-200 rounded-lg p-5">
                        <h4 class="font-semibold text-gray-900 mb-2 flex items-center text-lg">
                            <span class="text-blue-600 mr-2 text-2xl">✏️</span> Edit
                        </h4>
                        <p class="text-sm text-gray-700">Click edit icon to update name, parent, description, sort order, or status</p>
                    </div>
                    <div class="bg-white border-2 border-green-200 rounded-lg p-5">
                        <h4 class="font-semibold text-gray-900 mb-2 flex items-center text-lg">
                            <span class="text-green-600 mr-2 text-2xl">➕</span> Add Subcategory
                        </h4>
                        <p class="text-sm text-gray-700">Click plus icon to quickly create a subcategory (parent auto-set)</p>
                    </div>
                    <div class="bg-white border-2 border-red-200 rounded-lg p-5">
                        <h4 class="font-semibold text-gray-900 mb-2 flex items-center text-lg">
                            <span class="text-red-600 mr-2 text-2xl">🗑️</span> Delete
                        </h4>
                        <p class="text-sm text-gray-700">Click delete icon to remove category</p>
                        <p class="text-sm text-red-600 mt-1">⚠️ Cannot delete if has products or subcategories</p>
                    </div>
                </div>

                <h2 class="text-2xl font-bold mb-4">Category Hierarchy Example</h2>
                <div class="bg-gray-50 p-6 rounded-lg border-l-4 border-blue-500 mb-6">
                    <div class="space-y-2 font-mono text-sm">
                        <div class="text-base">📁 <strong>Finished Goods</strong> (Root)</div>
                        <div class="ml-8">├─ 📁 Electronics</div>
                        <div class="ml-8">└─ 📁 Consumables</div>
                        <div class="ml-16">└─ 📁 Office Supplies</div>
                        <div class="mt-3 text-base">📁 <strong>Raw Materials</strong> (Root)</div>
                        <div class="ml-8">├─ 📁 Metals</div>
                        <div class="ml-8">└─ 📁 Plastics</div>
                    </div>
                </div>

                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded-r-lg mb-6">
                    <h3 class="text-lg font-semibold text-yellow-900 mb-2">⚠️ Important Notes</h3>
                    <ul class="text-sm text-yellow-800 space-y-1">
                        <li>• Cannot delete categories with products - reassign products first</li>
                        <li>• Cannot delete categories with subcategories</li>
                        <li>• Deactivating hides from product dropdown but doesn't remove from existing products</li>
                        <li>• Category names must be unique</li>
                    </ul>
                </div>
            </div>

            <!-- ==================== PART 2: UNITS ==================== -->
            <div class="border-t-4 border-purple-500 pt-8 mt-12">
                <h1 class="text-3xl font-bold mb-6 text-purple-900 flex items-center">
                    <span class="mr-3">📏</span> Part 2: Units of Measurement
                </h1>

                <h2 class="text-2xl font-bold mb-4">Access Units</h2>
                <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                    <p class="text-gray-700 mb-2"><strong>Option 1:</strong> Navigate to <strong>Inventory → Units</strong></p>
                    <p class="text-gray-700"><strong>Option 2:</strong> From Categories page, click <strong>"Manage Units"</strong> button</p>
                </div>

                <h2 class="text-2xl font-bold mb-4">Dashboard Statistics</h2>
                    <img src="{{ asset('images/help/inventory_unit-list.png') }}" alt="Inventory Units Dashboard" class="w-full rounded-lg shadow-md mb-3">
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                        <div class="text-xl font-bold text-blue-600 mb-1">Total Units</div>
                        <p class="text-sm text-gray-700">All measurement units</p>
                    </div>
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded">
                        <div class="text-xl font-bold text-green-600 mb-1">Active Units</div>
                        <p class="text-sm text-gray-700">Available for products</p>
                    </div>
                    <div class="bg-purple-50 border-l-4 border-purple-500 p-4 rounded">
                        <div class="text-xl font-bold text-purple-600 mb-1">Base Units</div>
                        <p class="text-sm text-gray-700">Fundamental units</p>
                    </div>
                    <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded">
                        <div class="text-xl font-bold text-orange-600 mb-1">Derived Units</div>
                        <p class="text-sm text-gray-700">Converted from base</p>
                    </div>
                </div>

                <h2 class="text-2xl font-bold mb-4">Unit Types Explained</h2>
                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-blue-50 border-2 border-blue-300 rounded-lg p-6">
                        <h3 class="text-xl font-semibold text-blue-900 mb-3">🔵 Base Unit</h3>
                        <p class="text-sm text-blue-800 mb-3">A fundamental unit that stands on its own</p>
                        <div class="bg-white rounded p-3 text-sm">
                            <strong>Examples:</strong>
                            <ul class="mt-2 space-y-1 text-gray-700">
                                <li>• Kilogram (kg) - Weight</li>
                                <li>• Meter (m) - Length</li>
                                <li>• Liter (L) - Volume</li>
                                <li>• Piece (pcs) - Quantity</li>
                            </ul>
                        </div>
                    </div>

                    <div class="bg-purple-50 border-2 border-purple-300 rounded-lg p-6">
                        <h3 class="text-xl font-semibold text-purple-900 mb-3">🟣 Derived Unit</h3>
                        <p class="text-sm text-purple-800 mb-3">Converted from base unit with conversion factor</p>
                        <div class="bg-white rounded p-3 text-sm">
                            <strong>Examples:</strong>
                            <ul class="mt-2 space-y-1 text-gray-700">
                                <li>• Gram (g) from Kilogram</li>
                                <li>• Centimeter (cm) from Meter</li>
                                <li>• Milliliter (ml) from Liter</li>
                                <li>• Dozen (doz) from Piece</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <h2 class="text-2xl font-bold mb-4">Create a Base Unit</h2>
                <div class="space-y-6 mb-8">
                    <div class="border-l-4 border-purple-500 pl-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 bg-purple-500 text-white rounded-full flex items-center justify-center font-bold">1</div>
                            <h3 class="text-xl font-semibold">Click "Add Unit"</h3>
                        </div>
                        <p class="text-gray-700">Click the blue <strong>"+ Add Unit"</strong> button</p>
                    </div>

                    <div class="border-l-4 border-purple-500 pl-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 bg-purple-500 text-white rounded-full flex items-center justify-center font-bold">2</div>
                            <h3 class="text-xl font-semibold">Fill Unit Details</h3>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg space-y-3">
                            <div>
                                <strong class="text-gray-900">Unit Name</strong> <span class="text-red-500">*</span>
                                <p class="text-sm text-gray-600">Full name: "Kilogram", "Meter", "Liter"</p>
                            </div>
                            <div>
                                <strong class="text-gray-900">Unit Symbol</strong> <span class="text-red-500">*</span>
                                <p class="text-sm text-gray-600">Abbreviation (max 10 chars): "kg", "m", "L"</p>
                            </div>
                            <div>
                                <strong class="text-gray-900">Description</strong>
                                <p class="text-sm text-gray-600">Optional notes</p>
                            </div>
                        </div>
                    </div>

                    <div class="border-l-4 border-purple-500 pl-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 bg-purple-500 text-white rounded-full flex items-center justify-center font-bold">3</div>
                            <h3 class="text-xl font-semibold">Select "Base Unit" Type</h3>
                        </div>
                        <p class="text-gray-700">Choose the <strong>"Base Unit"</strong> radio button</p>
                    </div>

                    <div class="border-l-4 border-purple-500 pl-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 bg-purple-500 text-white rounded-full flex items-center justify-center font-bold">4</div>
                            <h3 class="text-xl font-semibold">Save</h3>
                        </div>
                        <p class="text-gray-700 mb-3">Check <strong>"Active"</strong> checkbox and click:</p>
                        <button class="bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold">✓ Create Unit</button>
                            <img src="{{ asset('images/help/inventory_unit-create.png') }}" alt="Create Inventory Unit" class="w-full rounded-lg shadow-md mb-3">
                    </div>
                </div>

                <h2 class="text-2xl font-bold mb-4">Create a Derived Unit</h2>
                <div class="bg-blue-50 border border-blue-300 rounded-lg p-5 mb-6">
                    <p class="text-blue-900 font-semibold mb-2">What's a Derived Unit?</p>
                    <p class="text-blue-800 text-sm">A unit automatically converted from a base unit. Example: Create Kilogram (base), then Gram (derived) - system handles conversions!</p>
                </div>

                <div class="space-y-6 mb-8">
                    <div class="border-l-4 border-orange-500 pl-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 bg-orange-500 text-white rounded-full flex items-center justify-center font-bold">1</div>
                            <h3 class="text-xl font-semibold">Start New Unit & Select "Derived Unit"</h3>
                        </div>
                        <p class="text-gray-700">Choose <strong>"Derived Unit"</strong> radio button</p>
                    </div>

                    <div class="border-l-4 border-orange-500 pl-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 bg-orange-500 text-white rounded-full flex items-center justify-center font-bold">2</div>
                            <h3 class="text-xl font-semibold">Select Base Unit</h3>
                        </div>
                        <p class="text-gray-700">Choose which base unit to derive from (e.g., Kilogram for Gram)</p>
                    </div>

                    <div class="border-l-4 border-orange-500 pl-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 bg-orange-500 text-white rounded-full flex items-center justify-center font-bold">3</div>
                            <h3 class="text-xl font-semibold">Enter Conversion Factor</h3>
                        </div>
                        <div class="bg-yellow-50 border-2 border-yellow-300 rounded-lg p-5 mb-4">
                            <p class="text-yellow-900 font-bold mb-2">Conversion Factor = How many BASE UNITS equal 1 of THIS UNIT?</p>
                            <p class="text-yellow-800 text-sm">Example: For Gram from Kilogram, enter <strong>0.001</strong> (because 1g = 0.001kg)</p>
                        </div>
                        <div class="bg-white border border-gray-300 rounded-lg p-4">
                            <p class="text-sm font-semibold text-gray-900 mb-2">📊 Result:</p>
                            <p class="text-sm text-gray-700">✓ 1 Gram (g) = 0.001 Kilogram (kg)</p>
                            <p class="text-sm text-gray-700">✓ 1 Kilogram (kg) = 1,000 Grams (g)</p>
                            <p class="text-xs text-green-700 mt-2">Both directions calculated automatically!</p>
                        </div>
                    </div>
                </div>

                <h2 class="text-2xl font-bold mb-4">Common Conversion Factors</h2>
                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">⚖️ Weight</h4>
                        <div class="space-y-2 text-sm">
                            <div class="bg-gray-50 p-2 rounded flex justify-between">
                                <span>Gram → Kilogram</span>
                                <strong class="font-mono text-blue-600">0.001</strong>
                            </div>
                            <div class="bg-gray-50 p-2 rounded flex justify-between">
                                <span>Ton → Kilogram</span>
                                <strong class="font-mono text-blue-600">1000</strong>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">📏 Length</h4>
                        <div class="space-y-2 text-sm">
                            <div class="bg-gray-50 p-2 rounded flex justify-between">
                                <span>Centimeter → Meter</span>
                                <strong class="font-mono text-blue-600">0.01</strong>
                            </div>
                            <div class="bg-gray-50 p-2 rounded flex justify-between">
                                <span>Kilometer → Meter</span>
                                <strong class="font-mono text-blue-600">1000</strong>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">💧 Volume</h4>
                        <div class="space-y-2 text-sm">
                            <div class="bg-gray-50 p-2 rounded flex justify-between">
                                <span>Milliliter → Liter</span>
                                <strong class="font-mono text-blue-600">0.001</strong>
                            </div>
                            <div class="bg-gray-50 p-2 rounded flex justify-between">
                                <span>Deciliter → Liter</span>
                                <strong class="font-mono text-blue-600">0.1</strong>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">📦 Quantity</h4>
                        <div class="space-y-2 text-sm">
                            <div class="bg-gray-50 p-2 rounded flex justify-between">
                                <span>Dozen → Piece</span>
                                <strong class="font-mono text-blue-600">12</strong>
                            </div>
                            <div class="bg-gray-50 p-2 rounded flex justify-between">
                                <span>Pack (6) → Piece</span>
                                <strong class="font-mono text-blue-600">6</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <h2 class="text-2xl font-bold mb-4">Managing Units</h2>
                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white border-2 border-blue-200 rounded-lg p-5">
                        <h4 class="font-semibold text-gray-900 mb-2">👁️ View</h4>
                        <p class="text-sm text-gray-700">See unit details, conversions, and products using this unit</p>
                    </div>
                    <div class="bg-white border-2 border-blue-200 rounded-lg p-5">
                        <h4 class="font-semibold text-gray-900 mb-2">✏️ Edit</h4>
                        <p class="text-sm text-gray-700">Update name, symbol, conversion factor, or status</p>
                    </div>
                    <div class="bg-white border-2 border-green-200 rounded-lg p-5">
                        <h4 class="font-semibold text-gray-900 mb-2">🔄 Toggle Status</h4>
                        <p class="text-sm text-gray-700">Click status badge to activate/deactivate</p>
                    </div>
                    <div class="bg-white border-2 border-red-200 rounded-lg p-5">
                        <h4 class="font-semibold text-gray-900 mb-2">🗑️ Delete</h4>
                        <p class="text-sm text-gray-700">Remove unused units</p>
                        <p class="text-sm text-red-600 mt-1">⚠️ Cannot delete if used by products or has derived units</p>
                    </div>
                </div>

                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded-r-lg mb-6">
                    <h3 class="text-lg font-semibold text-yellow-900 mb-2">⚠️ Important Notes</h3>
                    <ul class="text-sm text-yellow-800 space-y-1">
                        <li>• Cannot delete units used by products</li>
                        <li>• Cannot delete base units with derived units</li>
                        <li>• Conversion factors must be greater than 0</li>
                        <li>• Deactivating hides from product dropdown</li>
                        <li>• Use standard international symbols (kg, m, L)</li>
                    </ul>
                </div>

                <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded-r-lg">
                    <h3 class="text-lg font-semibold text-green-900 mb-2">✅ Best Practices</h3>
                    <ul class="text-sm text-green-800 space-y-1">
                        <li>• Create base units before derived units</li>
                        <li>• Use international standard symbols</li>
                        <li>• Test conversion factors before assigning to products</li>
                        <li>• Group related units together</li>
                        <li>• Document custom units clearly</li>
                    </ul>
                </div>
            </div>
        </div>
    `
},
