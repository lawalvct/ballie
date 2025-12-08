'inventory-stock': {
    template: `
        <div>
            <h1 class="text-3xl font-bold mb-6">📊 Stock Management (Stock Journal)</h1>

            <div class="bg-blue-50 border-l-4 border-blue-600 p-6 rounded-r-lg mb-8">
                <p class="text-gray-700 leading-relaxed">
                    Stock Journal entries track all inventory movements including material consumption, production receipts, stock adjustments, and transfers between locations. This ensures accurate inventory records and proper stock valuation.
                </p>
            </div>

            <h2 class="text-2xl font-bold mb-4">Accessing Stock Journal</h2>
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                <p class="text-gray-700 mb-3">From Inventory Dashboard, click <strong>"Stock Voucher"</strong> button to access stock journal entries.</p>
            </div>

            <h2 class="text-2xl font-bold mb-4">Stock Journal List</h2>
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                <img src="{{ asset('images/help/inventory_stock-journal-list.png') }}" alt="Stock Journal List" class="w-full rounded-lg shadow-md mb-4">
                <div class="space-y-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-2">Quick Create Buttons</h4>
                        <div class="grid md:grid-cols-4 gap-3 text-sm">
                            <div class="bg-green-50 p-2 rounded border border-green-200">
                                <strong class="text-green-800">Material Consumption</strong> - Record materials used
                            </div>
                            <div class="bg-blue-50 p-2 rounded border border-blue-200">
                                <strong class="text-blue-800">Product Receipt</strong> - Record finished goods produced
                            </div>
                            <div class="bg-red-50 p-2 rounded border border-red-200">
                                <strong class="text-red-800">Stock Adjustment</strong> - Correct stock discrepancies
                            </div>
                            <div class="bg-purple-50 p-2 rounded border border-purple-200">
                                <strong class="text-purple-800">Stock Transfer</strong> - Move stock between locations
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-2">Summary Cards</h4>
                        <ul class="text-sm text-gray-700 space-y-1 ml-4">
                            <li>• <strong>Total Entries:</strong> All stock journal entries</li>
                            <li>• <strong>Draft Entries:</strong> Saved but not posted</li>
                            <li>• <strong>Posted Entries:</strong> Finalized entries affecting stock</li>
                            <li>• <strong>This Month:</strong> Entries created this month</li>
                        </ul>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-2">Search & Filters</h4>
                        <ul class="text-sm text-gray-700 space-y-1 ml-4">
                            <li>• Search by journal number, reference, or narration</li>
                            <li>• Filter by Entry Type (All, Consumption, Production, Adjustment, Transfer)</li>
                            <li>• Filter by Status (All, Draft, Posted)</li>
                            <li>• Filter by Date Range (From Date - To Date)</li>
                        </ul>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">1. Material Consumption Entry</h2>
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                <img src="{{ asset('images/help/inventory_stock-material-comsumption.png') }}" alt="Material Consumption" class="w-full rounded-lg shadow-md mb-4">
                <div class="space-y-4">
                    <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                        <h4 class="font-semibold text-red-900 mb-2">What is Material Consumption?</h4>
                        <p class="text-sm text-red-800">Records raw materials or components used in production or operations. This reduces inventory and tracks material usage.</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-3">How to Create:</h4>
                        <ol class="text-sm text-gray-700 space-y-2 ml-4 list-decimal">
                            <li>Click <strong>"+ MATERIAL CONSUMPTION"</strong> button</li>
                            <li>Select <strong>Journal Date</strong></li>
                            <li>Entry Type is pre-selected as <strong>"Material Consumption"</strong></li>
                            <li>Add optional <strong>Reference Number</strong></li>
                            <li>Enter <strong>Narration</strong> (description of consumption)</li>
                            <li>Click <strong>"+ Add Item"</strong> to add products</li>
                            <li>For each item:
                                <ul class="ml-4 mt-1 space-y-1">
                                    <li>- Select <strong>Product</strong></li>
                                    <li>- Movement is set to <strong>"Out"</strong> (reducing stock)</li>
                                    <li>- View <strong>Current Stock</strong></li>
                                    <li>- Enter <strong>Quantity</strong> consumed</li>
                                    <li>- Enter <strong>Rate</strong> (cost per unit)</li>
                                    <li>- Amount auto-calculates</li>
                                    <li>- Optional: Add <strong>Batch Number</strong> and <strong>Expiry Date</strong></li>
                                </ul>
                            </li>
                            <li>Review <strong>Total Amount</strong></li>
                            <li>Click <strong>"Save as Draft"</strong> or <strong>"Save & Post"</strong></li>
                        </ol>
                    </div>
                    <div class="bg-yellow-50 p-3 rounded border border-yellow-200">
                        <p class="text-sm text-yellow-800"><strong>💡 Example:</strong> Used 50 units of Steel Rods and 20 units of Bolts for manufacturing chairs.</p>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">2. Production Receipt Entry</h2>
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                <img src="{{ asset('images/help/inventory_stock-journal-production.png') }}" alt="Production Receipt" class="w-full rounded-lg shadow-md mb-4">
                <div class="space-y-4">
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <h4 class="font-semibold text-blue-900 mb-2">What is Production Receipt?</h4>
                        <p class="text-sm text-blue-800">Records finished goods produced from raw materials. Shows both materials consumed (OUT) and products manufactured (IN) in a two-sided entry.</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-3">Two-Sided Production Entry:</h4>
                        <div class="grid md:grid-cols-2 gap-4 mb-3">
                            <div class="bg-red-50 p-3 rounded border border-red-200">
                                <h5 class="font-semibold text-red-800 mb-2">LEFT: Source (Consumption) - OUT</h5>
                                <p class="text-sm text-red-700">Raw materials and components used in production</p>
                            </div>
                            <div class="bg-green-50 p-3 rounded border border-green-200">
                                <h5 class="font-semibold text-green-800 mb-2">RIGHT: Destination (Production) - IN</h5>
                                <p class="text-sm text-green-700">Finished goods manufactured</p>
                            </div>
                        </div>
                        <ol class="text-sm text-gray-700 space-y-2 ml-4 list-decimal">
                            <li>Click <strong>"+ PRODUCT RECEIPT"</strong> button</li>
                            <li>Select <strong>Journal Date</strong></li>
                            <li>Entry Type is pre-selected as <strong>"Production Receipt"</strong></li>
                            <li>Add optional <strong>Reference Number</strong></li>
                            <li>Enter <strong>Narration</strong></li>
                            <li><strong>LEFT SIDE (Red - Consumption):</strong>
                                <ul class="ml-4 mt-1 space-y-1">
                                    <li>- Click <strong>"+ Add"</strong> to add raw materials</li>
                                    <li>- Select products consumed</li>
                                    <li>- Enter quantities and rates</li>
                                    <li>- View <strong>Total Consumption</strong></li>
                                </ul>
                            </li>
                            <li><strong>RIGHT SIDE (Green - Production):</strong>
                                <ul class="ml-4 mt-1 space-y-1">
                                    <li>- Click <strong>"+ Add"</strong> to add finished goods</li>
                                    <li>- Select products manufactured</li>
                                    <li>- Enter quantities and rates</li>
                                    <li>- View <strong>Total Production</strong></li>
                                </ul>
                            </li>
                            <li>Check <strong>Balance Check</strong> (optional - for information only)</li>
                            <li>Click <strong>"Save as Draft"</strong> or <strong>"Save & Post"</strong></li>
                        </ol>
                    </div>
                    <div class="bg-purple-50 p-3 rounded border border-purple-200">
                        <p class="text-sm text-purple-800"><strong>💡 Example:</strong> Used 100 units of Wood (OUT) and 50 units of Nails (OUT) to produce 10 units of Wooden Tables (IN).</p>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">3. Stock Adjustment Entry</h2>
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                <img src="{{ asset('images/help/inventory_stock-journal-adjustment-entry.png') }}" alt="Stock Adjustment" class="w-full rounded-lg shadow-md mb-4">
                <div class="space-y-4">
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <h4 class="font-semibold text-yellow-900 mb-2">What is Stock Adjustment?</h4>
                        <p class="text-sm text-yellow-800">Corrects stock discrepancies from physical counts, damage, theft, or errors. Can increase (IN) or decrease (OUT) inventory.</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-3">How to Create:</h4>
                        <ol class="text-sm text-gray-700 space-y-2 ml-4 list-decimal">
                            <li>Click <strong>"+ STOCK ADJUSTMENT"</strong> button</li>
                            <li>Select <strong>Journal Date</strong></li>
                            <li>Entry Type is pre-selected as <strong>"Stock Adjustment"</strong></li>
                            <li>Add optional <strong>Reference Number</strong></li>
                            <li>Enter <strong>Narration</strong> (reason for adjustment)</li>
                            <li>Click <strong>"+ Add Item"</strong></li>
                            <li>For each item:
                                <ul class="ml-4 mt-1 space-y-1">
                                    <li>- Select <strong>Product</strong></li>
                                    <li>- Choose <strong>Movement</strong>:
                                        <ul class="ml-4">
                                            <li>• <strong>IN</strong> - Increase stock (found missing items, correction)</li>
                                            <li>• <strong>OUT</strong> - Decrease stock (damage, theft, shrinkage)</li>
                                        </ul>
                                    </li>
                                    <li>- View <strong>Current Stock</strong></li>
                                    <li>- Enter <strong>Quantity</strong> to adjust</li>
                                    <li>- Enter <strong>Rate</strong></li>
                                    <li>- Optional: Add <strong>Batch Number</strong> and <strong>Expiry Date</strong></li>
                                </ul>
                            </li>
                            <li>Review <strong>Total Amount</strong></li>
                            <li>Click <strong>"Save as Draft"</strong> or <strong>"Save & Post"</strong></li>
                        </ol>
                    </div>
                    <div class="bg-green-50 p-3 rounded border border-green-200">
                        <p class="text-sm text-green-800"><strong>💡 Examples:</strong></p>
                        <ul class="text-sm text-green-700 ml-4 mt-1 space-y-1">
                            <li>• <strong>IN:</strong> Physical count shows 105 units but system shows 100 (add 5 units)</li>
                            <li>• <strong>OUT:</strong> 10 units damaged during storage (remove 10 units)</li>
                            <li>• <strong>OUT:</strong> Discovered 3 units missing due to theft (remove 3 units)</li>
                        </ul>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">4. Stock Transfer Entry</h2>
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                <img src="{{ asset('images/help/inventory_stock-transfer-entries.png') }}" alt="Stock Transfer" class="w-full rounded-lg shadow-md mb-4">
                <div class="space-y-4">
                    <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                        <h4 class="font-semibold text-purple-900 mb-2">What is Stock Transfer?</h4>
                        <p class="text-sm text-purple-800">Moves inventory between locations, warehouses, or branches. Shows items leaving one location (OUT) and arriving at another (IN).</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-3">Two-Sided Transfer Entry:</h4>
                        <ol class="text-sm text-gray-700 space-y-2 ml-4 list-decimal">
                            <li>Click <strong>"STOCK TRANSFER"</strong> button</li>
                            <li>Select <strong>Journal Date</strong></li>
                            <li>Entry Type is pre-selected as <strong>"Stock Transfer"</strong></li>
                            <li>Add optional <strong>Reference Number</strong></li>
                            <li>Enter <strong>Narration</strong></li>
                            <li><strong>Location Selection:</strong>
                                <ul class="ml-4 mt-1 space-y-1">
                                    <li>- Enter <strong>FROM Location/Branch</strong> (e.g., Main Warehouse, Lagos Branch)</li>
                                    <li>- Enter <strong>TO Location/Branch</strong> (e.g., Retail Store, Abuja Branch)</li>
                                </ul>
                            </li>
                            <li><strong>LEFT SIDE (Red - FROM Location OUT):</strong>
                                <ul class="ml-4 mt-1 space-y-1">
                                    <li>- Click <strong>"+ Add Item"</strong></li>
                                    <li>- Select products leaving source location</li>
                                    <li>- Enter quantities and rates</li>
                                    <li>- View <strong>Total OUT</strong></li>
                                </ul>
                            </li>
                            <li><strong>RIGHT SIDE (Green - TO Location IN):</strong>
                                <ul class="ml-4 mt-1 space-y-1">
                                    <li>- Click <strong>"+ Add Item"</strong></li>
                                    <li>- Select products arriving at destination</li>
                                    <li>- Enter quantities and rates</li>
                                    <li>- View <strong>Total IN</strong></li>
                                </ul>
                            </li>
                            <li>Check <strong>Transfer Balanced</strong> status (optional)</li>
                            <li>Click <strong>"Save as Draft"</strong> or <strong>"Save & Post"</strong></li>
                        </ol>
                    </div>
                    <div class="bg-indigo-50 p-3 rounded border border-indigo-200">
                        <p class="text-sm text-indigo-800"><strong>💡 Example:</strong> Transfer 50 units of Product A and 30 units of Product B from Main Warehouse (Lagos) to Retail Store (Abuja).</p>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">Understanding Draft vs Posted</h2>
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div class="bg-gray-50 border-l-4 border-gray-500 p-4 rounded-r-lg">
                    <h3 class="font-bold text-gray-900 mb-2">📝 Save as Draft</h3>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>✓ Saves entry without affecting stock</li>
                        <li>✓ Can edit or delete later</li>
                        <li>✓ Useful for review before finalizing</li>
                        <li>✓ Does not update inventory levels</li>
                    </ul>
                </div>
                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
                    <h3 class="font-bold text-green-900 mb-2">✅ Save & Post</h3>
                    <ul class="text-sm text-green-700 space-y-1">
                        <li>✓ Finalizes and posts entry</li>
                        <li>✓ Updates stock levels immediately</li>
                        <li>✓ Cannot edit after posting</li>
                        <li>✓ Generates journal number</li>
                    </ul>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">Entry Details & Actions</h2>
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-8">
                <h4 class="font-semibold text-gray-900 mb-3">Available Actions on Each Entry:</h4>
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 p-3 rounded">
                        <strong class="text-blue-900">👁️ View</strong>
                        <p class="text-sm text-blue-800 mt-1">View complete entry details</p>
                    </div>
                    <div class="bg-purple-50 p-3 rounded">
                        <strong class="text-purple-900">✏️ Edit</strong>
                        <p class="text-sm text-purple-800 mt-1">Edit draft entries only</p>
                    </div>
                    <div class="bg-red-50 p-3 rounded">
                        <strong class="text-red-900">🗑️ Delete</strong>
                        <p class="text-sm text-red-800 mt-1">Delete draft entries only</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-50 to-blue-50 p-6 rounded-lg border border-purple-200 mb-8">
                <h3 class="text-xl font-semibold text-purple-900 mb-4">💡 Best Practices</h3>
                <ul class="space-y-2 text-purple-800">
                    <li>✅ Record stock movements daily for accuracy</li>
                    <li>✅ Use clear narrations describing the transaction</li>
                    <li>✅ Add reference numbers for traceability</li>
                    <li>✅ Use batch numbers for products with expiry dates</li>
                    <li>✅ Review draft entries before posting</li>
                    <li>✅ Perform regular stock counts and adjustments</li>
                    <li>✅ Use production entries to track manufacturing costs</li>
                    <li>✅ Document transfer reasons in narration</li>
                </ul>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-r-lg">
                <h3 class="text-lg font-semibold text-yellow-900 mb-3">⚠️ Important Notes</h3>
                <ul class="space-y-2 text-yellow-800">
                    <li>• Posted entries cannot be edited or deleted</li>
                    <li>• Stock levels update only when entries are posted</li>
                    <li>• Balance check in production/transfer is informational only</li>
                    <li>• Always verify current stock before creating entries</li>
                    <li>• Use appropriate entry type for accurate reporting</li>
                </ul>
            </div>
        </div>
    `
},
