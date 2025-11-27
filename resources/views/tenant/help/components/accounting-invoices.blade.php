'accounting-invoices': {
    template: `
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-6">📄 How to Create an Invoice</h1>
            
            <div class="bg-blue-50 border-l-4 border-blue-600 p-6 rounded-r-lg mb-8">
                <p class="text-gray-700 leading-relaxed">
                    Invoices are essential for billing your customers. Follow this step-by-step guide to create professional invoices in Ballie.
                </p>
            </div>

            <h2 class="text-2xl font-bold text-gray-900 mb-4">Step-by-Step Guide</h2>

            <div class="space-y-8">
                <!-- Step 1 -->
                <div class="border-l-4 border-green-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">1</div>
                        <h3 class="text-xl font-semibold text-gray-900">Navigate to Accounting Dashboard</h3>
                    </div>
                    <p class="text-gray-700 mb-3">
                        Click on <strong>Accounting</strong> from the sidebar menu to access the accounting dashboard.
                    </p>
                    <div class="bg-white border border-gray-200 rounded-lg p-4 mb-3">
                        <img src="{{ asset('images/help/accounting_invoice_1.png') }}" alt="Accounting Dashboard" class="w-full rounded-lg shadow-md">
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <p class="text-sm text-gray-600 mb-2">💡 <strong>Tip:</strong> The accounting dashboard shows your total revenue, expenses, outstanding invoices, and net profit at a glance.</p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="border-l-4 border-green-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">2</div>
                        <h3 class="text-xl font-semibold text-gray-900">Click "Sales Invoice" Button</h3>
                    </div>
                    <p class="text-gray-700 mb-3">
                        On the accounting dashboard, click the green <strong>"+ SALES INVOICE"</strong> button at the top left.
                    </p>
                    <div class="bg-white border border-gray-200 rounded-lg p-4 mb-3">
                        <img src="{{ asset('images/help/accounting_invoice_2.png') }}" alt="Create Invoice Page" class="w-full rounded-lg shadow-md">
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg border border-purple-200 mb-3">
                        <p class="text-sm text-purple-800 mb-2">
                            <strong>⚡ Quick Tip:</strong> Press <kbd class="px-2 py-1 bg-white border border-purple-300 rounded text-xs font-mono">Ctrl+K</kbd> anywhere in Ballie, type <strong>"invoice"</strong>, and select:
                        </p>
                        <ul class="text-sm text-purple-700 ml-4 space-y-1">
                            <li>• <strong>Create Sales Invoice</strong> - to create a new invoice</li>
                            <li>• <strong>Sales Invoices</strong> - to view list of all invoices</li>
                        </ul>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-lg p-4 mb-3">
                        <img src="{{ asset('images/help/accounting_invoice_3.png') }}" alt="Search Modal" class="w-full rounded-lg shadow-md">
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <p class="text-sm text-gray-600">This will take you to the invoice creation page.</p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="border-l-4 border-green-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">3</div>
                        <h3 class="text-xl font-semibold text-gray-900">Fill Invoice Information</h3>
                    </div>
                    <div class="space-y-4">
                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Invoice Type</h4>
                            <p class="text-gray-700 text-sm">Select <strong>Sales (SV)</strong> for regular sales invoices.</p>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Invoice Date</h4>
                            <p class="text-gray-700 text-sm">The current date is pre-filled. You can change it if needed.</p>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Reference Number (Optional)</h4>
                            <p class="text-gray-700 text-sm">Add a custom reference number for your records.</p>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Invoice Number</h4>
                            <p class="text-gray-700 text-sm">Auto-generated when you save. Format: SV-XXXX</p>
                        </div>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="border-l-4 border-green-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">4</div>
                        <h3 class="text-xl font-semibold text-gray-900">Select Customer</h3>
                    </div>
                    <p class="text-gray-700 mb-3">
                        Type to search and select an existing customer. Click the <strong>+</strong> button to add a new customer if needed.
                    </p>
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <p class="text-sm text-yellow-800">⚠️ <strong>Note:</strong> You must add customers in the CRM module before creating invoices.</p>
                    </div>
                </div>

                <!-- Step 5 -->
                <div class="border-l-4 border-green-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">5</div>
                        <h3 class="text-xl font-semibold text-gray-900">Add Invoice Items</h3>
                    </div>
                    <p class="text-gray-700 mb-3">
                        In the <strong>Invoice Items</strong> section:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 mb-3">
                        <li><strong>Product:</strong> Search and select products from your inventory</li>
                        <li><strong>Description:</strong> Add or edit product description</li>
                        <li><strong>Quantity:</strong> Enter the quantity sold</li>
                        <li><strong>Rate:</strong> Unit price (auto-filled from product)</li>
                        <li><strong>Amount:</strong> Automatically calculated (Qty × Rate)</li>
                    </ul>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <p class="text-sm text-gray-600">💡 Click <strong>"+ Add Item"</strong> to add multiple products to the invoice.</p>
                    </div>
                </div>

                <!-- Step 6 -->
                <div class="border-l-4 border-green-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">6</div>
                        <h3 class="text-xl font-semibold text-gray-900">Add Additional Charges (Optional)</h3>
                    </div>
                    <p class="text-gray-700 mb-3">
                        Click <strong>"+ Add Charge"</strong> to include:
                    </p>
                    <ul class="list-disc list-inside space-y-1 text-gray-700">
                        <li>Shipping/Transport fees</li>
                        <li>Handling charges</li>
                        <li>Other miscellaneous charges</li>
                    </ul>
                </div>

                <!-- Step 7 -->
                <div class="border-l-4 border-green-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">7</div>
                        <h3 class="text-xl font-semibold text-gray-900">Add VAT (Optional)</h3>
                    </div>
                    <p class="text-gray-700 mb-3">
                        Check the <strong>"Add VAT (7.5%)"</strong> checkbox to automatically calculate and add VAT to the invoice total.
                    </p>
                </div>

                <!-- Step 8 -->
                <div class="border-l-4 border-green-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">8</div>
                        <h3 class="text-xl font-semibold text-gray-900">Save the Invoice</h3>
                    </div>
                    <p class="text-gray-700 mb-3">
                        Choose one of the save options at the bottom:
                    </p>
                    <div class="grid md:grid-cols-3 gap-4">
                        <div class="bg-gray-100 p-4 rounded-lg border border-gray-300">
                            <h4 class="font-semibold text-gray-900 mb-2">Save Draft</h4>
                            <p class="text-sm text-gray-700">Save without posting. You can edit later.</p>
                        </div>
                        <div class="bg-blue-100 p-4 rounded-lg border border-blue-300">
                            <h4 class="font-semibold text-gray-900 mb-2">Save & Post</h4>
                            <p class="text-sm text-gray-700">Post to accounts. Cannot edit after posting.</p>
                        </div>
                        <div class="bg-green-100 p-4 rounded-lg border border-green-300">
                            <h4 class="font-semibold text-gray-900 mb-2">Save, Post & New Sales</h4>
                            <p class="text-sm text-gray-700">Post and create another invoice immediately.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Tips -->
            <div class="mt-12 bg-gradient-to-r from-purple-50 to-blue-50 p-6 rounded-lg border border-purple-200">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">💡 Quick Tips</h3>
                <ul class="space-y-2 text-gray-700">
                    <li>✅ Always verify customer details before posting</li>
                    <li>✅ Double-check quantities and prices</li>
                    <li>✅ Use draft mode if you need to review later</li>
                    <li>✅ Posted invoices update your accounts and inventory automatically</li>
                    <li>✅ You can print or email invoices after posting</li>
                </ul>
            </div>

            <!-- Related Topics -->
            <div class="mt-8 bg-gray-50 p-6 rounded-lg border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">📚 Related Topics</h3>
                <div class="space-y-2">
                    <a href="#" class="block text-blue-600 hover:text-blue-800">→ How to add customers</a>
                    <a href="#" class="block text-blue-600 hover:text-blue-800">→ How to manage products</a>
                    <a href="#" class="block text-blue-600 hover:text-blue-800">→ How to record payments</a>
                    <a href="#" class="block text-blue-600 hover:text-blue-800">→ Understanding invoice statuses</a>
                </div>
            </div>
        </div>
    `
},
