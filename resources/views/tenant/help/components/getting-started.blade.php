'getting-started': {
    template: `
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Getting Started with Ballie</h1>
            
            <div class="bg-gradient-to-r from-purple-50 to-blue-50 border-l-4 border-purple-600 p-6 rounded-r-lg mb-8">
                <h2 class="text-2xl font-semibold text-purple-900 mb-3">Welcome! 🎉</h2>
                <p class="text-gray-700 leading-relaxed">
                    Ballie is your all-in-one business management solution designed for Nigerian businesses. 
                    Follow these steps to get up and running quickly.
                </p>
            </div>

            <h2 class="text-2xl font-bold text-gray-900 mb-4">Quick Start Guide</h2>
            
            <div class="space-y-6 mb-8">
                <div v-for="(step, idx) in steps" :key="idx" class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-10 h-10 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold text-lg" v-text="idx + 1"></div>
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2" v-text="step.title"></h3>
                            <p class="text-gray-700 mb-3" v-text="step.description"></p>
                            <a v-if="step.link" :href="step.link" class="inline-flex items-center text-purple-600 hover:text-purple-800 font-medium">
                                <span v-text="step.linkText"></span>
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold text-gray-900 mb-4 mt-12">Key Features</h2>
            
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div v-for="feature in features" :key="feature.title" :class="feature.bgClass">
                    <div class="flex items-center gap-3 mb-3">
                        <svg class="w-8 h-8" :class="feature.iconClass" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="feature.icon"/>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900" v-text="feature.title"></h3>
                    </div>
                    <p class="text-gray-700" v-text="feature.description"></p>
                </div>
            </div>

            <div class="bg-purple-50 border-l-4 border-purple-400 p-6 rounded-r-lg mt-8">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-purple-600 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-purple-900 mb-2">⚡ Quick Access Tip</h3>
                        <p class="text-purple-800 mb-2">
                            Press <kbd class="px-2 py-1 bg-white border border-purple-300 rounded text-sm font-mono">Ctrl+K</kbd> anywhere in Ballie to open the quick search modal.
                        </p>
                        <p class="text-purple-800">
                            Type any action you want (e.g., "invoice", "customer", "payroll") and instantly navigate to that page or create new records.
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-r-lg mt-6">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-yellow-600 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-yellow-900 mb-2">Need Help?</h3>
                        <p class="text-yellow-800 mb-3">
                            Explore the documentation sections on the left or contact our support team.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    `,
    data() {
        return {
            steps: [
                { 
                    title: 'Complete Company Profile', 
                    description: 'Set up your company information, logo, and business preferences to personalize your experience.',
                    link: '{{ route("tenant.settings.company", $tenant) }}',
                    linkText: 'Go to Company Settings'
                },
                { 
                    title: 'Add Products/Services', 
                    description: 'Create your product catalog with pricing, descriptions, and stock levels.',
                    link: '{{ route("tenant.inventory.products.index", $tenant) }}',
                    linkText: 'Manage Products'
                },
                { 
                    title: 'Add Customers & Vendors', 
                    description: 'Build your contact database to streamline invoicing and purchasing.',
                    link: '{{ route("tenant.crm.customers.index", $tenant) }}',
                    linkText: 'Add Customers'
                },
                { 
                    title: 'Create Your First Invoice', 
                    description: 'Start billing your customers with professional invoices.',
                    link: '{{ route("tenant.accounting.invoices.create", $tenant) }}',
                    linkText: 'Create Invoice'
                },
                { 
                    title: 'Set Up Payroll (Optional)', 
                    description: 'Add employees and configure salary components for automated payroll processing.',
                    link: '{{ route("tenant.payroll.employees.index", $tenant) }}',
                    linkText: 'Manage Employees'
                }
            ],
            features: [
                {
                    title: 'Accounting & Invoicing',
                    description: 'Create invoices, track payments, manage vouchers, and generate financial reports.',
                    icon: 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z',
                    iconClass: 'text-green-600',
                    bgClass: 'bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-lg border border-green-200'
                },
                {
                    title: 'Inventory Management',
                    description: 'Track stock levels, manage products, record stock movements, and prevent stockouts.',
                    icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                    iconClass: 'text-purple-600',
                    bgClass: 'bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-lg border border-purple-200'
                },
                {
                    title: 'Payroll System',
                    description: 'Process salaries, calculate PAYE tax, manage pension contributions, and generate payslips.',
                    icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                    iconClass: 'text-blue-600',
                    bgClass: 'bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-lg border border-blue-200'
                },
                {
                    title: 'Point of Sale (POS)',
                    description: 'Process quick sales, manage cash register, and track daily transactions.',
                    icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
                    iconClass: 'text-pink-600',
                    bgClass: 'bg-gradient-to-br from-pink-50 to-pink-100 p-6 rounded-lg border border-pink-200'
                }
            ]
        }
    }
},
