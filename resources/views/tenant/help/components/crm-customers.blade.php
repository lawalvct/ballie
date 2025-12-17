'crm-customers': {
    template: `
        <div>
            <h1 class="text-3xl font-bold mb-4">👥 Customer Management</h1>
            <p class="text-gray-600 mb-6">
                Comprehensive customer relationship management system to help you manage your customer database,
                track transactions, generate statements, and maintain strong business relationships.
            </p>

            <!-- Navigation Guide -->
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                <h3 class="font-semibold text-blue-900 mb-2">🧭 How to Access Customer Management</h3>
                <p class="text-sm text-blue-800 mb-2">Navigate to <strong>CRM → Customers</strong> from the main menu</p>
                <p class="text-xs text-blue-700">This is your central hub for managing all customer-related activities</p>
            </div>

            <!-- Adding a New Customer -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-4 flex items-center">
                    <span class="bg-blue-100 text-blue-800 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-lg">➕</span>
                    Adding a New Customer
                </h2>
                <p class="text-gray-700 mb-4">
                    Create comprehensive customer records with all necessary contact and business information.
                </p>

                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h3 class="font-semibold text-gray-900 mb-3">📝 Step-by-Step Guide:</h3>
                    <ol class="space-y-3 text-gray-700">
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">1</span>
                            <div>
                                <strong>Click "Add New Customer"</strong> button on the customers page
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">2</span>
                            <div>
                                <strong>Choose Customer Type:</strong>
                                <ul class="ml-4 mt-1 space-y-1 text-sm">
                                    <li>• <strong>Individual:</strong> For personal customers (requires First Name, Last Name)</li>
                                    <li>• <strong>Business:</strong> For corporate clients (requires Company Name, optional Tax ID)</li>
                                </ul>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">3</span>
                            <div>
                                <strong>Fill Required Information:</strong>
                                <ul class="ml-4 mt-1 space-y-1 text-sm">
                                    <li>• Email address (must be unique)</li>
                                    <li>• Phone number</li>
                                    <li>• Customer status (Active/Inactive)</li>
                                </ul>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">4</span>
                            <div>
                                <strong>Add Address Information (Optional):</strong>
                                <ul class="ml-4 mt-1 space-y-1 text-sm">
                                    <li>• Address Line 1 & 2</li>
                                    <li>• City, State/Province</li>
                                    <li>• Postal Code, Country</li>
                                </ul>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">5</span>
                            <div>
                                <strong>Set Banking Details (Optional):</strong>
                                <ul class="ml-4 mt-1 space-y-1 text-sm">
                                    <li>• Payment terms</li>
                                    <li>• Credit limit</li>
                                    <li>• Preferred payment method</li>
                                </ul>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">6</span>
                            <div>
                                <strong>Add Opening Balance (Optional):</strong> If the customer has an existing balance from previous transactions
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">7</span>
                            <div>
                                <strong>Click "Save Customer"</strong> to create the record
                            </div>
                        </li>
                    </ol>
                </div>

                <div class="bg-blue-50 rounded-lg p-4">
                    <h3 class="font-semibold text-blue-900 mb-2">💡 Pro Tips:</h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• The system automatically generates a unique customer code</li>
                        <li>• Email addresses must be unique across all customers</li>
                        <li>• Use the collapsible sections to organize information better</li>
                        <li>• Opening balance can be set during creation or later</li>
                    </ul>
                </div>
            </div>

            <!-- Customer List & Search -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-4 flex items-center">
                    <span class="bg-green-100 text-green-800 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-lg">🔍</span>
                    Viewing & Searching Customers
                </h2>
                <p class="text-gray-700 mb-4">
                    Efficiently find and manage customers using powerful search and filtering tools.
                </p>

                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h3 class="font-semibold text-gray-900 mb-3">🔎 Search Features:</h3>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Quick Search:</strong> Search by name, email, phone number, or company name in the main search bar</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Customer Type Filter:</strong> Filter by Individual or Business customers</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Status Filter:</strong> View Active or Inactive customers</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Sort Options:</strong> Sort by Date Added, Name, Email, or Total Spent</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Sort Direction:</strong> Ascending or Descending order</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-green-50 rounded-lg p-4">
                    <h3 class="font-semibold text-green-900 mb-2">📋 Customer List Display:</h3>
                    <p class="text-sm text-green-800 mb-2">Each customer entry shows:</p>
                    <ul class="text-sm text-green-800 space-y-1">
                        <li>• Customer avatar with initials</li>
                        <li>• Name and customer type badge</li>
                        <li>• Contact information (email, phone)</li>
                        <li>• Status indicator (Active/Inactive)</li>
                        <li>• Quick action buttons (View, Edit, Statement)</li>
                    </ul>
                </div>
            </div>

            <!-- Customer Details -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-4 flex items-center">
                    <span class="bg-purple-100 text-purple-800 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-lg">👤</span>
                    Viewing Customer Details
                </h2>
                <p class="text-gray-700 mb-4">
                    Access comprehensive customer information and transaction history in one place.
                </p>

                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h3 class="font-semibold text-gray-900 mb-3">📊 Customer Details Page Includes:</h3>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-purple-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Contact Information:</strong> Email, phone, mobile, and tax ID (for businesses)</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-purple-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Address Details:</strong> Complete address with city, state, postal code, and country</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-purple-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Financial Summary:</strong> Total receivables, total paid, outstanding balance</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-purple-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Transaction History:</strong> List of all invoices, payments, and credit notes</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-purple-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Quick Actions:</strong> View Statement, Edit Customer, Back to List</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Customer Statements -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-4 flex items-center">
                    <span class="bg-yellow-100 text-yellow-800 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-lg">📄</span>
                    Generating Customer Statements
                </h2>
                <p class="text-gray-700 mb-4">
                    Create detailed financial statements showing all transactions and balances for any customer.
                </p>

                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h3 class="font-semibold text-gray-900 mb-3">📋 How to Generate a Statement:</h3>
                    <ol class="space-y-3 text-gray-700">
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-yellow-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">1</span>
                            <div>
                                Navigate to the customer details page or click "View Statement" from the customer list
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-yellow-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">2</span>
                            <div>
                                <strong>Select Date Range:</strong> Choose start and end dates for the statement period
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-yellow-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">3</span>
                            <div>
                                Click "Apply Filter" to generate the statement
                            </div>
                        </li>
                    </ol>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h3 class="font-semibold text-gray-900 mb-3">📊 Statement Information:</h3>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Opening Balance:</strong> Customer balance at the start of the period</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Total Invoiced:</strong> Sum of all invoices in the period</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Total Paid:</strong> Sum of all payments received</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Closing Balance:</strong> Outstanding balance at the end of the period</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Transaction Details:</strong> Line-by-line list of all transactions with dates, references, and amounts</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-yellow-50 rounded-lg p-4">
                    <h3 class="font-semibold text-yellow-900 mb-2">🎯 Statement Actions:</h3>
                    <ul class="text-sm text-yellow-800 space-y-1">
                        <li>• <strong>Print:</strong> Print the statement for physical records</li>
                        <li>• <strong>Download PDF:</strong> Save as PDF to email to customer or archive</li>
                        <li>• <strong>Filter by Date:</strong> Generate statements for specific periods</li>
                        <li>• <strong>View All Transactions:</strong> See complete transaction history</li>
                    </ul>
                </div>
            </div>

            <!-- Bulk Import -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-4 flex items-center">
                    <span class="bg-green-100 text-green-800 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-lg">📤</span>
                    Bulk Upload Customers
                </h2>
                <p class="text-gray-700 mb-4">
                    Import multiple customers at once using an Excel or CSV file.
                </p>

                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h3 class="font-semibold text-gray-900 mb-3">📥 Import Process:</h3>
                    <ol class="space-y-3 text-gray-700">
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">1</span>
                            <div>
                                Click "Bulk Upload Customers" button on the customers page
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">2</span>
                            <div>
                                Download the sample template to see the required format
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">3</span>
                            <div>
                                Fill in your customer data following the template structure
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">4</span>
                            <div>
                                Upload your completed file (Excel .xlsx or .csv)
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">5</span>
                            <div>
                                Review the import summary and confirm
                            </div>
                        </li>
                    </ol>
                </div>

                <div class="bg-green-50 rounded-lg p-4">
                    <h3 class="font-semibold text-green-900 mb-2">⚠️ Important Notes:</h3>
                    <ul class="text-sm text-green-800 space-y-1">
                        <li>• Email addresses must be unique</li>
                        <li>• Required fields: Customer Type, Name/Company Name, Email, Phone</li>
                        <li>• The system will skip duplicate entries</li>
                        <li>• Review the template carefully to avoid errors</li>
                    </ul>
                </div>
            </div>

            <!-- Editing & Managing -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-4 flex items-center">
                    <span class="bg-orange-100 text-orange-800 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-lg">✏️</span>
                    Editing & Managing Customers
                </h2>
                <p class="text-gray-700 mb-4">
                    Update customer information and manage their status as needed.
                </p>

                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h3 class="font-semibold text-gray-900 mb-3">🔧 Common Tasks:</h3>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-orange-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Edit Customer:</strong> Click the edit button to update any customer information</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-orange-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Activate/Deactivate:</strong> Change customer status to Active or Inactive</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-orange-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Update Contact Info:</strong> Keep email, phone, and address up to date</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-orange-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Adjust Credit Limits:</strong> Modify payment terms and credit limits as relationships develop</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-4 flex items-center">
                    <span class="bg-red-100 text-red-800 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-lg">⚡</span>
                    Quick Actions from Customer Page
                </h2>

                <div class="grid md:grid-cols-2 gap-4">
                    <div class="bg-white border-2 border-blue-200 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-2 flex items-center">
                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Create Invoice
                        </h3>
                        <p class="text-sm text-gray-600">Quickly create a new invoice for any customer directly from the customer list</p>
                    </div>

                    <div class="bg-white border-2 border-green-200 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-2 flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export Customers
                        </h3>
                        <p class="text-sm text-gray-600">Download your entire customer database as Excel or CSV for backup or analysis</p>
                    </div>

                    <div class="bg-white border-2 border-purple-200 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-2 flex items-center">
                            <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Add Vendor
                        </h3>
                        <p class="text-sm text-gray-600">Quick access to add a new vendor if you need to switch to vendor management</p>
                    </div>

                    <div class="bg-white border-2 border-yellow-200 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-2 flex items-center">
                            <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            View Statement
                        </h3>
                        <p class="text-sm text-gray-600">Generate and view detailed financial statements for any customer</p>
                    </div>
                </div>
            </div>

            <!-- Tips and Best Practices -->
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg p-6 border-2 border-blue-200">
                <h2 class="text-xl font-bold mb-4 text-blue-900">💡 Best Practices</h2>
                <div class="space-y-3 text-gray-700">
                    <div class="flex items-start">
                        <span class="font-bold text-blue-600 mr-2">1.</span>
                        <span><strong>Keep Information Updated:</strong> Regularly update customer contact details to ensure smooth communication</span>
                    </div>
                    <div class="flex items-start">
                        <span class="font-bold text-blue-600 mr-2">2.</span>
                        <span><strong>Regular Statements:</strong> Send monthly or quarterly statements to customers for transparency</span>
                    </div>
                    <div class="flex items-start">
                        <span class="font-bold text-blue-600 mr-2">3.</span>
                        <span><strong>Use Proper Customer Types:</strong> Classify customers correctly (Individual vs Business) for accurate reporting</span>
                    </div>
                    <div class="flex items-start">
                        <span class="font-bold text-blue-600 mr-2">4.</span>
                        <span><strong>Monitor Outstanding Balances:</strong> Regularly review customer balances to manage receivables effectively</span>
                    </div>
                    <div class="flex items-start">
                        <span class="font-bold text-blue-600 mr-2">5.</span>
                        <span><strong>Export for Backup:</strong> Periodically export your customer database for backup purposes</span>
                    </div>
                    <div class="flex items-start">
                        <span class="font-bold text-blue-600 mr-2">6.</span>
                        <span><strong>Use Status Wisely:</strong> Mark inactive customers as "Inactive" rather than deleting them to maintain transaction history</span>
                    </div>
                </div>
            </div>

            <!-- Need Help? -->
            <div class="mt-8 bg-gray-100 rounded-lg p-6 text-center">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Need Additional Help?</h3>
                <p class="text-gray-600 mb-4">If you have questions about customer management or need assistance, our support team is here to help.</p>
                <a href="#" @click.prevent="$parent.activeMenu = 'support'; $parent.activeSubmenu = null"
                   class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Contact Support
                </a>
            </div>
        </div>
    `
},
