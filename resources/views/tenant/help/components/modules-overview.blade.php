'modules-overview': {
    template: `
        <section id="modules" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Modules Overview</h2>
            <div class="space-y-6">
                <div v-for="module in modules" :key="module.name" class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-lg mb-2" :class="module.color" v-text="module.icon + ' ' + module.name"></h3>
                    <p class="text-gray-700" v-text="module.description"></p>
                </div>
            </div>
        </section>
    `,
    data() {
        return {
            modules: [
                { icon: '📊', name: 'Accounting', color: 'text-green-600', description: 'Manage invoices, vouchers, quotations, and financial transactions. Track income, expenses, and generate financial reports.' },
                { icon: '📦', name: 'Inventory', color: 'text-purple-600', description: 'Track stock levels, manage products, categories, and units. Monitor stock movements and perform physical stock counts.' },
                { icon: '👥', name: 'CRM', color: 'text-pink-600', description: 'Manage customer and vendor relationships. Track contact information, outstanding balances, and transaction history.' },
                { icon: '🛒', name: 'POS', color: 'text-cyan-600', description: 'Point of Sale system for quick sales transactions. Manage cash registers and process payments efficiently.' },
                { icon: '💰', name: 'Payroll', color: 'text-emerald-600', description: 'Manage employee salaries, deductions, and payroll runs. Calculate PAYE tax and generate payslips.' },
                { icon: '👤', name: 'Admin', color: 'text-blue-600', description: 'Manage users, roles, and permissions. Control who has access to different parts of the system.' }
            ]
        }
    }
},
